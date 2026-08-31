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
}
