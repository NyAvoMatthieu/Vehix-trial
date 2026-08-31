<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    /** @use HasFactory<\Database\Factories\TrajetFactory> */
    use HasFactory;

        protected $fillable = [
        'vehicule_id',
        'user_id',
        'trajet_date',
        'departure',
        'destination',
        'distance',
        'heure_depart',
        'heure_arrivee',
        'purpose',
        'km_depart',
        'km_arrivee',
        'odo_start',
        'odo_end',
        'kilometrage_mode',
        'notes',
    ];

    protected $casts = [
        'trajet_date' => 'date',
        'distance' => 'decimal:2',
        'km_depart' => 'decimal:2',
        'km_arrivee' => 'decimal:2',
        'odo_start' => 'decimal:2',
        'odo_end' => 'decimal:2',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the last known odometer value for a vehicle
     */
    public static function getLastOdometer($vehiculeId)
    {
        return static::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('trajet_date', 'desc')
            ->orderBy('heure_arrivee', 'desc')
            ->value('odo_end') ?? 0;
    }

 
    /**
     * Get the last known odometer value for a vehicle
     */
    public static function getLastOdometer($vehiculeId)
    {
        return static::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('trajet_date', 'desc')
            ->orderBy('heure_arrivee', 'desc')
            ->value('odo_end');
    }

    /**
     * Boot method to calculate distance automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($trajet) {
            $vehicule = Vehicule::find($trajet->vehicule_id);
            
            if ($trajet->kilometrage_mode === 'odometer') {
                // Mode Odomètre
                if ($trajet->odo_start && $trajet->odo_end) {
                    $trajet->distance = $trajet->odo_end - $trajet->odo_start;
                    $trajet->km_depart = 0;
                    $trajet->km_arrivee = $trajet->distance;
                }
            } else {
                // Mode Kilométrage du trajet
                if ($trajet->km_depart !== null && $trajet->km_arrivee !== null) {
                    $trajet->distance = $trajet->km_arrivee - $trajet->km_depart;
                    
                    // Get last known odometer or use vehicle's initial mileage
                    $lastOdo = static::getLastOdometer($trajet->vehicule_id);
                    
                    // Si aucun trajet précédent, utiliser le mileage initial du véhicule
                    if ($lastOdo === null && $vehicule) {
                        $lastOdo = $vehicule->mileage;
                    }
                    
                    $trajet->odo_start = $lastOdo ?? 0;
                    $trajet->odo_end = $trajet->odo_start + $trajet->distance;
                }
            }
        });
    }
}
}
