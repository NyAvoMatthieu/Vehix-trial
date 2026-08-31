<template>
  <AppLayout title="Analyse de Consommation">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Analyse de Consommation
        </h2>
        <Link
          :href="route('vehicules.show', vehicule.id)"
          class="text-sm text-gray-600 hover:text-gray-900"
        >
          Retour au véhicule
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold mb-2">Analyse de Consommation</h1>
              <p class="text-blue-100 text-lg">
                {{ vehicule.make }} {{ vehicule.model }} • {{ vehicule.license_plate }}
              </p>
            </div>
            <div class="bg-white/20 rounded-full p-4">
              <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Consommation principale -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200">
          <div class="flex flex-col md:flex-row items-start gap-6">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg flex-shrink-0 w-full md:w-auto">
              <div class="flex items-center gap-3 mb-3">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="text-sm opacity-90 uppercase tracking-wide">Consommation</p>
              </div>
              <p class="text-5xl font-bold">
                {{ analysis.consumption || 'N/A' }}
              </p>
              <p class="text-lg opacity-90 mt-1">L/100km</p>
            </div>

            <div class="flex-1 space-y-4">
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <h3 class="font-semibold text-lg text-gray-900">Méthode de calcul</h3>
                </div>
                <p class="text-gray-700">{{ getMethodLabel(analysis.method) }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ analysis.message }}</p>
              </div>

              <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-600">Précision :</span>
                <span :class="getPrecisionClass(analysis.precision)" class="px-3 py-1 rounded-full text-sm font-semibold border-2">
                  {{ analysis.precision }}
                </span>
              </div>

              <div v-if="analysis.warning" class="bg-amber-50 border-2 border-amber-300 rounded-lg p-3">
                <p class="text-sm text-amber-900 flex items-start gap-2">
                  <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  {{ analysis.warning }}
                </p>
              </div>

              <div v-if="analysis.details" class="bg-gray-50 rounded-lg p-3 border-2 border-gray-200">
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Détails du calcul :</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div v-for="(value, key) in analysis.details" :key="key">
                    <span class="text-gray-600">{{ formatKey(key) }} :</span>
                    <span class="font-semibold text-gray-900 ml-2">
                      {{ formatValue(value) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerte de surconsommation -->
        <div v-if="analysis.overconsumption_alert" class="transition-all duration-300">
          <div v-if="!analysis.overconsumption_alert.alert" class="bg-green-50 border-2 border-green-300 rounded-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <h4 class="font-semibold text-green-900 mb-1">
                  {{ analysis.overconsumption_alert.message }}
                </h4>
                <p class="text-sm text-green-700">
                  Attendue: {{ analysis.overconsumption_alert.expected }} L/100km • 
                  Réelle: {{ analysis.overconsumption_alert.actual }} L/100km
                </p>
              </div>
            </div>
          </div>

          <div v-else :class="getAlertClass(analysis.overconsumption_alert.level)">
            <div class="flex items-start gap-3 mb-3">
              <svg class="w-6 h-6 flex-shrink-0 mt-0.5" :class="getAlertIconClass(analysis.overconsumption_alert.level)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="flex-1">
                <h4 class="font-bold text-lg mb-1">{{ analysis.overconsumption_alert.message }}</h4>
                <div class="grid grid-cols-2 gap-4 mb-3">
                  <div class="bg-white/60 rounded-lg p-2">
                    <p class="text-xs font-medium opacity-70">Attendue</p>
                    <p class="text-lg font-bold">{{ analysis.overconsumption_alert.expected }} L/100km</p>
                  </div>
                  <div class="bg-white/60 rounded-lg p-2">
                    <p class="text-xs font-medium opacity-70">Réelle</p>
                    <p class="text-lg font-bold">{{ analysis.overconsumption_alert.actual }} L/100km</p>
                  </div>
                </div>
                
                <div v-if="analysis.overconsumption_alert.recommendations" class="bg-white/80 rounded-lg p-3">
                  <h5 class="font-semibold text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Recommandations :
                  </h5>
                  <ul class="space-y-1">
                    <li v-for="(rec, index) in analysis.overconsumption_alert.recommendations" :key="index" class="text-sm flex items-start gap-2">
                      <span class="text-lg leading-none">•</span>
                      <span>{{ rec }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques -->
        <div v-if="stats" class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-900">Statistiques</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-4 border-2 border-green-200">
              <p class="text-sm font-medium text-green-700 mb-1">Minimum</p>
              <p class="text-3xl font-bold text-green-900">{{ stats.min }}</p>
              <p class="text-sm text-green-600">L/100km</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-cyan-100 rounded-xl p-4 border-2 border-blue-200">
              <p class="text-sm font-medium text-blue-700 mb-1">Moyenne</p>
              <p class="text-3xl font-bold text-blue-900">{{ stats.avg }}</p>
              <p class="text-sm text-blue-600">L/100km</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-xl p-4 border-2 border-red-200">
              <p class="text-sm font-medium text-red-700 mb-1">Maximum</p>
              <p class="text-3xl font-bold text-red-900">{{ stats.max }}</p>
              <p class="text-sm text-red-600">L/100km</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-4 border-2 border-purple-200">
              <p class="text-sm font-medium text-purple-700 mb-1">Pleins analysés</p>
              <p class="text-3xl font-bold text-purple-900">{{ stats.data_points }}</p>
              <p class="text-sm text-purple-600">enregistrements</p>
            </div>
          </div>

          <div v-if="stats.data_points < 3" class="mt-4 bg-blue-50 border-2 border-blue-200 rounded-lg p-3">
            <p class="text-sm text-blue-900 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Ajoutez plus de pleins pour améliorer la précision des statistiques
            </p>
          </div>
        </div>

        <!-- Derniers pleins -->
        <div v-if="refuelings && refuelings.length > 0" class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200">
          <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Derniers ravitaillements
          </h3>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Station</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Litres</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Odomètre</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="refueling in refuelings" :key="refueling.id" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(refueling.ravitaillement_date) }}</td>
                  <td class="px-4 py-3 text-sm text-gray-900">{{ refueling.station_service }}</td>
                  <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ refueling.liters_purchased }} L</td>
                  <td class="px-4 py-3 text-sm text-gray-900">{{ refueling.odo_station }} km</td>
                  <td class="px-4 py-3 text-sm text-gray-900">{{ formatCurrency(refueling.amount_paid) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Conseils -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-6 border-2 border-indigo-200 shadow-md">
          <h3 class="text-lg font-bold text-indigo-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Comment améliorer la précision ?
          </h3>
          <ul class="space-y-2 text-sm text-indigo-800">
            <li class="flex items-start gap-2">
              <span class="text-indigo-600 font-bold">1.</span>
              <span>Enregistrez régulièrement vos pleins avec le kilométrage exact</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-indigo-600 font-bold">2.</span>
              <span>Faites des pleins complets pour des calculs plus précis</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-indigo-600 font-bold">3.</span>
              <span>Plus vous avez de données, plus l'estimation sera fiable</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-indigo-600 font-bold">4.</span>
              <span>Vérifiez régulièrement votre véhicule si la consommation augmente</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  vehicule: Object,
  analysis: Object,
  refuelings: Array,
  stats: Object,
})

