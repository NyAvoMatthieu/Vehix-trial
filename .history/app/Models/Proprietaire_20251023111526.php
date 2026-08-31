<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietaire extends Model
{
    /** @use HasFactory<\Database\Factories\ProprietaireFactory> */
    use HasFactory;

        protected $fillable = [
        'user_id',
        'type',
        // Personnel
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'nationalite',
        'numero_piece_identite',
        'date_delivrance_piece',
        'situation_familiale',
        // Entreprise
        'raison_sociale',
        'nom_commercial',
        'forme_juridique',
        'nif',
        'statistique',
        'rcs',
        'date_creation',
        'secteur_activite',
        // Coordonnées
        'adresse_complete',
        'commune',
        'fokontany',
        'profession',
        'telephone_mobile',
        'telephone_fixe',
        'email',
        'site_web',
        // Représentant légal
        'representant_nom',
        'representant_prenom',
        'representant_fonction',
        'representant_telephone',
        'representant_email',
        'representant_numero_piece',
        'representant_date_delivrance',
        'representant_lieu_delivrance',
        // Contact administratif
        'responsable_flotte',
        'responsable_telephone',
        'responsable_email',
        // Permis
        'numero_permis',
        'categorie_permis',
        'date_delivrance_permis',
        // Autorisations
        'autorisation_transport',
        'autorisation_date_delivrance',
        'autorisation_validite',
        'autorisation_type',
        // Observations
        'observations',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_delivrance_piece' => 'date',
        'date_creation' => 'date',
        'representant_date_delivrance' => 'date',
        'date_delivrance_permis' => 'date',
        'autorisation_date_delivrance' => 'date',
        'autorisation_validite' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isLicenseExpiring(int $days = 30): bool
    {
        return $this->driver_license_expiry->lte(now()->addDays($days));
    }
}
