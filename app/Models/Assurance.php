<?php

namespace App\Models;

use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
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
        'cotisation',
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
        'last_alert_level',
    ];

    /**
     * Champs calculés automatiquement inclus dans la sérialisation JSON
     * (donc disponibles directement côté Vue sans requête supplémentaire).
     */
    protected $appends = [
        'statut_echeance',
        'jours_restants',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'date_delivrance' => 'date',
        'date_signature' => 'date',
        'cotisation' => 'decimal:2',
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
        return $this->end_date && $this->end_date->lte(now()->addDays($days));
    }

    public function isActive(): bool
    {
        return $this->start_date && $this->end_date && 
               $this->start_date->lte(now()) && $this->end_date->gte(now());
    }

    public function getDurationInDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Nombre de jours restants avant l'échéance (négatif si déjà dépassée).
     */
    public function getJoursRestantsAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->end_date->copy()->startOfDay(), false);
    }

    /**
     * Statut d'échéance calculé en temps réel à partir du seuil configuré
     * dans `alert_settings` : null (à jour), 'approaching' (bientôt expirée)
     * ou 'expired' (expirée).
     *
     * NB : distinct de `last_alert_level`, qui ne reflète que le dernier
     * niveau pour lequel une notification a été envoyée.
     */
    public function getStatutEcheanceAttribute(): ?string
    {
        if (!$this->end_date) {
            return null;
        }

        if ($this->end_date->isPast()) {
            return 'expired';
        }

        $seuil = AlertSetting::seuilPour(AlertType::ASSURANCE);

        if ($this->end_date->lte(now()->addDays($seuil))) {
            return 'approaching';
        }

        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($assurance) {
            // Calculer automatiquement le total des primes (cotisation + CP + DE + CA + DIV)
            $assurance->prime_total =
                ($assurance->cotisation ?? 0) +
                ($assurance->prime_cp ?? 0) +
                ($assurance->prime_de ?? 0) +
                ($assurance->prime_ca ?? 0) +
                ($assurance->prime_div ?? 0);
        });
    }
}
