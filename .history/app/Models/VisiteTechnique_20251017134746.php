<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisiteTechnique extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'user_id',
        'date_visite',
        'validite',
        'centre',
        'operateur',
        'vta',
        'verificateur',
        'type_visite',
        'aptitude',
        'numero_pv',
        'date_pv',
        'numero_recu',
        'droit',
        'pv_frais',
        'carte_frais',
        'tht',
        'tva',
        'total',
        'numero_carte_violette',
        'date_carte_violette',
        'numero_licence',
        'date_licence',
        'patente',
        'ani',
        'observations',
    ];

    protected $casts = [
        'date_visite' => 'date',
        'validite' => 'date',
        'date_pv' => 'date',
        'date_carte_violette' => 'date',
        'date_licence' => 'date',
        'droit' => 'decimal:2',
        'pv_frais' => 'decimal:2',
        'carte_frais' => 'decimal:2',
        'tht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total' => 'decimal:2',
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

    // Méthodes utilitaires
    public function isApte(): bool
    {
        return $this->aptitude === 'APTE';
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->validite && $this->validite->lte(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->validite && $this->validite->lt(now());
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->validite) {
            return null;
        }
        return now()->diffInDays($this->validite, false);
    }

    // Calculer automatiquement le total
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($visite) {
            // Calculer THT si vide
            if (empty($visite->tht)) {
                $visite->tht = ($visite->droit ?? 0) + ($visite->pv_frais ?? 0) + ($visite->carte_frais ?? 0);
            }
            
            // Calculer le total TTC
            $visite->total = ($visite->tht ?? 0) + ($visite->tva ?? 0);
        
        });
    }
}