<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRavitaillementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'ravitaillement_date' => 'required|date|before_or_equal:today',
            'station_name' => 'required|string|max:255',
            'liters' => 'required|numeric|min:0.01|max:1000',
            'price_per_liter' => 'required|numeric|min:0.01|max:10000',
            'odo_station' => 'nullable|integer|min:0|max:999999999',
            'odo_arrival' => 'nullable|integer|min:0|max:999999999|gte:odo_station',
            'fuel_type' => 'required|string|in:essence,diesel,gpl,electrique,hybride',
            'payment_method' => 'required|string|in:cash,card,mobile,check,voucher',
            'receipt_number' => 'nullable|string|max:100',
            'full_tank' => 'boolean',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'ravitaillement_date.before_or_equal' => 'La date de ravitaillement ne peut pas être dans le futur.',
            'liters.min' => 'La quantité de carburant doit être au minimum de 0.01 litre.',
            'liters.max' => 'La quantité de carburant ne peut pas dépasser 1000 litres.',
            'price_per_liter.min' => 'Le prix par litre doit être au minimum de 0.01 Ar.',
            'odo_arrival.gte' => 'Le kilométrage d\'arrivée doit être supérieur ou égal au kilométrage à la station.',
            'fuel_type.in' => 'Le type de carburant sélectionné n\'est pas valide.',
            'payment_method.in' => 'Le mode de paiement sélectionné n\'est pas valide.',
        ];
    }
}