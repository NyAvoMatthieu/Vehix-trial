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

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs
    public function getFullNameAttribute(): string
    {
        if ($this->type === 'personnel') {
            return trim("{$this->prenom} {$this->nom}");
        }
        return $this->raison_sociale ?? $this->nom_commercial ?? 'N/A';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'personnel') {
            return $this->full_name;
        }
        return $this->nom_commercial ?: $this->raison_sociale;
    }

    // Méthodes utilitaires
    public function isPersonnel(): bool
    {
        return $this->type === 'personnel';
    }

    public function isEntreprise(): bool
    {
        return $this->type === 'entreprise';
    }

    public function hasValidPermis(): bool
    {
        return $this->isPersonnel() && !empty($this->numero_permis);
    }

    public function hasValidAutorisation(): bool
    {
        if (!$this->isEntreprise() || !$this->autorisation_validite) {
            return false;
        }
        return \Illuminate\Support\Carbon::parse($this->autorisation_validite)->isFuture();
    }

    public function getAge(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }
        return $this->date_naissance->diffInYears(now());
    }

    public function getAncienneteEntreprise(): ?int
    {
        if (!$this->isEntreprise() || !$this->date_creation) {
            return null;
        }
        return $this->date_creation->diffInYears(now());
    }
}
