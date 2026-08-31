<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    ravitaillements: Object,
    stats: Object,
});

const deleteRavitaillement = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce ravitaillement ?')) {
        router.delete(route('ravitaillements.destroy', id));
    }
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

const getPaymentMethodLabel = (method) => {
    const labels = {
        cash: 'Espèces',
        carte: 'Carte',
        virement: 'Virement',
        mobile: 'Mobile Money',
    };
    return labels[method] || method;
};

/*const formatNumber = (value) => {
    if (typeof value === 'number' && !isNaN(value)) {
        return value.toFixed(2);
    }
    return '0.00';
};*/
const formatNumber = (value) => {
    // Gestion des valeurs null/undefined
    if (value == null || value === undefined || isNaN(value)) {
        return '0.00';
    }
    return parseFloat(value).toFixed(2);
};
</script>

<template>
    <AppLayout title="Ravitaillements">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Carburants
                </h2>
                <Link :href="route('ravitaillements.create')">
                    <PrimaryButton>
                        + Nouveau Carburant
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- 🚀 Statistics Cards - SIMPLIFIÉ (2 cards seulement) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Total dépensé -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden shadow-lg rounded-xl border border-blue-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-14 w-14 rounded-full bg-blue-500 flex items-center justify-center shadow-md">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-6 flex-1">
                                    <dl>
                                        <dt class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-1">
                                            💰 Total dépensé
                                        </dt>
                                        <dd class="text-3xl font-extrabold text-blue-900">
                                            {{ formatCurrency(stats.total_spent) }}
                                        </dd>
                                        <dd class="text-xs text-blue-600 mt-1">
                                            Toutes périodes confondues
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total litres -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 overflow-hidden shadow-lg rounded-xl border border-green-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-14 w-14 rounded-full bg-green-500 flex items-center justify-center shadow-md">
                                        <!-- 🚀 Icône de carburant/pompe à essence -->
                                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77zM12 13.5V19H6v-7h6v1.5zm0-3.5H6V5h6v5zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-6 flex-1">
                                    <dl>
                                        <dt class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-1">
                                            ⛽ Total litres
                                        </dt>
                                        <dd class="text-3xl font-extrabold text-green-900">
                                            {{ formatNumber(stats.total_liters) }} L
                                        </dd>
                                        <dd class="text-xs text-green-600 mt-1">
                                            Carburant consommé total
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ravitaillements List -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Véhicule
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Station
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Litres 
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix/L
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kilometrage au station
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="ravitaillements.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Aucun ravitaillement enregistré.
                                    </td>
                                </tr>
                                <tr v-for="ravitaillement in ravitaillements.data" :key="ravitaillement.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(ravitaillement.ravitaillement_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ravitaillement.vehicule.alias }} 
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ravitaillement.vehicule.make }} - {{ ravitaillement.vehicule.license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ravitaillement.station_service }}
                                    </td>
                                    <!-- 🚀 CORRECTION: Utiliser liters_purchased au lieu de total_liters -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatNumber(ravitaillement.liters_purchased) }} L
                                        <span v-if="ravitaillement.is_full_tank" class="ml-1 text-xs text-green-600">(Plein)</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatCurrency(ravitaillement.price_per_liter) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatCurrency(ravitaillement.total_cost) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ getPaymentMethodLabel(ravitaillement.odo_station) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('ravitaillements.show', ravitaillement.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('ravitaillements.edit', ravitaillement.id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="deleteRavitaillement(ravitaillement.id)"
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
                    <div v-if="ravitaillements.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Affichage de <span class="font-medium">{{ ravitaillements.from }}</span> à 
                                <span class="font-medium">{{ ravitaillements.to }}</span> sur
                                <span class="font-medium">{{ ravitaillements.total }}</span> résultats
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in ravitaillements.links"
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