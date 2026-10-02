<?php

namespace App\Services;

use App\Enums\MaintenanceEcheanceStatut;
use App\Models\Maintenance;
use App\Models\MaintenanceInterventionType;
use App\Models\Vehicule;
use App\Models\VehiculeMaintenanceIntervalle;
use App\Notifications\MaintenanceEcheanceNotification;
use Illuminate\Support\Collection;

class MaintenanceIntervalleService
{
    /**
     * S'assure qu'une ligne de suivi existe pour chaque (véhicule, type actif).
     * À appeler avant tout calcul : couvre les nouveaux véhicules et les nouveaux
     * types ajoutés au catalogue après coup.
     */
    public function assurerLignesDeSuivi(Vehicule $vehicule): void
    {
        $typesActifs = MaintenanceInterventionType::actif()->pluck('id');

        $typesExistants = VehiculeMaintenanceIntervalle::where('vehicule_id', $vehicule->id)
            ->pluck('intervention_type_id');

        foreach ($typesActifs->diff($typesExistants) as $typeId) {
            VehiculeMaintenanceIntervalle::create([
                'vehicule_id' => $vehicule->id,
                'intervention_type_id' => $typeId,
            ]);
        }
    }

    /**
     * Met à jour dernier_km_effectue / derniere_date_effectuee après validation
     * d'une maintenance rattachée à un type du catalogue.
     * À appeler depuis MaintenanceController::validateMaintenance().
     */
    public function synchroniserApresMaintenance(Maintenance $maintenance): void
    {
        if (!$maintenance->intervention_type_id) {
            return;
        }

        $suivi = VehiculeMaintenanceIntervalle::firstOrNew([
            'vehicule_id' => $maintenance->vehicule_id,
            'intervention_type_id' => $maintenance->intervention_type_id,
        ]);

        // On ne fait jamais reculer le suivi : seulement si cette intervention
        // est aussi récente ou plus récente que la dernière connue.
        $estPlusRecente = !$suivi->derniere_date_effectuee
            || $maintenance->date_debut->greaterThanOrEqualTo($suivi->derniere_date_effectuee);

        if ($estPlusRecente) {
            $suivi->dernier_km_effectue = (int) $maintenance->kilometrage_actuel;
            $suivi->derniere_date_effectuee = $maintenance->date_debut;
            $suivi->last_alert_level = null; // on repart sur un cycle d'alerte propre
        }

        $suivi->save();
    }

    /**
     * Calcule le statut de chaque ligne de suivi d'un véhicule et déclenche
     * les notifications nécessaires (anti-doublon via last_alert_level, même
     * principe qu'EcheanceService pour assurances/visites techniques).
     */
    public function checkVehicule(Vehicule $vehicule): Collection
    {
        $this->assurerLignesDeSuivi($vehicule);

        $suivis = VehiculeMaintenanceIntervalle::with('type', 'vehicule')
            ->where('vehicule_id', $vehicule->id)
            ->whereHas('type', fn ($q) => $q->where('actif', true))
            ->get();

        foreach ($suivis as $suivi) {
            $this->notifierSiNecessaire($suivi);
        }

        return $suivis;
    }

    public function checkAll(): Collection
    {
        return Vehicule::all()->flatMap(fn (Vehicule $v) => $this->checkVehicule($v));
    }

    protected function notifierSiNecessaire(VehiculeMaintenanceIntervalle $suivi): void
    {
        $statut = $suivi->statut;

        if ($statut === MaintenanceEcheanceStatut::A_JOUR) {
            return;
        }

        // On ne renotifie que si le niveau de sévérité a changé depuis la dernière alerte.
        if ($suivi->last_alert_level === $statut->value) {
            return;
        }

        // TODO: adapter le destinataire si les alertes doivent plutôt partir vers
        // un gestionnaire de flotte / admin plutôt que le propriétaire du véhicule
        // (vérifier comment EcheanceService::checkAssurances() / checkVisitesTechniques()
        // choisissent leur destinataire, pour rester cohérent).
        $destinataire = $suivi->vehicule->user;

        if ($destinataire) {
            $destinataire->notify(new MaintenanceEcheanceNotification($suivi));
        }

        $suivi->update(['last_alert_level' => $statut->value]);
    }
}
