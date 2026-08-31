<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    ravitaillement: Object,
    consumptionRate: Number,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value) + ' Ar';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getPaymentMethodLabel = (method) => {
    const labels = {
        cash: 'Espèces',
        carte: 'Carte bancaire',
        virement: 'Virement bancaire',
        mobile: 'Mobile Money',
    };
    return labels[method] || method;
};

const getPaymentMethodIcon = (method) => {
    const icons = {
        cash: '💵',
        carte: '💳',
        virement: '🏦',
        mobile: '📱',
    };
    return icons[method] || '💰';
};
</script>

<template>
    <AppLayout title="Détails du Ravitaillement">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails du Ravitaillement
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('ravitaillements.edit', ravitaillement.id)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        Modifier
                    </Link>
                    <Link
                        :href="route('ravitaillements.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        Retour
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Header Section -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold mb-2">
                                    {{ ravitaillement.station_service }}
                                </h3>
                                <p class="text-blue-100">
                                    {{ formatDate(ravitaillement.ravitaillement_date) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">
                                    {{ formatCurrency(ravitaillement.total_cost) }}
                                </div>
                                <div class="text-blue-100 mt-1">
                                    {{ getPaymentMethodIcon(ravitaillement.payment_method) }}
                                    {{ getPaymentMethodLabel(ravitaillement.payment_method) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle and Driver Info -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Véhicule</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ ravitaillement.vehicule.make }} {{ ravitaillement.vehicule.model }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ ravitaillement.vehicule.license_plate }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Type: {{ ravitaillement.fuel_type }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Conducteur</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ ravitaillement.chauffeur ? ravitaillement.chauffeur.name : ravitaillement.user.name }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ ravitaillement.chauffeur ? 'Chauffeur' : 'Propriétaire' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fuel Details -->
                    <div class="p-6 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Détails du ravitaillement</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl mb-1">⛽</div>
                                <div class="text-sm text-gray-600">Litres achetés</div>
                                <div class="text-xl font-bold text-gray-900">
                                    {{ ravitaillement.liters_purchased }} L
                                </div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl mb-1">💰</div>
                                <div class="text-sm text-gray-600">Prix par litre</div>
                                <div class="text-xl font-bold text-gray-900">
                                    {{ formatCurrency(ravitaillement.price_per_liter) }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl mb-1">💵</div>
                                <div class="text-sm text-gray-600">Montant payé</div>
                                <div class="text-xl font-bold text-gray-900">
                                    {{ formatCurrency(ravitaillement.amount_paid) }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl mb-1">📊</div>
                                <div class="text-sm text-gray-600">Total litres</div>
                                <div class="text-xl font-bold text-gray-900">
                                    {{ ravitaillement.total_liters }} L
                                </div>
                            </div>
                        </div>

                        <!-- Full Tank Indicator -->
                        <div v-if="ravitaillement.is_full_tank" class="mt-4 p-3 bg-green-100 border border-green-300 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800 font-medium">Plein complet effectué</span>
                        </div>
                    </div>

                    <!-- Odometer Readings -->
                    <div v-if="ravitaillement.odo_station || ravitaillement.odo_arrival" class="p-6 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Kilométrage</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div v-if="ravitaillement.odo_station" class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600 mb-1">À la station</div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ parseFloat(ravitaillement.odo_station).toLocaleString() }} km
                                </div>
                            </div>
                            <div v-if="ravitaillement.odo_arrival" class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-sm text-gray-600 mb-1">À l'arrivée</div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ parseFloat(ravitaillement.odo_arrival).toLocaleString() }} km
                                </div>
                            </div>
                            <div v-if="ravitaillement.odo_station && ravitaillement.odo_arrival" class="bg-indigo-50 p-4 rounded-lg">
                                <div class="text-sm text-indigo-600 mb-1">Distance parcourue</div>
                                <div class="text-2xl font-bold text-indigo-900">
                                    {{ (parseFloat(ravitaillement.odo_arrival) - parseFloat(ravitaillement.odo_station)).toLocaleString() }} km
                                </div>
                            </div>
                        </div>

                        <!-- Consumption Rate -->
                        <div v-if="consumptionRate" class="mt-4 p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-600">Consommation moyenne</div>
                                    <div class="text-3xl font-bold text-gray-900">
                                        {{ consumptionRate.toFixed(2) }} L/100km
                                    </div>
                                </div>
                                <div class="text-5xl">📈</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt and Notes -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-if="ravitaillement.receipt_number">
                                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Numéro de reçu</h4>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="font-mono text-gray-900">{{ ravitaillement.receipt_number }}</p>
                                </div>
                            </div>
                            <div v-if="ravitaillement.notes">
                                <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Remarques</h4>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-gray-700">{{ ravitaillement.notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <div>
                                Créé le {{ new Date(ravitaillement.created_at).toLocaleString('fr-FR') }}
                            </div>
                            <div v-if="ravitaillement.updated_at !== ravitaillement.created_at">
                                Modifié le {{ new Date(ravitaillement.updated_at).toLocaleString('fr-FR') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>