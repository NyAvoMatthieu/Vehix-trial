<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    ravitaillement: Object,
    vehicule: Object,
    lastOdometer: Number,
    fuelType: String,
    currentFuelPrice: Object,
});

const form = useForm({
    chauffeur_name: props.ravitaillement.chauffeur_name || '',
    ravitaillement_date: props.ravitaillement.ravitaillement_date,
    station_service: props.ravitaillement.station_service,
    liters_purchased: props.ravitaillement.liters_purchased,
    price_per_liter: props.ravitaillement.price_per_liter,
    amount_paid: props.ravitaillement.amount_paid,
    odo_station: props.ravitaillement.odo_station,
    payment_method: props.ravitaillement.payment_method,
    receipt_number: props.ravitaillement.receipt_number,
    notes: props.ravitaillement.notes,
    is_custom_price: props.ravitaillement.is_custom_price || false,
});

const officialPrice = ref(props.currentFuelPrice?.price || null);
const isPriceModified = ref(props.ravitaillement.is_custom_price || false);

// Watch for changes in liters_purchased to auto-calculate amount_paid
watch(() => form.liters_purchased, (newVal) => {
    if (newVal && form.price_per_liter && parseFloat(newVal) > 0) {
        const calculated = (parseFloat(newVal) * parseFloat(form.price_per_liter)).toFixed(2);
        form.amount_paid = calculated;
    }
});

// Watch for changes in amount_paid to auto-calculate liters_purchased
watch(() => form.amount_paid, (newVal) => {
    if (newVal && form.price_per_liter && parseFloat(form.price_per_liter) > 0) {
        const calculated = (parseFloat(newVal) / parseFloat(form.price_per_liter)).toFixed(2);
        form.liters_purchased = calculated;
    }
});

// Watch for changes in price_per_liter to recalculate and detect custom price
watch(() => form.price_per_liter, (newVal) => {
    if (newVal && parseFloat(newVal) > 0) {
        // Check if price was modified from official price
        if (officialPrice.value && parseFloat(newVal) !== parseFloat(officialPrice.value)) {
            isPriceModified.value = true;
            form.is_custom_price = true;
        } else {
            isPriceModified.value = false;
            form.is_custom_price = false;
        }

        // Recalculate based on what field has value
        if (form.liters_purchased) {
            form.amount_paid = (parseFloat(form.liters_purchased) * parseFloat(newVal)).toFixed(2);
        } else if (form.amount_paid) {
            form.liters_purchased = (parseFloat(form.amount_paid) / parseFloat(newVal)).toFixed(2);
        }
    }
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

const consumptionRate = computed(() => {
    if (props.vehicule.average_consumption) {
        return parseFloat(props.vehicule.average_consumption).toFixed(2);
    }
    return null;
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const resetToOfficialPrice = () => {
    if (officialPrice.value) {
        form.price_per_liter = officialPrice.value;
        isPriceModified.value = false;
        form.is_custom_price = false;
    }
};

const submit = () => {
    form.put(route('ravitaillements.update', props.ravitaillement.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Modifier Ravitaillement">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier Ravitaillement
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Vehicle Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Véhicule</h3>
                        <p class="text-gray-700">
                            <span class="font-medium">{{ vehicule.make }} {{ vehicule.model }}</span>
                            <span class="text-gray-500 ml-2">{{ vehicule.license_plate }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Type de carburant: <span class="font-medium">{{ fuelType }}</span>
                        </p>
                    </div>

                    <!-- Current Fuel Price Info -->
                    <div v-if="currentFuelPrice" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-green-800">
                                    Prix actuel du carburant
                                </h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p class="font-semibold text-lg">
                                        {{ parseFloat(currentFuelPrice.price).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }} Ar/L
                                    </p>
                                    <p class="text-xs mt-1">
                                        Effectif depuis le {{ formatDate(currentFuelPrice.effective_date) }}
                                    </p>
                                    <p v-if="currentFuelPrice.notes" class="text-xs mt-1 italic">
                                        {{ currentFuelPrice.notes }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Driver Name -->
                        <div>
                            <InputLabel for="chauffeur_name" value="Nom du conducteur" />
                            <TextInput
                                id="chauffeur_name"
                                v-model="form.chauffeur_name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Nom du conducteur (optionnel)"
                            />
                            <InputError :message="form.errors.chauffeur_name" class="mt-2" />
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
                                />
                                <InputError :message="form.errors.station_service" class="mt-2" />
                            </div>
                        </div>

                        <!-- Fuel Details with Auto-calculation -->
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
                                    placeholder="Ex: 45.5"
                                />
                                <InputError :message="form.errors.liters_purchased" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Calculé automatiquement si vous saisissez le montant
                                </p>
                            </div>

                            <div>
                                <InputLabel for="price_per_liter" value="Prix par litre (Ar) *" />
                                <div class="relative">
                                    <TextInput
                                        id="price_per_liter"
                                        v-model="form.price_per_liter"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        class="mt-1 block w-full"
                                        :class="{ 'border-orange-300': isPriceModified }"
                                        required
                                        placeholder="Prix par litre"
                                    />
                                    <div v-if="currentFuelPrice && !isPriceModified" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <InputError :message="form.errors.price_per_liter" class="mt-2" />
                                <p v-if="currentFuelPrice && !isPriceModified" class="mt-1 text-xs text-green-600">
                                    🔒 Prix officiel appliqué
                                </p>
                                <div v-if="isPriceModified" class="mt-1">
                                    <p class="text-xs text-orange-600 mb-1">
                                        ⚠️ Prix personnalisé (différent du prix officiel)
                                    </p>
                                    <button
                                        type="button"
                                        @click="resetToOfficialPrice"
                                        class="text-xs text-blue-600 hover:text-blue-800 underline"
                                    >
                                        Revenir au prix officiel
                                    </button>
                                </div>
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
                                    placeholder="Ex: 227500"
                                />
                                <InputError :message="form.errors.amount_paid" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Calculé automatiquement si vous saisissez les litres
                                </p>
                            </div>
                        </div>

                        <!-- Calculated Values Display -->
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                            <h4 class="font-semibold mb-3 text-indigo-900">📊 Calculs automatiques</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <span class="text-gray-600 text-sm">Coût total:</span>
                                    <div class="text-2xl font-bold text-indigo-600 mt-1">
                                        {{ parseFloat(totalCost).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }} Ar
                                    </div>
                                </div>
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <span class="text-gray-600 text-sm">Total litres:</span>
                                    <div class="text-2xl font-bold text-green-600 mt-1">
                                        {{ parseFloat(totalLiters).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }} L
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Odometer Reading -->
                        <div>
                            <InputLabel for="odo_station" value="Kilométrage à la station" />
                            <TextInput
                                id="odo_station"
                                v-model="form.odo_station"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.odo_station" class="mt-2" />
                        </div>

                        <!-- Consumption Info -->
                        <div v-if="consumptionRate" class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-semibold mb-2">Consommation du véhicule</h4>
                            <div class="text-sm">
                                <span class="text-gray-600">Consommation moyenne:</span>
                                <span class="ml-2 font-medium">{{ consumptionRate }} L/100km</span>
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
                            />
                            <InputError :message="form.errors.receipt_number" class="mt-2" />
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
                                Mettre à jour
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
