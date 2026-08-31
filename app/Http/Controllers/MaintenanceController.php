<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\User;
use App\Models\MaintenancePiece;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedVehiculeId = session('selected_vehicule_id');
    
        $query = Maintenance::with(['vehicule', 'pieces'])
            ->where('user_id', auth()->id());

        if ($selectedVehiculeId) {
            $query->where('vehicule_id', $selectedVehiculeId);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('nature_intervention', 'like', "%{$search}%")
                  ->orWhere('garage_nom', 'like', "%{$search}%")
                  ->orWhereHas('vehicule', function($q) use ($search) {
                      $q->where('license_plate', 'like', "%{$search}%");
                  });
            });
        }

        $maintenances = $query->orderBy('date_debut', 'desc')->paginate(15);

        // Recalculer le coût total pour chaque maintenance (au cas où)
        foreach ($maintenances as $maintenance) {
            $maintenance->cout_total = ($maintenance->cout_main_oeuvre ?? 0) + ($maintenance->cout_pieces ?? 0);
        }

        // Statistiques FILTRÉES
        $statsQuery = Maintenance::where('user_id', auth()->id());
        if ($selectedVehiculeId) {
            $statsQuery->where('vehicule_id', $selectedVehiculeId);
        }

        // Calculer les statistiques
        $allMaintenances = (clone $statsQuery)->get();
        
        $coutMainOeuvre = $allMaintenances->sum('cout_main_oeuvre');
        $coutPieces = $allMaintenances->sum('cout_pieces');
        $coutTotal = $coutMainOeuvre + $coutPieces;

        // Statistiques du mois en cours
        $statsQueryMois = Maintenance::where('user_id', auth()->id())
            ->whereMonth('date_debut', now()->month)
            ->whereYear('date_debut', now()->year);
        
        if ($selectedVehiculeId) {
            $statsQueryMois->where('vehicule_id', $selectedVehiculeId);
        }

        $maintenancesMois = $statsQueryMois->get();
        $coutMainOeuvreMois = $maintenancesMois->sum('cout_main_oeuvre');
        $coutPiecesMois = $maintenancesMois->sum('cout_pieces');
        $coutTotalMois = $coutMainOeuvreMois + $coutPiecesMois;

        // Compter les pièces en alerte
        $piecesAlerteQuery = MaintenancePiece::whereHas('maintenance', function($q) use ($selectedVehiculeId) {
            $q->where('user_id', auth()->id());
            if ($selectedVehiculeId) {
                $q->where('vehicule_id', $selectedVehiculeId);
            }
        })->where('alerte_proche_limite', true);

        $stats = [
            'total_maintenances' => $allMaintenances->count(),
            'cout_total_main_oeuvre' => (float) $coutMainOeuvre,
            'cout_total_pieces' => (float) $coutPieces,
            'cout_total_global' => (float) $coutTotal,
            
            // Statistiques du mois
            'total_maintenances_mois' => $maintenancesMois->count(),
            'cout_total_main_oeuvre_mois' => (float) $coutMainOeuvreMois,
            'cout_total_pieces_mois' => (float) $coutPiecesMois,
            'cout_total_global_mois' => (float) $coutTotalMois,
            
            // Alertes pièces
            'pieces_alerte' => $piecesAlerteQuery->count(),
        ];

        return Inertia::render('Maintenances/Index', [
            'maintenances' => $maintenances,
            'stats' => $stats,
            'filters' => $request->only(['search']),
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
        ]);
    }

    public function create()
    {
        $selectedVehiculeId = session('selected_vehicule_id');
        $vehicule = Vehicule::find($selectedVehiculeId);

        if (!$vehicule) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule.');
        }

        $lastKilometrage = Maintenance::getLastKilometrage($vehicule->id);

        return Inertia::render('Maintenances/Create', [
            'vehicule' => $vehicule,
            'lastKilometrage' => $lastKilometrage,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'nature_intervention' => 'required|string|max:255',
            'kilometrage_actuel' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'observation_generale' => 'nullable|string|max:2000',
            'cout_main_oeuvre' => 'required|numeric|min:0',
            'garage_nom' => 'required|string|max:255',
            'garage_lieu' => 'required|string|max:255',
            'garage_contact' => 'required|string|max:100',
            'pieces' => 'nullable|array',
            'pieces.*.nom_piece' => 'required|string|max:255',
            'pieces.*.marque_piece' => 'required|string|max:255',
            'pieces.*.reference_code' => 'nullable|string|max:100',
            'pieces.*.emplacement' => 'nullable|string|max:255',
            'pieces.*.etat_piece' => 'required|in:neuf,occasion',
            'pieces.*.vendeur' => 'required|string|max:255',
            'pieces.*.quantite' => 'required|integer|min:1',
            'pieces.*.prix_unitaire' => 'required|numeric|min:0',
            'pieces.*.date_installation' => 'required|date',
            'pieces.*.limite_utilisation' => 'required|numeric|min:0',
            'pieces.*.utilisation_actuelle' => 'nullable|numeric|min:0',
            'pieces.*.unite_mesure' => 'required|in:km,heures,cycles,tours,jours,mois,annees',
            'pieces.*.observation' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $validated['user_id'] = auth()->id();

            $pieces = $validated['pieces'] ?? [];
            unset($validated['pieces']);

            $maintenance = Maintenance::create($validated);

            foreach ($pieces as $pieceData) {
                if ($pieceData['etat_piece'] === 'neuf') {
                    $pieceData['utilisation_actuelle'] = 0;
                }
                $maintenance->pieces()->create($pieceData);
            }

            DB::commit();

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Maintenance créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création maintenance:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    public function show(Maintenance $maintenance)
    {
        $this->authorize('view', $maintenance);

        $maintenance->load([
            'vehicule',
            'user',
            'validateur',
            'pieces' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        $historique = Maintenance::where('vehicule_id', $maintenance->vehicule_id)
            ->where('id', '!=', $maintenance->id)
            ->whereNotNull('validated_at')
            ->orderBy('date_debut', 'desc')
            ->limit(5)
            ->get(['id', 'reference', 'date_debut', 'nature_intervention', 'kilometrage_actuel']);

        return Inertia::render('Maintenances/Show', [
            'maintenance' => $maintenance,
            'historique' => $historique,
        ]);
    }

    public function edit(Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);

        if ($maintenance->isValidee()) {
            return redirect()->route('maintenances.show', $maintenance)
                ->with('error', 'Une maintenance validée ne peut plus être modifiée.');
        }

        $maintenance->load(['pieces', 'vehicule']);

        return Inertia::render('Maintenances/Edit', [
            'maintenance' => $maintenance,
        ]);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);

        if ($maintenance->isValidee()) {
            return redirect()->route('maintenances.show', $maintenance)
                ->with('error', 'Une maintenance validée ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'nature_intervention' => 'required|string|max:255',
            'kilometrage_actuel' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'observation_generale' => 'nullable|string|max:2000',
            'cout_main_oeuvre' => 'required|numeric|min:0',
            'garage_nom' => 'required|string|max:255',
            'garage_lieu' => 'required|string|max:255',
            'garage_contact' => 'required|string|max:100',
            'pieces' => 'nullable|array',
            'pieces.*.id' => 'nullable|exists:maintenance_pieces,id',
            'pieces.*.nom_piece' => 'required|string|max:255',
            'pieces.*.marque_piece' => 'required|string|max:255',
            'pieces.*.reference_code' => 'nullable|string|max:100',
            'pieces.*.emplacement' => 'nullable|string|max:255',
            'pieces.*.etat_piece' => 'required|in:neuf,occasion',
            'pieces.*.vendeur' => 'required|string|max:255',
            'pieces.*.quantite' => 'required|integer|min:1',
            'pieces.*.prix_unitaire' => 'required|numeric|min:0',
            'pieces.*.date_installation' => 'required|date',
            'pieces.*.limite_utilisation' => 'required|numeric|min:0',
            'pieces.*.utilisation_actuelle' => 'nullable|numeric|min:0',
            'pieces.*.unite_mesure' => 'required|in:km,heures,cycles,tours,jours,mois,annees',
            'pieces.*.observation' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $pieces = $validated['pieces'] ?? [];
            unset($validated['pieces']);

            $maintenance->update($validated);

            $pieceIds = [];
            foreach ($pieces as $pieceData) {
                if ($pieceData['etat_piece'] === 'neuf') {
                    $pieceData['utilisation_actuelle'] = 0;
                }

                if (isset($pieceData['id'])) {
                    $piece = $maintenance->pieces()->find($pieceData['id']);
                    if ($piece) {
                        $piece->update($pieceData);
                        $pieceIds[] = $piece->id;
                    }
                } else {
                    $piece = $maintenance->pieces()->create($pieceData);
                    $pieceIds[] = $piece->id;
                }
            }

            $maintenance->pieces()->whereNotIn('id', $pieceIds)->delete();

            DB::commit();

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Maintenance mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(Maintenance $maintenance)
    {
        $this->authorize('delete', $maintenance);

        if ($maintenance->isValidee()) {
            return back()->with('error', 'Une maintenance validée ne peut pas être supprimée.');
        }

        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance supprimée avec succès.');
    }

    public function validateMaintenance(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'notes_validation' => 'nullable|string|max:1000',
            'date_fin' => 'required|date|after_or_equal:' . $maintenance->date_debut,
        ]);

        $maintenance->update([
            'validated_at' => now(),
            'validateur_id' => auth()->id(),
            'notes_validation' => $validated['notes_validation'] ?? null,
            'date_fin' => $validated['date_fin'],
        ]);

        return back()->with('success', 'Maintenance validée avec succès.');
    }
}