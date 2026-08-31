<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Trajet extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'user_id',
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
        'heure_depart' => 'datetime',
        'heure_arrivee' => 'datetime',
        'distance' => 'decimal:2',
        'km_depart' => 'decimal:2',
        'km_arrivee' => 'decimal:2',
        'odo_start' => 'decimal:2',
        'odo_end' => 'decimal:2',
    ];

    //Définition du format de sérialisation des dates
    protected $dateFormat = 'Y-m-d H:i:s';

    // accesseurs pour formater les dates pour les formulaires
    public function getHeureDepartFormattedAttribute()
    {
        return $this->heure_depart ? $this->heure_depart->format('Y-m-d\TH:i') : '';
    }

    public function getHeureArriveeFormattedAttribute()
    {
        return $this->heure_arrivee ? $this->heure_arrivee->format('Y-m-d\TH:i') : '';
    }

    //  Mutateurs pour gérer correctement le timezone
    public function setHeureDepartAttribute($value)
    {
        if ($value) {
            // S'assurer que la date est traitée comme locale
            $this->attributes['heure_depart'] = Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }

    public function setHeureArriveeAttribute($value)
    {
        if ($value) {
            // S'assurer que la date est traitée comme locale
            $this->attributes['heure_arrivee'] = Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }

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
            
              // Convertions des dates si elles sont des strings
            if (is_string($trajet->heure_depart)) {
                $trajet->heure_depart = Carbon::parse($trajet->heure_depart);
            }
            if (is_string($trajet->heure_arrivee)) {
                $trajet->heure_arrivee = Carbon::parse($trajet->heure_arrivee);
            }

            if ($trajet->kilometrage_mode === 'odometer') {
                // Mode Odomètre
                if ($trajet->odo_start && $trajet->odo_end) {
                    $trajet->distance = $trajet->odo_end - $trajet->odo_start;
                    $trajet->km_depart = 0;
                    $trajet->km_arrivee = $trajet->distance;
                }
            } else {
                // Mode Kilométrage du trajet
                if ($trajet->km_arrivee !== null) {
                    // Si km_depart n'est pas fourni, utiliser 0
                    if ($trajet->km_depart === null) {
                        $trajet->km_depart = 0;
                    }
                    
                    $trajet->distance = $trajet->km_arrivee - $trajet->km_depart;
                    
                    // Get last known odometer or use vehicle's initial mileage
                    $lastOdo = static::getLastOdometer($trajet->vehicule_id);
                    
                    // Si aucun trajet précédent, utiliser le mileage initial du véhicule
                    if ($lastOdo === null && $vehicule) {
                        $lastOdo = $vehicule->mileage;
                    }
                    
                    // Si odo_start n'est pas fourni, utiliser le dernier odomètre connu
                    if ($trajet->odo_start === null) {
                        $trajet->odo_start = $lastOdo ?? 0;
                    }
                    
                    $trajet->odo_end = $trajet->odo_start + $trajet->distance;
                }
            }
        });
    }
}