<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ravitaillement extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'user_id',
        'chauffeur_name',
        'ravitaillement_date',
        'station_service',
        'liters_purchased',
        'price_per_liter',
        'amount_paid',
        'total_liters',
        'total_cost',
        'odo_station',
        'fuel_type',
        'payment_method',
        'receipt_number',
        'notes',
        'is_custom_price',
    ];

    protected $casts = [
        'ravitaillement_date' => 'date',
        'liters_purchased' => 'decimal:2',
        'price_per_liter' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'total_liters' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'odo_station' => 'decimal:2',
        'is_custom_price' => 'boolean',
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
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'desc')
            ->first();

        if ($lastRavitaillement) {
            return $lastRavitaillement->odo_station;
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

            // Check if custom price was used
            if ($ravitaillement->is_custom_price && $vehicule) {
                // Get current official price for this fuel type
                $officialPrice = FuelPrice::getCurrentPrice($vehicule->fuel_type);

                if ($officialPrice && $ravitaillement->price_per_liter != $officialPrice->price_per_liter) {
                    // Log this custom price for admin visibility
                    static::logCustomPrice($ravitaillement, $officialPrice);
                }
            }
        });
    }

    /**
     * Log custom prices for admin tracking
     */
    protected static function logCustomPrice($ravitaillement, $officialPrice)
    {
        // This will be tracked in the FuelPrice model for admin visibility
        // Admins can see these deviations in the FuelPrices index
    }

    /**
     * Get custom prices statistics for admins
     */
    public static function getCustomPriceStats($fuelType = null)
    {
        $query = static::where('is_custom_price', true);

        if ($fuelType) {
            $query->where('fuel_type', $fuelType);
        }

        return [
            'count' => $query->count(),
            'avg_price' => $query->avg('price_per_liter'),
            'min_price' => $query->min('price_per_liter'),
            'max_price' => $query->max('price_per_liter'),
            'recent' => $query->with(['user', 'vehicule'])
                ->orderBy('ravitaillement_date', 'desc')
                ->limit(10)
                ->get(),
        ];
    }
}
