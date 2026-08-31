<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    vehicule: Object,
    lastOdometer: Number,
    chauffeurs: Array,
    fuelType: String,
});

const form = useForm({
    vehicule_id: props.vehicule.id,
    chauffeur_id: null,
    ravitaillement_date: new Date().toISOString().split('T')[0],
    station_service: '',
    liters_purchased: '',
    price_per_liter: '',
    amount_paid: '',
    odo_station: props.lastOdometer || '',
    odo_arrival: '',
    payment_method: 'cash',
    receipt_number: '',
    is_full_tank: false,
    notes: '',
});

// Computed values
const totalCost = computed(() => {
    if (form.liters_purchased && form.price_per_liter) {
        return (parseFloat(form.liters_purchased) * parseFloat(form.price_per_liter)).toFixed(2);
    }
    return '0.00';
});

const totalLiters = computed(() => {
    if (form.amount_paid && form.price_per_liter && parseFloat(form.price_per_liter) > 0) {
        return (parseFloat(form.amount_paid) / parseFloat(form.price_per_liter)).toFixed(2);
    }
    return '0.00';
});

const distanceTraveled = computed(() => {
    if (form.odo_station && form.odo_arrival) {
        return (parseFloat(form.odo_arrival) - parseFloat(form.odo_station)).toFixed(2);
    }
    return '0.00';
});

const consumptionRate = computed(() => {
    if (distanceTraveled.value > 0 && form.liters_purchased) {
        return ((parseFloat(form.liters_purchased) / parseFloat(distanceTraveled.value)) * 100).toFixed(2);
    }
    return '0.00';
});

// Watch for full tank changes
watch(() => form.is_full_tank, (newValue) => {
    if (!newValue) {
        // Reset liters when not full tank
        form.liters_purchased = '';
    }
});

const submit = () => {
    form.post(route('ravitaillements.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Nouveau Ravitaillement">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nouveau Ravitaillement
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Vehicle Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Véhicule sélectionné</h3>
                        <p class="text-gray-700">
                            <span class="font-medium">{{ vehicule.make }} {{ vehicule.model }}</span>
                            <span class="text-gray-500 ml-2">{{ vehicule.license_plate }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Type de carburant: <span class="font-medium">{{ fuelType }}</span>
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Driver Selection -->
                        <div>
                            <InputLabel for="chauffeur_id" value="Conducteur" />
                            <select
                                id="chauffeur_id"
                                v-model="form.chauffeur_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option :value="null">Moi-même ({{ $page.props.auth.user.name }})</option>
                                <option v-for="chauffeur in chauffeurs" :key="chauffeur.id" :value="chauffeur.id">
                                    {{ chauffeur.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.chauffeur_id" class="mt-2" />
                        </div>

                        <!-- Date and Station -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="ravitaillement_date" value="Date de ravitaillement *" />
                                <TextInput
                                    id="ravitaillement_date"
                                    v-model="form.ravitaillement_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                    :max="new Date().toISOString().split('T')[0]"
                                />
                                <InputError :message="form.errors.ravitaillement_date" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="station_service" value="Station-service *" />
                                <TextInput
                                    id="station_service"
                                    v-model="form.station_service"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="Ex: Total, Shell, Jirama..."
                                />
                                <InputError :message="form.errors.station_service" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fuel Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="liters_purchased" value="Litres achetés *" />
                                <TextInput
                                    id="liters_purchased"
                                    v-model="form.liters_purchased"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.liters_purchased" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="price_per_liter" value="Prix par litre (Ar) *" />
                                <TextInput
                                    id="price_per_liter"
                                    v-model="form.price_per_liter"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.price_per_liter" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="amount_paid" value="Montant payé (Ar) *" />
                                <TextInput
                                    id="amount_paid"
                                    v-model="form.amount_paid"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.amount_paid" class="mt-2" />
                            </div>
                        </div>

                        <!-- Calculated Values -->
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold mb-2">Calculs automatiques</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Coût total:</span>
                                    <span class="ml-2 font-medium">{{ totalCost }} Ar</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total litres:</span>
                                    <span class="ml-2 font-medium">{{ totalLiters }} L</span>
                                </div>
                            </div>
                        </div>

                        <!-- Odometer Readings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="odo_station" value="Kilométrage à la station" />
                                <TextInput
                                    id="odo_station"
                                    v-model="form.odo_station"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    placeholder="Auto-rempli si vide"
                                />
                                <InputError :message="form.errors.odo_station" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Dernier relevé: {{ lastOdometer }} km
                                </p>
                            </div>

                            <div>
                                <InputLabel for="odo_arrival" value="Kilométrage d'arrivée" />
                                <TextInput
                                    id="odo_arrival"
                                    v-model="form.odo_arrival"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.odo_arrival" class="mt-2" />
                            </div>
                        </div>

                        <!-- Distance and Consumption -->
                        <div v-if="form.odo_station && form.odo_arrival" class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-semibold mb-2">Statistiques</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Distance parcourue:</span>
                                    <span class="ml-2 font-medium">{{ distanceTraveled }} km</span>
                                </div>
                                <div v-if="consumptionRate > 0">
                                    <span class="text-gray-600">Consommation:</span>
                                    <span class="ml-2 font-medium">{{ consumptionRate }} L/100km</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <InputLabel for="payment_method" value="Mode de paiement *" />
                            <select
                                id="payment_method"
                                v-model="form.payment_method"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="cash">Espèces</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="virement">Virement bancaire</option>
                                <option value="mobile">Mobile Money</option>
                            </select>
                            <InputError :message="form.errors.payment_method" class="mt-2" />
                        </div>

                        <!-- Receipt Number -->
                        <div>
                            <InputLabel for="receipt_number" value="Numéro de reçu (optionnel)" />
                            <TextInput
                                id="receipt_number"
                                v-model="form.receipt_number"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ex: REC-2024-001"
                            />
                            <InputError :message="form.errors.receipt_number" class="mt-2" />
                        </div>

                        <!-- Full Tank -->
                        <div class="flex items-center">
                            <input
                                id="is_full_tank"
                                v-model="form.is_full_tank"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <label for="is_full_tank" class="ml-2 text-sm text-gray-700">
                                Plein complet
                            </label>
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" value="Remarques" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Notes additionnelles..."
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4">
                            <a
                                :href="route('ravitaillements.index')"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Annuler
                            </a>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enregistrer
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>