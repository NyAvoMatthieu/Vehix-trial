<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceInterventionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'intervalle_km',
        'intervalle_jours',
        'actif',
    ];

    protected $casts = [
        'intervalle_km' => 'integer',
        'intervalle_jours' => 'integer',
        'actif' => 'boolean',
    ];

    // Relations
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'intervention_type_id');
    }

    public function suivisVehicules()
    {
        return $this->hasMany(VehiculeMaintenanceIntervalle::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
