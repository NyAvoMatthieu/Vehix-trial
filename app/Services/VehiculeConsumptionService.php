<?php

namespace App\Services;

use App\Models\Vehicule;
use App\Models\Ravitaillement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VehiculeConsumptionService
{
    /**
     * Coefficients de consommation par type de carburant
     */
    private const FUEL_COEFFICIENTS = [
        'essence' => 5.5,
        'diesel' => 4.5,
        'hybride' => 3.8,
        'electrique' => 0,
        'gpl' => 5.0,
    ];

    /**
     * Consommations de référence par type de véhicule et carburant
     */
    private const REFERENCE_CONSUMPTIONS = [
        'voiture' => [
            'essence' => ['min' => 5.5, 'max' => 9, 'avg' => 7.5],
            'diesel' => ['min' => 4.5, 'max' => 7, 'avg' => 6],
            'hybride' => ['min' => 3, 'max' => 5, 'avg' => 4],
            'gpl' => ['min' => 6, 'max' => 10, 'avg' => 8],
        ],
        'moto' => [
            'essence' => ['min' => 3, 'max' => 5, 'avg' => 4],
        ],
        'utilitaire' => [
            'essence' => ['min' => 8, 'max' => 12, 'avg' => 10],
            'diesel' => ['min' => 7, 'max' => 10, 'avg' => 8.5],
        ],
        'camion' => [
            'diesel' => ['min' => 15, 'max' => 25, 'avg' => 18],
        ],
        'bus' => [
            'diesel' => ['min' => 20, 'max' => 35, 'avg' => 25],
        ],
    ];

    /**
     * Point d'entrée principal - Détermine automatiquement la consommation
     */
    public function determineConsumption(Vehicule $vehicule): array
    {
        // Méthode 1: Consommation déjà enregistrée
        if ($vehicule->average_consumption && $vehicule->average_consumption > 0) {
            return [
                'consumption' => $vehicule->average_consumption,
                'precision' => 'élevée',
                'method' => 'enregistrée',
                'message' => 'Consommation enregistrée par l\'utilisateur',
                'overconsumption_alert' => $this->checkOverconsumption($vehicule),
            ];
        }

        // Méthode 2: Calcul basé sur les pleins réels
        $realConsumption = $this->calculateFromRefuelings($vehicule);
        if ($realConsumption) {
            return $realConsumption;
        }

        // Méthode 3: Recherche dans base de données officielle
        $officialConsumption = $this->fetchOfficialConsumption($vehicule);
        if ($officialConsumption) {
            return $officialConsumption;
        }

        // Méthode 4: Estimation interne (cylindrée + carburant)
        $estimatedConsumption = $this->estimateFromSpecs($vehicule);
        if ($estimatedConsumption) {
            return $estimatedConsumption;
        }

        // Méthode 5: Valeur par défaut selon type de véhicule
        return $this->getDefaultConsumption($vehicule);
    }

    /**
     * MÉTHODE 2: Calcul basé sur les pleins carburant
     */
    private function calculateFromRefuelings(Vehicule $vehicule): ?array
    {
        $refuelings = Ravitaillement::where('vehicule_id', $vehicule->id)
            ->whereNotNull('odo_station')
            ->whereNotNull('liters_purchased')
            ->where('liters_purchased', '>', 0)
            ->orderBy('ravitaillement_date', 'asc')
            ->get();

        if ($refuelings->count() === 0) {
            return null;
        }

        // CAS A: Un seul plein
        if ($refuelings->count() === 1) {
            return $this->calculateSingleRefueling($refuelings->first(), $vehicule);
        }

        // CAS B: Plusieurs pleins
        return $this->calculateMultipleRefuelings($refuelings, $vehicule);
    }

    /**
     * Calcul avec un seul plein
     */
    private function calculateSingleRefueling($refueling, Vehicule $vehicule): array
    {
        // Déterminer le kilométrage de départ
        $startOdo = $vehicule->mileage ?? 0;

        // Si c'est le premier enregistrement, essayer de trouver un trajet antérieur
        $previousTrajet = \App\Models\Trajet::where('vehicule_id', $vehicule->id)
            ->where('heure_depart', '<', $refueling->ravitaillement_date)
            ->orderBy('heure_depart', 'desc')
            ->first();

        if ($previousTrajet && $previousTrajet->odo_end) {
            $startOdo = $previousTrajet->odo_end;
        }

        $kmParcourus = $refueling->odo_station - $startOdo;

        // Validation
        if ($kmParcourus <= 0 || $kmParcourus > 2000) {
            return [
                'consumption' => null,
                'precision' => 'impossible',
                'method' => 'calcul_un_plein',
                'message' => 'Distance parcourue invalide pour le calcul',
                'overconsumption_alert' => null,
            ];
        }

        $consumption = ($refueling->liters_purchased / $kmParcourus) * 100;

        // Validation du résultat
        if ($consumption < 1 || $consumption > 50) {
            return [
                'consumption' => null,
                'precision' => 'invalide',
                'method' => 'calcul_un_plein',
                'message' => 'Résultat de consommation aberrant',
                'overconsumption_alert' => null,
            ];
        }

        return [
            'consumption' => round($consumption, 2),
            'precision' => 'moyenne',
            'method' => 'calcul_un_plein',
            'message' => 'Consommation estimée avec 1 plein (' . round($kmParcourus) . ' km parcourus)',
            'warning' => 'Estimation provisoire - À confirmer avec plus de pleins',
            'details' => [
                'litres' => $refueling->liters_purchased,
                'km_parcourus' => round($kmParcourus, 2),
            ],
            'overconsumption_alert' => $this->checkOverconsumptionValue($consumption, $vehicule),
        ];
    }

    /**
     * Calcul avec plusieurs pleins
     */
    private function calculateMultipleRefuelings($refuelings, Vehicule $vehicule): array
    {
        $totalLiters = 0;
        $totalDistance = 0;
        $validRefuelings = 0;

        for ($i = 1; $i < $refuelings->count(); $i++) {
            $previous = $refuelings[$i - 1];
            $current = $refuelings[$i];

            $distance = $current->odo_station - $previous->odo_station;

            // Ignorer les valeurs aberrantes
            if ($distance > 0 && $distance < 2000) {
                $totalDistance += $distance;
                $totalLiters += $current->liters_purchased;
                $validRefuelings++;
            }
        }

        if ($totalDistance <= 0 || $validRefuelings === 0) {
            return [
                'consumption' => null,
                'precision' => 'impossible',
                'method' => 'calcul_multiple_pleins',
                'message' => 'Données insuffisantes pour le calcul',
                'overconsumption_alert' => null,
            ];
        }

        $consumption = ($totalLiters / $totalDistance) * 100;

        // Validation
        if ($consumption < 1 || $consumption > 50) {
            return [
                'consumption' => null,
                'precision' => 'invalide',
                'method' => 'calcul_multiple_pleins',
                'message' => 'Résultat de consommation aberrant',
                'overconsumption_alert' => null,
            ];
        }

        // Déterminer la précision selon le nombre de pleins
        $precision = 'moyenne';
        if ($validRefuelings >= 5) {
            $precision = 'élevée';
        } elseif ($validRefuelings >= 3) {
            $precision = 'bonne';
        }

        return [
            'consumption' => round($consumption, 2),
            'precision' => $precision,
            'method' => 'calcul_multiple_pleins',
            'message' => "Consommation réelle calculée sur {$validRefuelings} pleins (" . round($totalDistance) . " km)",
            'details' => [
                'pleins_utilises' => $validRefuelings,
                'total_litres' => round($totalLiters, 2),
                'total_km' => round($totalDistance, 2),
            ],
            'overconsumption_alert' => $this->checkOverconsumptionValue($consumption, $vehicule),
        ];
    }

    /**
     * MÉTHODE 3: Recherche dans base de données officielle
     */
    private function fetchOfficialConsumption(Vehicule $vehicule): ?array
    {
        // Clé de cache unique
        $cacheKey = "vehicule_consumption_{$vehicule->make}_{$vehicule->model}_{$vehicule->year}_{$vehicule->fuel_type}";

        // Vérifier le cache (valable 30 jours)
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            return [
                'consumption' => $cachedData['consumption'],
                'precision' => 'élevée',
                'method' => 'base_officielle',
                'message' => 'Consommation officielle (WLTP/NEDC)',
                'source' => $cachedData['source'],
                'overconsumption_alert' => $this->checkOverconsumptionValue($cachedData['consumption'], $vehicule),
            ];
        }

        // Tentative de récupération depuis une API externe
        try {
            // Exemple avec CarData API (remplacer par votre clé API)
            $response = Http::timeout(5)->get('https://api.cardata.com/v1/vehicles', [
                'make' => $vehicule->make,
                'model' => $vehicule->model,
                'year' => substr($vehicule->year, 0, 4),
                'fuel_type' => $vehicule->fuel_type,
            ]);

            if ($response->successful() && isset($response->json()['consumption'])) {
                $consumption = $response->json()['consumption'];

                // Mise en cache
                Cache::put($cacheKey, [
                    'consumption' => $consumption,
                    'source' => 'CarData API',
                ], now()->addDays(30));

                return [
                    'consumption' => round($consumption, 2),
                    'precision' => 'élevée',
                    'method' => 'base_officielle',
                    'message' => 'Consommation officielle (WLTP/NEDC)',
                    'source' => 'CarData API',
                    'overconsumption_alert' => $this->checkOverconsumptionValue($consumption, $vehicule),
                ];
            }
        } catch (\Exception $e) {
            // Échec silencieux - on passe à la méthode suivante
            Log::info("Failed to fetch official consumption: " . $e->getMessage());
        }

        return null;
    }

    /**
     * MÉTHODE 4: Estimation interne (cylindrée + carburant)
     */
    private function estimateFromSpecs(Vehicule $vehicule): ?array
    {
        if (!$vehicule->cylindree || !$vehicule->fuel_type) {
            return null;
        }

        $coefficient = self::FUEL_COEFFICIENTS[$vehicule->fuel_type] ?? 5.5;
        $consumption = ($vehicule->cylindree / 1000) * $coefficient;

        // Ajustement selon le type de véhicule
        $typeMultiplier = match($vehicule->vehicule_type) {
            'moto' => 0.6,
            'utilitaire' => 1.3,
            'camion' => 2.5,
            'bus' => 3.5,
            default => 1.0,
        };

        $consumption *= $typeMultiplier;

        // Limiter dans une fourchette réaliste
        $consumption = max(3, min(40, $consumption));

        return [
            'consumption' => round($consumption, 2),
            'precision' => 'faible',
            'method' => 'estimation_cylindree',
            'message' => 'Estimation basée sur la cylindrée (' . $vehicule->cylindree . ' cm³)',
            'warning' => 'Valeur approximative - À affiner avec des pleins réels',
            'overconsumption_alert' => null, // Pas de comparaison possible
        ];
    }

    /**
     * MÉTHODE 5: Valeur par défaut selon type de véhicule
     */
    private function getDefaultConsumption(Vehicule $vehicule): array
    {
        $vehiculeType = $vehicule->vehicule_type ?? 'voiture';
        $fuelType = $vehicule->fuel_type ?? 'essence';

        $reference = self::REFERENCE_CONSUMPTIONS[$vehiculeType][$fuelType] ??
                     self::REFERENCE_CONSUMPTIONS['voiture']['essence'];

        return [
            'consumption' => $reference['avg'],
            'precision' => 'très faible',
            'method' => 'valeur_defaut',
            'message' => 'Valeur par défaut pour ' . $vehiculeType . ' ' . $fuelType,
            'warning' => 'Estimation générique - Enregistrez des pleins pour un calcul précis',
            'range' => [
                'min' => $reference['min'],
                'max' => $reference['max'],
            ],
            'overconsumption_alert' => null,
        ];
    }

    /**
     * MÉTHODE 6: Vérification de surconsommation
     */
    private function checkOverconsumption(Vehicule $vehicule): ?array
    {
        if (!$vehicule->average_consumption) {
            return null;
        }

        return $this->checkOverconsumptionValue($vehicule->average_consumption, $vehicule);
    }

    private function checkOverconsumptionValue(float $consumption, Vehicule $vehicule): ?array
    {
        $vehiculeType = $vehicule->vehicule_type ?? 'voiture';
        $fuelType = $vehicule->fuel_type ?? 'essence';

        $reference = self::REFERENCE_CONSUMPTIONS[$vehiculeType][$fuelType] ?? null;

        if (!$reference) {
            return null;
        }

        $expectedConsumption = $reference['avg'];
        $threshold = $expectedConsumption * 1.20; // +20%

        if ($consumption >= $threshold) {
            $excess = (($consumption - $expectedConsumption) / $expectedConsumption) * 100;
            $rounded_excess = round($excess,2);
            return [
                'alert' => true,
                'level' => $excess > 40 ? 'critique' : 'élevée',
                'message' => "⚠️ Surconsommation détectée (+{$rounded_excess}%)",
                'expected' => round($expectedConsumption, 2),
                'actual' => round($consumption, 2),
                'excess_percent' => round($excess, 1),
                'recommendations' => $this->getRecommendations($excess),
            ];
        }

        return [
            'alert' => false,
            'message' => '✓ Consommation normale',
            'expected' => round($expectedConsumption, 2),
            'actual' => round($consumption, 2),
        ];
    }

    /**
     * Recommandations en cas de surconsommation
     */
    private function getRecommendations(float $excessPercent): array
    {
        $recommendations = [
            'Vérifier la pression des pneus',
            'Contrôler le filtre à air',
        ];

        if ($excessPercent > 30) {
            $recommendations[] = 'Faire vérifier le moteur par un professionnel';
            $recommendations[] = 'Contrôler les injecteurs';
        }

        if ($excessPercent > 40) {
            $recommendations[] = '🚨 Problème mécanique probable - diagnostic urgent';
        }

        return $recommendations;
    }

    /**
     * Mise à jour automatique de la consommation du véhicule
     */
    public function updateVehiculeConsumption(Vehicule $vehicule): void
    {
        $result = $this->determineConsumption($vehicule);

        if ($result['consumption'] && $result['precision'] !== 'très faible') {
            $vehicule->update([
                'average_consumption' => $result['consumption']
            ]);
        }
    }
}
