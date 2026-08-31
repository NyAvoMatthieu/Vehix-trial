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
            ->orderBy('trajet_date', 'desc')
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
        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('status', 'valide')
            ->get(['id', 'make', 'model', 'license_plate', 'mileage']);

         // Get last known odometer for each vehicle
        $lastOdometers = [];
        foreach ($vehicules as $vehicule) {
            $lastOdo = Trajet::getLastOdometer($vehicule->id);
            // Si aucun trajet n'existe, utiliser le mileage initial du véhicule
            $lastOdometers[$vehicule->id] = $lastOdo ?: $vehicule->mileage;
        }

        return Inertia::render('Trajets/Create', [
            'vehicules' => $vehicules,
            'lastOdometers' => $lastOdometers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
           'vehicule_id' => 'required|exists:vehicules,id',
            'trajet_date' => 'required|date',
            'departure' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'heure_depart' => 'required|date_format:H:i',
            'heure_arrivee' => 'required|date_format:H:i|after:heure_depart',
            'purpose' => 'required|string|max:255',
            'kilometrage_mode' => 'required|in:odometer,trajet',
            'km_depart' => 'nullable|numeric|min:0',
            'km_arrivee' => 'nullable|numeric|gt:km_depart',
            'odo_start' => 'nullable|numeric|min:0',
            'odo_end' => 'nullable|numeric|gt:odo_start',
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

        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('status', 'valide')
            ->get(['id', 'make', 'model', 'license_plate']);

            // Get last known odometer for each vehicle
        $lastOdometers = [];
        foreach ($vehicules as $vehicule) {
            $lastOdo = Trajet::getLastOdometer($vehicule->id);
            // Si aucun trajet n'existe, utiliser le mileage initial du véhicule
            $lastOdometers[$vehicule->id] = $lastOdo ?: $vehicule->mileage;
        }

        return Inertia::render('Trajets/Edit', [
            'trajet' => $trajet,
            'vehicules' => $vehicules,
            'lastOdometers' => $lastOdometers,
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
            'trajet_date' => 'required|date',
            'departure' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'heure_depart' => 'required|date_format:H:i',
            'heure_arrivee' => 'required|date_format:H:i|after:heure_depart',
            'purpose' => 'required|string|max:255',
            'km_depart' => 'required|numeric|min:0',
            'km_arrivee' => 'required|numeric|gt:km_depart',
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