<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssuranceRequest extends FormRequest
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
       return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'company' => 'nullable|string|max:255',
            'assureur' => 'required|string|max:255',
            'agence' => 'nullable|string|max:255',
            'policy_number' => [
                'required',
                'string',
                'max:255',
                $assuranceId 
                    ? 'unique:assurances,policy_number,' . $assuranceId 
                    : 'unique:assurances,policy_number'
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'date_delivrance' => 'nullable|date|before_or_equal:start_date',
            'prime_cp' => 'required|numeric|min:0',
            'prime_de' => 'nullable|numeric|min:0',
            'prime_ca' => 'nullable|numeric|min:0',
            'prime_div' => 'nullable|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'coverage_details' => 'nullable|string|max:5000',
            'lieu_signature' => 'nullable|string|max:255',
            'date_signature' => 'nullable|date|before_or_equal:today',
            'agent_nom' => 'nullable|string|max:255',
            'notes_signature' => 'nullable|string|max:1000',
        ];
    }

     public function messages(): array
    {
        return [
            'vehicule_id.required' => 'Veuillez sélectionner un véhicule.',
            'vehicule_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'assureur.required' => 'Le nom de l\'assureur est obligatoire.',
            'policy_number.required' => 'Le numéro de police est obligatoire.',
            'policy_number.unique' => 'Ce numéro de police existe déjà.',
            'start_date.required' => 'La date de début est obligatoire.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'date_delivrance.before_or_equal' => 'La date de délivrance doit être avant ou égale à la date de début.',
            'prime_cp.required' => 'La prime CP est obligatoire.',
            'prime_cp.min' => 'La prime CP doit être positive.',
            'date_signature.before_or_equal' => 'La date de signature ne peut pas être dans le futur.',
        ];
    }
}
