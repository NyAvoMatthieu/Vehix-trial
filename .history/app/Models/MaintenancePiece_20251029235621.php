<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaintenancePiece extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'maintenance_id',
        'nom_piece',
        'marque_piece',
        'reference_code',
        'emplacement',
        'etat_piece',
        'vendeur',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'date_installation',
        'limite_utilisation',
        'utilisation_actuelle',
        'potentiel_restant',
        'unite_mesure',
        'prochaine_maintenance',
        'observation',
        'alerte_proche_limite',
    ];

    protected $casts = [
        'date_installation' => 'date',
        'prochaine_maintenance' => 'date',
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
        'limite_utilisation' => 'decimal:2',
        'utilisation_actuelle' => 'decimal:2',
        'potentiel_restant' => 'decimal:2',
        'alerte_proche_limite' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($piece) {
            // Si la pièce est neuve, forcer l'utilisation actuelle à 0
            if ($piece->etat_piece === 'neuf') {
                $piece->utilisation_actuelle = 0;
            }
            
            // Calculer le prix total
            $piece->prix_total = ($piece->quantite ?? 1) * ($piece->prix_unitaire ?? 0);

            // Calculer le potentiel restant
            $piece->potentiel_restant = ($piece->limite_utilisation ?? 0) - ($piece->utilisation_actuelle ?? 0);

            // Vérifier si proche de la limite (< 20%)
            if (($piece->limite_utilisation ?? 0) > 0) {
                $pourcentageRestant = ($piece->potentiel_restant / $piece->limite_utilisation) * 100;
                $piece->alerte_proche_limite = $pourcentageRestant < 20;
            } else {
                $piece->alerte_proche_limite = false;
            }

            // Calculer la prochaine maintenance selon l'unité
            $piece->prochaine_maintenance = $piece->calculateNextMaintenance();
        });

        static::saved(function ($piece) {
            // Mettre à jour le coût des pièces de la maintenance
            if ($piece->maintenance) {
                $piece->maintenance->updateCoutPieces();
            }
        });

        static::deleted(function ($piece) {
            // Mettre à jour le coût des pièces de la maintenance
            if ($piece->maintenance) {
                $piece->maintenance->updateCoutPieces();
            }
        });
    }

    // Relations
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    // Méthodes utiles
    public function calculateNextMaintenance(): ?Carbon
    {
        if (!$this->limite_utilisation || !$this->date_installation) {
            return null;
        }

        try {
            $dateInstallation = $this->date_installation instanceof Carbon 
                ? $this->date_installation 
                : Carbon::parse($this->date_installation);

            return match($this->unite_mesure) {
                'jours' => $dateInstallation->copy()->addDays($this->limite_utilisation),
                'mois' => $dateInstallation->copy()->addMonths($this->limite_utilisation),
                'annees' => $dateInstallation->copy()->addYears($this->limite_utilisation),
                default => null, // Pour km, heures, cycles - pas de calcul automatique de date
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateUtilisation(float $nouvelleUtilisation)
    {
        // Ne pas permettre la mise à jour si la pièce est neuve
        if ($this->etat_piece === 'neuf') {
            return false;
        }
        
        $this->utilisation_actuelle = $nouvelleUtilisation;
        $this->save();
        return true;
    }

    public function getPourcentageUtilisation(): float
    {
        if (($this->limite_utilisation ?? 0) <= 0) {
            return 0;
        }
        return (($this->utilisation_actuelle ?? 0) / $this->limite_utilisation) * 100;
    }

    public function getPourcentageRestant(): float
    {
        return 100 - $this->getPourcentageUtilisation();
    }

    public function isExpired(): bool
    {
        return ($this->potentiel_restant ?? 0) <= 0;
    }

    public function needsAlert(): bool
    {
        return $this->alerte_proche_limite || $this->isExpired();
    }

    public function getStatusBadge(): array
    {
        $pourcentage = $this->getPourcentageRestant();

        if ($pourcentage <= 0) {
            return ['class' => 'bg-red-100 text-red-800', 'label' => 'Expiré'];
        } elseif ($pourcentage < 20) {
            return ['class' => 'bg-orange-100 text-orange-800', 'label' => 'Critique'];
        } elseif ($pourcentage < 50) {
            return ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Attention'];
        } else {
            return ['class' => 'bg-green-100 text-green-800', 'label' => 'Bon'];
        }
    }

    // Attribut pour obtenir le libellé de l'état
    public function getEtatLabelAttribute(): string
    {
        return match($this->etat_piece) {
            'neuf' => 'Neuf',
            'occasion' => 'Occasion',
            default => $this->etat_piece,
        };
    }

    // Attribut pour obtenir des informations complètes sur la pièce
    public function getFullInfoAttribute(): string
    {
        return "{$this->nom_piece} ({$this->marque_piece}) - {$this->etat_label}";
    }
}