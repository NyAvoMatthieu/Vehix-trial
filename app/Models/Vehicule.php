<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VehiculeStatus;

class Vehicule extends Model
{
    /** @use HasFactory<\Database\Factories\VehiculeFactory> */
    use HasFactory;

     protected $fillable = [
        'user_id',
        'proprietaire_id',
        'make',
        'model',
        'alias',
        'vehicule_type',
        'year',
        'license_plate',
        'vin',
        'color',
        'fuel_type',
        'mileage',
        'status',
        'validation_notes',
        'average_consumption',
        'validated_by',
        'validated_at',

        'categorie',
        'numero_serie_type',
        'carrosserie',
        'numero_moteur',
        'cylindree',
        'puissance_administrative',
        'places_assises',
        'poids_total_charge',
        'poids_vide',
        'charge_utile',
    ];

    protected $casts = [
        'status' => VehiculeStatus::class,
        'validated_at' => 'datetime',
        'year' => 'date:Y-m-d',
        'mileage' => 'integer',
        'average_consumption' => 'decimal:2',
        'cylindree' => 'integer',
        'puissance_administrative' => 'integer',
        'places_assises' => 'integer',
        'poids_total_charge' => 'decimal:2',
        'poids_vide' => 'decimal:2',
        'charge_utile' => 'decimal:2',
    ];

        // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function ravitaillements()
    {
        return $this->hasMany(Ravitaillement::class);
    }

    public function trajets()
    {
        return $this->hasMany(Trajet::class);
    }
    public function visiteTechniques()
{
    return $this->hasMany(VisiteTechnique::class);
}

    // Méthodes utilitaires
    public function isPending(): bool
    {
        return $this->status === VehiculeStatus::PENDING;
    }

    public function isValidated(): bool
    {
        return $this->status === VehiculeStatus::VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === VehiculeStatus::REJECTED;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    // Accesseurs pour les nouveaux champs
    public function getColorAttribute($value): ?string
    {
        return $value;
    }

    public function getFuelTypeAttribute($value): ?string
    {
        return $value;
    }

    public function getMileageAttribute($value): int
    {
        return (int) $value;
    }

    public function getVehicleTypeAttribute($value): ?string
    {
        return $value;
    }

    // Méthodes utilitaires pour le type de véhicule
    public function isCar(): bool
    {
        return $this->vehicle_type === 'voiture';
    }

    public function isMotorcycle(): bool
    {
        return $this->vehicle_type === 'moto';
    }

    public function isTruck(): bool
    {
        return $this->vehicle_type === 'camion';
    }

    public function isBus(): bool
    {
        return $this->vehicle_type === 'bus';
    }

    public function isVan(): bool
    {
        return $this->vehicle_type === 'utilitaire';
    }

    public function isScooter(): bool
    {
        return $this->vehicle_type === 'scooter';
    }

    public function isLightTruck(): bool
    {
        return $this->vehicle_type === 'camionnette';
    }

    // Méthodes utilitaires pour le type de carburant
    public function isGasoline(): bool
    {
        return $this->fuel_type === 'essence';
    }

    public function isDiesel(): bool
    {
        return $this->fuel_type === 'diesel';
    }

    public function isHybrid(): bool
    {
        return $this->fuel_type === 'hybride';
    }

    public function isElectric(): bool
    {
        return $this->fuel_type === 'electrique';
    }

    public function isGpl(): bool
    {
        return $this->fuel_type === 'gpl';
    }
    // Méthode utilitaire pour vérifier la validité de la visite technique
    public function hasValidVisiteTechnique(): bool
    {
        return $this->visiteTechniques()
            ->where('aptitude', 'APTE')
            ->where('validite', '>=', now())
            ->exists();
    }

    public function getLatestVisiteTechnique()
    {
        return $this->visiteTechniques()
            ->orderBy('date_visite', 'desc')
            ->first();
    }

    // Méthode pour calculer automatiquement la charge utile
    public function calculateChargeUtile(): ?float
    {
        if ($this->poids_total_charge && $this->poids_vide) {
            return $this->poids_total_charge - $this->poids_vide;
        }
        return null;
    }

    /**
     * Calculer et mettre à jour la consommation moyenne basée sur les ravitaillements
     */
    public function updateAverageConsumption(): void
    {
        $ravitaillements = $this->ravitaillements()
            ->whereNotNull('odo_station')
            ->orderBy('ravitaillement_date', 'asc')
            ->get();

        if ($ravitaillements->count() < 2) {
            // Pas assez de données pour calculer
            return;
        }

        $totalLiters = 0;
        $totalDistance = 0;

        for ($i = 1; $i < $ravitaillements->count(); $i++) {
            $previous = $ravitaillements[$i - 1];
            $current = $ravitaillements[$i];

            $distance = $current->odo_station - $previous->odo_station;

            // Ignorer les valeurs aberrantes
            if ($distance > 0 && $distance < 2000) { // Distance max entre 2 ravitaillements : 2000 km
                $totalDistance += $distance;
                $totalLiters += $current->liters_purchased;
            }
        }

        if ($totalDistance > 0) {
            // Calcul : (litres / distance) * 100
            $averageConsumption = ($totalLiters / $totalDistance) * 100;
            
            $this->update([
                'average_consumption' => round($averageConsumption, 2)
            ]);
        }
    }

    // Ajoutez également cette méthode helper
    public function getConsumptionRate(): ?float
    {
        return $this->average_consumption ? (float) $this->average_consumption : null;
    }
}
