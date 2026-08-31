<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vehicule;
use App\Models\Assurance;
use App\Models\Maintenance;
use App\Models\MaintenancePiece;
use App\Models\Ravitaillement;
use App\Models\Trajet;
use App\Models\VisiteTechnique;
use App\Models\Proprietaire;
use App\Enums\UserRole;
use App\Enums\VehiculeStatus;
use App\Services\VehiculeConsumptionService;

class DashboardController extends Controller
{
     protected $consumptionService;

    public function __construct(VehiculeConsumptionService $consumptionService)
    {
        $this->consumptionService = $consumptionService;
    }

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

        // Récupérer le kilométrage actuel (dernier enregistrement)
        $lastOdometer = $this->getLastKilometrage($vehiculeId);
        $lastUpdateDate = $this->getLastKilometrageDate($vehiculeId);

        // Stats Ravitaillement
        $ravitaillementStats = [
            'totalLiters' => Ravitaillement::where('vehicule_id', $vehiculeId)
                ->where('user_id', $user->id)
                ->sum('liters_purchased') ?? 0,
            'totalCost' => Ravitaillement::where('vehicule_id', $vehiculeId)
                ->where('user_id', $user->id)
                ->sum('amount_paid') ?? 0,
        ];

        // Stats Trajet
        $lastTrajet = Trajet::where('vehicule_id', $vehiculeId)
            ->where('user_id', $user->id)
            ->orderBy('heure_depart', 'desc')
            ->first();

        $trajetStats = [
            'totalTrajets' => Trajet::where('vehicule_id', $vehiculeId)
                ->where('user_id', $user->id)
                ->count(),
            'lastDestination' => $lastTrajet ? $lastTrajet->destination : null,
        ];

        // Stats Maintenance
        $totalMaintenances = Maintenance::where('vehicule_id', $vehiculeId)
            ->where('user_id', $user->id)
            ->count();

        $totalPieces = MaintenancePiece::whereHas('maintenance', function($q) use ($vehiculeId, $user) {
            $q->where('vehicule_id', $vehiculeId)
              ->where('user_id', $user->id);
        })->count();

        $recentPieces = MaintenancePiece::whereHas('maintenance', function($q) use ($vehiculeId, $user) {
            $q->where('vehicule_id', $vehiculeId)
              ->where('user_id', $user->id);
        })
        ->orderBy('date_installation', 'desc')
        ->limit(5)
        ->get();

        $maintenanceStats = [
            'totalMaintenances' => $totalMaintenances,
            'totalPieces' => $totalPieces,
            'recentPieces' => $recentPieces,
        ];

        // Info Assurance (dernière active)
        $assurance = Assurance::where('vehicule_id', $vehiculeId)
            ->where('user_id', $user->id)
            ->orderBy('end_date', 'desc')
            ->first();

        $assuranceInfo = $assurance ? [
            'id' => $assurance->id,
            'company' => $assurance->company,
            'policy_number' => $assurance->policy_number,
            'start_date' => $assurance->start_date,
            'end_date' => $assurance->end_date,
            'isExpiringSoon' => $assurance->end_date &&
                               now()->diffInDays($assurance->end_date, false) <= 30 &&
                               now()->diffInDays($assurance->end_date, false) >= 0,
        ] : null;

        // Info Visite Technique (dernière)
        $visiteTechnique = VisiteTechnique::where('vehicule_id', $vehiculeId)
            ->where('user_id', $user->id)
            ->orderBy('date_visite', 'desc')
            ->first();

        $visiteTechniqueInfo = $visiteTechnique ? [
            'id' => $visiteTechnique->id,
            'centre' => $visiteTechnique->centre,
            'numero_pv' => $visiteTechnique->numero_pv,
            'date_visite' => $visiteTechnique->date_visite,
            'validite' => $visiteTechnique->validite,
            'aptitude' => $visiteTechnique->aptitude,
        ] : null;

        // Info Propriétaire
        $proprietaire = Proprietaire::find($selectedVehicule->proprietaire_id);
        $proprietaireInfo = $proprietaire ? [
            'id' => $proprietaire->id,
            'display_name' => $proprietaire->display_name,
            'type' => $proprietaire->type,
            'nif' => $proprietaire->nif,
            'telephone' => $proprietaire->telephone ?? $proprietaire->contact_principal,
        ] : null;

         $consumptionAnalysis = $this->consumptionService->determineConsumption($selectedVehicule);

