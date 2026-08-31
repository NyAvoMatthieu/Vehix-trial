<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Models\Trajet;
use App\Models\Ravitaillement;
use App\Models\Maintenance;
use App\Models\Assurance;
use App\Models\VisiteTechnique;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class RapportController extends Controller
{
    /**
     * Afficher l'historique complet des activités
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedVehiculeId = session('selected_vehicule_id');

        // Valeurs par défaut pour les dates (mois en cours)
        $defaultStartDate = now()->startOfMonth()->format('Y-m-d');
        $defaultEndDate = now()->endOfMonth()->format('Y-m-d');

        // Récupérer les filtres
        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $activityType = $request->input('activity_type', 'all');
        $searchTerm = $request->input('search', '');

        // Convertir en Carbon pour les comparaisons
        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        // Collecter toutes les activités
        $activities = collect();

        // Base query - filtrer par véhicule sélectionné ou tous les véhicules de l'utilisateur
        $baseQuery = function($query) use ($user, $selectedVehiculeId) {
            $query->where('user_id', $user->id);
            if ($selectedVehiculeId) {
                $query->where('vehicule_id', $selectedVehiculeId);
            }
        };

        // 1. TRAJETS
        if ($activityType === 'all' || $activityType === 'trajets') {
            $trajets = Trajet::with(['vehicule'])
                ->where($baseQuery)
                ->whereBetween('heure_depart', [$startCarbon, $endCarbon])
                ->get();

            foreach ($trajets as $trajet) {
                $activities->push([
                    'id' => $trajet->id,
                    'type' => 'trajet',
                    'date' => $trajet->heure_depart,
                    'title' => "Trajet: {$trajet->departure} → {$trajet->destination}",
                    'description' => "Distance: {$trajet->distance} km",
                    'vehicule' => $trajet->vehicule->full_name,
                    'license_plate' => $trajet->vehicule->license_plate,
                    'amount' => null,
                    'details' => [
                        'Départ' => $trajet->departure,
                        'Destination' => $trajet->destination,
                        'Distance' => "{$trajet->distance} km",
                        'Durée' => $trajet->heure_arrivee ? $trajet->heure_depart->diffForHumans($trajet->heure_arrivee, true) : 'N/A',
                    ],
                ]);
            }
        }

        // 2. RAVITAILLEMENTS
        if ($activityType === 'all' || $activityType === 'ravitaillements') {
            $ravitaillements = Ravitaillement::with(['vehicule'])
                ->where($baseQuery)
                ->whereBetween('ravitaillement_date', [$startCarbon, $endCarbon])
                ->get();

            foreach ($ravitaillements as $ravitaillement) {
                $activities->push([
                    'id' => $ravitaillement->id,
                    'type' => 'ravitaillement',
                    'date' => $ravitaillement->ravitaillement_date,
                    'title' => "Ravitaillement: {$ravitaillement->station_service}",
                    'description' => "{$ravitaillement->liters_purchased}L de {$ravitaillement->fuel_type}",
                    'vehicule' => $ravitaillement->vehicule->full_name,
                    'license_plate' => $ravitaillement->vehicule->license_plate,
                    'amount' => $ravitaillement->amount_paid,
                    'details' => [
                        'Station' => $ravitaillement->station_service,
                        'Litres' => "{$ravitaillement->liters_purchased}L",
                        'Prix/L' => number_format($ravitaillement->price_per_liter, 0) . ' Ar',
                        'Total' => number_format($ravitaillement->amount_paid, 0) . ' Ar',
                        'Carburant' => ucfirst($ravitaillement->fuel_type),
                    ],
                ]);
            }
        }

        // 3. MAINTENANCES
        if ($activityType === 'all' || $activityType === 'maintenances') {
            $maintenances = Maintenance::with(['vehicule', 'pieces'])
                ->where($baseQuery)
                ->whereBetween('date_debut', [$startCarbon, $endCarbon])
                ->get();

            foreach ($maintenances as $maintenance) {

            //  le coût total en temps réel
                $coutMainOeuvre = $maintenance->cout_main_oeuvre ?? 0;
                $coutPieces = $maintenance->pieces->sum('prix_total') ?? 0;
                $coutTotal = $coutMainOeuvre + $coutPieces;

                 //  Compter le nombre de pièces
                $nombrePieces = $maintenance->pieces->count();

                //  description détaillée des pièces
                $detailsPieces = [];
                if ($nombrePieces > 0) {
                    foreach ($maintenance->pieces as $index => $piece) {
                        $detailsPieces["Pièce " . ($index + 1)] = "{$piece->nom_piece} ({$piece->marque_piece}) - " . number_format($piece->prix_total, 0) . ' Ar';
                    }
                }


                $activities->push([
                    'id' => $maintenance->id,
                    'type' => 'maintenance',
                    'date' => $maintenance->date_debut,
                    'title' => "Maintenance: {$maintenance->nature_intervention}",
                    'description' => "Garage: {$maintenance->garage_nom}",
                    'vehicule' => $maintenance->vehicule->full_name,
                    'license_plate' => $maintenance->vehicule->license_plate,
                    'amount' => $coutTotal,
                     'details' => array_merge([
                        'Référence' => $maintenance->reference,
                        'Nature' => $maintenance->nature_intervention,
                        'Garage' => $maintenance->garage_nom,
                        'Main d\'œuvre' => number_format($coutMainOeuvre, 0) . ' Ar',
                        'Pièces (Total)' => number_format($coutPieces, 0) . ' Ar',
                        'Nombre de pièces' => $nombrePieces,
                        '💰 TOTAL' => number_format($coutTotal, 0) . ' Ar',
                    ], $detailsPieces), //  les détails des pièces
                ]);
            }
        }
        // 4. ASSURANCES - Version simplifiée
        if ($activityType === 'all' || $activityType === 'assurances') {
            $assurances = Assurance::with(['vehicule'])
                ->where($baseQuery)
                ->where(function($query) use ($startCarbon, $endCarbon) {
                    // Afficher les assurances créées pendant la période
                    $query->whereBetween('created_at', [$startCarbon, $endCarbon])
                        // OU celles qui commencent pendant la période
                        ->orWhereBetween('start_date', [$startCarbon, $endCarbon]);
                })
                ->get();

            foreach ($assurances as $assurance) {
                $activities->push([
                    'id' => $assurance->id,
                    'type' => 'assurance',
                    'date' => $assurance->start_date,
                    'title' => "Assurance: {$assurance->company}",
                    'description' => "Police N°: {$assurance->policy_number}",
                    'vehicule' => $assurance->vehicule->full_name,
                    'license_plate' => $assurance->vehicule->license_plate,
                    'amount' => $assurance->prime_total,
                    'details' => [
                        'Compagnie' => $assurance->company,
                        'Police' => $assurance->policy_number,
                        'Début' => $assurance->start_date->format('d/m/Y'),
                        'Fin' => $assurance->end_date ? $assurance->end_date->format('d/m/Y') : 'N/A',
                        'Prime totale' => number_format($assurance->prime_total, 0) . ' Ar',
                    ],
                ]);
            }
        }

        // 5. VISITES TECHNIQUES
        if ($activityType === 'all' || $activityType === 'visites') {
            $visites = VisiteTechnique::with(['vehicule'])
                ->where($baseQuery)
                ->whereBetween('date_visite', [$startCarbon, $endCarbon])
                ->get();

            foreach ($visites as $visite) {
                $activities->push([
                    'id' => $visite->id,
                    'type' => 'visite',
                    'date' => $visite->date_visite,
                    'title' => "Visite Technique: {$visite->centre}",
                    'description' => "Aptitude: {$visite->aptitude}",
                    'vehicule' => $visite->vehicule->full_name,
                    'license_plate' => $visite->vehicule->license_plate,
                    'amount' => $visite->total,
                    'details' => [
                        'Centre' => $visite->centre,
                        'PV N°' => $visite->numero_pv,
                        'Aptitude' => $visite->aptitude,
                        'Validité' => $visite->validite ? $visite->validite->format('d/m/Y') : 'N/A',
                        'Total' => number_format($visite->total, 0) . ' Ar',
                    ],
                ]);
            }
        }

        // Filtrer par recherche si nécessaire
        if (!empty($searchTerm)) {
            $activities = $activities->filter(function($activity) use ($searchTerm) {
                $searchLower = strtolower($searchTerm);
                return str_contains(strtolower($activity['title']), $searchLower) ||
                       str_contains(strtolower($activity['description']), $searchLower) ||
                       str_contains(strtolower($activity['vehicule']), $searchLower) ||
                       str_contains(strtolower($activity['license_plate']), $searchLower);
            });
        }

        // Trier par date décroissante
        $activities = $activities->sortByDesc('date')->values();

        // Calcule des statistiques
        $stats = [
            'total_activities' => $activities->count(),
            'total_amount' => $activities->whereNotNull('amount')->sum('amount'),
            'by_type' => [
                'trajets' => $activities->where('type', 'trajet')->count(),
                'ravitaillements' => $activities->where('type', 'ravitaillement')->count(),
                'maintenances' => $activities->where('type', 'maintenance')->count(),
                'assurances' => $activities->where('type', 'assurance')->count(),
                'visites' => $activities->where('type', 'visite')->count(),
            ],
            'costs_by_type' => [
                'ravitaillements' => $activities->where('type', 'ravitaillement')->whereNotNull('amount')->sum('amount'),
                'maintenances' => $activities->where('type', 'maintenance')->whereNotNull('amount')->sum('amount'),
                'reparations' => $activities->where('type', 'reparation')->whereNotNull('amount')->sum('amount'),
                'assurances' => $activities->where('type', 'assurance')->whereNotNull('amount')->sum('amount'),
                'visites' => $activities->where('type', 'visite')->whereNotNull('amount')->sum('amount'),
            ],
        ];

        // Pagination manuelle
        $perPage = 20;
        $currentPage = $request->input('page', 1);
        $total = $activities->count();
        $items = $activities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedActivities = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Rapports/Index', [
            'activities' => $paginatedActivities,
            'stats' => $stats,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'activity_type' => $activityType,
                'search' => $searchTerm,
            ],
            'selectedVehicule' => $selectedVehiculeId ? Vehicule::find($selectedVehiculeId) : null,
        ]);
    }

    /**
     * Exporter les rapports en PDF ou Excel
     */
    public function export(Request $request)
    {
        // À implémenter plus tard si nécessaire
        return response()->json(['message' => 'Export feature coming soon']);
    }
}
