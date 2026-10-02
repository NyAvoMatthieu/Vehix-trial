<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\AlertSetting;
use App\Models\Assurance;
use App\Models\VisiteTechnique;
use Illuminate\Support\Collection;

class EcheanceService
{
    public const NIVEAU_APPROCHE = 'approaching';
    public const NIVEAU_EXPIRE = 'expired';

    /**
     * Échéances d'assurance nécessitant une alerte (approche ou dépassée).
     */
    public function checkAssurances(): Collection
    {
        $seuil = AlertSetting::seuilPour(AlertType::ASSURANCE);

        return Assurance::with('user')
            ->whereNotNull('end_date')
            ->get()
            ->map(fn (Assurance $assurance) => $this->evaluer($assurance, 'end_date', $seuil))
            ->filter(fn (array $r) => $r['niveau'] !== null)
            ->values();
    }

    /**
     * Visites techniques nécessitant une alerte (approche ou dépassée).
     */
    public function checkVisitesTechniques(): Collection
    {
        $seuil = AlertSetting::seuilPour(AlertType::VISITE_TECHNIQUE);

        return VisiteTechnique::with('user')
            ->whereNotNull('validite')
            ->get()
            ->map(fn (VisiteTechnique $visite) => $this->evaluer($visite, 'validite', $seuil))
            ->filter(fn (array $r) => $r['niveau'] !== null)
            ->values();
    }

    /**
     * En attente de la Partie 4 (maintenance préventive).
     *
     * La table `maintenances` n'a pas encore de colonne "prochaine échéance"
     * exploitable : `date_debut`/`date_fin`/`maintenance_date` ne décrivent
     * que des interventions déjà réalisées. Dès que la Partie 4 ajoute les
     * colonnes de calcul (prochaine échéance par date et/ou par km), cette
     * méthode s'implémentera exactement sur le modèle des deux ci-dessus.
     */
    public function checkMaintenances(): Collection
    {
        // return collect();
        return app(\App\Services\MaintenanceIntervalleService::class)->checkAll();
    }

    /**
     * Résultat agrégé des 3 domaines, prêt à être consommé par la commande planifiée.
     */
    public function checkAll(): array
    {
        return [
            AlertType::ASSURANCE->value => $this->checkAssurances(),
            AlertType::VISITE_TECHNIQUE->value => $this->checkVisitesTechniques(),
            AlertType::MAINTENANCE->value => $this->checkMaintenances(),
        ];
    }

    /**
     * Évalue le niveau d'alerte d'une échéance sans envoyer de notification.
     *
     * @return array{model: mixed, niveau: string|null, is_nouveau: bool}
     */
    protected function evaluer($model, string $dateColumn, int $seuilJours): array
    {
        $date = $model->{$dateColumn};
        $niveau = null;

        if ($date) {
            if ($date->isPast()) {
                $niveau = self::NIVEAU_EXPIRE;
            } elseif ($date->lte(now()->addDays($seuilJours))) {
                $niveau = self::NIVEAU_APPROCHE;
            }
        }

        return [
            'model' => $model,
            'niveau' => $niveau,
            // "nouveau" = le niveau vient de changer -> une notification doit partir
            'is_nouveau' => $niveau !== null && $model->last_alert_level !== $niveau,
        ];
    }
}
