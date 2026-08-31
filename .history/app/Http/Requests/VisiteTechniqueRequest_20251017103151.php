<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisiteTechniqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'kilometrage' => 'nullable|integer|min:0',
            
            // Visite
            'date_visite' => 'required|date',
            'validite' => 'nullable|date|after:date_visite',
            'centre' => 'nullable|string|max:255',
            'operateur' => 'nullable|string|max:255',
            'vta' => 'nullable|string|max:255',
            'verificateur' => 'nullable|string|max:255',
            'type_visite' => 'required|in:Initiale,Périodique,Contre-visite',
            'aptitude' => 'required|in:APTE,INAPTE',
            
            // PV
            'numero_pv' => 'nullable|string|max:255',
            'date_pv' => 'nullable|date',
            
            // Reçu
            'numero_recu' => 'nullable|string|max:255',
            
            // Paiement
            'droit' => 'nullable|numeric|min:0',
            'pv_frais' => 'nullable|numeric|min:0',
            'carte_frais' => 'nullable|numeric|min:0',
            'tht' => 'nullable|numeric|min:0',
            'tva' => 'nullable|numeric|min:0',
            
            // Carte violette
            'numero_carte_violette' => 'nullable|string|max:255',
            'date_carte_violette' => 'nullable|date',
            
            // Licence
            'numero_licence' => 'nullable|string|max:255',
            'date_licence' => 'nullable|date',
            
            // Autres
            'patente' => 'nullable|string|max:255',
            'ani' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicule_id.required' => 'Le véhicule est obligatoire.',
            'vehicule_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'date_visite.required' => 'La date de visite est obligatoire.',
            'date_visite.date' => 'La date de visite doit être une date valide.',
            'validite.after' => 'La date de validité doit être après la date de visite.',
            'type_visite.required' => 'Le type de visite est obligatoire.',
            'aptitude.required' => 'L\'aptitude est obligatoire.',
        ];
    }
}