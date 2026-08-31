<?php

namespace App\Observers;

use App\Models\Ravitaillement;

class RavitaillementObserver
{
    /**
     * Handle the Ravitaillement "created" event.
     */
    public function created(Ravitaillement $ravitaillement): void
    {
        // Mettre à jour la consommation moyenne du véhicule
        $ravitaillement->vehicule->updateAverageConsumption();
    }

    /**
     * Handle the Ravitaillement "updated" event.
     */
    public function updated(Ravitaillement $ravitaillement): void
    {
        // Mettre à jour la consommation moyenne du véhicule
        $ravitaillement->vehicule->updateAverageConsumption();
    }

    /**
     * Handle the Ravitaillement "deleted" event.
     */
    public function deleted(Ravitaillement $ravitaillement): void
    {
        // Mettre à jour la consommation moyenne du véhicule
        $ravitaillement->vehicule->updateAverageConsumption();
    }

    /**
     * Handle the Ravitaillement "restored" event.
     */
    public function restored(Ravitaillement $ravitaillement): void
    {
        //
    }

    /**
     * Handle the Ravitaillement "force deleted" event.
     */
    public function forceDeleted(Ravitaillement $ravitaillement): void
    {
        //
    }
}
