<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    maintenances: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const typeFilter = ref(props.filters?.type || 'all');

const applyFilters = () => {
    router.get(route('maintenances.index'), {
        search: search.value,
        status: statusFilter.value,
        type: typeFilter.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteMaintenance = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette maintenance ?')) {
        router.delete(route('maintenances.destroy', id));
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

const getStatusBadge = (status) => {
    const badges = {
        en_attente: 'bg-yellow-100 text-yellow-800',
        en_cours: 'bg-blue-100 text-blue-800',
        validee: 'bg-green-100 text-green-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        en_attente: 'En attente',
        en_cours: 'En cours',
        validee: 'Validée',
    };
    return labels[status] || status;
};

const getTypeIcon = (type) => {
    const icons = {
        preventive: '🔧',
        corrective: '⚠️',
        diagnostique: '🔍',
    };
    return icons[type] || '🔧';
};

const getTypeLabel = (type) => {
    const labels = {
        preventive: 'Préventive',
        corrective: 'Corrective',
        diagnostique: 'Diagnostique',
    };
    return labels[type] || type;
};
</script>

<template>
    <AppLayout title="Maintenances">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Maintenances
                </h2>
                <Link :href="route('maintenances.create')">
                    <PrimaryButton>
                        + Nouvelle Maintenance
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.total_maintenances }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            En cours
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.en_cours }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Ce mois
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.validees_ce_mois }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Coût annuel
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ formatCurrency(stats.cout_total_annee) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Alertes pièces
                                        </dt>
                                        <dd class="text-lg font-semibold text-gray-900">
                                            {{ stats.pieces_alerte }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Référence, véhicule..."
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select
                                v-model="statusFilter"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                @change="applyFilters"
                            >
                                <option value="all">Tous les statuts</option>
                                <option value="en_attente">En attente</option>
                                <option value="en_cours">En cours</option>
                                <option value="validee">Validée</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select
                                v-model="typeFilter"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                @change="applyFilters"
                            >
                                <option value="all">Tous les types</option>
                                <option value="preventive">Préventive</option>
                                <option value="corrective">Corrective</option>
                                <option value="diagnostique">Diagnostique</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="applyFilters"
                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                Filtrer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Maintenances Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Référence
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Véhicule
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nature
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Coût
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="maintenances.data.length === 0">
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Aucune maintenance enregistrée.
                                    </td>
                                </tr>
                                <tr v-for="maintenance in maintenances.data" :key="maintenance.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ maintenance.reference }}
                                        </div>
                                        <div v-if="maintenance.pieces.length > 0" class="text-xs text-gray-500">
                                            {{ maintenance.pieces.length }} pièce(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ maintenance.vehicule.make }} {{ maintenance.vehicule.model }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ maintenance.vehicule.license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg mr-1">{{ getTypeIcon(maintenance.type) }}</span>
                                        <span class="text-sm text-gray-900">{{ getTypeLabel(maintenance.type) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ maintenance.nature_intervention }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(maintenance.date_debut) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusBadge(maintenance.status)]">
                                            {{ getStatusLabel(maintenance.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatCurrency(maintenance.cout_total) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('maintenances.show', maintenance.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            v-if="maintenance.status !== 'validee'"
                                            :href="route('maintenances.edit', maintenance.id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            v-if="maintenance.status !== 'validee'"
                                            @click="deleteMaintenance(maintenance.id)"
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
                    <div v-if="maintenances.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Affichage de <span class="font-medium">{{ maintenances.from }}</span> à 
                                <span class="font-medium">{{ maintenances.to }}</span> sur
                                <span class="font-medium">{{ maintenances.total }}</span> résultats
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in maintenances.links"
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