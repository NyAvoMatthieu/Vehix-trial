<?php

namespace App\Http\Controllers;

use App\Models\VisiteTechnique;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Http\Requests\VisiteTechniqueRequest;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;

class VisiteTechniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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

        // Filtres
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_pv', 'like', "%{$search}%")
                  ->orWhere('numero_recu', 'like', "%{$search}%")
                  ->orWhere('centre', 'like', "%{$search}%")
                  ->orWhere('immatriculation', 'like', "%{$search}%");
            });
        }

        if ($request->has('aptitude') && $request->get('aptitude') !== 'all') {
            $query->where('aptitude', $request->get('aptitude'));
        }

        $visites = $query->orderBy('date_visite', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('VisitesTechniques/Index', [
            'visites' => $visites,
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
            'filters' => $request->only(['search', 'aptitude']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
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

        return Inertia::render('VisitesTechniques/Create', [
            'selectedVehicule' => $selectedVehicule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VisiteTechnique ,Request $request)
    {
        //
         $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        VisiteTechnique::create($validated);

        return redirect()->route('visite-techniques.index')
            ->with('success', 'Visite technique enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VisiteTechnique $visiteTechnique)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisiteTechnique $visiteTechnique)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VisiteTechnique $visiteTechnique)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisiteTechnique $visiteTechnique)
    {
        //
    }
}
