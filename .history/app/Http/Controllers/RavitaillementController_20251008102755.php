<?php

namespace App\Http\Controllers;

use App\Models\Ravitaillement;
use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RavitaillementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ravitaillements = Ravitaillement::with(['vehicule', 'user', 'chauffeur'])
            ->where('user_id', auth()->id())
            ->orderBy('ravitaillement_date', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_spent' => Ravitaillement::where('user_id', auth()->id())->sum('total_cost'),
            'total_liters' => Ravitaillement::where('user_id', auth()->id())->sum('total_liters'),
            'this_month_spent' => Ravitaillement::where('user_id', auth()->id())
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('total_cost'),
            'this_month_liters' => Ravitaillement::where('user_id', auth()->id())
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('total_liters'),
        ];

        return Inertia::render('Ravitaillements/Index', [
            'ravitaillements' => $ravitaillements,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $selectedVehiculeId = session('selected_vehicule_id');
        $vehicule = Vehicule::with(['user'])->find($selectedVehiculeId);

        if (!$vehicule) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule.');
        }

        // Get last odometer reading
        $lastOdometer = Ravitaillement::getLastOdometer($vehicule->id);
        if ($lastOdometer === null) {
            $lastOdometer = $vehicule->mileage;
        }

        // Get available drivers (chauffeurs)
        $chauffeurs = User::where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Ravitaillements/Create', [
            'vehicule' => $vehicule,
            'lastOdometer' => $lastOdometer,
            'chauffeurs' => $chauffeurs,
            'fuelType' => $vehicule->fuel_type,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:users,id',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_service' => 'required|string|max:255',
            'liters_purchased' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'amount_paid' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|numeric|min:0',
            'odo_arrival' => 'nullable|numeric|min:0|gt:odo_station',
            'payment_method' => 'required|in:carte,cash,virement,mobile',
            'receipt_number' => 'nullable|string|max:100',
            'is_full_tank' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['fuel_type'] = Vehicule::find($validated['vehicule_id'])->fuel_type;

        Ravitaillement::create($validated);

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ravitaillement $ravitaillement)
    {
        $this->authorize('view', $ravitaillement);

        $ravitaillement->load(['vehicule', 'user', 'chauffeur']);

        return Inertia::render('Ravitaillements/Show', [
            'ravitaillement' => $ravitaillement,
            'consumptionRate' => $ravitaillement->getConsumptionRate(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ravitaillement $ravitaillement)
    {
        $this->authorize('update', $ravitaillement);

        $vehicule = Vehicule::with(['user'])->find($ravitaillement->vehicule_id);

        // Get last odometer reading (excluding current record)
        $lastOdometer = Ravitaillement::where('vehicule_id', $vehicule->id)
            ->where('id', '!=', $ravitaillement->id)
            ->whereNotNull('odo_arrival')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('odo_arrival');

        if ($lastOdometer === null) {
            $lastOdometer = $vehicule->mileage;
        }

        // Get available drivers
        $chauffeurs = User::where('id', '!=', auth()->id())
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Ravitaillements/Edit', [
            'ravitaillement' => $ravitaillement,
            'vehicule' => $vehicule,
            'lastOdometer' => $lastOdometer,
            'chauffeurs' => $chauffeurs,
            'fuelType' => $vehicule->fuel_type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ravitaillement $ravitaillement)
    {
        $this->authorize('update', $ravitaillement);

        $validated = $request->validate([
            'chauffeur_id' => 'nullable|exists:users,id',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_service' => 'required|string|max:255',
            'liters_purchased' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'amount_paid' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|numeric|min:0',
            'odo_arrival' => 'nullable|numeric|min:0|gt:odo_station',
            'payment_method' => 'required|in:carte,cash,virement,mobile',
            'receipt_number' => 'nullable|string|max:100',
            'is_full_tank' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $ravitaillement->update($validated);

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ravitaillement $ravitaillement)
    {
        $this->authorize('delete', $ravitaillement);

        $ravitaillement->delete();

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement supprimé avec succès.');
    }
}
