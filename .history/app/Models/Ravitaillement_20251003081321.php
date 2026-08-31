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
        'ravitaillement_date',
        'station_name',
        'liters',
        'price_per_liter',
        'total_cost',
        'odo_station',
        'odo_arrival',
        'fuel_type',
        'payment_method',
        'receipt_number',
        'full_tank',
        'fuel_left',
        'remarks',
    ];

    protected $casts = [
       'ravitaillement_date' => 'date',
        'liters' => 'decimal:2',
        'price_per_liter' => 'decimal:3',
        'total_cost' => 'decimal:2',
        'odo_station' => 'integer',
        'odo_arrival' => 'integer',
        'full_tank' => 'boolean',
        'fuel_left' => 'decimal:2',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recus()
    {
        return $this->morphMany(Recu::class, 'recuable');
    }

    /**
     * Calculer le carburant restant
     */
    public function calculateFuelLeft(): float
    {
        $previousFueling = self::where('vehicule_id', $this->vehicule_id)
            ->where('full_tank', true)
            ->where('id', '<', $this->id)
            ->orderBy('odo_station', 'desc')
            ->first();

        if (!$previousFueling) {
            return $this->liters;
        }

        $distanceTraveled = $this->odo_station - $previousFueling->odo_station;

        if ($distanceTraveled <= 0) {
            return $this->liters;
        }

        // Utiliser la consommation moyenne du véhicule (L/100km)
        $avgConsumption = $this->vehicule->average_consumption ?? 7.5;
        $fuelConsumed = ($distanceTraveled / 100) * $avgConsumption;

        $fuelRemaining = $previousFueling->liters - $fuelConsumed;
        $fuelLeft = max(0, $fuelRemaining) + $this->liters;

        return round($fuelLeft, 2);
    }

    /**
     * Boot method pour calculer automatiquement fuel_left
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ravitaillement) {
            if ($ravitaillement->full_tank && $ravitaillement->odo_station) {
                $ravitaillement->fuel_left = $ravitaillement->calculateFuelLeft();
            }
        });
    }
}