const getMethodLabel = (method) => {
  const labels = {
    'enregistrée': 'Consommation enregistrée',
    'calcul_un_plein': 'Calculée (1 plein)',
    'calcul_multiple_pleins': 'Calculée (plusieurs pleins)',
    'base_officielle': 'Base de données officielle',
    'estimation_cylindree': 'Estimation (cylindrée)',
    'valeur_defaut': 'Valeur par défaut',
  }
  return labels[method] || method
}

const getPrecisionClass = (precision) => {
  const classes = {
    'élevée': 'bg-green-100 text-green-800 border-green-300',
    'bonne': 'bg-blue-100 text-blue-800 border-blue-300',
    'moyenne': 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'faible': 'bg-orange-100 text-orange-800 border-orange-300',
    'très faible': 'bg-red-100 text-red-800 border-red-300',
  }
  return classes[precision] || classes['faible']
}

const getAlertClass = (level) => {
  const classes = {
    'élevée': 'bg-orange-50 border-2 border-orange-300 rounded-xl p-4 shadow-md text-orange-900',
    'critique': 'bg-red-50 border-2 border-red-300 rounded-xl p-4 shadow-md text-red-900',
  }
  return classes[level] || classes['élevée']
}

const getAlertIconClass = (level) => {
  const classes = {
    'élevée': 'text-orange-600',
    'critique': 'text-red-600 animate-pulse',
  }
  return classes[level] || classes['élevée']
}

const formatKey = (key) => {
  return key.replace(/_/g, ' ')
}

const formatValue = (value) => {
  return typeof value === 'number' ? value.toFixed(2) : value
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0) + ' Ar'
}
</script>