<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { TrashIcon, PencilIcon, EyeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    ravitaillements: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const searchRavitaillements = () => {
    router.get(route('ravitaillements.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const deleteRavitaillement = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce ravitaillement ?')) {
        router.delete(route('ravitaillements.destroy', id));
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
        carte: 'Carte',
        mobile: 'Mobile',
        bon: 'Bon',
    };
    return labels[method] || method;
};
</script>

<template>
    <AppLayout title="Ravitaillements">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ravitaillements
                </h2>
                <a
                    :href="route('ravitaillements.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
                >
                    + Nouveau Ravitaillement
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Barre de recherche -->
                <div class="mb-6 bg-white rounded-lg shadow p-4">
                    <div class="flex gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Rechercher par station, reçu, immatriculation..."
                            class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            @keyup.enter="searchRavitaillements"
                        />
                        <button
                            @click="searchRavitaillements"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                        >
                            Rechercher
                        </button>
                    </div>
                </div>

                <!-- Liste des ravitaillements -->
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div v-if="ravitaillements.data.length === 0" class="p-6 text-center text-gray-500">
                        Aucun ravitaillement enregistré.
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
                                        Station
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Carburant
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantité
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Coût
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Km
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Paiement
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="ravitaillement in ravitaillements.data"
                                    :key="ravitaillement.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ new Date(ravitaillement.ravitaillement_date).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ravitaillement.vehicule.make }} {{ ravitaillement.vehicule.model }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ravitaillement.vehicule.license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ravitaillement.station_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ getFuelTypeLabel(ravitaillement.fuel_type) }}
                                        </span>
                                        <span v-if="ravitaillement.is_full_tank" class="ml-1 text-xs text-green-600">
                                            🔋 Plein
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ravitaillement.liters }} L
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ravitaillement.total_cost }} Ar
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ ravitaillement.price_per_liter }} Ar/L
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ ravitaillement.odo_station }} km</div>
                                        <div v-if="ravitaillement.odo_arrival" class="text-xs text-gray-500">
                                            → {{ ravitaillement.odo_arrival }} km
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ getPaymentMethodLabel(ravitaillement.payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a
                                            :href="route('ravitaillements.show', ravitaillement.id)"
                                            class="text-blue-600 hover:text-blue-900 inline-flex items-center"
                                            title="Voir"
                                        >
                                            <EyeIcon class="h-5 w-5" />
                                        </a>
                                        <a
                                            :href="route('ravitaillements.edit', ravitaillement.id)"
                                            class="text-indigo-600 hover:text-indigo-900 inline-flex items-center"
                                            title="Modifier"
                                        >
                                            <PencilIcon class="h-5 w-5" />
                                        </a>
                                        <button
                                            @click="deleteRavitaillement(ravitaillement.id)"
                                            class="text-red-600 hover:text-red-900 inline-flex items-center"
                                            title="Supprimer"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="ravitaillements.data.length > 0" class="px-6 py-4 border-t">
                        <Pagination :links="ravitaillements.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>