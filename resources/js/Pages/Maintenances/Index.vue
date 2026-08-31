<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    maintenances: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const applyFilters = () => {
    router.get(route('maintenances.index'), {
        search: search.value,
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

const getCurrentMonth = () => {
    return new Date().toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                                <!-- Statistiques Mensuelles -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 {{ getCurrentMonth() }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-2 border-purple-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Maintenances
                                            </dt>
                                            <dd class="text-2xl font-bold text-purple-900">
                                                {{ stats.total_maintenances_mois }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 overflow-hidden shadow-lg rounded-xl border border-cyan-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-cyan-200 rounded-lg flex items-center justify-center">
                                            <span class="text-xl">🔧</span>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-cyan-700 truncate">
                                                Main d'œuvre
                                            </dt>
                                            <dd class="text-lg font-bold text-cyan-900">
                                                {{ formatCurrency(stats.cout_total_main_oeuvre_mois) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 overflow-hidden shadow-lg rounded-xl border border-teal-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-teal-200 rounded-lg flex items-center justify-center">
                                            <span class="text-xl">🔩</span>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-teal-700 truncate">
                                                Pièces
                                            </dt>
                                            <dd class="text-lg font-bold text-teal-900">
                                                {{ formatCurrency(stats.cout_total_pieces_mois) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden shadow-lg rounded-xl border border-orange-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-orange-700 truncate">
                                                Alertes Pièces
                                            </dt>
                                            <dd class="text-2xl font-bold text-orange-900">
                                                {{ stats.pieces_alerte }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 overflow-hidden shadow-lg rounded-xl border-2 border-purple-300">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                                            <span class="text-xl">💵</span>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-purple-700 truncate">
                                                Total Mois
                                            </dt>
                                            <dd class="text-lg font-bold text-purple-900">
                                                {{ formatCurrency(stats.cout_total_global_mois) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques Globales 
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques Globales par Jours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total Maintenances
                                            </dt>
                                            <dd class="text-2xl font-bold text-gray-900">
                                                {{ stats.total_maintenances }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden shadow-lg rounded-xl border border-blue-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-blue-700 truncate">
                                                Main d'œuvre
                                            </dt>
                                            <dd class="text-xl font-bold text-blue-900">
                                                {{ formatCurrency(stats.cout_total_main_oeuvre) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 overflow-hidden shadow-lg rounded-xl border border-green-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-green-700 truncate">
                                                Pièces
                                            </dt>
                                            <dd class="text-xl font-bold text-green-900">
                                                {{ formatCurrency(stats.cout_total_pieces) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 overflow-hidden shadow-lg rounded-xl border-2 border-indigo-300">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-indigo-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-indigo-700 truncate">
                                                Total Global
                                            </dt>
                                            <dd class="text-xl font-bold text-indigo-900">
                                                {{ formatCurrency(stats.cout_total_global) }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden shadow-lg rounded-xl border border-orange-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                                            <svg class="h-6 w-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-orange-700 truncate">
                                                Alertes Pièces
                                            </dt>
                                            <dd class="text-2xl font-bold text-orange-900">
                                                {{ stats.pieces_alerte }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->



                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Recherche</h3>
                            <p class="text-sm text-gray-500">Rechercher dans les maintenances</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Référence, véhicule, garage..."
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <button
                            @click="applyFilters"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                        >
                            Rechercher
                        </button>
                    </div>
                </div>

                <!-- Maintenances Table -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200">
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
                                        Garage
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nature
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
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
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Aucune maintenance enregistrée</p>
                                            <p class="text-sm text-gray-400">Commencez par créer votre première maintenance</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="maintenance in maintenances.data" :key="maintenance.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ maintenance.reference }}
                                        </div>
                                        <div v-if="maintenance.pieces.length > 0" class="text-xs text-gray-500">
                                            🔩 {{ maintenance.pieces.length }} pièce(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                              {{ maintenance.vehicule.alias }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ maintenance.vehicule.make }} - {{ maintenance.vehicule.license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            🔧 {{ maintenance.garage_nom }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            📍 {{ maintenance.garage_lieu }}
                                        </div>
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
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(maintenance.cout_total) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            M: {{ formatCurrency(maintenance.cout_main_oeuvre) }} | P: {{ formatCurrency(maintenance.cout_pieces) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link
                                            :href="route('maintenances.show', maintenance.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            v-if="!maintenance.validated_at"
                                            :href="route('maintenances.edit', maintenance.id)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            v-if="!maintenance.validated_at"
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
                                        'px-3 py-2 text-sm rounded-md transition-colors',
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