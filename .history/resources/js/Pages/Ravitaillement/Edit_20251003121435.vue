<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    ravitaillement: Object,
    fuelTypes: Object,
    paymentMethods: Object,
});

const form = useForm({
    ravitaillement_date: props.ravitaillement.ravitaillement_date,
    station_name: props.ravitaillement.station_name,
    liters: props.ravitaillement.liters,
    price_per_liter: props.ravitaillement.price_per_liter,
    odo_station: props.ravitaillement.odo_station,
    odo_arrival: props.ravitaillement.odo_arrival,
    fuel_type: props.ravitaillement.fuel_type,
    payment_method: props.ravitaillement.payment_method,
    receipt_number: props.ravitaillement.receipt_number,
    full_tank: props.ravitaillement.full_tank,
    remarks: props.ravitaillement.remarks,
});

// Calcul automatique du coût total
const totalCost = computed(() => {
    const liters = parseFloat(form.liters) || 0;
    const pricePerLiter = parseFloat(form.price_per_liter) || 0;
    return (liters * pricePerLiter).toFixed(2);
});

const submit = () => {
    form.put(route('ravitaillements.update', props.ravitaillement.id));
};
</script>

<template>
    <AppLayout title="Modifier Ravitaillement">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier le Ravitaillement - {{ ravitaillement.vehicule.brand }} {{ ravitaillement.vehicule.model }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Informations véhicule (non modifiable) -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-lg mb-2">Véhicule</h3>
                            <p class="text-gray-700">
                                {{ ravitaillement.vehicule.brand }} {{ ravitaillement.vehicule.model }} - {{ ravitaillement.vehicule.registration_number }}
                            </p>
                        </div>

                        <!-- Date et Station -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Date de ravitaillement *
                                </label>
                                <input
                                    type="date"
                                    v-model="form.ravitaillement_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <div v-if="form.errors.ravitaillement_date" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.ravitaillement_date }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Station-service *
                                </label>
                                <input
                                    type="text"
                                    v-model="form.station_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <div v-if="form.errors.station_name" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.station_name }}
                                </div>
                            </div>
                        </div>

                        <!-- Quantité et Prix -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Litres ravitaillés *
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    v-model="form.liters"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <div v-if="form.errors.liters" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.liters }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Prix par litre *
                                </label>
                                <input
                                    type="number"
                                    step="0.001"
                                    v-model="form.price_per_liter"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                />
                                <div v-if="form.errors.price_per_liter" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.price_per_liter }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Coût total
                                </label>
                                <div class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700 font-semibold">
                                    {{ totalCost }} Ar
                                </div>
                            </div>
                        </div>

                        <!-- Kilométrage -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Kilométrage à la station (ODO)
                                </label>
                                <input
                                    type="number"
                                    v-model="form.odo_station"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div v-if="form.errors.odo_station" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.odo_station }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Kilométrage d'arrivée
                                </label>
                                <input
                                    type="number"
                                    v-model="form.odo_arrival"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div v-if="form.errors.odo_arrival" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.odo_arrival }}
                                </div>
                            </div>
                        </div>

                        <!-- Type carburant et paiement -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Type de carburant *
                                </label>
                                <select
                                    v-model="form.fuel_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option v-for="(label, value) in fuelTypes" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.fuel_type" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.fuel_type }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Mode de paiement *
                                </label>
                                <select
                                    v-model="form.payment_method"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option v-for="(label, value) in paymentMethods" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.payment_method" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.payment_method }}
                                </div>
                            </div>
                        </div>

                        <!-- Reçu et options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Numéro de reçu (optionnel)
                                </label>
                                <input
                                    type="text"
                                    v-model="form.receipt_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div v-if="form.errors.receipt_number" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.receipt_number }}
                                </div>
                            </div>

                            <div class="flex items-center pt-6">
                                <input
                                    type="checkbox"
                                    id="full_tank"
                                    v-model="form.full_tank"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="full_tank" class="ml-2 block text-sm text-gray-700">
                                    Plein complet
                                </label>
                            </div>
                        </div>

                        <!-- Remarques -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Remarques
                            </label>
                            <textarea
                                v-model="form.remarks"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                            <div v-if="form.errors.remarks" class="text-red-600 text-sm mt-1">
                                {{ form.errors.remarks }}
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end space-x-4">
                            <button
                                type="button"
                                @click="router.visit(route('ravitaillements.index'))"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                <span v-if="form.processing">Mise à jour...</span>
                                <span v-else>Mettre à jour</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>