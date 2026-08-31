<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PencilIcon, TrashIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    ravitaillement: Object,
});

const distance = computed(() => {
    if (props.ravitaillement.odo_arrival && props.ravitaillement.odo_station) {
        return props.ravitaillement.odo_arrival - props.ravitaillement.odo_station;
    }
    return null;
});

const consumption = computed(() => {
    if (distance.value && distance.value > 0) {
        return ((props.ravitaillement.liters / distance.value) * 100).toFixed(2);
    }
    return null;
});

const deleteRavitaillement = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce ravitaillement ?')) {
        router.delete(route('ravitaillements.destroy', props.ravitaillement.id));
    }
};

const getFuelTypeLabel = (type) => {
    const labels = {
        essence: 'Essence',
        diesel: 'Diesel',
        gpl: 'GPL',
        electrique: 'Électrique',
        hybride: 'Hybride',
    };
    return labels[type] || type;
};

const getPaymentMethodLabel = (method) => {
    const labels = {
        especes: 'Espèces',
        carte: 'Carte bancaire',
        mobile: 'Mobile Money',
        bon: 'Bon carburant',
    };
    return labels[method] || method;
};
</script>

<template>
    <AppLayout :title="`Ravitaillement - ${new Date(ravitaillement.ravitaillement_date).toLocaleDateString()}`">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a
                        :href="route('ravitaillements.index')"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        <ArrowLeftIcon class="h-6 w-6" />
                    </a>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Détails du Ravitaillement
                    </h2>
                </div>
                <div class="flex space-x-3">
                    <a
                        :href="route('ravitaillements.edit', ravitaillement.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        <PencilIcon class="h-4 w-4 mr-2" />
                        Modifier
                    </a>
                    <button
                        @click="deleteRavitaillement"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                    >
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Supprimer
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informations principales -->
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ new Date(ravitaillement.ravitaillement_date).toLocaleDateString('fr-FR', { 
                                    weekday: 'long', 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                }) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Véhicule</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ ravitaillement.vehicule.make }} {{ ravitaillement.vehicule.model }}
                                <span class="text-sm text-gray-600">
                                    ({{ ravitaillement.vehicule.license_plate }})
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Station-service</label>
                            <p class="mt-1 text-lg text-gray-900">{{ ravitaillement.station_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Utilisateur</label>
                            <p class="mt-1 text-lg text-gray-900">{{ ravitaillement.user.name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Détails carburant -->
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Détails du carburant</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type de carburant</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ getFuelTypeLabel(ravitaillement.fuel_type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Quantité</label>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ ravitaillement.liters }} L</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Plein complet</label>
                            <p class="mt-1">
                                <span v-if="ravitaillement.is_full_tank" class="text-green-600 font-semibold">
                                    ✓ Oui
                                </span>
                                <span v-else class="text-gray-600">
                                    ✗ Non
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Prix par litre</label>
                            <p class="mt-1 text-lg text-gray-900">{{ ravitaillement.price_per_liter }} Ar</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Coût total</label>
                            <p class="mt-1 text-2xl font-bold text-green-600">{{ ravitaillement.total_cost }} Ar</p>
                        </div>
                        <div v-if="ravitaillement.fuel_left">
                            <label class="text-sm font-medium text-gray-500">Carburant restant</label>
                            <p class="mt-1 text-lg text-gray-900">{{ ravitaillement.fuel_left }} L</p>
                        </div>
                    </div>
                </div>

                <!-- Kilométrage et consommation -->
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Kilométrage et consommation</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Km à la station</label>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ ravitaillement.odo_station }} km</p>
                        </div>
                        <div v-if="ravitaillement.odo_arrival">
                            <label class="text-sm font-medium text-gray-500">Km d'arrivée</label>
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ ravitaillement.odo_arrival }} km</p>
                        </div>
                        <div v-if="distance">
                            <label class="text-sm font-medium text-gray-500">Distance parcourue</label>
                            <p class="mt-1 text-xl font-semibold text-blue-600">{{ distance }} km</p>
                        </div>
                        <div v-if="consumption">
                            <label class="text-sm font-medium text-gray-500">Consommation</label>
                            <p class="mt-1 text-xl font-semibold text-orange-600">{{ consumption }} L/100km</p>
                        </div>
                        <div v-if="ravitaillement.average_consumption">
                            <label class="text-sm font-medium text-gray-500">Conso. moyenne estimée</label>
                            <p class="mt-1 text-lg text-gray-900">{{ ravitaillement.average_consumption }} L/100km</p>
                        </div>
                    </div>
                </div>

                <!-- Paiement -->
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informations de paiement</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mode de paiement</label>
                            <p class="mt-1 text-lg text-gray-900">{{ getPaymentMethodLabel(ravitaillement.payment_method) }}</p>
                        </div>
                        <div v-if="ravitaillement.receipt_number">
                            <label class="text-sm font-medium text-gray-500">Numéro de reçu</label>
                            <p class="mt-1 text-lg font-mono text-gray-900">{{ ravitaillement.receipt_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Remarques -->
                <div v-if="ravitaillement.notes" class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Remarques</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ ravitaillement.notes }}</p>
                    </div>
                </div>

                <!-- Reçus associés -->
                <div v-if="ravitaillement.recus && ravitaillement.recus.length > 0" class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Reçus associés</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="recu in ravitaillement.recus"
                                :key="recu.id"
                                class="border rounded-lg p-4 hover:shadow-md transition"
                            >
                                <p class="text-sm text-gray-600">{{ recu.filename }}</p>
                                <a :href="recu.url" target="_blank" class="text-blue-600 hover:underline text-sm">
                                    Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>