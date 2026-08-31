<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vehicule;
use App\Models\Assurance;
use App\Models\Reparation;
use App\Models\Maintenance;
use App\Models\Ravitaillement;
use App\Models\Trajet;
use App\Enums\UserRole;
use App\Enums\VehiculeStatus;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Handle client redirection logic
        if ($user->isClient()) {
            return $this->handleClientDashboard($user);
        }

        // For validators and admins, show their respective dashboards
        $dashboardData = $this->getDashboardData($user);
        return Inertia::render('Dashboard/Index', $dashboardData);
    }

    private function handleClientDashboard($user)
    {
        $vehiculeCount = $user->vehicules()->count();

        // Case 1: No vehicles at all
        if ($vehiculeCount === 0) {
            return redirect()->route('vehicules.create')
                ->with('info', 'Veuillez ajouter votre premier véhicule pour commencer.');
        }

        // Case 2: Has vehicles but none selected
        $selectedVehiculeId = session('selected_vehicule_id');

        if (!$selectedVehiculeId) {
            return redirect()->route('vehicules.selection')
                ->with('info', 'Veuillez sélectionner un véhicule pour continuer.');
        }

        // Case 3: Verify selected vehicle
        $selectedVehicule = Vehicule::where('id', $selectedVehiculeId)
            ->where('user_id', $user->id)
            ->first();

        if (!$selectedVehicule) {
            session()->forget('selected_vehicule_id');
            return redirect()->route('vehicules.selection')
                ->with('error', 'Le véhicule sélectionné n\'est plus disponible.');
        }

        if ($selectedVehicule->status !== VehiculeStatus::VALIDATED) {
            session()->forget('selected_vehicule_id');
            return redirect()->route('vehicules.selection')
                ->with('error', 'Le véhicule sélectionné n\'est pas validé.');
        }

        // Case 4: Show dashboard with selected vehicle data
        $dashboardData = $this->getClientDashboardData($user, $selectedVehicule);
        return Inertia::render('Dashboard/Index', $dashboardData);
    }

    private function getDashboardData($user)
    {
        $baseData = [
            'user' => $user->load('proprietaire'),
            'userRole' => $user->role->value,
        ];

        switch ($user->role) {
            case UserRole::VALIDATOR:
                return array_merge($baseData, $this->getValidatorDashboardData($user));
            case UserRole::ADMIN:
                return array_merge($baseData, $this->getAdminDashboardData($user));
            default:
                return $baseData;
        }
    }

    private function getClientDashboardData($user, $selectedVehicule)
    {
        $vehiculeId = $selectedVehicule->id;

        return [
            'selectedVehicule' => [
                'id' => $selectedVehicule->id,
                'make' => $selectedVehicule->make,
                'model' => $selectedVehicule->model,
                'year' => $selectedVehicule->year,
                'license_plate' => $selectedVehicule->license_plate,
                'fuel_type' => $selectedVehicule->fuel_type,
                'mileage' => $selectedVehicule->mileage,
                'color' => $selectedVehicule->color,
                'full_name' => $selectedVehicule->full_name,
                'vehicule_type' => $selectedVehicule->vehicule_type,
            ],
            'stats' => [
                'totalVehicules' => $user->vehicules()->where('status', VehiculeStatus::VALIDATED)->count(),
                // IMPORTANT: Filtrer par vehicule_id ET user_id
                'totalReparations' => Reparation::where('vehicule_id', $vehiculeId)
                    ->where('user_id', $user->id)
                    ->count(),
                'totalMaintenances' => Maintenance::where('vehicule_id', $vehiculeId)
                    ->where('user_id', $user->id)
                    ->count(),
                'monthlyFuelCost' => Ravitaillement::where('vehicule_id', $vehiculeId)
                    ->where('user_id', $user->id)
                    ->whereMonth('ravitaillement_date', now()->month)
                    ->sum('amount_paid') ?? 0,
                'totalTrajets' => Trajet::where('vehicule_id', $vehiculeId)
                    ->where('user_id', $user->id)
                    ->count(),
            ],
            'recentActivities' => $this->getRecentActivities($vehiculeId, $user->id),
            'expiringAssurances' => Assurance::where('vehicule_id', $vehiculeId)
                ->where('user_id', $user->id)
                ->where('end_date', '<=', now()->addDays(30))
                ->where('end_date', '>=', now())
                ->orderBy('end_date')
                ->take(5)
                ->get(),
        ];
    }

    private function getRecentActivities($vehiculeId, $userId)
    {
        $activities = collect();

        // Reparations récentes
        $reparations = Reparation::where('vehicule_id', $vehiculeId)
            ->where('user_id', $userId)
            ->orderBy('reparation_date', 'desc')
            ->take(3)
            ->get();
        
        foreach ($reparations as $reparation) {
            $activities->push([
                'type' => 'reparation',
                'date' => $reparation->reparation_date,
                'description' => "Réparation: {$reparation->description}",
                'amount' => $reparation->cost,
            ]);
        }

        // Maintenances récentes
        $maintenances = Maintenance::where('vehicule_id', $vehiculeId)
            ->where('user_id', $userId)
            ->orderBy('date_debut', 'desc')
            ->take(3)
            ->get();
        
        foreach ($maintenances as $maintenance) {
            $activities->push([
                'type' => 'maintenance',
                'date' => $maintenance->date_debut,
                'description' => "Maintenance: {$maintenance->nature_intervention}",
                'amount' => $maintenance->cout_total ?? 0,
            ]);
        }

        // Ravitaillements récents
        $ravitaillements = Ravitaillement::where('vehicule_id', $vehiculeId)
            ->where('user_id', $userId)
            ->orderBy('ravitaillement_date', 'desc')
            ->take(3)
            ->get();
        
        foreach ($ravitaillements as $ravitaillement) {
            $activities->push([
                'type' => 'ravitaillement',
                'date' => $ravitaillement->ravitaillement_date,
                'description' => "Ravitaillement: {$ravitaillement->liters_purchased}L",
                'amount' => $ravitaillement->amount_paid ?? 0,
            ]);
        }

        // Trajets récents
        $trajets = Trajet::where('vehicule_id', $vehiculeId)
            ->where('user_id', $userId)
            ->orderBy('heure_depart', 'desc')
            ->take(3)
            ->get();
        
        foreach ($trajets as $trajet) {
            $activities->push([
                'type' => 'trajet',
                'date' => $trajet->heure_depart,
                'description' => "{$trajet->departure} → {$trajet->destination} ({$trajet->distance} km)",
                'amount' => null,
            ]);
        }

        return $activities->sortByDesc('date')->take(10)->values();
    }

    private function getValidatorDashboardData($user)
    {
        $pendingVehicules = Vehicule::where('status', VehiculeStatus::PENDING)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'stats' => [
                'pendingValidations' => $pendingVehicules->count(),
                'validatedToday' => Vehicule::where('validated_by', $user->id)
                    ->whereDate('validated_at', today())
                    ->count(),
                'totalValidated' => Vehicule::where('validated_by', $user->id)->count(),
            ],
            'pendingVehicules' => $pendingVehicules->take(10),
            'recentValidations' => Vehicule::where('validated_by', $user->id)
                ->with(['user'])
                ->orderBy('validated_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    private function getAdminDashboardData($user)
    {
        return [
            'stats' => [
                'totalUsers' => \App\Models\User::count(),
                'totalClients' => \App\Models\User::where('role', UserRole::CLIENT)->count(),
                'totalValidators' => \App\Models\User::where('role', UserRole::VALIDATOR)->count(),
                'totalVehicules' => Vehicule::count(),
                'pendingVehicules' => Vehicule::where('status', VehiculeStatus::PENDING)->count(),
                'validatedVehicules' => Vehicule::where('status', VehiculeStatus::VALIDATED)->count(),
                'newUsersThisMonth' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
            ],
            'recentUsers' => \App\Models\User::orderBy('created_at', 'desc')->take(10)->get(),
            'recentVehicules' => Vehicule::with(['user'])->orderBy('created_at', 'desc')->take(5)->get(),
        ];
    }
}