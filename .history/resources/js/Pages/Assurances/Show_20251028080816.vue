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
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        
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
          <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-blue-50 border-b-4 border-indigo-500">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">
                  Contrat d'Assurance Véhicule
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                  Document officiel d'enregistrement
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-gray-700">Référence:</p>
                <p class="text-lg font-mono font-semibold text-indigo-600">
                  {{ assurance.policy_number }}
                </p>
                <span :class="statusBadgeClass" class="mt-2 inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                  {{ statusText }}
                </span>
              </div>
            </div>
          </div>

          <!-- Véhicule assuré -->
          <div class="px-6 py-6 bg-gray-50">
            <div class="max-w-md">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Véhicule assuré
              </label>
              <div class="flex items-center p-4 bg-white border-2 border-indigo-200 rounded-lg">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ assurance.vehicule.make }} - {{ assurance.vehicule.model }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ assurance.vehicule.license_plate }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- 🏢 INFORMATIONS ADMINISTRATIVES DU CONTRAT -->
          <div class="px-6 py-6">
            <div class="mb-6">
              <h4 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="text-2xl mr-2">🏢</span>
                Informations Administratives du Contrat
              </h4>
              <div class="mt-1 h-1 w-24 bg-indigo-500 rounded"></div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 w-1/4">
                      Champ
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 w-1/2">
                      Description / Exemple
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/4">
                      Valeur
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Assureur
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Nom de la compagnie (ex: MAMA, Ny Havana, ARO,...)
                    </td>
                    <td class="px-4 py-4 text-gray-900 font-semibold">
                      {{ assurance.assureur }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Agence
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Nom ou code de l'agence / courtier
                    </td>
                    <td class="px-4 py-4 text-gray-900">
                      {{ assurance.agence || 'N/A' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Police d'assurance
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Numéro unique du contrat
                    </td>
                    <td class="px-4 py-4 text-gray-900 font-mono font-semibold">
                      {{ assurance.policy_number }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Date de délivrance
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Date d'émission du contrat/attestation
                    </td>
                    <td class="px-4 py-4 text-gray-900">
                      {{ formatDate(assurance.date_delivrance) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Date de début
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Début de la couverture d'assurance
                    </td>
                    <td class="px-4 py-4 text-gray-900">
                      {{ formatDate(assurance.start_date) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Date de fin
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Date d'expiration de la couverture
                    </td>
                    <td class="px-4 py-4 text-gray-900">
                      {{ formatDate(assurance.end_date) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                      Durée du contrat
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Nombre de jours de couverture
                    </td>
                    <td class="px-4 py-4 text-gray-900">
                      {{ durationDays }} jours
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 💰 INFORMATIONS SUR LES COTISATIONS -->
          <div class="px-6 py-6 bg-gray-50">
            <div class="mb-6">
              <h4 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="text-2xl mr-2">💰</span>
                Informations sur les Cotisations
              </h4>
              <div class="mt-1 h-1 w-24 bg-green-500 rounded"></div>
            </div>

            <!-- Champ Cotisation au-dessus du tableau -->
            <div class="mb-6 max-w-md">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Cotisation (Ar)
              </label>
              <div class="block w-full border-2 border-gray-300 rounded-md bg-white px-4 py-3 text-lg font-semibold text-gray-900">
                {{ formatCurrency(assurance.cotisation) }}
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Montant principal de la cotisation d'assurance
              </p>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-20 border-r border-gray-300">
                      Code
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-1/4 border-r border-gray-300">
                      Signification
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-1/3 border-r border-gray-300">
                      Détail
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase w-1/5">
                      Montant (Ar)
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-4 py-4 font-bold text-indigo-600 border-r border-gray-300 bg-indigo-50">
                      CP
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300">
                      Corps propre / RC
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Prime de base obligatoire (dommages à autrui)
                    </td>
                    <td class="px-4 py-4 text-right font-semibold text-gray-900">
                      {{ formatCurrency(assurance.prime_cp) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-bold text-blue-600 border-r border-gray-300 bg-blue-50">
                      DE
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300">
                      Dommages équipements
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Accessoires, équipements ou extensions assurés
                    </td>
                    <td class="px-4 py-4 text-right font-semibold text-gray-900">
                      {{ formatCurrency(assurance.prime_de) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-bold text-orange-600 border-r border-gray-300 bg-orange-50">
                      CA
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300">
                      Couverture accident
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Bris / incendie / vol (sinistres matériels)
                    </td>
                    <td class="px-4 py-4 text-right font-semibold text-gray-900">
                      {{ formatCurrency(assurance.prime_ca) }}
                    </td>
                  </tr>

                  <tr>
                    <td class="px-4 py-4 font-bold text-purple-600 border-r border-gray-300 bg-purple-50">
                      DIV
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300">
                      Divers / frais annexes
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                      Frais de dossier, taxes, contributions
                    </td>
                    <td class="px-4 py-4 text-right font-semibold text-gray-900">
                      {{ formatCurrency(assurance.prime_div) }}
                    </td>
                  </tr>

                  <tr class="bg-green-50 border-t-2 border-green-500">
                    <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-900 text-lg border-r border-gray-300">
                      TOTAL
                    </td>
                    <td class="px-4 py-4">
                      <div class="text-right text-2xl font-bold text-green-600">
                        {{ formatCurrency(assurance.prime_total) }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Franchise -->
            <div class="mt-6 max-w-md">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Franchise (Ar)
              </label>
              <div class="block w-full border-2 border-gray-300 rounded-md bg-white px-4 py-3 text-base font-semibold text-gray-900">
                {{ formatCurrency(assurance.deductible) }}
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Montant restant à charge en cas de sinistre
              </p>
            </div>
          </div>

          <!-- Détails de couverture -->
          <div v-if="assurance.coverage_details" class="px-6 py-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Détails de couverture / Notes
            </label>
            <div class="block w-full border border-gray-300 rounded-md bg-gray-50 px-4 py-3">
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
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  📅 Date de signature
                </label>
                <div class="block w-full border border-gray-300 rounded-md bg-white px-4 py-2 text-gray-900">
                  {{ formatDate(assurance.date_signature) }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  📍 Lieu de signature
                </label>
                <div class="block w-full border border-gray-300 rounded-md bg-white px-4 py-2 text-gray-900">
                  {{ assurance.lieu_signature || 'N/A' }}
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  🧾 Nom de l'agent / assureur
                </label>
                <div class="block w-full border border-gray-300 rounded-md bg-white px-4 py-2 text-gray-900">
                  {{ assurance.agent_nom || 'N/A' }}
                </div>
              </div>
            </div>

            <!-- Notes de signature -->
            <div v-if="assurance.notes_signature" class="mt-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Notes complémentaires
              </label>
              <div class="block w-full border border-gray-300 rounded-md bg-gray-50 px-4 py-3">
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
              <div>
                <p class="text-sm text-gray-500">Jours restants</p>
                <p class="text-base font-semibold" :class="daysUntilExpiry < 0 ? 'text-red-600' : daysUntilExpiry <= 30 ? 'text-yellow-600' : 'text-green-600'">
                  {{ daysUntilExpiry < 0 ? 'Expiré' : `${daysUntilExpiry} jours` }}
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
  if (value === null || value === undefined) return '0,00 Ar'
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}
</script>