<?php

namespace App\Http\Controllers;

use App\Models\VisiteTechnique;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\AlertSetting;
use App\Enums\AlertType;
use App\Http\Requests\VisiteTechniqueRequest;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;

class VisiteTechniqueController extends Controller
{
    // Affiche la liste des ressources
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        $query = VisiteTechnique::with(['vehicule', 'user']);

        if ($user->isClient()) {
            if ($selectedVehiculeId) {
                $query->where('vehicule_id', $selectedVehiculeId);
            } else {
                $query->whereHas('vehicule', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        // Filtre recherche libre
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_pv', 'like', "%{$search}%")
                  ->orWhere('numero_recu', 'like', "%{$search}%")
                  ->orWhere('centre', 'like', "%{$search}%");
            });
        }

        // Filtre aptitude
        if ($request->filled('aptitude') && $request->get('aptitude') !== 'all') {
            $query->where('aptitude', $request->get('aptitude'));
        }

        // Filtre véhicule
        if ($request->filled('vehicule_id') && $request->get('vehicule_id') !== 'all') {
            $query->where('vehicule_id', $request->get('vehicule_id'));
        }

        // Filtre période (sur la date de visite)
        if ($request->filled('date_debut')) {
            $query->whereDate('date_visite', '>=', $request->get('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_visite', '<=', $request->get('date_fin'));
        }

        // Filtre statut d'échéance (à jour / bientôt échue / expirée)
        if ($request->filled('statut') && $request->get('statut') !== 'all') {
            $seuil = AlertSetting::seuilPour(AlertType::VISITE_TECHNIQUE);

            match ($request->get('statut')) {
                'expired' => $query->whereNotNull('validite')->where('validite', '<', now()),
                'approaching' => $query->whereNotNull('validite')
                    ->where('validite', '>=', now())
                    ->where('validite', '<=', now()->addDays($seuil)),
                'ok' => $query->where(function ($q) use ($seuil) {
                    $q->whereNull('validite')
                      ->orWhere('validite', '>', now()->addDays($seuil));
                }),
                default => null,
            };
        }

        $visites = $query->orderBy('date_visite', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Véhicules disponibles pour le filtre, scopés selon le rôle
        $vehiculesQuery = Vehicule::query();
        if ($user->isClient()) {
            $vehiculesQuery->where('user_id', $user->id);
        }
        $vehicules = $vehiculesQuery->orderBy('make')->get(['id', 'make', 'model', 'license_plate']);

        return Inertia::render('VisiteTechniques/Index', [
            'visites' => $visites,
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
            'vehicules' => $vehicules,
            'filters' => $request->only(['search', 'aptitude', 'vehicule_id', 'statut', 'date_debut', 'date_fin']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        // Si pas de véhicule sélectionné, rediriger
        if (!$selectedVehiculeId) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez d\'abord sélectionner un véhicule.');
        }

        $selectedVehicule = Vehicule::findOrFail($selectedVehiculeId);

        // Vérifier que le véhicule appartient à l'utilisateur
        if ($selectedVehicule->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('VisiteTechniques/Create', [
            'selectedVehicule' => $selectedVehicule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VisiteTechniqueRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        VisiteTechnique::create($validated);

        return redirect()->route('visite-techniques.index')
            ->with('success', 'Visite technique enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, VisiteTechnique $visiteTechnique)
    {
        $this->authorize('view', $visiteTechnique);

        $visiteTechnique->load(['vehicule', 'user']);

        // Historique des autres visites du même véhicule
        $historique = VisiteTechnique::where('vehicule_id', $visiteTechnique->vehicule_id)
            ->where('id', '!=', $visiteTechnique->id)
            ->orderBy('date_visite', 'desc')
            ->limit(10)
            ->get(['id', 'date_visite', 'validite', 'aptitude', 'numero_pv', 'centre']);

        // Dernière visite connue du véhicule (2.3.1 du cahier des charges),
        // toutes visites confondues (y compris celle affichée si elle est la plus récente)
        $derniereVisiteVehicule = VisiteTechnique::where('vehicule_id', $visiteTechnique->vehicule_id)
            ->max('date_visite');

        return Inertia::render('VisiteTechniques/Show', [
            'visite' => $visiteTechnique,
            'historique' => $historique,
            'derniereVisiteVehicule' => $derniereVisiteVehicule,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisiteTechnique $visiteTechnique)
    {
        $this->authorize('update', $visiteTechnique);

        $visiteTechnique->load('vehicule');

        return Inertia::render('VisiteTechniques/Edit', [
            'visite' => $visiteTechnique,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VisiteTechniqueRequest $request, VisiteTechnique $visiteTechnique)
    {
        $this->authorize('update', $visiteTechnique);

        $visiteTechnique->update($request->validated());

        return redirect()->route('visite-techniques.index')
            ->with('success', 'Visite technique mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisiteTechnique $visiteTechnique)
    {
        $this->authorize('delete', $visiteTechnique);

        $visiteTechnique->delete();

        return redirect()->route('visite-techniques.index')
            ->with('success', 'Visite technique supprimée avec succès.');
    }
}
