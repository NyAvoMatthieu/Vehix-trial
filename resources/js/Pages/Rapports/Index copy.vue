<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    DocumentArrowDownIcon,
    MapIcon,
    FireIcon,
    WrenchScrewdriverIcon,
    ShieldCheckIcon,
    ClipboardDocumentCheckIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    ChartBarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    activities: Object,
    stats: Object,
    filters: Object,
    selectedVehicule: Object,
});

const activityTypeIcons = {
    trajet: MapIcon,
    ravitaillement: FireIcon,
    maintenance: WrenchScrewdriverIcon,
    reparation: WrenchScrewdriverIcon,
    assurance: ShieldCheckIcon,
    visite: ClipboardDocumentCheckIcon,
};

const activityTypeColors = {
    trajet: 'bg-blue-100 text-blue-700 border-blue-200',
    ravitaillement: 'bg-orange-100 text-orange-700 border-orange-200',
    maintenance: 'bg-purple-100 text-purple-700 border-purple-200',
    reparation: 'bg-red-100 text-red-700 border-red-200',
    assurance: 'bg-green-100 text-green-700 border-green-200',
    visite: 'bg-indigo-100 text-indigo-700 border-indigo-200',
};

const activityTypeLabels = {
    trajet: 'Trajet',
    ravitaillement: 'Ravitaillement',
    maintenance: 'Maintenance',
    reparation: 'Réparation',
    assurance: 'Assurance',
    visite: 'Visite Technique',
};

const searchForm = ref({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
    activity_type: props.filters.activity_type,
    search: props.filters.search,
});

const submitSearch = () => {
    router.get(route('rapports.index'), searchForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    searchForm.value = {
        start_date: new Date().toISOString().split('T')[0].slice(0, 8) + '01',
        end_date: new Date().toISOString().split('T')[0],
        activity_type: 'all',
        search: '',
    };
    submitSearch();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount) + ' Ar';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const exportData = () => {
    // Placeholder pour l'export
    alert('Fonctionnalité d\'export à venir');
};
</script>

<template>
    <AppLayout title="Rapports d'Activités">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Historique des Activités
                    </h2>
                    <p v-if="selectedVehicule" class="mt-1 text-sm text-gray-600">
                        {{ selectedVehicule.full_name }} - {{ selectedVehicule.license_plate }}
                    </p>
                </div>
                <button
                    @click="exportData"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
                >
                    <DocumentArrowDownIcon class="h-5 w-5 mr-2" />
                    Exporter
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <ChartBarIcon class="h-8 w-8" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Activités</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_activities }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <CurrencyDollarIcon class="h-8 w-8" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Coût Total</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.total_amount) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <MapIcon class="h-8 w-8" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Trajets</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.by_type.trajets }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                                <FireIcon class="h-8 w-8" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Carburant</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.costs_by_type.ravitaillements) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <FunnelIcon class="h-5 w-5 text-gray-400 mr-2" />
                        <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
                    </div>

                    <form @submit.prevent="submitSearch" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input
                                v-model="searchForm.start_date"
                                type="date"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input
                                v-model="searchForm.end_date"
                                type="date"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type d'activité</label>
                            <select
                                v-model="searchForm.activity_type"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="all">Toutes les activités</option>
                                <option value="trajets">Trajets</option>
                                <option value="ravitaillements">Ravitaillements</option>
                                <option value="maintenances">Maintenances</option>
                                <option value="reparations">Réparations</option>
                                <option value="assurances">Assurances</option>
                                <option value="visites">Visites Techniques</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                            <div class="relative">
                                <input
                                    v-model="searchForm.search"
                                    type="text"
                                    placeholder="Rechercher..."
                                    class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                                <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                            </div>
                        </div>

                        <div class="md:col-span-2 lg:col-span-4 flex gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                            >
                                Appliquer les filtres
                            </button>
                            <button
                                type="button"
                                @click="resetFilters"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                            >
                                Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Liste des activités -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            Activités ({{ activities.total }})
                        </h3>
                    </div>

                    <div v-if="activities.data.length === 0" class="p-12 text-center">
                        <CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune activité</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucune activité trouvée pour cette période.</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="activity in activities.data"
                            :key="`${activity.type}-${activity.id}`"
                            class="p-6 hover:bg-gray-50 transition"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div :class="['p-2 rounded-lg border-2', activityTypeColors[activity.type]]">
                                        <component :is="activityTypeIcons[activity.type]" class="h-6 w-6" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', activityTypeColors[activity.type]]">
                                                {{ activityTypeLabels[activity.type] }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ formatDate(activity.date) }}</span>
                                        </div>

                                        <h4 class="text-base font-medium text-gray-900 mb-1">{{ activity.title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ activity.description }}</p>

                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <span class="font-medium">Véhicule:</span>
                                                <span class="ml-1">{{ activity.vehicule }}</span>
                                                <span class="ml-1 text-indigo-600 font-medium">({{ activity.license_plate }})</span>
                                            </div>
                                        </div>

                                        <!-- Détails supplémentaires -->
                                        <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
                                            <div
                                                v-for="(value, key) in activity.details"
                                                :key="key"
                                                class="text-xs"
                                            >
                                                <span class="text-gray-500">{{ key }}:</span>
                                                <span class="ml-1 text-gray-900 font-medium">{{ value }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="activity.amount" class="ml-4 flex-shrink-0">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 mb-1">Montant</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ formatCurrency(activity.amount) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="activities.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Affichage de {{ activities.from }} à {{ activities.to }} sur {{ activities.total }} résultats
                            </div>
                            <div class="flex gap-2">
                                <a
                                    v-for="link in activities.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-md',
                                        link.active
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300',
                                        !link.url && 'opacity-50 cursor-not-allowed',
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