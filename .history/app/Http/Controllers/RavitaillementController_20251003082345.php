<?php

namespace App\Http\Controllers;

use App\Models\Ravitaillement;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use App\Models\Trajet;
use Inertia\Inertia;

class RavitaillementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $ravitaillements = Ravitaillement::with(['vehicule', 'user'])
            ->where('user_id', auth()->id())
            ->orderBy('ravitaillement_date', 'desc')
            ->paginate(10);

        return Inertia::render('Ravitaillements/Index', [
            'ravitaillements' => $ravitaillements
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $vehicule = session('selected_vehicule_id')
            ? Vehicule::find(session('selected_vehicule_id'))
            : auth()->user()->vehicules()->where('is_validated', true)->first();

        if (!$vehicule) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule validé.');
        }

        // Récupérer le trajet en cours
        $currentTrajet = Trajet::where('vehicule_id', $vehicule->id)
            ->where('user_id', auth()->id())
            ->whereNull('arrival_date')
            ->first();

        // Récupérer le dernier ravitaillement
        $lastFueling = Ravitaillement::where('vehicule_id', $vehicule->id)
            ->orderBy('odo_station', 'desc')
            ->first();

        return Inertia::render('Ravitaillements/Create', [
            'vehicule' => $vehicule,
            'currentTrajet' => $currentTrajet,
            'lastFueling' => $lastFueling,
            'fuelTypes' => $this->getFuelTypes(),
            'paymentMethods' => $this->getPaymentMethods(),
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
            'ravitaillement_date' => 'required|date',
            'station_name' => 'required|string|max:255',
            'liters' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|integer|min:0',
            'odo_arrival' => 'nullable|integer|min:0',
            'fuel_type' => 'required|string',
            'payment_method' => 'required|string',
            'receipt_number' => 'nullable|string|max:100',
            'full_tank' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);

        // Si odo_station n'est pas renseigné
        if (empty($validated['odo_station'])) {
            // Essayer de prendre le kilométrage du trajet en cours
            $currentTrajet = Trajet::where('vehicule_id', $vehicule->id)
                ->where('user_id', auth()->id())
                ->whereNull('arrival_date')
                ->first();

            if ($currentTrajet) {
                $validated['odo_station'] = $currentTrajet->odo_departure;
            } else {
                // Sinon prendre le kilométrage initial du véhicule
                $validated['odo_station'] = $vehicule->current_mileage ?? 0;
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['total_cost'] = $validated['liters'] * $validated['price_per_liter'];

        $ravitaillement = Ravitaillement::create($validated);

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ravitaillement $ravitaillement)
    {
        //
        $this->authorize('view', $ravitaillement);

        $ravitaillement->load(['vehicule', 'user', 'recus']);

        return Inertia::render('Ravitaillements/Show', [
            'ravitaillement' => $ravitaillement
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ravitaillement $ravitaillement)
    {
        //
        $this->authorize('update', $ravitaillement);
        $ravitaillement->load(['vehicule']);

        return Inertia::render('Ravitaillements/Edit', [
            'ravitaillement' => $ravitaillement,
            'fuelTypes' => $this->getFuelTypes(),
            'paymentMethods' => $this->getPaymentMethods(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ravitaillement $ravitaillement)
    {
        //
        $validated = $request->validate([
            'ravitaillement_date' => 'required|date',
            'station_name' => 'required|string|max:255',
            'liters' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|integer|min:0',
            'odo_arrival' => 'nullable|integer|min:0',
            'fuel_type' => 'required|string',
            'payment_method' => 'required|string',
            'receipt_number' => 'nullable|string|max:100',
            'full_tank' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $validated['total_cost'] = $validated['liters'] * $validated['price_per_liter'];

        $ravitaillement->update($validated);

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ravitaillement $ravitaillement)
    {
        //
        $ravitaillement->delete();

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement supprimé avec succès.');
    }

    private function getFuelTypes()
    {
        return [
            'essence' => 'Essence',
            'diesel' => 'Diesel',
            'gpl' => 'GPL',
            'electrique' => 'Électrique',
            'hybride' => 'Hybride',
        ];
    }

    private function getPaymentMethods()
    {
        return [
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'mobile' => 'Paiement mobile',
            'check' => 'Chèque',
            'voucher' => 'Bon d\'essence',
        ];
    }
}
