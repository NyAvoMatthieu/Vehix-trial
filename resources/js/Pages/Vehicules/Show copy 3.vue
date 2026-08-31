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
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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
                    <p class="mt-1 text-gray-700 whitespace-pre-line">{{ vehicule.validation_notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 space-y-8">

            <!-- En-tête avec badge de statut -->
            <div class="flex items-center justify-between pb-6 border-b-2 border-gray-200">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">
                  {{ vehicule.make }} {{ vehicule.model }}
                </h3>
                <p class="mt-1 text-sm text-gray-600 font-mono">
                  {{ vehicule.license_plate }}
                </p>
              </div>
              <VehiculeStatusBadge :status="vehicule.status" />
            </div>

            <!-- Propriétaire du véhicule -->
            <div v-if="vehicule.proprietaire" class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <UserCircleIcon class="h-6 w-6 text-indigo-600 mr-2" />
                <label class="text-base font-semibold text-gray-800">
                  Propriétaire du véhicule
                </label>
              </div>

              <div class="p-4 bg-white border-2 border-indigo-200 rounded-lg shadow-sm">
                <div class="flex items-center space-x-3">
                  <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-md">
                    <UserIcon v-if="vehicule.proprietaire.type === 'personnel'" class="h-6 w-6 text-white" />
                    <BuildingOfficeIcon v-else class="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ getProprietaireDisplayName(vehicule.proprietaire) }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ vehicule.proprietaire.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informations générales -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <TruckIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Informations générales</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Marque</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.make || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Modèle</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.model || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Type véhicule</label>
                  <p class="text-base font-semibold text-gray-900">{{ getVehiculeTypeLabel(vehicule.vehicule_type) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Date 1ère mise en circulation</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatYear(vehicule.year) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Plaque d'immatriculation</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.license_plate || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Catégorie (Genre)</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.categorie || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Carrosserie</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.carrosserie || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Couleur</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.color || 'Non spécifié' }}</p>
                </div>
              </div>
            </div>

            <!-- Identification technique -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Identification technique</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Numéro VIN (Châssis)</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.vin || 'Non spécifié' }}</p>
                  <p class="mt-1 text-xs text-gray-500">Le numéro VIN se trouve généralement sur le tableau de bord côté conducteur</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">N° dans la série du type</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.numero_serie_type || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Numéro moteur</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.numero_moteur || 'Non spécifié' }}</p>
                </div>
              </div>
            </div>

            <!-- Caractéristiques moteur -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <CogIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Caractéristiques moteur</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Type de carburant</label>
                  <p class="text-base font-semibold text-gray-900">{{ getFuelTypeLabel(vehicule.fuel_type) }}</p>
                </div>

                <div v-if="vehicule.average_consumption" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border-2 border-green-200 shadow-sm">
                  <label class="block text-sm font-medium text-green-700 mb-1">Consommation moyenne</label>
                  <p class="text-2xl font-bold text-green-700">
                    {{ parseFloat(vehicule.average_consumption).toFixed(2) }} <span class="text-sm">L/100km</span>
                  </p>
                  <p class="mt-1 text-xs text-green-600">💡 Calculée automatiquement</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Cylindrée</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.cylindree ? `${formatNumber(vehicule.cylindree)} cm³` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Puissance administrative</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.puissance_administrative ? `${vehicule.puissance_administrative} CV` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Kilométrage actuel</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatNumber(vehicule.mileage) }} km</p>
                </div>
              </div>
            </div>

            <!-- Capacités et poids -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <ScaleIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Capacités et poids</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Places assises</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.places_assises ? `${vehicule.places_assises} places` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Poids à vide</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.poids_vide ? `${formatNumber(vehicule.poids_vide)} kg` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1 flex items-center">
                    PTAC
                    <span class="ml-1 text-xs text-gray-400" title="Poids Total Autorisé en Charge">ⓘ</span>
                  </label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.poids_total_charge ? `${formatNumber(vehicule.poids_total_charge)} kg` : 'Non spécifié' }}
                  </p>
                  <p class="mt-1 text-xs text-gray-500">Poids Total Autorisé en Charge</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Charge utile</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.charge_utile ? `${formatNumber(vehicule.charge_utile)} kg` : 'Non spécifié' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Informations propriétaire et validation -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <InformationCircleIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Propriétaire et validation</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Propriétaire</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.user?.name || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Date d'ajout</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatDate(vehicule.created_at) }}</p>
                </div>

                <div v-if="vehicule.validated_at" class="sm:col-span-2 bg-green-50 rounded-lg p-4 border-2 border-green-200">
                  <label class="block text-sm font-medium text-green-700 mb-1">✅ Validé par</label>
                  <p class="text-base font-semibold text-green-900">
                    {{ vehicule.validator?.name }} le {{ formatDate(vehicule.validated_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <ChartBarIcon class="h-6 w-6 text-blue-600 mr-2" />
                <h3 class="text-lg font-semibold text-gray-800">Statistiques rapides</h3>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-200">
                  <div class="flex items-center justify-between">
                    <span class="text-2xl">🛡️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.assurances?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Assurances</p>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-200">
                  <div class="flex items-center justify-between">
                    <span class="text-2xl">⚙️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.maintenances?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Maintenances</p>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-200">
                  <div class="flex items-center justify-between">
                    <span class="text-2xl">⛽</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.ravitaillements?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Ravitaillements</p>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-200">
                  <div class="flex items-center justify-between">
                    <span class="text-2xl">🗺️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.trajets?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Trajets</p>
                </div>
              </div>
            </div>

            <!-- Validation Section (for validators/admins) -->
            <div v-if="canValidate && vehicule.status === 'en_attente'" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <CheckCircleIcon class="h-6 w-6 text-green-600 mr-2" />
                <h3 class="text-lg font-semibold text-gray-800">Validation du véhicule</h3>
              </div>
              <ValidationForm :vehicule="vehicule" @validated="handleValidation" />
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
  CheckCircleIcon,
  InformationCircleIcon,
  UserIcon,
  BuildingOfficeIcon
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

const getProprietaireDisplayName = (proprietaire) => {
  if (!proprietaire) return 'Non spécifié'

  if (proprietaire.type === 'personnel') {
    return `${proprietaire.prenom || ''} ${proprietaire.nom || ''}`.trim() || 'Non spécifié'
  }

  return proprietaire.nom_commercial || proprietaire.raison_sociale || 'Non spécifié'
}

const getVehiculeTypeLabel = (type) => {
  const labels = {
    'voiture': '🚗 Voiture',
    'moto': '🏍️ Moto',
    'utilitaire': '🚚 Véhicule utilitaire',
    'camion': '🚛 Camion',
    'bus': '🚌 Bus',
    'autre': '🔧 Autre'
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
  if (!date) return 'Non spécifié'
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
