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
        'reference_code',
        'emplacement',
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
            // Calculer le prix total
            $piece->prix_total = $piece->quantite * $piece->prix_unitaire;

            // Calculer le potentiel restant
            $piece->potentiel_restant = $piece->limite_utilisation - $piece->utilisation_actuelle;

            // Vérifier si proche de la limite (< 20%)
            if ($piece->limite_utilisation > 0) {
                $pourcentageRestant = ($piece->potentiel_restant / $piece->limite_utilisation) * 100;
                $piece->alerte_proche_limite = $pourcentageRestant < 20;
            }

            // Calculer la prochaine maintenance selon l'unité
            $piece->prochaine_maintenance = $piece->calculateNextMaintenance();
        });

        static::saved(function ($piece) {
            // Mettre à jour le coût des pièces de la maintenance
            $piece->maintenance->updateCoutPieces();
        });

        static::deleted(function ($piece) {
            // Mettre à jour le coût des pièces de la maintenance
            $piece->maintenance->updateCoutPieces();
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

        return match($this->unite_mesure) {
            'jours' => $this->date_installation->addDays($this->limite_utilisation),
            'mois' => $this->date_installation->addMonths($this->limite_utilisation),
            'annees' => $this->date_installation->addYears($this->limite_utilisation),
            default => null, // Pour km, heures, cycles - pas de calcul automatique de date
        };
    }

    public function updateUtilisation(float $nouvelleUtilisation)
    {
        $this->utilisation_actuelle = $nouvelleUtilisation;
        $this->save();
    }

    public function getPourcentageUtilisation(): float
    {
        if ($this->limite_utilisation <= 0) {
            return 0;
        }
        return ($this->utilisation_actuelle / $this->limite_utilisation) * 100;
    }

    public function getPourcentageRestant(): float
    {
        return 100 - $this->getPourcentageUtilisation();
    }

    public function isExpired(): bool
    {
        return $this->potentiel_restant <= 0;
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
}