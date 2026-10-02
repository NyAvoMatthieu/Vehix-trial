<?php

namespace App\Console\Commands;

use App\Enums\AlertType;
use App\Models\AlertSetting;
use App\Models\Assurance;
use App\Models\VisiteTechnique;
use App\Notifications\AssuranceEcheanceNotification;
use App\Notifications\VisiteTechniqueEcheanceNotification;
use App\Services\EcheanceService;
use Illuminate\Console\Command;

class CheckEcheances extends Command
{
    protected $signature = 'echeances:check';

    protected $description = "Vérifie les échéances (assurances, visites techniques) et envoie les alertes nécessaires";

    public function handle(EcheanceService $service): int
    {
        $nbAssurances = $this->traiterAssurances($service);
        $nbVisites = $this->traiterVisitesTechniques($service);

        $service->checkMaintenances(); // déclenche déjà ses propres notifications en interne

        $this->info("Vérification terminée : {$nbAssurances} alerte(s) assurance, {$nbVisites} alerte(s) visite technique.");

        return self::SUCCESS;
    }

    protected function traiterAssurances(EcheanceService $service): int
    {
        $compteur = 0;

        foreach ($service->checkAssurances() as $resultat) {
            /** @var Assurance $assurance */
            $assurance = $resultat['model'];

            if ($resultat['is_nouveau']) {
                $assurance->user?->notify(new AssuranceEcheanceNotification($assurance, $resultat['niveau']));
                $compteur++;
            }

            if ($assurance->last_alert_level !== $resultat['niveau']) {
                $assurance->forceFill(['last_alert_level' => $resultat['niveau']])->saveQuietly();
            }
        }

        // Réinitialiser les assurances qui sont sorties de la zone d'alerte (ex: renouvelées)
        $seuil = AlertSetting::seuilPour(AlertType::ASSURANCE);
        Assurance::whereNotNull('last_alert_level')
            ->where('end_date', '>', now()->addDays($seuil))
            ->update(['last_alert_level' => null]);

        return $compteur;
    }

    protected function traiterVisitesTechniques(EcheanceService $service): int
    {
        $compteur = 0;

        foreach ($service->checkVisitesTechniques() as $resultat) {
            /** @var VisiteTechnique $visite */
            $visite = $resultat['model'];

            if ($resultat['is_nouveau']) {
                $visite->user?->notify(new VisiteTechniqueEcheanceNotification($visite, $resultat['niveau']));
                $compteur++;
            }

            if ($visite->last_alert_level !== $resultat['niveau']) {
                $visite->forceFill(['last_alert_level' => $resultat['niveau']])->saveQuietly();
            }
        }

        $seuil = AlertSetting::seuilPour(AlertType::VISITE_TECHNIQUE);
        VisiteTechnique::whereNotNull('last_alert_level')
            ->where('validite', '>', now()->addDays($seuil))
            ->update(['last_alert_level' => null]);

        return $compteur;
    }
}
