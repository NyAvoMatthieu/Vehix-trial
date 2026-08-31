<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trajets = Trajet::with(['vehicule', 'user'])
            ->where('user_id', auth()->id())
            ->orderBy('heure_depart', 'desc')
            ->paginate(10);

        return Inertia::render('Trajets/Index', [
            'trajets' => $trajets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupérer le véhicule sélectionné depuis la session
        $selectedVehiculeId = session('selected_vehicule_id');
        
        if (!$selectedVehiculeId) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez d\'abord sélectionner un véhicule.');
        }

        $vehicule = Vehicule::where('id', $selectedVehiculeId)
            ->where('user_id', auth()->id())
            ->where('status', 'valide')
            ->firstOrFail();

        // Récupérer le dernier kilométrage ou utiliser le kilométrage initial
        $lastOdo = Trajet::getLastOdometer($vehicule->id);
        $lastKnownOdo = $lastOdo ?: $vehicule->mileage;

        return Inertia::render('Trajets/Create', [
            'vehicule' => $vehicule,
            'lastKnownOdo' => $lastKnownOdo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'departure' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'heure_depart' => 'nullable|date',
            'heure_arrivee' => 'nullable|date|after:heure_depart',
            'purpose' => 'nullable|string|max:255',
            'kilometrage_mode' => 'required|in:odometer,trajet',
            'km_depart' => 'nullable|numeric|min:0',
            'km_arrivee' => 'required_if:kilometrage_mode,trajet|nullable|numeric|gt:km_depart',
            'odo_start' => 'nullable|numeric|min:0',
            'odo_end' => 'required_if:kilometrage_mode,odometer|nullable|numeric|gt:odo_start',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        Trajet::create($validated);

        return redirect()->route('trajets.index')
            ->with('success', 'Trajet créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trajet $trajet)
    {
        $this->authorize('view', $trajet);

        $trajet->load(['vehicule', 'user']);

        return Inertia::render('Trajets/Show', [
            'trajet' => $trajet,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trajet $trajet)
    {
        $this->authorize('update', $trajet);

        $vehicule = $trajet->vehicule;

        // Récupérer le dernier kilométrage
        $lastOdo = Trajet::getLastOdometer($vehicule->id);
        $lastKnownOdo = $lastOdo ?: $vehicule->mileage;

        return Inertia::render('Trajets/Edit', [
            'trajet' => $trajet,
            'vehicule' => $vehicule,
            'lastKnownOdo' => $lastKnownOdo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trajet $trajet)
    {
        $this->authorize('update', $trajet);

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'departure' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'heure_depart' => 'nullable|date',
            'heure_arrivee' => 'nullable|date|after:heure_depart',
            'purpose' => 'nullable|string|max:255',
            'kilometrage_mode' => 'required|in:odometer,trajet',
            'km_depart' => 'nullable|numeric|min:0',
            'km_arrivee' => 'required_if:kilometrage_mode,trajet|nullable|numeric|gt:km_depart',
            'odo_start' => 'nullable|numeric|min:0',
            'odo_end' => 'required_if:kilometrage_mode,odometer|nullable|numeric|gt:odo_start',
            'notes' => 'nullable|string',
        ]);

        $trajet->update($validated);

        return redirect()->route('trajets.index')
            ->with('success', 'Trajet modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trajet $trajet)
    {
        $this->authorize('delete', $trajet);

        $trajet->delete();

        return redirect()->route('trajets.index')
            ->with('success', 'Trajet supprimé avec succès.');
    }
}