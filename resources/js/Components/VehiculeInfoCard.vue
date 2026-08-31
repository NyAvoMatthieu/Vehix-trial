<template>
  <div class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200">
    <div class="px-6 py-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- Icône du véhicule -->
          <div class="bg-indigo-100 p-4 rounded-full">
            <TruckIcon class="h-12 w-12 text-indigo-600" />
          </div>
          
          <!-- Informations principales -->
          <div>
            <h2 class="text-3xl font-bold text-gray-900">
              {{ displayName }}
            </h2>
            <p class="text-gray-600 text-lg mt-1">
              {{ vehicule.make }} • {{ vehicule.model }} • {{ vehicule.license_plate }}
            </p>
            <div class="flex items-center space-x-4 mt-2">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                {{ getVehiculeTypeLabel(vehicule.vehicule_type) }}
              </span>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                {{ getFuelTypeLabel(vehicule.fuel_type) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Bouton Changer -->
        <div class="flex flex-col items-end space-y-2">
          <Link
            :href="route('vehicules.selection')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors font-medium shadow-lg"
          >
            <ArrowPathIcon class="h-5 w-5 mr-2" />
            Changer
          </Link>
          
          <Link
            :href="route('vehicules.show', vehicule.id)"
            class="text-sm text-indigo-600 hover:text-indigo-800 underline"
          >
            Voir les détails
          </Link>
        </div>
      </div>

      <!-- Propriétaire Info -->
      <div v-if="proprietaire" class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="bg-purple-100 p-2 rounded-full">
              <UserIcon v-if="proprietaire.type === 'personnel'" class="h-5 w-5 text-purple-600" />
              <BuildingOfficeIcon v-else class="h-5 w-5 text-purple-600" />
            </div>
            <div>
              <p class="text-xs text-gray-500">Propriétaire</p>
              <p class="text-gray-900 font-semibold">{{ proprietaire.display_name }}</p>
            </div>
          </div>
          
          <div class="text-right">
            <p class="text-xs text-gray-500">Type</p>
            <p class="text-gray-900 font-medium">
              {{ proprietaire.type === 'personnel' ? 'Personnel' : 'Entreprise' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Infos supplémentaires - Style Cards -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Immatriculation & Modèle -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 p-4 rounded-lg hover:shadow-md transition-shadow">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0 shadow">
              <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-blue-700 font-medium">Immatriculation</p>
              <p class="text-blue-900 font-bold text-sm font-mono truncate">{{ vehicule.license_plate }}</p>
              <p class="text-xs text-blue-600 truncate">{{ vehicule.model }}</p>
            </div>
          </div>
        </div>

        <!-- Carburant -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 p-4 rounded-lg hover:shadow-md transition-shadow">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0 shadow">
              <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-green-700 font-medium">Carburant</p>
              <p class="text-green-900 font-semibold text-sm truncate">{{ getFuelTypeLabel(vehicule.fuel_type) }}</p>
            </div>
          </div>
        </div>

        <!-- Couleur -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 p-4 rounded-lg hover:shadow-md transition-shadow">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0 shadow">
              <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-purple-700 font-medium">Couleur</p>
              <p class="text-purple-900 font-semibold text-sm truncate">{{ vehicule.color || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Kilométrage actuel -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 p-4 rounded-lg hover:shadow-md transition-shadow">
          <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-orange-500 flex items-center justify-center flex-shrink-0 shadow">
              <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-orange-700 font-medium">Kilométrage actuel</p>
              <p class="text-orange-900 font-bold text-lg truncate">
                {{ formatNumber(currentKilometrage !== null ? currentKilometrage : vehicule.mileage) }} km
              </p>
              <p v-if="lastUpdate" class="text-xs text-orange-600 mt-1 truncate">
                {{ formatDateShort(lastUpdate) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
  TruckIcon, 
  ArrowPathIcon,
  UserIcon,
  BuildingOfficeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: {
    type: Object,
    required: true
  },
  proprietaire: {
    type: Object,
    default: null
  },
  currentKilometrage: {
    type: [Number, String],
    default: null
  },
  lastUpdate: {
    type: String,
    default: null
  }
})

// Computed property pour afficher l'alias ou le nom complet
const displayName = computed(() => {
  if (props.vehicule.alias && props.vehicule.alias.trim() !== '') {
    return props.vehicule.alias
  }
  
  // Fallback: afficher l'année, la marque et le modèle
  const year = props.vehicule.year ? new Date(props.vehicule.year).getFullYear() : ''
  return `${year} ${props.vehicule.make} ${props.vehicule.model}`.trim()
})

const getVehiculeTypeLabel = (type) => {
  const labels = {
    'voiture': 'Voiture',
    'moto': 'Moto',
    'utilitaire': 'Utilitaire',
    'camion': 'Camion',
    'bus': 'Bus',
    'scooter': 'Scooter',
    'camionnette': 'Camionnette'
  }
  return labels[type] || type || 'N/A'
}

const getFuelTypeLabel = (type) => {
  const labels = {
    'essence': 'Essence',
    'diesel': 'Diesel',
    'hybride': 'Hybride',
    'electrique': 'Électrique',
    'gpl': 'GPL'
  }
  return labels[type] || type || 'N/A'
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('fr-FR').format(num || 0)
}

const formatDateShort = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}
</script>