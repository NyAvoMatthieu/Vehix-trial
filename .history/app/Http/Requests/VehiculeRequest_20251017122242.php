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
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => [
                'required',
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
        ];
    }

    public function messages(): array
    {
        return [
            'make.required' => 'La marque est obligatoire.',
            'model.required' => 'Le modèle est obligatoire.',
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
        ];
    }
}
