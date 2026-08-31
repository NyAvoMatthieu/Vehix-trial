<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicule;

class CalculateVehiculeConsumption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicules:calculate-consumption {vehicule_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculer la consommation moyenne de tous les véhicules ou d\'un véhicule spécifique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vehiculeId = $this->argument('vehicule_id');

        if ($vehiculeId) {
            // Calculer pour un véhicule spécifique
            $vehicule = Vehicule::find($vehiculeId);
            
            if (!$vehicule) {
                $this->error("Véhicule #{$vehiculeId} introuvable.");
                return 1;
            }

            $this->info("Calcul de la consommation pour : {$vehicule->full_name}");
            $vehicule->updateAverageConsumption();
            $this->info("✅ Consommation moyenne : " . ($vehicule->average_consumption ?? 'N/A') . " L/100km");
            
        } else {
            // Calculer pour tous les véhicules
            $vehicules = Vehicule::all();
            $this->info("Calcul de la consommation pour {$vehicules->count()} véhicules...");
            
            $bar = $this->output->createProgressBar($vehicules->count());
            $bar->start();

            foreach ($vehicules as $vehicule) {
                $vehicule->updateAverageConsumption();
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅ Calcul terminé !");
        }

        return 0;
    }
}
