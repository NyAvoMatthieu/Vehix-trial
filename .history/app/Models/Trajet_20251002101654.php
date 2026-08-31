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
        'notes',
    ];

    protected $casts = [
        'trajet_date' => 'date',
        'distance' => 'decimal:2',
        'km_depart' => 'decimal:2',
        'km_arrivee' => 'decimal:2',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
