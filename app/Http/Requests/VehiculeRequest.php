<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehiculeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       $vehiculeId = $this->route('vehicule') ? $this->route('vehicule')->id : null;

        return [
            'proprietaire_id' => ['required', 'exists:proprietaires,id'],
            'make' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'alias' => ['nullable', 'string', 'max:100'],
            'year' => [
                'nullable',
                'date',
                'after:1899-12-31', // Équivalent à min:1900 pour une date
                'before_or_equal:' . (date('Y') + 1) . '-12-31', // Équivalent à max: année courante + 1
                // 'date_format:Y-m-d'
            ],
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehicules', 'license_plate')->ignore($vehiculeId),
            ],
            'vin' => [
                'nullable',
                'string',
                'size:17',
                'regex:/^[A-HJ-NPR-Z0-9]{17}$/',
                Rule::unique('vehicules', 'vin')->ignore($vehiculeId),
            ],
            'color' => ['nullable', 'string', 'max:50'],
            'fuel_type' => ['nullable', 'string', 'in:essence,diesel,hybride,electrique,gpl'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'average_consumption' => 'nullable|numeric|min:0|max:99.99',
            // Nouveaux champs
            'categorie' => ['nullable', 'string', 'max:50'],
            'numero_serie_type' => ['nullable', 'string', 'max:100'],
            'carrosserie' => ['nullable', 'string', 'max:50'],
            'numero_moteur' => ['nullable', 'string', 'max:100'],
            'cylindree' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'puissance_administrative' => ['nullable', 'integer', 'min:0', 'max:999'],
            'places_assises' => ['nullable', 'integer', 'min:1', 'max:99'],
            'poids_total_charge' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'poids_vide' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'charge_utile' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'proprietaire_id.required' => 'Vous devez sélectionner un propriétaire.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'existe pas.',
            'make.required' => 'La marque est obligatoire.',
            'model.required' => 'Le modèle est obligatoire.',
            'alias.max' => 'Le surnom ne peut pas dépasser 100 caractères.',
            'year.required' => 'Date de première mise en circulation est obligatoire.',
            'year.date' => 'Le format de la date de mise en circulation est invalide.',
            'year.after' => 'La date de première mise en circulation doit être postérieure à 1899-12-31.', // Message clair pour l'année minimun
            'year.before_or_equal' => 'La date de première mise en circulation ne peut pas être dans le futur (max '. (date('Y') + 1) . ').', // Message clair pour l'année max
            'license_plate.required' => 'La plaque d\'immatriculation est obligatoire.',
            'license_plate.unique' => 'Cette plaque d\'immatriculation existe déjà.',
            'vin.required' => 'Le numéro VIN est obligatoire.',
            'vin.size' => 'Le numéro VIN doit contenir exactement 17 caractères.',
            'vin.regex' => 'Le format du numéro VIN est invalide.',
            'vin.unique' => 'Ce numéro VIN existe déjà.',

            'cylindree.integer' => 'La cylindrée doit être un nombre entier.',
            'cylindree.min' => 'La cylindrée ne peut pas être négative.',
            'cylindree.max' => 'La cylindrée ne peut pas dépasser 99999 cm³.',
            'puissance_administrative.integer' => 'La puissance administrative doit être un nombre entier.',
            'puissance_administrative.min' => 'La puissance administrative ne peut pas être négative.',
            'puissance_administrative.max' => 'La puissance administrative ne peut pas dépasser 999 CV.',
            'places_assises.integer' => 'Le nombre de places doit être un nombre entier.',
            'places_assises.min' => 'Le véhicule doit avoir au moins 1 place.',
            'places_assises.max' => 'Le nombre de places ne peut pas dépasser 99.',
            'poids_total_charge.numeric' => 'Le PTAC doit être un nombre.',
            'poids_vide.numeric' => 'Le poids à vide doit être un nombre.',
            'charge_utile.numeric' => 'La charge utile doit être un nombre.',
            'average_consumption.numeric' => 'La consommation moyenne doit être un nombre.',
            'average_consumption.min' => 'La consommation moyenne ne peut pas être négative.',
            'average_consumption.max' => 'La consommation moyenne ne peut pas dépasser 99.99 L/100km.',
        ];
    }

     /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validation personnalisée: poids_vide ne peut pas être supérieur au poids_total_charge
            if ($this->poids_vide && $this->poids_total_charge) {
                if ($this->poids_vide > $this->poids_total_charge) {
                    $validator->errors()->add('poids_vide', 'Le poids à vide ne peut pas être supérieur au PTAC.');
                }
            }
            // Vérification du propriétaire appartient à l'utilisateur
        if ($this->proprietaire_id) {
            $proprietaire = \App\Models\Proprietaire::find($this->proprietaire_id);
            if ($proprietaire && $proprietaire->user_id !== $this->user()->id) {
                $validator->errors()->add('proprietaire_id', 'Ce propriétaire ne vous appartient pas.');
            }
        }
        });
    }
}
