<?php

namespace App\Http\Controllers;

use App\Models\Ravitaillement;
use App\Models\Vehicule;
use App\Models\FuelPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Enums\UserRole;
use App\Notifications\CustomFuelPriceNotification;

class RavitaillementControllercopy extends Controller
{
    public function index()
    {
        $selectedVehiculeId = session('selected_vehicule_id');

        $query = Ravitaillement::with(['vehicule', 'user'])
            ->where('user_id', auth()->id());

        if ($selectedVehiculeId) {
            $query->where('vehicule_id', $selectedVehiculeId);
        }

        $ravitaillements = $query->orderBy('ravitaillement_date', 'desc')->paginate(15);

        $statsQuery = Ravitaillement::where('user_id', auth()->id());
        if ($selectedVehiculeId) {
            $statsQuery->where('vehicule_id', $selectedVehiculeId);
        }

        /*$stats = [
            'total_spent' => (clone $statsQuery)->sum('amount_paid'),
            'total_liters' => (clone $statsQuery)->sum('liters_purchased'),
            'this_month_spent' => (clone $statsQuery)
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('amount_paid'),
            'this_month_liters' => (clone $statsQuery)
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('liters_purchased'),
        ];*/
        $stats = [
            'total_spent' => (clone $statsQuery)->sum('amount_paid'),
            // 🚀 CHANGEMENT: Utiliser liters_purchased au lieu de total_liters
            'total_liters' => (clone $statsQuery)->sum('liters_purchased'),
            'this_month_spent' => (clone $statsQuery)
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('amount_paid'),
            'this_month_liters' => (clone $statsQuery)
                ->whereMonth('ravitaillement_date', now()->month)
                ->sum('liters_purchased'),
        ];


        return Inertia::render('Ravitaillements/Index', [
            'ravitaillements' => $ravitaillements,
            'stats' => $stats,
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
        ]);
    }

    public function create()
    {
        $selectedVehiculeId = session('selected_vehicule_id');
        $vehicule = Vehicule::with(['user'])
            ->select('id', 'user_id', 'make', 'model', 'license_plate', 'fuel_type', 'mileage', 'average_consumption')
            ->find($selectedVehiculeId);

        if (!$vehicule) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Veuillez sélectionner un véhicule.');
        }

        $lastOdometer = Ravitaillement::getLastOdometer($vehicule->id);
        if ($lastOdometer === null) {
            $lastOdometer = $vehicule->mileage;
        }

        // Get current fuel price for this vehicle's fuel type
        $currentFuelPrice = FuelPrice::getCurrentPrice($vehicule->fuel_type);

        return Inertia::render('Ravitaillements/Create', [
            'vehicule' => $vehicule,
            'lastOdometer' => $lastOdometer,
            'fuelType' => $vehicule->fuel_type,
            'currentFuelPrice' => $currentFuelPrice ? [
                'price' => $currentFuelPrice->price_per_liter,
                'effective_date' => $currentFuelPrice->effective_date,
                'notes' => $currentFuelPrice->notes,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        /*$validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_name' => 'nullable|string|max:255',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_service' => 'required|string|max:255',
            'liters_purchased' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'amount_paid' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:carte,cash,virement,mobile',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_custom_price' => 'boolean',
        ]);

        //$validated['user_id'] = auth()->id();
        //$validated['fuel_type'] = Vehicule::find($validated['vehicule_id'])->fuel_type;

        //Ravitaillement::create($validated);

        $vehicule = Vehicule::findOrFail($request->vehicule_id);

        // Autorisation
        if ($vehicule->user_id !== $request->user()->id) {
            abort(403, 'Non autorisé.');
        }

        // Déterminer le fuel_type depuis le véhicule
        $fuelType = $vehicule->fuel_type;

        // Récupérer le prix officiel actuel
        $currentPrice = FuelPrice::where('fuel_type', $fuelType)
            ->where('is_active', true)
            ->latest('effective_date')
            ->first();

        $officialPrice = $currentPrice ? $currentPrice->price_per_liter : null;

        // Vérifier si c'est un prix personnalisé
        $isCustomPrice = false;
        if ($officialPrice && abs($request->price_per_liter - $officialPrice) > 0.01) {
            $isCustomPrice = true;
        }

        // Créer le ravitaillement
        $ravitaillement = Ravitaillement::create([
            'user_id' => $request->user()->id,
            'vehicule_id' => $request->vehicule_id,
            'fuel_type' => $fuelType,
            'chauffeur_name' => $request->chauffeur_name,
            'ravitaillement_date' => $request->ravitaillement_date,
            'station_service' => $request->station_service,
            'liters_purchased' => $request->liters_purchased,
            'price_per_liter' => $request->price_per_liter,
            'amount_paid' => $request->amount_paid,
            'total_cost' => $request->amount_paid,
            'odo_station' => $request->odo_station,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'notes' => $request->notes,
            'is_custom_price' => $isCustomPrice,
        ]);

        // NOTIFICATION: Si prix personnalisé, notifier les admins
        if ($isCustomPrice && $officialPrice) {
            $admins = User::where('role', UserRole::ADMIN)->get();

            foreach ($admins as $admin) {
                $admin->notify(new CustomFuelPriceNotification(
                    $ravitaillement,
                    $request->user(),
                    $officialPrice
                ));
            }
        }


        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement enregistré avec succès.');
    }*/

           // Validation
        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_name' => 'nullable|string|max:255',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_service' => 'required|string|max:255',
            'liters_purchased' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'amount_paid' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:carte,cash,virement,mobile',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_custom_price' => 'boolean',
        ]);

        // Récupérer le véhicule
        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);

