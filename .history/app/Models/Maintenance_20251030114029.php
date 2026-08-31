<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\MaintenanceStatus;
use Carbon\Carbon;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'vehicule_id',
        'user_id',
        'nature_intervention',
        'kilometrage_actuel',
        'date_debut',
        'date_fin',
        'maintenance_date',
        'observation_generale',
        'status',
        'validateur_id',
        'validated_at',
        'notes_validation',
        'signature_technicien',
        'signature_superviseur',
        'signature_client',
        'cout_main_oeuvre',
        'cout_pieces',
        'cout_total',
        
        // Nouveaux champs garage
        'garage_nom',
        'garage_lieu',
        'garage_contact',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'validated_at' => 'datetime',
        'kilometrage_actuel' => 'decimal:2',
        'cout_main_oeuvre' => 'decimal:2',
        'cout_pieces' => 'decimal:2',
        'cout_total' => 'decimal:2',
        'status' => MaintenanceStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            if (!$maintenance->reference) {
                $maintenance->reference = self::generateReference();
            }
            // ⭐ AJOUT: Auto-remplir maintenance_date avec date_debut
            if (!$maintenance->maintenance_date && $maintenance->date_debut) {
                $maintenance->maintenance_date = $maintenance->date_debut;
            }
            
            if (!isset($maintenance->cout_pieces)) {
                $maintenance->cout_pieces = 0;
            }
        });
            
            // Initialiser cout_pieces si non défini
            if (!isset($maintenance->cout_pieces)) {
                $maintenance->cout_pieces = 0;
            }
        });

        static::saving(function ($maintenance) {
            // S'assurer que cout_pieces existe avant de calculer
            if (!isset($maintenance->cout_pieces)) {
                $maintenance->cout_pieces = 0;
            }
            
            // Calculer le coût total automatiquement
            $maintenance->cout_total = ($maintenance->cout_main_oeuvre ?? 0) + ($maintenance->cout_pieces ?? 0);
        });
    }

    public static function generateReference(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastMaintenance = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();

        $number = $lastMaintenance ? (int) substr($lastMaintenance->reference, -4) + 1 : 1;

        return sprintf('MNT-%s%s-%04d', $year, $month, $number);
    }

    // Relations
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function pieces()
    {
        return $this->hasMany(MaintenancePiece::class);
    }

    public function recus()
    {
        return $this->morphMany(Recu::class, 'recuable');
    }

    // Méthodes utiles
    public function isEnAttente(): bool
    {
        return $this->status === MaintenanceStatus::EN_ATTENTE;
    }

    public function isEnCours(): bool
    {
        return $this->status === MaintenanceStatus::EN_COURS;
    }

    public function isValidee(): bool
    {
        return $this->status === MaintenanceStatus::VALIDEE;
    }

    public function getDurationInDays(): ?int
    {
        if (!$this->date_fin) {
            return null;
        }
        return $this->date_debut->diffInDays($this->date_fin);
    }

    public function updateCoutPieces()
    {
        $totalPieces = $this->pieces()->sum('prix_total');
        $this->cout_pieces = $totalPieces;
        
        // Éviter de re-déclencher le saving event
        $this->saveQuietly();
    }

    public function getPiecesProchesLimite()
    {
        return $this->pieces()->where('alerte_proche_limite', true)->get();
    }

    public static function getLastKilometrage($vehiculeId)
    {
        return self::where('vehicule_id', $vehiculeId)
            ->orderBy('date_debut', 'desc')
            ->value('kilometrage_actuel') ?? Vehicule::find($vehiculeId)->mileage ?? 0;
    }

    // Méthode pour obtenir les informations du garage formatées
    public function getGarageInfoAttribute(): string
    {
        return "{$this->garage_nom} - {$this->garage_lieu} ({$this->garage_contact})";
    }
}