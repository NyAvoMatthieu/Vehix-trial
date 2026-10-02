<?php

namespace App\Http\Controllers;

use App\Enums\AlertType;
use App\Models\AlertSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlertSettingController extends Controller
{
    /**
     * Affiche l'écran de réglage des seuils d'alerte (un par type d'échéance).
     */
    public function index()
    {
        $settings = AlertSetting::all()->keyBy('type');

        $rows = collect(AlertType::cases())->map(function (AlertType $type) use ($settings) {
            $setting = $settings->get($type->value);

            return [
                'type' => $type->value,
                'label' => $type->label(),
                'seuil_jours' => $setting->seuil_jours ?? 30,
                'periodicite_jours' => $setting->periodicite_jours ?? null,
                'actif' => $setting->actif ?? true,
                'seuil_km' => $setting->seuil_km ?? null,
                'retard_jours' => $setting->retard_jours ?? null,
                'retard_km' => $setting->retard_km ?? null,
                'actif' => $setting->actif ?? true,

            ];
        })->values();

        return Inertia::render('Admin/AlertSettings/Index', [
            'settings' => $rows,
        ]);
    }

    /**
     * Met à jour les seuils (un par type) en une seule requête.
     */
    public function update(Request $request)
    {
        $validTypes = array_map(fn(AlertType $t) => $t->value, AlertType::cases());

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.type' => 'required|string|in:' . implode(',', $validTypes),
            'settings.*.seuil_jours' => 'required|integer|min:1|max:365',
            'settings.*.periodicite_jours' => 'nullable|integer|min:1|max:3650',
            'settings.*.seuil_km' => 'nullable|integer|min:0',
            'settings.*.retard_jours' => 'nullable|integer|min:0',
            'settings.*.retard_km' => 'nullable|integer|min:0',
            'settings.*.actif' => 'required|boolean',
        ]);
        foreach ($validated['settings'] as $row) {
            AlertSetting::updateOrCreate(
                ['type' => $row['type']],
                [
                    'seuil_jours' => $row['seuil_jours'],
                    'periodicite_jours' => $row['periodicite_jours'] ?? null,
                    'actif' => $row['actif'],
                    'updated_by' => $request->user()->id,
                    'seuil_km' => $row['seuil_km'] ?? null,
                    'retard_jours' => $row['retard_jours'] ?? null,
                    'retard_km' => $row['retard_km'] ?? null,
                    'actif' => $row['actif'],
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        return back()->with('success', "Seuils d'alerte mis à jour avec succès.");
    }
}
