<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type',
        'price_per_liter',
        'effective_date',
        'set_by',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'price_per_liter' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur qui a défini le prix
     */
    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Get current active price for a specific fuel type
     */
    public static function getCurrentPrice($fuelType)
    {
        return static::where('fuel_type', $fuelType)
            ->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get all current active prices
     */
    public static function getCurrentPrices()
    {
        $fuelTypes = ['diesel', 'essence', 'gpl', 'electrique'];
        $prices = [];

        foreach ($fuelTypes as $type) {
            $price = static::getCurrentPrice($type);
            if ($price) {
                $prices[] = $price;
            }
        }

        return $prices;
    }

    /**
     * Get price history for a fuel type
     */
    public static function getPriceHistory($fuelType, $limit = 10)
    {
        return static::where('fuel_type', $fuelType)
            ->orderBy('effective_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get price at a specific date
     */
    public static function getPriceAtDate($fuelType, $date)
    {
        return static::where('fuel_type', $fuelType)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Deactivate all other prices of same fuel type when activating
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($fuelPrice) {
            // If activating this price, deactivate others of same type
            if ($fuelPrice->is_active && $fuelPrice->isDirty('is_active')) {
                static::where('fuel_type', $fuelPrice->fuel_type)
                    ->where('id', '!=', $fuelPrice->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Scope for active prices
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific fuel type
     */
    public function scopeForFuelType($query, $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }

    /**
     * Get fuel type label in French
     */
    public function getFuelTypeLabelAttribute()
    {
        $labels = [
            'diesel' => 'Diesel',
            'essence' => 'Essence',
            'gpl' => 'GPL',
            'electrique' => 'Électrique',
        ];

        return $labels[$this->fuel_type] ?? $this->fuel_type;
    }

    /**
     * Check if price is currently active
     */
    public function isCurrentlyActive()
    {
        return $this->is_active && $this->effective_date <= now();
    }

    /**
     * Get price variation compared to previous price
     */
    public function getPriceVariation()
    {
        $previousPrice = static::where('fuel_type', $this->fuel_type)
            ->where('effective_date', '<', $this->effective_date)
            ->orderBy('effective_date', 'desc')
            ->first();

        if (!$previousPrice) {
            return null;
        }

        $difference = $this->price_per_liter - $previousPrice->price_per_liter;
        $percentage = ($difference / $previousPrice->price_per_liter) * 100;

        return [
            'difference' => $difference,
            'percentage' => round($percentage, 2),
            'previous_price' => $previousPrice->price_per_liter,
        ];
    }
}
