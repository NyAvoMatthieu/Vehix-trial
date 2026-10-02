<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
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
  ClockIcon,
  CheckCircleIcon,
} from "@heroicons/vue/24/outline";

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
  assurance: ShieldCheckIcon,
  visite: ClipboardDocumentCheckIcon,
};

const activityTypeColors = {
  trajet: "bg-blue-50 text-blue-700 border-blue-200",
  ravitaillement: "bg-orange-50 text-orange-700 border-orange-200",
  maintenance: "bg-purple-50 text-purple-700 border-purple-200",
  assurance: "bg-green-50 text-green-700 border-green-200",
  visite: "bg-indigo-50 text-indigo-700 border-indigo-200",
};

const activityTypeBadgeColors = {
  trajet: "bg-blue-100 text-blue-800 border-blue-300",
  ravitaillement: "bg-orange-100 text-orange-800 border-orange-300",
  maintenance: "bg-purple-100 text-purple-800 border-purple-300",
  assurance: "bg-green-100 text-green-800 border-green-300",
  visite: "bg-indigo-100 text-indigo-800 border-indigo-300",
};

const activityTypeLabels = {
  trajet: "Trajet",
  ravitaillement: "Ravitaillement",
  maintenance: "Maintenance",
  assurance: "Assurance",
  visite: "Visite Technique",
};

const searchForm = ref({
  start_date: props.filters.start_date,
  end_date: props.filters.end_date,
  activity_type: props.filters.activity_type,
  search: props.filters.search,
});

const showFilters = ref(false);

const submitSearch = () => {
  router.get(route("rapports.index"), searchForm.value, {
    preserveState: true,
    preserveScroll: true,
  });
};

const resetFilters = () => {
  searchForm.value = {
    start_date: new Date().toISOString().split("T")[0].slice(0, 8) + "01",
    end_date: new Date().toISOString().split("T")[0],
    activity_type: "all",
    search: "",
  };
  submitSearch();
};

