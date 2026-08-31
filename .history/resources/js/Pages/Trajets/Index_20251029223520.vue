<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    trajets: Object,
});

const deleteTrajet = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?')) {
        router.delete(route('trajets.destroy', id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const formatTime = (time) => {
    // Vérifie si 'time' est null, undefined, ou une chaîne vide
    if (!time) {
        return 'n/a'; // ou 'N/A', ou '--:--', selon ce que vous préférez afficher
    }
    return time.substring(0, 5);
};
</script>

<template>
    <AppLayout title="Trajets">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestion des Trajets
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Liste des Trajets
                            </h3>
                            <Link
                                :href="route('trajets.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
                            >
                                Nouveau Trajet
                            </Link>
                        </div>

                        <div v-if="trajets.data.length === 0" class="text-center py-8 text-gray-500">
                            Aucun trajet enregistré pour le moment.
                        </div>

                        <div v-else class="overflow-x-auto">
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
                                            Trajet
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Horaires
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Distance (km)
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Motif
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="trajet in trajets.data" :key="trajet.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(trajet.heure_depart) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ trajet.vehicule.make }} {{ trajet.vehicule.model }}</div>
                                            <div class="text-xs text-gray-500">{{ trajet.vehicule.license_plate }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>{{ trajet.departure }}</div>
                                            <div class="flex items-center text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                {{ trajet.destination }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ formatDate(trajet.heure_depart) }}</div>
                                            <div class="text-gray-500">{{ formatDate(trajet.heure_arrivee) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ trajet.distance }} km
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ trajet.purpose }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('trajets.show', trajet.id)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Voir
                                            </Link>
                                            <Link
                                                :href="route('trajets.edit', trajet.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Modifier
                                            </Link>
                                            <button
                                                @click="deleteTrajet(trajet.id)"
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
                        <div v-if="trajets.links.length > 3" class="mt-6 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <Link
                                    v-for="(link, index) in trajets.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === trajets.links.length - 1 ? 'rounded-r-md' : '',
                                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                                    ]"
                                    :disabled="!link.url"
                                >
                                    <span v-if="link.label" v-html="link.label"></span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
