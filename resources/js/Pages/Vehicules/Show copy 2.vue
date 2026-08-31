<template>
  <AppLayout title="Détails du véhicule">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails du véhicule
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('vehicules.selection')"
            class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            v-if="canEdit"
            :href="route('vehicules.edit', vehicule.id)"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Modifier
          </Link>
          <button
            v-if="canDelete"
            @click="confirmDelete"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <TrashIcon class="h-4 w-4 mr-2" />
            Supprimer
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Status Alert -->
        <div v-if="vehicule.status !== 'valide'" class="mb-6">
          <div :class="getAlertClass(vehicule.status)" class="rounded-xl p-4 shadow-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <component :is="getStatusIcon(vehicule.status)" class="h-6 w-6" :class="getAlertIconColor(vehicule.status)" />
              </div>
              <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold" :class="getAlertTextColor(vehicule.status)">
                  {{ getStatusTitle(vehicule.status) }}
                </h3>
                <div class="mt-2 text-sm" :class="getAlertTextColor(vehicule.status)">
                  <p>{{ getStatusMessage(vehicule.status) }}</p>
                  <div v-if="vehicule.validation_notes" class="mt-3 p-3 bg-white rounded-lg border-2 shadow-sm">
                    <p class="font-semibold text-gray-900">📝 Notes du validateur:</p>
                    <p class="mt-1 text-gray-700">{{ vehicule.validation_notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Information -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Vehicle Details Card -->
            <div class="bg-white shadow-xl overflow-hidden sm:rounded-xl border border-gray-100">
              <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-100 flex items-center justify-between">
                <div>
                  <h3 class="text-xl leading-6 font-bold text-gray-900">
                    {{ vehicule.full_name }}
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-600">
                    Informations détaillées du véhicule
                  </p>
                </div>
                <VehiculeStatusBadge :status="vehicule.status" />
              </div>
              
              <div class="border-t border-gray-200">
                <!-- Informations générales -->
                <div class="bg-gray-50 px-6 py-4">
                  <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <TruckIcon class="h-4 w-4 mr-2 text-indigo-600" />
                    Informations générales
                  </h4>
                </div>
                <dl>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Marque</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.make }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Modèle</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.model }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Type de véhicule</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ getVehiculeTypeLabel(vehicule.vehicule_type) }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Catégorie (Genre)</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.categorie || 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Année</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ formatYear(vehicule.year) }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Plaque d'immatriculation</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold font-mono">{{ vehicule.license_plate }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Carrosserie</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.carrosserie || 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Couleur</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.color || 'Non spécifié' }}</dd>
                  </div>
                </dl>

                <!-- Identification technique -->
                <div class="bg-gray-50 px-6 py-4 mt-6 border-t-2">
                  <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <DocumentTextIcon class="h-4 w-4 mr-2 text-indigo-600" />
                    Identification technique
                  </h4>
                </div>
                <dl>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Numéro VIN (Châssis)</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold font-mono">{{ vehicule.vin || 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">N° dans la série du type</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.numero_serie_type || 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Numéro moteur</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold font-mono">{{ vehicule.numero_moteur || 'Non spécifié' }}</dd>
                  </div>
                </dl>

                <!-- Caractéristiques moteur -->
                <div class="bg-gray-50 px-6 py-4 mt-6 border-t-2">
                  <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <CogIcon class="h-4 w-4 mr-2 text-indigo-600" />
                    Caractéristiques moteur
                  </h4>
                </div>
                <dl>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Type de carburant</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ getFuelTypeLabel(vehicule.fuel_type) }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Cylindrée</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.cylindree ? `${formatNumber(vehicule.cylindree)} cm³` : 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Puissance administrative</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.puissance_administrative ? `${vehicule.puissance_administrative} CV` : 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Kilométrage</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ formatNumber(vehicule.mileage) }} km</dd>
                  </div>
                </dl>

                <!-- Capacités et poids -->
                <div class="bg-gray-50 px-6 py-4 mt-6 border-t-2">
                  <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <ScaleIcon class="h-4 w-4 mr-2 text-indigo-600" />
                    Capacités et poids
                  </h4>
                </div>
                <dl>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Places assises</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.places_assises ? `${vehicule.places_assises} places` : 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Poids à vide</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.poids_vide ? `${formatNumber(vehicule.poids_vide)} kg` : 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600 flex items-center">
                      PTAC
                      <span class="ml-1 text-xs text-gray-400" title="Poids Total Autorisé en Charge">ⓘ</span>
                    </dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.poids_total_charge ? `${formatNumber(vehicule.poids_total_charge)} kg` : 'Non spécifié' }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Charge utile</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.charge_utile ? `${formatNumber(vehicule.charge_utile)} kg` : 'Non spécifié' }}</dd>
                  </div>
                </dl>

                <!-- Section Consommation moyenne -->
                <div v-if="vehicule.average_consumption" class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">⛽ Consommation</h4>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Consommation moyenne</p>
                                <p class="text-3xl font-bold text-green-700">
                                    {{ parseFloat(vehicule.average_consumption).toFixed(2) }} L/100km
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Calculée automatiquement</p>
                                <p class="text-xs text-gray-500">basée sur les ravitaillements</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations propriétaire -->
                <div class="bg-gray-50 px-6 py-4 mt-6 border-t-2">
                  <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide flex items-center">
                    <UserCircleIcon class="h-4 w-4 mr-2 text-indigo-600" />
                    Propriétaire et validation
                  </h4>
                </div>
                <dl>
                  <div class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Propriétaire</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.user?.name }}</dd>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Date d'ajout</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ formatDate(vehicule.created_at) }}</dd>
                  </div>
                  <div v-if="vehicule.validated_at" class="bg-white px-6 py-4 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-gray-600">Validé par</dt>
                    <dd class="text-sm text-gray-900 col-span-2 font-semibold">{{ vehicule.validator?.name }} le {{ formatDate(vehicule.validated_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Validation Section (for validators/admins) -->
            <div v-if="canValidate && vehicule.status === 'en_attente'" class="bg-white shadow-xl sm:rounded-xl border border-gray-100">
              <div class="px-6 py-5 bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-green-200">
                <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center">
                  <CheckCircleIcon class="h-6 w-6 mr-2 text-green-600" />
                  Validation du véhicule
                </h3>
              </div>
              <div class="px-6 py-6">
                <ValidationForm :vehicule="vehicule" @validated="handleValidation" />
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-cyan-50 border-b-2 border-blue-200">
                <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center">
                  <ChartBarIcon class="h-6 w-6 mr-2 text-blue-600" />
                  Statistiques rapides
                </h3>
              </div>
              <div class="px-6 py-5">
                <dl class="space-y-4">
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <dt class="text-sm font-medium text-gray-600 flex items-center">
                      <span class="text-lg mr-2">🛡️</span>
                      Assurances
                    </dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ vehicule.assurances?.length || 0 }}</dd>
                  </div>
                  
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <dt class="text-sm font-medium text-gray-600 flex items-center">
                      <span class="text-lg mr-2">⚙️</span>
                      Maintenances
                    </dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ vehicule.maintenances?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <dt class="text-sm font-medium text-gray-600 flex items-center">
                      <span class="text-lg mr-2">⛽</span>
                      Ravitaillements
                    </dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ vehicule.ravitaillements?.length || 0 }}</dd>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <dt class="text-sm font-medium text-gray-600 flex items-center">
                      <span class="text-lg mr-2">🗺️</span>
                      Trajets
                    </dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ vehicule.trajets?.length || 0 }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-xl sm:rounded-xl border border-gray-100 overflow-hidden">
              <div class="px-6 py-5 bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-purple-200">
                <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center">
                  <BoltIcon class="h-6 w-6 mr-2 text-purple-600" />
                  Actions rapides
                </h3>
              </div>
              <div class="px-6 py-5">
                <div class="space-y-3">
                  <Link
                    v-if="canEdit"
                    :href="route('vehicules.edit', vehicule.id)"
                    class="w-full flex items-center justify-center px-4 py-3 border-2 border-indigo-300 rounded-lg shadow-sm text-sm font-semibold text-indigo-700 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 transition-all duration-200"
                  >
                    <PencilIcon class="h-5 w-5 mr-2" />
                    Modifier
                  </Link>
                  <button
                    v-if="canDelete"
                    @click="confirmDelete"
                    class="w-full flex items-center justify-center px-4 py-3 border-2 border-red-300 rounded-lg shadow-sm text-sm font-semibold text-red-700 bg-gradient-to-r from-red-50 to-orange-50 hover:from-red-100 hover:to-orange-100 transition-all duration-200"
                  >
                    <TrashIcon class="h-5 w-5 mr-2" />
                    Supprimer
                  </button>
                </div>
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
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ValidationForm from '@/Components/ValidationForm.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentDuplicateIcon,
  TruckIcon,
  DocumentTextIcon,
  CogIcon,
  ScaleIcon,
  UserCircleIcon,
  ChartBarIcon,
  BoltIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: Object,
  canValidate: Boolean
})

