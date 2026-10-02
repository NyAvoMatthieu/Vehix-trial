<?php

namespace App\Models;

use App\Enums\AlertType;
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
        'last_alert_level',
    ];

    /**
     * Champs calculés automatiquement inclus dans la sérialisation JSON.
     */
    protected $appends = [
        'statut_echeance',
        'jours_restants',
        'prochaine_visite_estimee',
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

    /**
     * Date de prochaine visite à utiliser pour le statut/l'affichage :
     * la date officielle (`validite`) si elle est renseignée, sinon une
     * estimation (date de cette visite + périodicité configurée par l'admin),
     * conformément au 2.3.1 du cahier des charges ("lorsque cela est possible").
     */
    public function getProchaineVisiteEstimeeAttribute(): ?\Carbon\Carbon
    {
        if ($this->validite) {
            return $this->validite;
        }

        $periodiciteJours = AlertSetting::periodicitePour(AlertType::VISITE_TECHNIQUE);

        if (!$periodiciteJours || !$this->date_visite) {
            return null;
        }

        return $this->date_visite->copy()->addDays($periodiciteJours);
    }

    /**
     * Nombre de jours restants avant la fin de validité (négatif si dépassée).
     */
    public function getJoursRestantsAttribute(): ?int
    {
        $date = $this->prochaine_visite_estimee;

        if (!$date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false);
    }

    /**
     * Statut d'échéance calculé en temps réel à partir du seuil configuré
     * dans `alert_settings` : null (à jour), 'approaching' (bientôt échue)
     * ou 'expired' (expirée).
     */
    public function getStatutEcheanceAttribute(): ?string
    {
        $date = $this->prochaine_visite_estimee;

        if (!$date) {
            return null;
        }

        if ($date->isPast()) {
            return 'expired';
        }

        $seuil = AlertSetting::seuilPour(AlertType::VISITE_TECHNIQUE);

        if ($date->lte(now()->addDays($seuil))) {
            return 'approaching';
        }

        return null;
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
