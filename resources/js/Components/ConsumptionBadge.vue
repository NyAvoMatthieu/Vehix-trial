<template>
  <div class="inline-flex items-center gap-2">
    <!-- Badge de consommation -->
    <div 
      :class="[
        'px-3 py-1.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all',
        getConsumptionClass()
      ]"
      :title="getTooltip()"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <span>{{ consumption }} L/100km</span>
    </div>

    <!-- Badge de précision -->
    <div 
      v-if="precision"
      :class="[
        'px-2 py-1 rounded-md text-xs font-medium border',
        getPrecisionClass()
      ]"
      :title="`Précision: ${precision}`"
    >
      {{ precision }}
    </div>

    <!-- Badge d'alerte de surconsommation -->
    <div 
      v-if="overconsumptionAlert && overconsumptionAlert.alert"
      class="px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-300 flex items-center gap-1 animate-pulse"
      :title="overconsumptionAlert.message"
    >
      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      ⚠️ +{{ overconsumptionAlert.excess_percent }}%
    </div>

    <!-- Lien vers l'analyse détaillée -->
    <Link 
      v-if="vehiculeId"
      :href="route('vehicules.consumption', vehiculeId)"
      class="text-indigo-600 hover:text-indigo-900 text-xs font-medium underline"
    >
      Voir l'analyse
    </Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  consumption: {
    type: [Number, String],
    required: true,
  },
  precision: {
    type: String,
    default: null,
  },
  method: {
    type: String,
    default: null,
  },
  overconsumptionAlert: {
    type: Object,
    default: null,
  },
  vehiculeId: {
    type: Number,
    default: null,
  },
})

const getConsumptionClass = () => {
  const consumption = parseFloat(props.consumption)
  
  if (isNaN(consumption)) {
    return 'bg-gray-100 text-gray-700 border border-gray-300'
  }

  // Alerte si surconsommation
  if (props.overconsumptionAlert && props.overconsumptionAlert.alert) {
    if (props.overconsumptionAlert.level === 'critique') {
      return 'bg-red-100 text-red-800 border border-red-400'
    }
    return 'bg-orange-100 text-orange-800 border border-orange-400'
  }

  // Consommation normale
  if (consumption <= 6) {
    return 'bg-green-100 text-green-800 border border-green-300'
  } else if (consumption <= 8) {
    return 'bg-blue-100 text-blue-800 border border-blue-300'
  } else if (consumption <= 10) {
    return 'bg-yellow-100 text-yellow-800 border border-yellow-300'
  } else {
    return 'bg-orange-100 text-orange-800 border border-orange-300'
  }
}

const getPrecisionClass = () => {
  const classes = {
    'élevée': 'bg-green-50 text-green-700 border-green-300',
    'bonne': 'bg-blue-50 text-blue-700 border-blue-300',
    'moyenne': 'bg-yellow-50 text-yellow-700 border-yellow-300',
    'faible': 'bg-orange-50 text-orange-700 border-orange-300',
    'très faible': 'bg-red-50 text-red-700 border-red-300',
  }
  return classes[props.precision] || 'bg-gray-50 text-gray-700 border-gray-300'
}

const getTooltip = () => {
  const methodLabels = {
    'enregistrée': 'Consommation enregistrée',
    'calcul_un_plein': 'Calculée avec 1 plein',
    'calcul_multiple_pleins': 'Calculée avec plusieurs pleins',
    'base_officielle': 'Donnée officielle (WLTP/NEDC)',
    'estimation_cylindree': 'Estimation basée sur la cylindrée',
    'valeur_defaut': 'Valeur par défaut',
  }
  
  const methodLabel = methodLabels[props.method] || 'Consommation estimée'
  
  return `${methodLabel} • Précision: ${props.precision || 'inconnue'}`
}
</script>