const canEdit = computed(() => {
  const statusValue = typeof props.vehicule.status === 'object' 
    ? props.vehicule.status.value 
    : props.vehicule.status
  return ['en_attente', 'refuse', 'a_corriger', 'doublon'].includes(statusValue)
})

const canDelete = computed(() => {
  return canEdit.value
})

const getStatusValue = (status) => {
  return typeof status === 'object' ? status.value : status
}

const getVehiculeTypeLabel = (type) => {
  const labels = {
    'voiture': '🚗 Voiture',
    'moto': '🏍️ Moto'
  }
  return labels[type] || type || 'Non spécifié'
}

const getFuelTypeLabel = (type) => {
  const labels = {
    'essence': '⛽ Essence',
    'diesel': '🛢️ Diesel',
    'hybride': '🔋 Hybride',
    'electrique': '⚡ Électrique',
    'gpl': '💨 GPL'
  }
  return labels[type] || type || 'Non spécifié'
}

const getStatusIcon = (status) => {
  const statusValue = getStatusValue(status)
  const icons = {
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return icons[statusValue] || ClockIcon
}

const getAlertClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'en_attente': 'bg-yellow-50 border-2 border-yellow-200',
    'refuse': 'bg-red-50 border-2 border-red-200',
    'a_corriger': 'bg-orange-50 border-2 border-orange-200',
    'doublon': 'bg-purple-50 border-2 border-purple-200'
  }
  return classes[statusValue] || 'bg-gray-50 border-2 border-gray-200'
}

const getAlertIconColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-500',
    'refuse': 'text-red-500',
    'a_corriger': 'text-orange-500',
    'doublon': 'text-purple-500'
  }
  return colors[statusValue] || 'text-gray-400'
}

const getAlertTextColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-900',
    'refuse': 'text-red-900',
    'a_corriger': 'text-orange-900',
    'doublon': 'text-purple-900'
  }
  return colors[statusValue] || 'text-gray-800'
}

const getStatusTitle = (status) => {
  const statusValue = getStatusValue(status)
  const titles = {
    'en_attente': '⏳ Véhicule en attente de validation',
    'refuse': '❌ Véhicule refusé',
    'a_corriger': '✏️ Corrections requises',
    'doublon': '📋 Doublon détecté'
  }
  return titles[statusValue] || 'Information'
}

const getStatusMessage = (status) => {
  const statusValue = getStatusValue(status)
  const messages = {
    'en_attente': 'Ce véhicule est en cours de validation par notre équipe.',
    'refuse': 'Ce véhicule a été refusé. Consultez les notes ci-dessous.',
    'a_corriger': 'Des corrections sont nécessaires avant validation.',
    'doublon': 'Ce véhicule semble déjà exister dans le système.'
  }
  return messages[statusValue] || ''
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatYear = (date) => {
  if (!date) return 'Non spécifié'
  return new Date(date).getFullYear()
}

const formatNumber = (num) => {
  if (num === null || num === undefined) return '0'
  return new Intl.NumberFormat('fr-FR').format(num)
}

const confirmDelete = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')) {
    router.delete(route('vehicules.destroy', props.vehicule.id), {
      onSuccess: () => {
        router.visit(route('vehicules.selection'))
      }
    })
  }
}

const handleValidation = () => {
  router.reload({ only: ['vehicule'] })
}
</script>