<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    fuelPrices: Object,
    currentPrices: Array,
    customPriceStats: Object,
    recentCustomPrices: Array,
});

const showCustomPrices = ref(false);

const deleteFuelPrice = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce prix ?')) {
        router.delete(route('admin.fuel-prices.destroy', id));
    }
};

const toggleActive = (id) => {
    router.post(route('admin.fuel-prices.toggle-active', id));
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value) + ' Ar';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const getFuelTypeLabel = (type) => {
    const labels = {
        diesel: 'Diesel',
        essence: 'Essence',
        gpl: 'GPL',
        electrique: 'Électrique',
    };
    return labels[type] || type;
};

const getFuelTypeColor = (type) => {
    const colors = {
        diesel: 'bg-yellow-100 text-yellow-800',
        essence: 'bg-blue-100 text-blue-800',
        gpl: 'bg-green-100 text-green-800',
        electrique: 'bg-purple-100 text-purple-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const hasCustomPrices = Object.keys(props.customPriceStats || {}).length > 0;
</script>

<template>
    <AppLayout title="Gestion des Prix Carburant">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestion des Prix Carburant
                </h2>
                <Link :href="route('admin.fuel-prices.create')">
                    <PrimaryButton>
                        + Nouveau Prix
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Current Active Prices -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Prix Actuels Actifs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div
                            v-for="price in currentPrices"
                            :key="price.id"
                            class="bg-white overflow-hidden shadow rounded-lg"
                        >
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <span
                                        :class="['px-3 py-1 rounded-full text-xs font-semibold', getFuelTypeColor(price.fuel_type)]"
                                    >
                                        {{ getFuelTypeLabel(price.fuel_type) }}
                                    </span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        Actif
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-gray-900 mb-1">
                                    {{ formatCurrency(price.price_per_liter) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    par litre
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    Depuis le {{ formatDate(price.effective_date) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Prices Alert -->
                <div v-if="hasCustomPrices" class="bg-orange-50 border-l-4 border-orange-400 p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-orange-800">
                                Prix personnalisés détectés
                            </h3>
                            <div class="mt-2 text-sm text-orange-700">
                                <p>Certains utilisateurs ont utilisé des prix différents des prix officiels.</p>
                                <button
                                    @click="showCustomPrices = !showCustomPrices"
                                    class="mt-2 text-orange-800 hover:text-orange-900 underline font-medium"
                                >
                                    {{ showCustomPrices ? 'Masquer' : 'Voir les détails' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Prices Statistics -->
                <div v-if="showCustomPrices && hasCustomPrices" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Statistiques des Prix Personnalisés</h3>
                    </div>

                    <!-- Stats Cards -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div v-for="(stats, fuelType) in customPriceStats" :key="fuelType" class="border rounded-lg p-4">
                            <span
                                :class="['px-3 py-1 rounded-full text-xs font-semibold inline-block mb-3', getFuelTypeColor(fuelType)]"
                            >
                                {{ getFuelTypeLabel(fuelType) }}
                            </span>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Utilisations:</span>
                                    <span class="ml-2 font-medium">{{ stats.count }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Prix moyen:</span>
                                    <span class="ml-2 font-medium">{{ formatCurrency(stats.avg_price) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Min:</span>
                                    <span class="ml-2 font-medium">{{ formatCurrency(stats.min_price) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Max:</span>
                                    <span class="ml-2 font-medium">{{ formatCurrency(stats.max_price) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Custom Prices Table -->
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Ravitaillements avec Prix Personnalisés (20 derniers)</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Station</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix/L</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Litres</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Véhicule</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in recentCustomPrices" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(item.date) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span :class="['px-2 py-1 rounded-full text-xs font-semibold', getFuelTypeColor(item.fuel_type)]">
                                                {{ getFuelTypeLabel(item.fuel_type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ item.station }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-orange-600">
                                            {{ formatCurrency(item.custom_price) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.liters }} L
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ formatCurrency(item.total_cost) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ item.user_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div>{{ item.vehicule }}</div>
                                            <div class="text-xs text-gray-500">{{ item.license_plate }}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- All Prices History -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Historique des Prix Officiels</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type Carburant
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix/Litre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Effective
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Défini par
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notes
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="fuelPrices.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Aucun prix enregistré.
                                    </td>
                                </tr>
                                <tr v-for="price in fuelPrices.data" :key="price.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="['px-3 py-1 rounded-full text-xs font-semibold', getFuelTypeColor(price.fuel_type)]"
                                        >
                                            {{ getFuelTypeLabel(price.fuel_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatCurrency(price.price_per_liter) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(price.effective_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ price.setter.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleActive(price.id)"
                                            :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer',
                                                price.is_active
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                            ]"
                                        >
                                            {{ price.is_active ? 'Actif' : 'Inactif' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ price.notes ? (price.notes.length > 50 ? price.notes.substring(0, 50) + '...' : price.notes) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('admin.fuel-prices.edit', price.id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="deleteFuelPrice(price.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="fuelPrices.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Affichage de <span class="font-medium">{{ fuelPrices.from }}</span> à
                                <span class="font-medium">{{ fuelPrices.to }}</span> sur
                                <span class="font-medium">{{ fuelPrices.total }}</span> résultats
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in fuelPrices.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-md',
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
