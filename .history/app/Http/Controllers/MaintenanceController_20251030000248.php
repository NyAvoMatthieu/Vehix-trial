<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\User;
use App\Enums\MaintenanceStatus;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Maintenance::with(['vehicule', 'pieces'])
            ->where('user_id', auth()->id());

        // Filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        // Statistiques
        $stats = [
            'total_maintenances' => Maintenance::where('user_id', auth()->id())->count(),
            'en_cours' => Maintenance::where('user_id', auth()->id())->where('status', MaintenanceStatus::EN_COURS)->count(),
            'validees_ce_mois' => Maintenance::where('user_id', auth()->id())
                ->where('status', MaintenanceStatus::VALIDEE)
                ->whereMonth('date_debut', now()->month)
                ->count(),
            'cout_total_annee' => Maintenance::where('user_id', auth()->id())
                ->whereYear('date_debut', now()->year)
                ->sum('cout_total'),
            'pieces_alerte' => auth()->user()->maintenances()
                ->with('pieces')
                ->get()
                ->pluck('pieces')
                ->flatten()
                ->where('alerte_proche_limite', true)
                ->count(),
        ];

        return Inertia::render('Maintenances/Index', [
            'maintenances' => $maintenances,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $selectedVehiculeId = session('selected_vehicule_id');
        $vehicule = Vehicule::find($selectedVehiculeId);

        if (!$vehicule) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule.');
        }

        // Récupérer le dernier kilométrage
        $lastKilometrage = Maintenance::getLastKilometrage($vehicule->id);

        return Inertia::render('Maintenances/Create', [
            'vehicule' => $vehicule,
            'lastKilometrage' => $lastKilometrage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
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
            
            // Informations du garage
            'garage_nom' => 'required|string|max:255',
            'garage_lieu' => 'required|string|max:255',
            'garage_contact' => 'required|string|max:100',
            
            // Pièces
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
            'pieces.*.unite_mesure' => 'required|in:km,heures,cycles,jours,mois,annees',
            'pieces.*.observation' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $validated['user_id'] = auth()->id();
            $validated['status'] = MaintenanceStatus::EN_ATTENTE->value;

            // Créer la maintenance
            $pieces = $validated['pieces'] ?? [];
            unset($validated['pieces']);

            $maintenance = Maintenance::create($validated);

            // Créer les pièces
            foreach ($pieces as $pieceData) {
                // Si la pièce est neuve, forcer utilisation_actuelle à 0
                if ($pieceData['etat_piece'] === 'neuf') {
                    $pieceData['utilisation_actuelle'] = 0;
                }
                
                $maintenance->pieces()->create($pieceData);
            }

            DB::commit();

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Maintenance créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {
        $this->authorize('view', $maintenance);

        $maintenance->load([
            'vehicule',
            'user',
            'validateur',
            'pieces' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        // Historique des maintenances du véhicule
        $historique = Maintenance::where('vehicule_id', $maintenance->vehicule_id)
            ->where('id', '!=', $maintenance->id)
            ->where('status', MaintenanceStatus::VALIDEE)
            ->orderBy('date_debut', 'desc')
            ->limit(5)
            ->get(['id', 'reference', 'date_debut', 'nature_intervention', 'kilometrage_actuel']);

        return Inertia::render('Maintenances/Show', [
            'maintenance' => $maintenance,
            'historique' => $historique,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);

        if ($maintenance->isValidee()) {
            return redirect()->route('maintenances.show', $maintenance)
                ->with('error', 'Une maintenance validée ne peut plus être modifiée.');
        }

        // Charger les relations nécessaires
        $maintenance->load(['pieces', 'vehicule']);

        return Inertia::render('Maintenances/Edit', [
            'maintenance' => $maintenance,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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
            'status' => 'required|in:en_attente,en_cours',
            
            // Informations du garage
            'garage_nom' => 'required|string|max:255',
            'garage_lieu' => 'required|string|max:255',
            'garage_contact' => 'required|string|max:100',
            
            // Pièces
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
            'pieces.*.unite_mesure' => 'required|in:km,heures,cycles,jours,mois,annees',
            'pieces.*.observation' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $pieces = $validated['pieces'] ?? [];
            unset($validated['pieces']);

            $maintenance->update($validated);

            // Gérer les pièces
            $pieceIds = [];
            foreach ($pieces as $pieceData) {
                // Si la pièce est neuve, forcer utilisation_actuelle à 0
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

            // Supprimer les pièces non présentes
            $maintenance->pieces()->whereNotIn('id', $pieceIds)->delete();

            DB::commit();

            return redirect()->route('maintenances.show', $maintenance)
                ->with('success', 'Maintenance mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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

    public function validate(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'notes_validation' => 'nullable|string|max:1000',
            'date_fin' => 'required|date|after_or_equal:' . $maintenance->date_debut,
        ]);

        $maintenance->update([
            'status' => MaintenanceStatus::VALIDEE,
            'validated_at' => now(),
            'validateur_id' => auth()->id(),
            'notes_validation' => $validated['notes_validation'] ?? null,
            'date_fin' => $validated['date_fin'],
        ]);

        return back()->with('success', 'Maintenance validée avec succès.');
    }
}