        // Vérifier l'autorisation
        if ($vehicule->user_id !== $request->user()->id) {
            abort(403, 'Non autorisé.');
        }

        // Déterminer le fuel_type depuis le véhicule
        $fuelType = $vehicule->fuel_type;

        // Récupérer le prix officiel actuel
        $currentPrice = FuelPrice::where('fuel_type', $fuelType)
            ->where('is_active', true)
            ->latest('effective_date')
            ->first();

        $officialPrice = $currentPrice ? $currentPrice->price_per_liter : null;

        // Vérifier si c'est un prix personnalisé
        $isCustomPrice = false;
        if ($officialPrice && abs($validated['price_per_liter'] - $officialPrice) > 0.01) {
            $isCustomPrice = true;
        }

        // Préparer les données pour la création
        $dataToCreate = [
            'user_id' => $request->user()->id,
            'vehicule_id' => $validated['vehicule_id'],
            'fuel_type' => $fuelType,
            'chauffeur_name' => $validated['chauffeur_name'] ?? null,
            'ravitaillement_date' => $validated['ravitaillement_date'],
            'station_service' => $validated['station_service'],
            'liters_purchased' => $validated['liters_purchased'],
            'price_per_liter' => $validated['price_per_liter'],
            'amount_paid' => $validated['amount_paid'],
            'total_cost' => $validated['amount_paid'], // Même valeur que amount_paid
            'odo_station' => $validated['odo_station'] ?? null,
            'payment_method' => $validated['payment_method'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_custom_price' => $isCustomPrice,
        ];

        // Créer le ravitaillement (UNE SEULE FOIS)
        $ravitaillement = Ravitaillement::create($dataToCreate);

        // ✉️ NOTIFICATION: Si prix personnalisé, notifier les admins
        if ($isCustomPrice && $officialPrice) {
            $admins = User::where('role', UserRole::ADMIN)->get();

            foreach ($admins as $admin) {
                $admin->notify(new CustomFuelPriceNotification(
                    $ravitaillement,
                    $request->user(),
                    $officialPrice
                ));
            }
        }

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement enregistré avec succès.');
    }

    public function show(Ravitaillement $ravitaillement)
    {
        $this->authorize('view', $ravitaillement);

        $ravitaillement->load(['vehicule', 'user']);

        // Get current fuel price for comparison
        $currentFuelPrice = FuelPrice::getCurrentPrice($ravitaillement->fuel_type);

        return Inertia::render('Ravitaillements/Show', [
            'ravitaillement' => $ravitaillement,
            'currentFuelPrice' => $currentFuelPrice ? [
                'price' => $currentFuelPrice->price_per_liter,
                'effective_date' => $currentFuelPrice->effective_date,
                'notes' => $currentFuelPrice->notes,
            ] : null,
        ]);
    }

    public function edit(Ravitaillement $ravitaillement)
    {
        $this->authorize('update', $ravitaillement);

        $vehicule = Vehicule::with(['user'])
             ->select('id', 'user_id', 'make', 'model', 'license_plate', 'fuel_type', 'mileage', 'average_consumption')
            ->find($ravitaillement->vehicule_id);

        $lastOdometer = Ravitaillement::where('vehicule_id', $vehicule->id)
            ->where('id', '!=', $ravitaillement->id)
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('odo_station');

        if ($lastOdometer === null) {
            $lastOdometer = $vehicule->mileage;
        }

        // Get current fuel price
        $currentFuelPrice = FuelPrice::getCurrentPrice($vehicule->fuel_type);

        return Inertia::render('Ravitaillements/Edit', [
            'ravitaillement' => $ravitaillement,
            'vehicule' => $vehicule,
            'lastOdometer' => $lastOdometer,
            'fuelType' => $vehicule->fuel_type,
            'currentFuelPrice' => $currentFuelPrice ? [
                'price' => $currentFuelPrice->price_per_liter,
                'effective_date' => $currentFuelPrice->effective_date,
                'notes' => $currentFuelPrice->notes,
            ] : null,
        ]);
    }

    public function update(Request $request, Ravitaillement $ravitaillement)
    {
        $this->authorize('update', $ravitaillement);

        $validated = $request->validate([
            'chauffeur_name' => 'nullable|string|max:255',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_service' => 'required|string|max:255',
            'liters_purchased' => 'required|numeric|min:0.01',
            'price_per_liter' => 'required|numeric|min:0.01',
            'amount_paid' => 'required|numeric|min:0.01',
            'odo_station' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:carte,cash,virement,mobile',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_custom_price' => 'boolean',
        ]);

        $ravitaillement->update($validated);

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement modifié avec succès.');
    }

    public function destroy(Ravitaillement $ravitaillement)
    {
        $this->authorize('delete', $ravitaillement);

        $ravitaillement->delete();

        return redirect()->route('ravitaillements.index')
            ->with('success', 'Ravitaillement supprimé avec succès.');
    }
}
