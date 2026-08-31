<?php

namespace App\Http\Controllers;

use App\Models\FuelPrice;
use App\Models\Ravitaillement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuelPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrateur,validator']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fuelPrices = FuelPrice::with('setter')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $currentPrices = FuelPrice::getCurrentPrices();

        // Get custom prices statistics for each fuel type
        $customPriceStats = [];
        foreach (['diesel', 'essence', 'gpl', 'electrique'] as $fuelType) {
            $stats = Ravitaillement::getCustomPriceStats($fuelType);
            if ($stats['count'] > 0) {
                $customPriceStats[$fuelType] = $stats;
            }
        }

        // Get recent custom prices across all fuel types
        $recentCustomPrices = Ravitaillement::where('is_custom_price', true)
            ->with(['user', 'vehicule'])
            ->orderBy('ravitaillement_date', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($ravitaillement) {
                return [
                    'id' => $ravitaillement->id,
                    'date' => $ravitaillement->ravitaillement_date,
                    'fuel_type' => $ravitaillement->fuel_type,
                    'custom_price' => $ravitaillement->price_per_liter,
                    'station' => $ravitaillement->station_service,
                    'user_name' => $ravitaillement->user->name,
                    'vehicule' => $ravitaillement->vehicule->make . ' ' . $ravitaillement->vehicule->model,
                    'license_plate' => $ravitaillement->vehicule->license_plate,
                    'liters' => $ravitaillement->liters_purchased,
                    'total_cost' => $ravitaillement->total_cost,
                ];
            });

        return Inertia::render('Admin/FuelPrices/Index', [
            'fuelPrices' => $fuelPrices,
            'currentPrices' => $currentPrices,
            'customPriceStats' => $customPriceStats,
            'recentCustomPrices' => $recentCustomPrices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/FuelPrices/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_type' => 'required|in:diesel,essence,gpl,electrique',
            'price_per_liter' => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['set_by'] = auth()->id();
        $validated['is_active'] = true;

        // Deactivate other prices of same fuel type
        FuelPrice::where('fuel_type', $validated['fuel_type'])
            ->update(['is_active' => false]);

        FuelPrice::create($validated);

        return redirect()->route('admin.fuel-prices.index')
            ->with('success', 'Prix du carburant enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FuelPrice $fuelPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FuelPrice $fuelPrice)
    {
        return Inertia::render('Admin/FuelPrices/Edit', [
            'fuelPrice' => $fuelPrice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FuelPrice $fuelPrice)
    {
        $validated = $request->validate([
            'fuel_type' => 'required|in:diesel,essence,gpl,electrique',
            'price_per_liter' => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $fuelPrice->update($validated);

        return redirect()->route('admin.fuel-prices.index')
            ->with('success', 'Prix du carburant modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FuelPrice $fuelPrice)
    {
        $fuelPrice->delete();

        return redirect()->route('admin.fuel-prices.index')
            ->with('success', 'Prix du carburant supprimé avec succès.');
    }

    public function toggleActive(FuelPrice $fuelPrice)
    {
        // If activating, deactivate others of same fuel type
        if (!$fuelPrice->is_active) {
            FuelPrice::where('fuel_type', $fuelPrice->fuel_type)
                ->where('id', '!=', $fuelPrice->id)
                ->update(['is_active' => false]);
        }

        $fuelPrice->update(['is_active' => !$fuelPrice->is_active]);

        return redirect()->back()
            ->with('success', 'Statut du prix modifié avec succès.');
    }

    /**
     * API endpoint for clients to get current fuel prices
     */
    public function getCurrentPrices()
    {
        return response()->json([
            'prices' => FuelPrice::getCurrentPrices(),
        ]);
    }
}
