<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    /** @use HasFactory<\Database\Factories\AssuranceFactory> */
    use HasFactory;
    protected $fillable = [
        'vehicule_id',
        'user_id',
        'company',
        'assureur',
        'agence',
        'policy_number',
        'start_date',
        'end_date',
        'date_delivrance',
        'premium',
        'prime_cp',
        'prime_de',
        'prime_ca',
        'prime_div',
        'prime_total',
        'deductible',
        'coverage_details',
        'lieu_signature',
        'date_signature',
        'agent_nom',
        'notes_signature',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'date_delivrance' => 'date',
        'date_signature' => 'date',
        'premium' => 'decimal:2',
        'prime_cp' => 'decimal:2',
        'prime_de' => 'decimal:2',
        'prime_ca' => 'decimal:2',
        'prime_div' => 'decimal:2',
        'prime_total' => 'decimal:2',
        'deductible' => 'decimal:2',
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

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->lte(now()->addDays($days));
    }

     public function isActive(): bool
    {
        return $this->start_date->lte(now()) && $this->end_date->gte(now());
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }
    
}
