<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
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

    /**
     * Boot method to calculate distance automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($trajet) {
            if ($trajet->km_depart && $trajet->km_arrivee) {
                $trajet->distance = $trajet->km_arrivee - $trajet->km_depart;
            }
        });
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}