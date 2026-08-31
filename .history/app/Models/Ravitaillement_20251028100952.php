<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ravitaillement extends Model
{
    /** @use HasFactory<\Database\Factories\RavitaillementFactory> */
    use HasFactory;

   protected $fillable = [
        'vehicule_id',
        'user_id',
        'chauffeur_id',
        'ravitaillement_date',
        'station_service',
        'liters_purchased',
        'price_per_liter',
        'amount_paid',
        'total_liters',
        'total_cost',
        'odo_station',
        'odo_arrival',
        'fuel_type',
        'payment_method',
        'receipt_number',
        'is_full_tank',
        'notes',
    ];

    protected $casts = [
        'ravitaillement_date' => 'date',
        'liters_purchased' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'total_liters' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'odo_station' => 'decimal:2',
        'odo_arrival' => 'decimal:2',
        'is_full_tank' => 'boolean',
    ];

    // Relations
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(User::class, 'chauffeur_id');
    }

    /**
     * Get the last known odometer value for a vehicle
     */
    public static function getLastOdometer($vehiculeId)
    {
        // Check trajets first
        $lastTrajet = Trajet::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_end')
            ->orderBy('heure_arrivee', 'desc')
            ->first();

        if ($lastTrajet) {
            return $lastTrajet->odo_end;
        }

        // Check ravitaillements
        $lastRavitaillement = static::where('vehicule_id', $vehiculeId)
            ->whereNotNull('odo_arrival')
            ->orderBy('ravitaillement_date', 'desc')
            ->first();

        if ($lastRavitaillement) {
            return $lastRavitaillement->odo_arrival;
        }

        return null;
    }

    /**
     * Boot method to calculate values automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ravitaillement) {
            $vehicule = Vehicule::find($ravitaillement->vehicule_id);

            // Calculate total_liters from amount_paid and price_per_liter
            if ($ravitaillement->amount_paid && $ravitaillement->price_per_liter) {
                $ravitaillement->total_liters = $ravitaillement->amount_paid / $ravitaillement->price_per_liter;
            }

            // Calculate total_cost from liters_purchased and price_per_liter
            if ($ravitaillement->liters_purchased && $ravitaillement->price_per_liter) {
                $ravitaillement->total_cost = $ravitaillement->liters_purchased * $ravitaillement->price_per_liter;
            }

            // Use amount_paid as display value if available
            if (!$ravitaillement->total_cost && $ravitaillement->amount_paid) {
                $ravitaillement->total_cost = $ravitaillement->amount_paid;
            }

            // Set odo_station if not provided
            if (!$ravitaillement->odo_station) {
                $lastOdo = static::getLastOdometer($ravitaillement->vehicule_id);
                
                if ($lastOdo === null && $vehicule) {
                    $lastOdo = $vehicule->mileage;
                }
                
                $ravitaillement->odo_station = $lastOdo ?? 0;
            }

            // Set fuel_type from vehicle if not provided
            if (!$ravitaillement->fuel_type && $vehicule) {
                $ravitaillement->fuel_type = $vehicule->fuel_type;
            }
        });
    }

    /**
     * Get consumption rate (L/100km)
     */
    public function getConsumptionRate()
    {
        if ($this->odo_station && $this->odo_arrival && $this->liters_purchased) {
            $distance = $this->odo_arrival - $this->odo_station;
            if ($distance > 0) {
                return ($this->liters_purchased / $distance) * 100;
            }
        }
        return null;
    }
}