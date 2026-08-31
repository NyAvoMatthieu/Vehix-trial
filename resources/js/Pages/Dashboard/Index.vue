<template>
  <AppLayout title="Dashboard" :user-role="userRole">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tableau de Bord - {{ userRoleLabel }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Carte d'information du véhicule sélectionné (CLIENT UNIQUEMENT) -->
        <div v-if="userRole === 'client' && selectedVehicule" class="mb-6">
          <VehiculeInfoCard :vehicule="selectedVehicule" :proprietaire="proprietaireInfo" :current-kilometrage="currentKilometrage"
            :last-update="lastKilometrageUpdate"/>
        </div>

        <!-- Client Dashboard -->
        <div v-if="userRole === 'client'" class="space-y-6">
          <!-- Assurance & Visite Technique (côte à côte) -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Assurance -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                    <ShieldCheckIcon class="h-6 w-6 text-green-600 mr-2" />
                    Assurance
                  </h3>
                  <Link
                    :href="route('assurances.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                  >
                    Voir
                  </Link>
                </div>

                <div v-if="assuranceInfo" class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-700">{{ assuranceInfo.company }}</p>
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      assuranceInfo.isExpiringSoon ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                    ]">
                      {{ assuranceInfo.isExpiringSoon ? 'Expire bientôt' : 'Valide' }}
                    </span>
                  </div>

                  <div class="space-y-1">
                    <div class="text-xs text-gray-600">
                        <div><span class="font-medium">Début:</span> {{ formatDate(assuranceInfo.start_date) }}</div>
                        <div class="flex items-center ml-2">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        <span class="font-medium">Fin:</span> {{ formatDate(assuranceInfo.end_date) }}
                        </div>
                    </div>
                    <p class="text-xs text-gray-600">
                        <span class="font-medium">Police:</span> {{ assuranceInfo.policy_number }}
                    </p>
                    </div>

                </div>
                <div v-else class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg">
                  Aucune assurance active
                </div>
              </div>
            </div>

            <!-- Visite Technique -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                    <ClipboardDocumentCheckIcon class="h-6 w-6 text-purple-600 mr-2" />
                    Visite Technique
                  </h3>
                  <Link
                    :href="route('visite-techniques.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                  >
                    Voir
                  </Link>
                </div>

                <div v-if="visiteTechniqueInfo" class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-700">{{ visiteTechniqueInfo.centre }}</p>
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      visiteTechniqueInfo.aptitude === 'APTE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                    ]">
                      {{ visiteTechniqueInfo.aptitude }}
                    </span>
                  </div>

                    <div class="space-y-1">
                        <div class="text-xs text-gray-600">
                            <div><span class="font-medium">Date:</span> {{ formatDate(visiteTechniqueInfo.date_visite) }}</div>
                            <div class="flex items-center ml-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            <span class="font-medium">Validité:</span> {{ formatDate(visiteTechniqueInfo.validite) }}
                            </div>
                        </div>
                        <p class="text-xs text-gray-600">
                            <span class="font-medium">N° PV:</span> {{ visiteTechniqueInfo.numero_pv }}
                        </p>
                    </div>

                </div>
                <div v-else class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg">
                  Aucune visite technique enregistrée
                </div>
              </div>
            </div>
          </div>

          <!-- Maintenances (carte complète) -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <WrenchScrewdriverIcon class="h-6 w-6 text-yellow-600 mr-2" />
                  Maintenances
                </h3>
                <Link
                  :href="route('maintenances.index')"
                  class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                >
                  Voir tout
                </Link>
              </div>

              <div class="space-y-4">
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total effectuées</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ maintenanceStats?.totalMaintenances || 0 }}
                    </p>
                  </div>
                  <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Pièces changées</p>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ maintenanceStats?.totalPieces || 0 }}
                    </p>
                  </div>
                </div>

                <!-- Dernières pièces -->
                <div v-if="maintenanceStats?.recentPieces && maintenanceStats.recentPieces.length > 0">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Dernières pièces installées</h4>
                  <div class="space-y-2">
                    <div
                      v-for="piece in maintenanceStats.recentPieces.slice(0, 3)"
                      :key="piece.id"
                      class="flex items-center justify-between p-2 bg-gray-50 rounded"
                    >
                      <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ piece.nom_piece }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(piece.date_installation) }}</p>
                      </div>
                      <span :class="[
                        'px-2 py-1 text-xs font-medium rounded-full',
                        piece.etat_piece === 'neuf' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'
                      ]">
                        {{ piece.etat_piece === 'neuf' ? 'Neuf' : 'Occasion' }}
                      </span>
                    </div>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500 text-center py-4">
                  Aucune maintenance enregistrée
                </div>
              </div>
            </div>
          </div>

          <!-- Ravitaillement, Trajets et Propriétaire (3 cartes côte à côte) -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ravitaillement Total -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <FireIcon class="h-10 w-10 text-orange-600" />
                  </div>
                  <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Ravitaillement Total</h3>
                    <p class="text-lg font-bold text-gray-900">
                      {{ formatNumber(ravitaillementStats?.totalLiters || 0) }} L
                    </p>
                    <p class="text-sm font-medium text-green-600">
                      {{ formatCurrency(ravitaillementStats?.totalCost || 0) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Trajets -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <MapIcon class="h-10 w-10 text-blue-600" />
                  </div>
                  <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Trajets Effectués</h3>
                    <p class="text-2xl font-bold text-gray-900">
                      {{ trajetStats?.totalTrajets || 0 }}
                    </p>
                    <p v-if="trajetStats?.lastDestination" class="text-xs text-gray-500 mt-1 truncate">
                      Dernier: {{ trajetStats.lastDestination }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Propriétaire Info -->
            <div v-if="proprietaireInfo" class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <UserGroupIcon class="h-10 w-10 text-indigo-600" />
                  </div>
                  <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Propriétaire</h3>
                    <p class="text-sm font-bold text-gray-900">
                      {{ proprietaireInfo.display_name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ proprietaireInfo.type === 'personnel' ? '👤 Personne' : '🏢 Entreprise' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>



          <!-- 🚀 NOUVEAU: Affichage de la consommation -->
        <div v-if="userRole === 'client' && consumptionAnalysis" class="mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                Consommation du véhicule
                </h3>
                <ConsumptionBadge 
                :consumption="consumptionAnalysis.consumption"
                :precision="consumptionAnalysis.precision"
                :method="consumptionAnalysis.method"
                :overconsumption-alert="consumptionAnalysis.overconsumption_alert"
                :vehicule-id="selectedVehicule?.id"
                />
            </div>
            <Link
                :href="route('vehicules.consumption', selectedVehicule.id)"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Analyse détaillée
            </Link>
            </div>

            <!-- Message d'information -->
            <p class="text-sm text-gray-600 mt-3">
            {{ consumptionAnalysis.message }}
            </p>

            <!-- Alerte si surconsommation -->
            <div 
            v-if="consumptionAnalysis.overconsumption_alert?.alert" 
            class="mt-4 bg-red-50 border-2 border-red-300 rounded-lg p-3"
            >
            <p class="text-sm text-red-900 font-semibold">
                {{ consumptionAnalysis.overconsumption_alert.message }}
            </p>
            </div>
        </div>
        </div>


          <!-- Recent Activities -->
          <div v-if="recentActivities && recentActivities.length > 0" class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Activités récentes
              </h3>
              <div class="flow-root">
                <ul class="-mb-8">
                  <li v-for="(activity, index) in recentActivities" :key="index" class="relative pb-8">
                    <div v-if="index !== recentActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                    <div class="relative flex space-x-3">
                      <div>
                        <span :class="getActivityIcon(activity.type)">
                          <component :is="getActivityIconComponent(activity.type)" class="h-5 w-5 text-white" />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500">{{ activity.description }}</p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                          <time :datetime="activity.date">{{ formatDate(activity.date) }}</time>
                          <div v-if="activity.amount" class="font-medium text-gray-900">
                            {{ formatCurrency(activity.amount) }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

        </div>

        <!-- Validator Dashboard -->
        <div v-else-if="userRole === 'validator'" class="space-y-6">
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <DashboardCard
              v-for="stat in statsCards"
              :key="stat.title"
              :title="stat.title"
              :value="stat.value"
              :icon="stat.icon"
              :color="stat.color"
              :trend="stat.trend"
              :trend-up="stat.trendUp"
            />
          </div>

          <!-- Pending Validations -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Véhicules en attente de validation
                </h3>
                <Link
                  :href="route('validator.pending')"
                  class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                >
                  Voir tout
                </Link>
              </div>
              <div v-if="pendingVehicules && pendingVehicules.length > 0" class="space-y-4">
                <div
                  v-for="vehicule in pendingVehicules"
                  :key="vehicule.id"
                  class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">
                        {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }}
                      </h4>
                      <p class="text-sm text-gray-500 mt-1">
                        Propriétaire: {{ vehicule.user.name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        Plaque: {{ vehicule.license_plate }} | VIN: {{ vehicule.vin }}
                      </p>
                      <p class="text-xs text-gray-400 mt-1">
                        Soumis le {{ formatDate(vehicule.created_at) }}
                      </p>
                    </div>
                    <div class="flex space-x-2">
                      <Link
                        :href="route('vehicules.show', vehicule.id)"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors"
                      >
                        Examiner
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <TruckIcon class="h-12 w-12 text-gray-400 mx-auto mb-3" />
                <p class="text-sm text-gray-500">Aucun véhicule en attente</p>
              </div>
            </div>
          </div>

          <!-- Recent Validations -->
          <div v-if="recentValidations && recentValidations.length > 0" class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Validations récentes
              </h3>
              <div class="space-y-3">
                <div
                  v-for="vehicule in recentValidations"
                  :key="vehicule.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }}
                    </p>
                    <p class="text-sm text-gray-500">
                      {{ vehicule.user.name }} - {{ formatDate(vehicule.validated_at) }}
                    </p>
                  </div>
                  <VehiculeStatusBadge :status="vehicule.status" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Admin Dashboard -->
        <div v-else-if="userRole === 'administrateur'" class="space-y-6">
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <DashboardCard
              v-for="stat in statsCards"
              :key="stat.title"
              :title="stat.title"
              :value="stat.value"
              :icon="stat.icon"
              :color="stat.color"
              :trend="stat.trend"
              :trend-up="stat.trendUp"
            />
          </div>

          <!-- System Overview -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Nouveaux utilisateurs
                  </h3>
                  <Link
                    :href="route('administrateur.users.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                  >
                    Voir tout
                  </Link>
                </div>
                <div v-if="recentUsers && recentUsers.length > 0" class="space-y-3">
                  <div
                    v-for="user in recentUsers"
                    :key="user.id"
                    class="flex items-center justify-between"
                  >
                    <div class="flex items-center">
                      <img :src="user.profile_photo_url" :alt="user.name" class="h-10 w-10 rounded-full">
                      <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-sm text-gray-500">{{ user.email }}</p>
                      </div>
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ formatDate(user.created_at) }}
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <p class="text-sm text-gray-500">Aucun nouvel utilisateur</p>
                </div>
              </div>
            </div>

            <!-- Recent Vehicule Submissions -->
            <div v-if="systemActivity && systemActivity.recentVehicules" class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Véhicules récents
                </h3>
                <div v-if="systemActivity.recentVehicules.length > 0" class="space-y-3">
                  <div
                    v-for="vehicule in systemActivity.recentVehicules"
                    :key="vehicule.id"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ vehicule.user.name }}
                      </p>
                    </div>
                    <VehiculeStatusBadge :status="vehicule.status" />
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <p class="text-sm text-gray-500">Aucun véhicule récent</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Validations -->
          <div v-if="systemActivity && systemActivity.recentValidations" class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Validations récentes
              </h3>
              <div v-if="systemActivity.recentValidations.length > 0" class="space-y-3">
                <div
                  v-for="vehicule in systemActivity.recentValidations"
                  :key="vehicule.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }}
                    </p>
                    <p class="text-sm text-gray-500">
                      Par {{ vehicule.validator?.name || 'N/A' }} - {{ formatDate(vehicule.validated_at) }}
                    </p>
                  </div>
                  <VehiculeStatusBadge :status="vehicule.status" />
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-sm text-gray-500">Aucune validation récente</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardCard from '@/Components/DashboardCard.vue'
import VehiculeInfoCard from '@/Components/VehiculeInfoCard.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ConsumptionBadge from '@/Components/ConsumptionBadge.vue'

import {
  TruckIcon,
  ShieldCheckIcon,
  WrenchScrewdriverIcon,
  BoltIcon,
  FireIcon,
  UserGroupIcon,
  CheckCircleIcon,
  MapIcon,
  ClipboardDocumentCheckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  user: Object,
  userRole: String,
  selectedVehicule: Object,
  stats: Object,
  recentActivities: Array,
  upcomingMaintenances: Array,
  expiringAssurances: Array,
  pendingVehicules: Array,
  recentUsers: Array,
  recentValidations: Array,
  systemActivity: Object,
  vehiculeDetails: Object,
  ravitaillementStats: Object,
  trajetStats: Object,
  maintenanceStats: Object,
  assuranceInfo: Object,
  visiteTechniqueInfo: Object,
  consumptionAnalysis: Object,
  proprietaireInfo: Object
})

const userRoleLabel = computed(() => {
  const labels = {
    'client': 'Client',
    'validator': 'Validateur',
    'administrateur': 'Administrateur'
  }
  return labels[props.userRole] || 'Utilisateur'
})

// COMPUTAGE POUR LE KILOMÉTRAGE ACTUEL
const currentKilometrage = computed(() => {
  // Priorité aux données de vehiculeDetails (calculées par le backend)
  if (props.vehiculeDetails && props.vehiculeDetails.currentKilometrage) {
    return props.vehiculeDetails.currentKilometrage
  }
  // Fallback sur le kilométrage du véhicule
  return props.selectedVehicule?.mileage || 0
})

const lastKilometrageUpdate = computed(() => {
  return props.vehiculeDetails?.lastUpdate || null
})

const statsCards = computed(() => {
  if (!props.stats) return []

  if (props.userRole === 'validator') {
    return [
      {
        title: 'En attente',
        value: props.stats.pendingValidations || 0,
        icon: TruckIcon,
        color: 'yellow'
      },
      {
        title: 'Validés aujourd\'hui',
        value: props.stats.validatedToday || 0,
        icon: CheckCircleIcon,
        color: 'green'
      },
      {
        title: 'Total validés',
        value: props.stats.totalValidated || 0,
        icon: ShieldCheckIcon,
        color: 'blue'
      },
      {
        title: 'Rejetés aujourd\'hui',
        value: props.stats.rejectedToday || 0,
        icon: WrenchScrewdriverIcon,
        color: 'red'
      }
    ]
  } else if (props.userRole === 'administrateur') {
    return [
      {
        title: 'Utilisateurs',
        value: props.stats.totalUsers || 0,
        icon: UserGroupIcon,
        color: 'blue'
      },
      {
        title: 'Véhicules',
        value: props.stats.totalVehicules || 0,
        icon: TruckIcon,
        color: 'green'
      },
      {
        title: 'En attente',
        value: props.stats.pendingVehicules || 0,
        icon: TruckIcon,
        color: 'yellow'
      },
      {
        title: 'Nouveaux ce mois',
        value: props.stats.newUsersThisMonth || 0,
        icon: UserGroupIcon,
        color: 'purple'
      }
    ]
  }

  return []
})

const getActivityIcon = (type) => {
  const baseClass = 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
  const colors = {
    reparation: 'bg-red-500',
    maintenance: 'bg-yellow-500',
    ravitaillement: 'bg-green-500',
    trajet: 'bg-blue-500',
    default: 'bg-gray-500'
  }
  return `${baseClass} ${colors[type] || colors.default}`
}

const getActivityIconComponent = (type) => {
  const icons = {
    reparation: WrenchScrewdriverIcon,
    maintenance: BoltIcon,
    ravitaillement: FireIcon,
    trajet: MapIcon,
    default: TruckIcon
  }
  return icons[type] || icons.default
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}

/*const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount || 0)
}*/

const formatDateShort = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0) + ' Ar'
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('fr-FR').format(num || 0)
}
</script>
