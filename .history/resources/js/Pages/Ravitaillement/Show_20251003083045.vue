<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    ravitaillement: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const formatNumber = (number, decimals = 2) => {
    return parseFloat(number).toFixed(decimals);
};

const getFuelTypeLabel = (type) => {
    const types = {
        'essence': 'Essence',
        'diesel': 'Diesel',
        'gpl': 'GPL',
        'electrique': 'Électrique',
        'hybride': 'Hybride',
    };
    return types[type] || type;
};

const getPaymentMethodLabel = (method) => {
    const methods = {
        'cash': 'Espèces',
        'card': 'Carte bancaire',
        'mobile': 'Paiement mobile',
        'check': 'Chèque',
        'voucher': 'Bon d\'essence',
    };
    return methods[method] || method;
};
</script>

<template>
    <AppLayout title="Détails du Ravitaillement">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails du Ravitaillement
                </h2>
                <Link
                    :href="route('ravitaillements.index')"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition"
                >
                    ← Retour
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- En-tête avec véhicule -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold">
                                    {{ ravitaillement.vehicule.brand }} {{ ravitaillement.vehicule.model }}
                                </h3>
                                <p class="text-indigo-100 mt-1">
                                    {{ ravitaillement.vehicule.registration_number }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-indigo-200">Date</p>
                                <p class="text-lg font-semibold">
                                    {{ formatDate(ravitaillement.ravitaillement_date) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Corps des détails -->
                    <div class="p-6 space-y-6">
                        <!-- Informations principales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Station-service</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ ravitaillement.station_name }}
                                </p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 mb-1">Type de carburant</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ getFuelTypeLabel(ravitaillement.fuel_type) }}
                                </p>
                            </div>
                        </div>

                        <!-- Quantités et prix -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Quantités et Coûts</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Litres ravitaillés</p>
                                    <p class="text-3xl font-bold text-blue-600 mt-2">
                                        {{ formatNumber(ravitaillement.liters) }}
                                    </p>
                                    <p class="text-sm text-gray-500">litres</p>
                                </div>

                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Prix par litre</p>
                                    <p class="text-3xl font-bold text-green-600 mt-2">
                                        {{ formatNumber(ravitaillement.price_per_liter, 3) }}
                                    </p>
                                    <p class="text-sm text-gray-500">Ar/L</p>
                                </div>

                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Coût total</p>
                                    <p class="text-3xl font-bold text-purple-600 mt-2">
                                        {{ formatNumber(ravitaillement.total_cost) }}
                                    </p>
                                    <p class="text-sm text-gray-500">Ariary</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kilométrage -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Kilométrage</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">ODO Station</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ ravitaillement.odo_station || 'N/A' }}
                                        <span v-if="ravitaillement.odo_station" class="text-sm font-normal text-gray-500">km</span>
                                    </p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">ODO Arrivée</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ ravitaillement.odo_arrival || 'N/A' }}
                                        <span v-if="ravitaillement.odo_arrival" class="text-sm font-normal text-gray-500">km</span>
                                    </p>
                                </div>

                                <div v-if="ravitaillement.fuel_left" class="bg-green-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Carburant restant</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ formatNumber(ravitaillement.fuel_left) }}
                                        <span class="text-sm font-normal text-green-500">L</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de paiement -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Mode de paiement</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ getPaymentMethodLabel(ravitaillement.payment_method) }}
                                    </p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Numéro de reçu</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ ravitaillement.receipt_number || 'Non renseigné' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="border-t pt-6">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <div :class="[
                                        'w-5 h-5 rounded flex items-center justify-center mr-2',
                                        ravitaillement.full_tank ? 'bg-green-500' : 'bg-gray-300'
                                    ]">
                                        <svg v-if="ravitaillement.full_tank" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-700">
                                        {{ ravitaillement.full_tank ? 'Plein complet' : 'Plein partiel' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Remarques -->
                        <div v-if="ravitaillement.remarks" class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Remarques</h4>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-gray-700">{{ ravitaillement.remarks }}</p>
                            </div>
                        </div>

                        <!-- Informations utilisateur -->
                        <div class="border-t pt-6">
                            <p class="text-sm text-gray-500">
                                Enregistré par {{ ravitaillement.user.name }} 
                                le {{ formatDate(ravitaillement.created_at) }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="border-t pt-6 flex justify-end space-x-4">
                            <Link
                                :href="route('ravitaillements.edit', ravitaillement.id)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Modifier
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>