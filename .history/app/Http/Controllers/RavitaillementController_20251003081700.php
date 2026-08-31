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
    }

    /**
     * Display the specified resource.
     */
    public function show(Ravitaillement $ravitaillement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ravitaillement $ravitaillement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ravitaillement $ravitaillement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ravitaillement $ravitaillement)
    {
        //
    }
}
