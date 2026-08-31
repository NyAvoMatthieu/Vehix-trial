<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProprietaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'in:personnel,entreprise'],

            // Coordonnées communes
            'adresse_complete' => ['nullable', 'string', 'max:500'],
            'commune' => ['nullable', 'string', 'max:100'],
            'fokontany' => ['nullable', 'string', 'max:100'],
            'telephone_mobile' => ['nullable', 'string', 'max:20'],
            'telephone_fixe' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];

        // Règles spécifiques au type personnel
        if ($this->type === 'personnel') {
            $rules = array_merge($rules, [
                'nom' => ['required', 'string', 'max:100'],
                'prenom' => ['required', 'string', 'max:100'],
                'date_naissance' => ['required', 'date', 'before:today'],
                'lieu_naissance' => ['required', 'string', 'max:100'],
                'sexe' => ['required', 'in:masculin,feminin'],
                'nationalite' => ['required', 'string', 'max:100'],
                'numero_piece_identite' => ['required', 'string', 'max:50'],
                'date_delivrance_piece' => ['required', 'date', 'before_or_equal:today'],
                'situation_familiale' => ['nullable', 'string', 'max:50'],
                'profession' => ['nullable', 'string', 'max:100'],

                // Permis de conduire
                'numero_permis' => ['nullable', 'string', 'max:50'],
                'categorie_permis' => ['nullable', 'string', 'max:50'],
                'date_delivrance_permis' => ['nullable', 'date', 'before_or_equal:today'],
            ]);
        }

        // Règles spécifiques au type entreprise
        if ($this->type === 'entreprise') {
            $rules = array_merge($rules, [
                'raison_sociale' => ['required', 'string', 'max:255'],
                'nom_commercial' => ['nullable', 'string', 'max:255'],
                'forme_juridique' => ['required', 'string', 'max:100'],
                'nif' => ['required', 'string', 'max:50', Rule::unique('proprietaires')->ignore($this->proprietaire)],
                'statistique' => ['nullable', 'string', 'max:50'],
                'rcs' => ['nullable', 'string', 'max:50'],
                'date_creation' => ['required', 'date', 'before_or_equal:today'],
                'secteur_activite' => ['required', 'string', 'max:100'],
                'site_web' => ['nullable', 'url', 'max:255'],

                // Représentant légal
                'representant_nom' => ['required', 'string', 'max:100'],
                'representant_prenom' => ['required', 'string', 'max:100'],
                'representant_fonction' => ['required', 'string', 'max:100'],
                'representant_telephone' => ['nullable', 'string', 'max:20'],
                'representant_email' => ['nullable', 'email', 'max:255'],
                'representant_numero_piece' => ['required', 'string', 'max:50'],
                'representant_date_delivrance' => ['required', 'date', 'before_or_equal:today'],
                'representant_lieu_delivrance' => ['required', 'string', 'max:100'],

                // Contact administratif
                'responsable_flotte' => ['nullable', 'string', 'max:100'],
                'responsable_telephone' => ['nullable', 'string', 'max:20'],
                'responsable_email' => ['nullable', 'email', 'max:255'],

                // Autorisations
                'autorisation_transport' => ['nullable', 'string', 'max:100'],
                'autorisation_date_delivrance' => ['nullable', 'date', 'before_or_equal:today'],
                'autorisation_validite' => ['nullable', 'date', 'after:today'],
                'autorisation_type' => ['nullable', 'string', 'max:100'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de propriétaire est requis.',
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'date_naissance.required' => 'La date de naissance est requise.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'raison_sociale.required' => 'La raison sociale est requise.',
            'nif.required' => 'Le numéro NIF est requis.',
            'nif.unique' => 'Ce numéro NIF est déjà enregistré.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
        ];
    }
}
