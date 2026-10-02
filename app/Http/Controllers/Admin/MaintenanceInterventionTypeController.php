<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceInterventionType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceInterventionTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/MaintenanceInterventionTypes/Index', [
            'types' => MaintenanceInterventionType::orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        MaintenanceInterventionType::create($validated);

        return back()->with('success', "Type d'intervention créé avec succès.");
    }

    public function update(Request $request, MaintenanceInterventionType $maintenanceInterventionType)
    {
        $validated = $this->validated($request);

        $maintenanceInterventionType->update($validated);

        return back()->with('success', "Type d'intervention mis à jour.");
    }

    public function destroy(MaintenanceInterventionType $maintenanceInterventionType)
    {
        // Désactivation plutôt que suppression : préserve l'historique des
        // maintenances déjà rattachées à ce type.
        $maintenanceInterventionType->update(['actif' => false]);

        return back()->with('success', "Type d'intervention désactivé.");
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'intervalle_km' => 'nullable|integer|min:0|required_without:intervalle_jours',
            'intervalle_jours' => 'nullable|integer|min:0|required_without:intervalle_km',
            'actif' => 'boolean',
        ]);
    }
}
