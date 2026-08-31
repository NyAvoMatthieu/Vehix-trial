<template>
  <AppLayout title="Détails de l'assurance">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails du contrat d'assurance
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('assurances.index')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            :href="route('assurances.edit', assurance.id)"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Modifier
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Statut d'expiration -->
        <div v-if="expiryWarning" :class="expiryWarning.class" class="mb-6 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <component :is="expiryWarning.icon" class="h-5 w-5" :class="expiryWarning.iconColor" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium" :class="expiryWarning.textColor">
                {{ expiryWarning.title }}
              </h3>
              <div class="mt-2 text-sm" :class="expiryWarning.textColor">
                <p>{{ expiryWarning.message }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">

          <!-- En-tête du contrat -->
          <div class="px-6 py-8 bg-gradient-to-r from-indigo-50 to-blue-50 border-b-4 border-indigo-500">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-3xl font-bold text-gray-900">
                  Contrat d'Assurance Véhicule
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                  Document officiel d'assurance
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-gray-700">Police d'assurance:</p>
                <p class="text-2xl font-mono font-bold text-indigo-600">
                  {{ assurance.policy_number }}
                </p>
                <span :class="statusBadgeClass" class="mt-2 inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                  {{ statusText }}
                </span>
              </div>
            </div>
          </div>

          <!-- Informations du véhicule assuré -->
          <div class="px-6 py-6 bg-gray-50 border-b">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">🚗 Véhicule Assuré</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p class="text-sm text-gray-500">Marque et modèle</p>
                <p class="text-base font-semibold text-gray-900">
                  {{ assurance.vehicule.year }} {{ assurance.vehicule.make }} {{ assurance.vehicule.model }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Plaque d'immatriculation</p>
                <p class="text-base font-mono font-semibold text-gray-900">
                  {{ assurance.vehicule.license_plate }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">VIN</p>
                <p class="text-base font-mono font-semibold text-gray-900">
                  {{ assurance.vehicule.vin }}
                </p>
              </div>
            </div>
          </div>

          <!-- 🏢 INFORMATIONS ADMINISTRATIVES -->
          <div class="px-6 py-6 border-b">
            <div class="mb-6">
              <h4 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="text-2xl mr-2">🏢</span>
                Informations Administratives du Contrat
              </h4>
              <div class="mt-1 h-1 w-24 bg-indigo-500 rounded"></div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50 w-1/4">Assureur</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">
                      {{ formatCurrency(assurance.deductible) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Détails de couverture -->
          <div v-if="assurance.coverage_details" class="px-6 py-6 border-b">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">📋 Détails de Couverture</h4>
            <div class="prose max-w-none">
              <p class="text-gray-700 whitespace-pre-line">{{ assurance.coverage_details }}</p>
            </div>
          </div>

          <!-- 📝 SIGNATURES ET VALIDATION -->
          <div class="px-6 py-6 bg-gradient-to-b from-gray-50 to-white">
            <div class="mb-6">
              <h4 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="text-2xl mr-2">📝</span>
                Signatures et Validation
              </h4>
              <div class="mt-1 h-1 w-24 bg-indigo-500 rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Informations de signature -->
              <div class="space-y-4">
                <div v-if="assurance.date_signature">
                  <p class="text-sm font-medium text-gray-500">📅 Date de signature</p>
                  <p class="text-base font-semibold text-gray-900">
                    {{ formatDate(assurance.date_signature) }}
                  </p>
                </div>

                <div v-if="assurance.lieu_signature">
                  <p class="text-sm font-medium text-gray-500">📍 Lieu de signature</p>
                  <p class="text-base font-semibold text-gray-900">
                    {{ assurance.lieu_signature }}
                  </p>
                </div>

                <div v-if="assurance.agent_nom">
                  <p class="text-sm font-medium text-gray-500">🧾 Agent / Assureur</p>
                  <p class="text-base font-semibold text-gray-900">
                    {{ assurance.agent_nom }}
                  </p>
                </div>
              </div>

              <!-- Zones de signature visuelles -->
              <div class="space-y-4">
                <div class="border-2 border-gray-300 rounded-lg p-6 bg-white">
                  <div class="text-center">
                    <p class="text-sm font-semibold text-gray-700 mb-2">✅ Signature de l'assuré</p>
                    <div class="h-24 flex items-center justify-center border-t-2 border-gray-300 mt-4">
                      <span class="text-gray-400 text-sm italic">Signé par {{ assurance.user.name }}</span>
                    </div>
                  </div>
                </div>

                <div class="border-2 border-indigo-300 rounded-lg p-6 bg-indigo-50">
                  <div class="text-center">
                    <p class="text-sm font-semibold text-indigo-700 mb-2">🧾 Cachet et signature agent</p>
                    <div class="h-24 flex items-center justify-center border-t-2 border-indigo-300 mt-4">
                      <span class="text-indigo-500 text-sm italic">
                        {{ assurance.agent_nom || 'Agent assureur' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes de signature -->
            <div v-if="assurance.notes_signature" class="mt-6">
              <p class="text-sm font-medium text-gray-500 mb-2">Notes complémentaires</p>
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-gray-700 whitespace-pre-line">{{ assurance.notes_signature }}</p>
              </div>
            </div>
          </div>

          <!-- Informations supplémentaires -->
          <div class="px-6 py-6 bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Informations Supplémentaires</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Propriétaire</p>
                <p class="text-base font-semibold text-gray-900">
                  {{ assurance.user.name }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Date d'enregistrement</p>
                <p class="text-base font-semibold text-gray-900">
                  {{ formatDate(assurance.created_at) }}
                </p>
              </div>
              <div v-if="assurance.created_at !== assurance.updated_at">
                <p class="text-sm text-gray-500">Dernière modification</p>
                <p class="text-base font-semibold text-gray-900">
                  {{ formatDate(assurance.updated_at) }}
                </p>
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
import {
  ArrowLeftIcon,
  PencilIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  assurance: Object
})

const durationDays = computed(() => {
  const start = new Date(props.assurance.start_date)
  const end = new Date(props.assurance.end_date)
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24))
})

const daysUntilExpiry = computed(() => {
  const now = new Date()
  const endDate = new Date(props.assurance.end_date)
  return Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))
})

const statusText = computed(() => {
  if (daysUntilExpiry.value < 0) return 'Expiré'
  if (daysUntilExpiry.value <= 30) return `Expire dans ${daysUntilExpiry.value} jour(s)`
  return 'Actif'
})

const statusBadgeClass = computed(() => {
  if (daysUntilExpiry.value < 0) return 'bg-red-100 text-red-800'
  if (daysUntilExpiry.value <= 30) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
})

const expiryWarning = computed(() => {
  if (daysUntilExpiry.value < 0) {
    return {
      class: 'bg-red-50 border border-red-200',
      icon: XCircleIcon,
      iconColor: 'text-red-400',
      textColor: 'text-red-800',
      title: '⚠️ Contrat expiré',
      message: `Ce contrat d'assurance a expiré le ${formatDate(props.assurance.end_date)}. Veuillez renouveler votre assurance au plus vite.`
    }
  }

  if (daysUntilExpiry.value <= 30) {
    return {
      class: 'bg-yellow-50 border border-yellow-200',
      icon: ExclamationTriangleIcon,
      iconColor: 'text-yellow-400',
      textColor: 'text-yellow-800',
      title: '⏰ Renouvellement proche',
      message: `Votre contrat d'assurance expire dans ${daysUntilExpiry.value} jour(s). Pensez à le renouveler.`
    }
  }

  if (daysUntilExpiry.value <= 60) {
    return {
      class: 'bg-blue-50 border border-blue-200',
      icon: CheckCircleIcon,
      iconColor: 'text-blue-400',
      textColor: 'text-blue-800',
      title: '✓ Contrat actif',
      message: `Votre contrat est valide jusqu'au ${formatDate(props.assurance.end_date)}.`
    }
  }

  return null
})

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}
</script>gray-900">{{ assurance.assureur }}</td>
                  </tr>
                  <tr v-if="assurance.agence">
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Agence</td>
                    <td class="px-6 py-4 text-gray-900">{{ assurance.agence }}</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Police d'assurance</td>
                    <td class="px-6 py-4 text-gray-900 font-mono">{{ assurance.policy_number }}</td>
                  </tr>
                  <tr v-if="assurance.date_delivrance">
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Date de délivrance</td>
                    <td class="px-6 py-4 text-gray-900">{{ formatDate(assurance.date_delivrance) }}</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Date de début</td>
                    <td class="px-6 py-4 text-gray-900">{{ formatDate(assurance.start_date) }}</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Date de fin</td>
                    <td class="px-6 py-4 text-gray-900">{{ formatDate(assurance.end_date) }}</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 font-semibold text-gray-700 bg-gray-50">Durée du contrat</td>
                    <td class="px-6 py-4 text-gray-900">{{ durationDays }} jours</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 💰 COTISATIONS -->
          <div class="px-6 py-6 border-b bg-gray-50">
            <div class="mb-6">
              <h4 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="text-2xl mr-2">💰</span>
                Détail des Cotisations
              </h4>
              <div class="mt-1 h-1 w-24 bg-green-500 rounded"></div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Signification</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Détail</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Montant</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr class="bg-indigo-50">
                    <td class="px-6 py-4 font-bold text-indigo-600">CP</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Corps propre / RC</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Prime de base obligatoire</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatCurrency(assurance.prime_cp) }}</td>
                  </tr>
                  <tr v-if="assurance.prime_de > 0" class="bg-blue-50">
                    <td class="px-6 py-4 font-bold text-blue-600">DE</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Dommages équipements</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Accessoires et extensions</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatCurrency(assurance.prime_de) }}</td>
                  </tr>
                  <tr v-if="assurance.prime_ca > 0" class="bg-orange-50">
                    <td class="px-6 py-4 font-bold text-orange-600">CA</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Couverture accident</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Bris / incendie / vol</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatCurrency(assurance.prime_ca) }}</td>
                  </tr>
                  <tr v-if="assurance.prime_div > 0" class="bg-purple-50">
                    <td class="px-6 py-4 font-bold text-purple-600">DIV</td>
                    <td class="px-6 py-4 font-medium text-gray-900">Divers / frais annexes</td>
                    <td class="px-6 py-4 text-sm text-gray-600">Frais de dossier, taxes</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatCurrency(assurance.prime_div) }}</td>
                  </tr>
                  <tr class="bg-green-100 border-t-2 border-green-500">
                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 text-lg">TOTAL COTISATION</td>
                    <td class="px-6 py-4 text-right text-2xl font-bold text-green-600">
                      {{ formatCurrency(assurance.prime_total) }}
                    </td>
                  </tr>
                  <tr v-if="assurance.deductible > 0">
                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">Franchise</td>
                    <td class="px-6 py-4 text-
