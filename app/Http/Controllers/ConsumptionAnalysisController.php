<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Services\VehiculeConsumptionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConsumptionAnalysisController extends Controller
{
    protected $consumptionService;

    public function __construct(VehiculeConsumptionService $consumptionService)
    {
        $this->consumptionService = $consumptionService;
    }

    /**
     * Afficher l'analyse de consommation d'un véhicule
     */
    public function show(Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        // Obtenir l'analyse complète de consommation
        $consumptionAnalysis = $this->consumptionService->determineConsumption($vehicule);

        // Récupérer l'historique des pleins
        $refuelings = $vehicule->ravitaillements()
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->limit(10)
            ->get();

        // Calculer les statistiques
        $stats = $this->calculateStats($vehicule);

        return Inertia::render('Consumption/Analysis', [
            'vehicule' => $vehicule,
            'analysis' => $consumptionAnalysis,
            'refuelings' => $refuelings,
            'stats' => $stats,
        ]);
    }

    /**
     * API pour obtenir la consommation estimée
     */
    public function estimate(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        $analysis = $this->consumptionService->determineConsumption($vehicule);

        return response()->json($analysis);
    }

    /**
     * Forcer la mise à jour de la consommation
     */
    public function update(Vehicule $vehicule)
    {
        $this->authorize('update', $vehicule);

        $this->consumptionService->updateVehiculeConsumption($vehicule);

        return redirect()->back()->with('success', 'Consommation mise à jour avec succès');
    }

    /**
     * Calculer les statistiques de consommation
     */
    private function calculateStats(Vehicule $vehicule)
    {
        $refuelings = $vehicule->ravitaillements()
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'asc')
            ->get();

        if ($refuelings->count() < 2) {
            return null;
        }

        $consumptions = [];
        for ($i = 1; $i < $refuelings->count(); $i++) {
            $previous = $refuelings[$i - 1];
            $current = $refuelings[$i];

            $distance = $current->odo_station - $previous->odo_station;

            if ($distance > 0 && $distance < 2000) {
                $consumption = ($current->liters_purchased / $distance) * 100;
                if ($consumption > 1 && $consumption < 50) {
                    $consumptions[] = $consumption;
                }
            }
        }

        if (empty($consumptions)) {
            return null;
        }

        return [
            'min' => round(min($consumptions), 2),
            'max' => round(max($consumptions), 2),
            'avg' => round(array_sum($consumptions) / count($consumptions), 2),
            'data_points' => count($consumptions),
        ];
    }
}