        return [
            'userRole' => $user->role->value,
            'selectedVehicule' => [
                'id' => $selectedVehicule->id,
                'make' => $selectedVehicule->make,
                'model' => $selectedVehicule->model,
                'alias' => $selectedVehicule->alias,
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
                'totalMaintenances' => $totalMaintenances,
                'monthlyFuelCost' => Ravitaillement::where('vehicule_id', $vehiculeId)
                    ->where('user_id', $user->id)
                    ->whereMonth('ravitaillement_date', now()->month)
                    ->sum('amount_paid') ?? 0,
                'totalTrajets' => $trajetStats['totalTrajets'],
            ],
            'vehiculeDetails' => [
                'currentKilometrage' => $lastOdometer ?? $selectedVehicule->mileage,
                'lastUpdate' => $lastUpdateDate,
            ],
            'ravitaillementStats' => $ravitaillementStats,
            'trajetStats' => $trajetStats,
            'maintenanceStats' => $maintenanceStats,
            'assuranceInfo' => $assuranceInfo,
            'visiteTechniqueInfo' => $visiteTechniqueInfo,
            'proprietaireInfo' => $proprietaireInfo,
            'recentActivities' => $this->getRecentActivities($vehiculeId, $user->id),
            'consumptionAnalysis' => $consumptionAnalysis,
        ];
    }

    /*private function getLastKilometrage($vehiculeId)
    {
        // Chercher dans les maintenances
        $maintenanceKm = Maintenance::where('vehicule_id', $vehiculeId)
            ->whereNotNull('kilometrage_actuel')
            ->orderBy('date_debut', 'desc')
            ->value('kilometrage_actuel');

        if ($maintenanceKm) {
            return $maintenanceKm;
        }

        // Chercher dans les ravitaillements - CORRECTED: odo_arrival changed to odo_station
        $ravitaillementKm = Ravitaillement::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('odo_station');

        if ($ravitaillementKm) {
            return $ravitaillementKm;
        }

        // Chercher dans les trajets
        $trajetKm = Trajet::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('heure_depart', 'desc')
            ->value('odo_end');

        return $trajetKm;
    }

    private function getLastKilometrageDate($vehiculeId)
    {
        // Récupérer les dates de tous les enregistrements avec kilométrage
        $maintenanceDate = Maintenance::where('vehicule_id', $vehiculeId)
            ->whereNotNull('kilometrage_actuel')
            ->orderBy('date_debut', 'desc')
            ->value('date_debut');

        // CORRECTED: odo_arrival changed to odo_station
        $ravitaillementDate = Ravitaillement::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('ravitaillement_date');

        $trajetDate = Trajet::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('heure_depart', 'desc')
            ->value('heure_depart');

        // Retourner la date la plus récente
        $dates = array_filter([$maintenanceDate, $ravitaillementDate, $trajetDate]);

        return !empty($dates) ? max($dates) : null;
    }*/


    private function getLastKilometrage($vehiculeId)
    {
        //  PRIORITÉ 1: Chercher dans les trajets (odo_end) - Plus fréquent
        $trajetKm = Trajet::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('heure_depart', 'desc')
            ->value('odo_end');

        if ($trajetKm) {
            return $trajetKm;
        }

        //  PRIORITÉ 2: Chercher dans les maintenances
        $maintenanceKm = Maintenance::where('vehicule_id', $vehiculeId)
            ->whereNotNull('kilometrage_actuel')
            ->orderBy('date_debut', 'desc')
            ->value('kilometrage_actuel');

        if ($maintenanceKm) {
            return $maintenanceKm;
        }

        //  PRIORITÉ 3: Chercher dans les ravitaillements
        $ravitaillementKm = Ravitaillement::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('odo_station');

        if ($ravitaillementKm) {
            return $ravitaillementKm;
        }

        //  FALLBACK: Retourner le kilométrage initial du véhicule
        return Vehicule::where('id', $vehiculeId)->value('mileage') ?? 0;
    }

    private function getLastKilometrageDate($vehiculeId)
    {
        //  PRIORITÉ 1: Date du dernier trajet avec odo_end
        $trajetDate = Trajet::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('heure_depart', 'desc')
            ->value('heure_depart');

        if ($trajetDate) {
            return $trajetDate;
        }

        //  PRIORITÉ 2: Date de la dernière maintenance
        $maintenanceDate = Maintenance::where('vehicule_id', $vehiculeId)
            ->whereNotNull('kilometrage_actuel')
            ->orderBy('date_debut', 'desc')
            ->value('date_debut');

        if ($maintenanceDate) {
            return $maintenanceDate;
        }

        //  PRIORITÉ 3: Date du dernier ravitaillement
        $ravitaillementDate = Ravitaillement::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->value('ravitaillement_date');

        if ($ravitaillementDate) {
            return $ravitaillementDate;
        }

        // FALLBACK: Date de création du véhicule
        return Vehicule::where('id', $vehiculeId)->value('created_at');
    }


    private function getRecentActivities($vehiculeId, $userId)
    {
        $activities = collect();


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
            'systemActivity' => [
                'recentVehicules' => Vehicule::with(['user'])->orderBy('created_at', 'desc')->take(5)->get(),
                'recentValidations' => Vehicule::whereNotNull('validated_at')
                    ->with(['user', 'validator'])
                    ->orderBy('validated_at', 'desc')
                    ->take(5)
                    ->get(),
            ],
        ];
    }
}
