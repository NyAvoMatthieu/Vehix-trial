<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    ravitaillements: Object,
});

const deleteRavitaillement = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce ravitaillement ?')) {
        router.delete(route('ravitaillements.destroy', id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const formatNumber = (number, decimals = 2) => {
    return parseFloat(number).toFixed(decimals);
};
</script>

<template>
    <AppLayout title="Ravitaillements">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes Ravitaillements
                </h2>
                <Link
                    :href="route('ravitaillements.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    + Nouveau Ravitaillement
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Message de succès -->
                    <div v-if="$page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-6">
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- Tableau -->
                    <div v-if="ravitaillements.data.length > 0" class="overflow-x-auto">
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
                                        Coût
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ODO Station
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="ravitaillement in ravitaillements.data" :key="ravitaillement.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(ravitaillement.ravitaillement_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ravitaillement.vehicule.brand }} {{ ravitaillement.vehicule.model }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ravitaillement.vehicule.registration_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ravitaillement.station_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatNumber(ravitaillement.liters) }} L
                                        <span v-if="ravitaillement.full_tank" class="ml-1 text-xs text-green-600">
                                            (Plein)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ formatNumber(ravitaillement.total_cost) }} Ar
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ravitaillement.odo_station }} km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ravitaillement.fuel_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link
                                            :href="route('ravitaillements.show', ravitaillement.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('ravitaillements.edit', ravitaillement.id)"
                                            class="text-green-600 hover:text-green-900"
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

                    <!-- Message si vide -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun ravitaillement</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par enregistrer votre premier ravitaillement.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('ravitaillements.create')"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                            >
                                + Nouveau Ravitaillement
                            </Link>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="ravitaillements.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de
                                <span class="font-medium">{{ ravitaillements.from }}</span>
                                à
                                <span class="font-medium">{{ ravitaillements.to }}</span>
                                sur
                                <span class="font-medium">{{ ravitaillements.total }}</span>
                                résultats
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in ravitaillements.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-md',
                                        link.active 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                    ]"
                                    :disabled="!link.url"
                                >
                                    {{ link.label }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>