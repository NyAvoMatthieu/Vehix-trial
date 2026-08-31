<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use Inertia\Inertia;


class TrajetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $trajets = Trajet::with(['vehicule', 'user'])
            ->where('user_id', auth()->user()->id)
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
        //
        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('status', 'valide')
            ->get(['id', 'make', 'model', 'license_plate']);

        return Inertia::render('Trajets/Create', [
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
        $this->authorize('update', $trajet);

        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('status', 'valide')
            ->get(['id', 'make', 'model', 'license_plate']);

        return Inertia::render('Trajets/Edit', [
            'trajet' => $trajet,
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trajet $trajet)
    {
        //
          // Vérifier que le trajet appartient à l'utilisateur connecté
        if ($trajet->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce trajet.');
        }


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
        //
        $this->authorize('delete', $trajet);

         // Vérifier que le trajet appartient à l'utilisateur connecté
        if ($trajet->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce trajet.');
        }

        $trajet->delete();

        return redirect()->route('trajets.index')
            ->with('success', 'Trajet supprimé avec succès.');
    }
}