const formatCurrency = (amount) => {
  return (
    new Intl.NumberFormat("fr-FR", {
      style: "decimal",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount) + " Ar"
  );
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const exportData = () => {
  window.location.href = route("rapports.export", searchForm.value);
};

// Calculer le pourcentage de chaque type d'activité
const activityPercentages = computed(() => {
  const total = props.stats.total_activities;
  if (total === 0) return {};

  return {
    trajets: ((props.stats.by_type.trajets / total) * 100).toFixed(0),
    ravitaillements: ((props.stats.by_type.ravitaillements / total) * 100).toFixed(0),
    maintenances: ((props.stats.by_type.maintenances / total) * 100).toFixed(0),
    assurances: ((props.stats.by_type.assurances / total) * 100).toFixed(0),
    visites: ((props.stats.by_type.visites / total) * 100).toFixed(0),
  };
});
</script>

<template>
  <AppLayout title="Rapports d'Activités">
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            📊 Rapport d'Activités
          </h2>
          <p v-if="selectedVehicule" class="mt-1 text-sm text-gray-600">
            🚗 {{ selectedVehicule.make }} - {{ selectedVehicule.license_plate }} (
            {{ selectedVehicule.alias }} )
          </p>
        </div>
        <button
          @click="exportData"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
        >
          <DocumentArrowDownIcon class="h-5 w-5 mr-2" />
          Exporter
        </button>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow-sm rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <ChartBarIcon class="h-6 w-6 mr-2 text-indigo-600" />
            Total Activités
          </h3>
          <!-- Statistiques principales - Design amélioré -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Activités -->
            <div
              class="bg-gradient-to-br from-indigo-500 to-indigo-600 overflow-hidden shadow-lg rounded-xl p-6 text-white transform hover:scale-105 transition-transform duration-200"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-indigo-100 uppercase tracking-wide">
                    Total Activités
                  </p>
                  <p class="text-3xl font-bold mt-2">{{ stats.total_activities }}</p>
                  <p class="text-xs text-indigo-200 mt-1">Sur la période</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                  <ChartBarIcon class="h-10 w-10" />
                </div>
              </div>
            </div>

            <!-- Coût Total -->
            <div
              class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg rounded-xl p-6 text-white transform hover:scale-105 transition-transform duration-200"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-green-100 uppercase tracking-wide">
                    Coût Total
                  </p>
                  <p class="text-3xl font-bold mt-2">
                    {{ formatCurrency(stats.total_amount) }}
                  </p>
                  <p class="text-xs text-green-200 mt-1">Toutes dépenses</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                  <CurrencyDollarIcon class="h-10 w-10" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Répartition des coûts par type -->
        <div class="bg-white shadow-sm rounded-xl p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <ChartBarIcon class="h-6 w-6 mr-2 text-indigo-600" />
            Répartition des Dépenses
          </h3>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!--trajet-->
            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
              <MapIcon class="h-8 w-8 mx-auto text-blue-600 mb-2" />
              <p class="text-xs text-gray-600 mb-1">Trajets</p>
              <p class="text-lg font-bold text-gray-900">
                {{ stats.by_type.trajets }} trajets
              </p>
              <p class="text-xs text-blue-200 mt-1">
                {{ activityPercentages.trajets }}% du total
              </p>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
              <FireIcon class="h-8 w-8 mx-auto text-orange-600 mb-2" />
              <p class="text-xs text-gray-600 mb-1">Carburant</p>
              <p class="text-lg font-bold text-gray-900">
                {{ formatCurrency(stats.costs_by_type.ravitaillements) }}
              </p>
              <p class="text-xs text-orange-200 mt-1">
                {{ stats.by_type.ravitaillements }} ravitaillements
              </p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
              <WrenchScrewdriverIcon class="h-8 w-8 mx-auto text-purple-600 mb-2" />
              <p class="text-xs text-gray-600 mb-1">Maintenance</p>
              <p class="text-lg font-bold text-gray-900">
                {{ formatCurrency(stats.costs_by_type.maintenances) }}
              </p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
              <ShieldCheckIcon class="h-8 w-8 mx-auto text-green-600 mb-2" />
              <p class="text-xs text-gray-600 mb-1">Assurances</p>
              <p class="text-lg font-bold text-gray-900">
                {{ formatCurrency(stats.costs_by_type.assurances) }}
              </p>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-lg border border-indigo-200">
              <ClipboardDocumentCheckIcon class="h-8 w-8 mx-auto text-indigo-600 mb-2" />
              <p class="text-xs text-gray-600 mb-1">Visites Tech.</p>
              <p class="text-lg font-bold text-gray-900">
                {{ formatCurrency(stats.costs_by_type.visites) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Filtres améliorés -->
        <div class="bg-white shadow-sm rounded-xl overflow-hidden">
          <button
            @click="showFilters = !showFilters"
            class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors duration-200"
          >
            <div class="flex items-center">
              <FunnelIcon class="h-5 w-5 text-indigo-600 mr-2" />
              <h3 class="text-lg font-medium text-gray-900">Filtres de Recherche</h3>
            </div>
            <svg
              :class="[
                'h-5 w-5 text-gray-400 transition-transform duration-200',
                showFilters ? 'rotate-180' : '',
              ]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </button>

          <div v-show="showFilters" class="px-6 pb-6 border-t border-gray-100">
            <form @submit.prevent="submitSearch" class="mt-4">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Date de début
                  </label>
                  <input
                    v-model="searchForm.start_date"
                    type="date"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Date de fin
                  </label>
                  <input
                    v-model="searchForm.end_date"
                    type="date"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    🏷️ Type d'activité
                  </label>
                  <select
                    v-model="searchForm.activity_type"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  >
                    <option value="all">Toutes les activités</option>
                    <option value="trajets">Trajets</option>
                    <option value="ravitaillements">Ravitaillements</option>
                    <option value="maintenances">Maintenances</option>
                    <option value="assurances">Assurances</option>
                    <option value="visites">Visites Techniques</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    🔍 Rechercher
                  </label>
                  <div class="relative">
                    <input
                      v-model="searchForm.search"
                      type="text"
                      placeholder="Mot-clé..."
                      class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    />
                    <MagnifyingGlassIcon
                      class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                    />
                  </div>
                </div>
              </div>

              <div class="flex gap-3">
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                >
                  <CheckCircleIcon class="h-4 w-4 mr-2" />
                  Appliquer
                </button>
                <button
                  type="button"
                  @click="resetFilters"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                >
                  Réinitialiser
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Liste des activités - Design amélioré -->
        <div class="bg-white shadow-sm rounded-xl overflow-hidden">
          <div
            class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"
          >
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
              <ClockIcon class="h-6 w-6 mr-2 text-indigo-600" />
              Historique des Activités ({{ activities.total }})
            </h3>
          </div>

          <div v-if="activities.data.length === 0" class="p-16 text-center">
            <CalendarIcon class="mx-auto h-16 w-16 text-gray-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              Aucune activité trouvée
            </h3>
            <p class="text-sm text-gray-500">
              Aucune activité ne correspond à vos critères de recherche.
            </p>
            <button
              @click="resetFilters"
              class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200"
            >
              Réinitialiser les filtres
            </button>
          </div>

          <div v-else class="divide-y divide-gray-100">
            <div
              v-for="activity in activities.data"
              :key="`${activity.type}-${activity.id}`"
              class="p-6 hover:bg-gray-50 transition-all duration-200 cursor-pointer group"
            >
              <div class="flex items-start gap-4">
                <!-- Icône avec badge coloré -->
                <div
                  :class="[
                    'p-3 rounded-xl border-2 flex-shrink-0 group-hover:scale-110 transition-transform duration-200',
                    activityTypeColors[activity.type],
                  ]"
                >
                  <component :is="activityTypeIcons[activity.type]" class="h-7 w-7" />
                </div>

                <!-- Contenu principal -->
                <div class="flex-1 min-w-0">
                  <!-- En-tête avec badge et date -->
                  <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span
                      :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border',
                        activityTypeBadgeColors[activity.type],
                      ]"
                    >
                      {{ activityTypeLabels[activity.type] }}
                    </span>
                    <span class="text-sm text-gray-500 flex items-center">
                      <CalendarIcon class="h-4 w-4 mr-1" />
                      {{ formatDate(activity.date) }}
                    </span>
                  </div>

                  <!-- Titre et description -->
                  <h4
                    class="text-base font-semibold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors duration-200"
                  >
                    {{ activity.title }}
                  </h4>
                  <p class="text-sm text-gray-600 mb-3">{{ activity.description }}</p>

                  <!-- Informations véhicule -->
                  <div
                    class="flex items-center gap-2 text-sm mb-3 p-2 bg-gray-50 rounded-lg inline-flex"
                  >
                    <span class="text-gray-600">🚗 Véhicule:</span>
                    <span
                      class="px-2 py-0.5 bg-indigo-100 text-green-700 rounded font-semibold text-xs"
                    >
                      {{ selectedVehicule.make }} - ( {{ selectedVehicule.alias }} )
                    </span>
                    <span
                      class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold text-xs"
                    >
                      {{ activity.license_plate }}
                    </span>
                  </div>

                  <!-- Détails supplémentaires en grille -->
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div
                      v-for="(value, key) in activity.details"
                      :key="key"
                      class="bg-white border border-gray-200 rounded-lg p-2"
                    >
                      <p class="text-xs text-gray-500 mb-1">{{ key }}</p>
                      <p
                        class="text-sm text-gray-900 font-medium truncate"
                        :title="value"
                      >
                        {{ value }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Montant (si présent) -->
                <div v-if="activity.amount" class="flex-shrink-0 text-right">
                  <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">
                    Montant
                  </p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ formatCurrency(activity.amount) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination améliorée -->
          <div
            v-if="activities.data.length > 0"
            class="px-6 py-4 bg-gray-50 border-t border-gray-200"
          >
            <div class="flex items-center justify-between flex-wrap gap-4">
              <div class="text-sm text-gray-700">
                Affichage de <span class="font-semibold">{{ activities.from }}</span> à
                <span class="font-semibold">{{ activities.to }}</span> sur
                <span class="font-semibold">{{ activities.total }}</span> résultats
              </div>
              <div class="flex gap-2">
                <a
                  v-for="link in activities.links"
                  :key="link.label"
                  :href="link.url"
                  v-html="link.label"
                  :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                    link.active
                      ? 'bg-indigo-600 text-white shadow-md'
                      : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300',
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
