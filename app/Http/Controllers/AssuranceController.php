<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;

class AssuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        $query = Assurance::with(['vehicule', 'user']);

        if ($user->isClient()) {
            if ($selectedVehiculeId) {
                $query->where('vehicule_id', $selectedVehiculeId);
            } else {
                $query->whereHas('vehicule', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        $assurances = $query->orderBy('end_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Assurances/Index', [
            'assurances' => $assurances,
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        // Si aucun véhicule n'est sélectionné, rediriger vers la sélection
        if (!$selectedVehiculeId) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule avant de créer une assurance.');
        }

        // Récupérer le véhicule sélectionné
        $selectedVehicule = Vehicule::where('id', $selectedVehiculeId)
            ->where('user_id', $user->id)
            ->where('status', VehiculeStatus::VALIDATED)
            ->first();

        if (!$selectedVehicule) {
            session()->forget('selected_vehicule_id');
            return redirect()->route('vehicules.selection')
                ->with('error', 'Le véhicule sélectionné n\'est plus valide.');
        }

        return Inertia::render('Assurances/Create', [
            'selectedVehicule' => $selectedVehicule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'company' => 'nullable|string|max:255',
            'assureur' => 'nullable|string|max:255',
            'agence' => 'nullable|string|max:255',
            'policy_number' => 'nullable|string|max:255|unique:assurances,policy_number',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'date_delivrance' => 'nullable|date',
            'cotisation' => 'nullable|numeric|min:0',
            'prime_cp' => 'nullable|numeric|min:0',
            'prime_de' => 'nullable|numeric|min:0',
            'prime_ca' => 'nullable|numeric|min:0',
            'prime_div' => 'nullable|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'coverage_details' => 'nullable|string',
            'lieu_signature' => 'nullable|string|max:255',
            'date_signature' => 'nullable|date',
            'agent_nom' => 'nullable|string|max:255',
            'notes_signature' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        Assurance::create($validated);

        return redirect()->route('assurances.index')
            ->with('success', 'Assurance créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Assurance $assurance)
    {
        $this->authorize('view', $assurance);

        $assurance->load(['vehicule', 'user', 'recus']);

        return Inertia::render('Assurances/Show', [
            'assurance' => $assurance,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assurance $assurance)
    {
        $this->authorize('update', $assurance);

        $vehicules = Vehicule::where('user_id', auth()->id())
            ->where('status', VehiculeStatus::VALIDATED)
            ->get(['id', 'make', 'model', 'year', 'license_plate']);

        return Inertia::render('Assurances/Edit', [
            'assurance' => $assurance,
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assurance $assurance)
    {
        $this->authorize('update', $assurance);

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'company' => 'nullable|string|max:255',
            'assureur' => 'nullable|string|max:255',
            'agence' => 'nullable|string|max:255',
            'policy_number' => 'nullable|string|max:255|unique:assurances,policy_number,' . $assurance->id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'date_delivrance' => 'nullable|date',
            'cotisation' => 'nullable|numeric|min:0',
            'prime_cp' => 'nullable|numeric|min:0',
            'prime_de' => 'nullable|numeric|min:0',
            'prime_ca' => 'nullable|numeric|min:0',
            'prime_div' => 'nullable|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'coverage_details' => 'nullable|string',
            'lieu_signature' => 'nullable|string|max:255',
            'date_signature' => 'nullable|date',
            'agent_nom' => 'nullable|string|max:255',
            'notes_signature' => 'nullable|string',
        ]);

        $assurance->update($validated);

        return redirect()->route('assurances.index')
            ->with('success', 'Assurance mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assurance $assurance)
    {
        $this->authorize('delete', $assurance);

        $assurance->delete();

        return redirect()->route('assurances.index')
            ->with('success', 'Assurance supprimée avec succès.');
    }
}