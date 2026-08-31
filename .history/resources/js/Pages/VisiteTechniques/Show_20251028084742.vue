<template>
  <AppLayout title="Détails de la visite technique">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails de la visite technique
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('visite-techniques.index')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            :href="route('visite-techniques.edit', visite.id)"
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
        
        <!-- Bande d'état aptitude -->
        <div :class="aptitudeBannerClass" class="mb-6 rounded-lg p-6 border-l-4 shadow-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <component :is="aptitudeBannerIcon" class="h-10 w-10 mr-4" />
              <div>
                <h3 class="font-bold text-2xl">{{ aptitudeBannerTitle }}</h3>
                <p class="text-sm mt-1">{{ aptitudeBannerText }}</p>
              </div>
            </div>
            <div v-if="validityWarning" class="text-right">
              <span :class="validityWarning.class" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ validityWarning.text }}
              </span>
            </div>
          </div>
        </div>

        <!-- En-tête du document -->
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden mb-6">
          <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-blue-50 border-b-4 border-indigo-500">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">
                  Procès-Verbal de Visite Technique
                </h3>
                <p class="mt-1 text-sm text-gray-600">
                  Document officiel de contrôle technique
                </p>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-gray-700">N° PV:</p>
                <p class="text-lg font-mono font-semibold text-indigo-600">
                  {{ visite.numero_pv || '—' }}
                </p>
                <p class="text-sm text-gray-600 mt-2">
                  {{ visite.type_visite }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Colonne principale -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- 🚗 Véhicule -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-indigo-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <TruckIcon class="h-6 w-6 mr-2 text-indigo-600" />
                  Véhicule inspecté
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Marque & Modèle</p>
                    <p class="text-base font-semibold text-gray-900">{{ visite.vehicule.make }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Immatriculation</p>
                    <p class="text-base font-mono font-semibold text-gray-900">{{ visite.vehicule.model }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Kilométrage</p>
                    <p class="text-base font-semibold text-gray-900">
                      {{ visite.kilometrage ? formatNumber(visite.kilometrage) + ' km' : '—' }}
                    </p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Propriétaire</p>
                    <p class="text-base font-semibold text-gray-900">{{ proprietaire.Nom }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 📅 Informations de visite -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-green-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <CalendarIcon class="h-6 w-6 mr-2 text-green-600" />
                  Informations de visite
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700 w-2/5">Date de visite</td>
                        <td class="py-4 text-sm text-gray-900 font-medium">{{ formatDate(visite.date_visite) }}</td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Validité jusqu'au</td>
                        <td class="py-4 text-sm text-gray-900 font-medium">
                          {{ visite.validite ? formatDate(visite.validite) : '—' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Centre</td>
                        <td class="py-4 text-sm text-gray-900">{{ visite.centre || '—' }}</td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Opérateur</td>
                        <td class="py-4 text-sm text-gray-900">{{ visite.operateur || '—' }}</td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">VTA</td>
                        <td class="py-4 text-sm text-gray-900">{{ visite.vta || '—' }}</td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Vérificateur</td>
                        <td class="py-4 text-sm text-gray-900">{{ visite.verificateur || '—' }}</td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Type de visite</td>
                        <td class="py-4">
                          <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            {{ visite.type_visite }}
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <td class="py-4 text-sm font-semibold text-gray-700">Résultat</td>
                        <td class="py-4">
                          <span :class="visite.aptitude === 'APTE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ visite.aptitude === 'APTE' ? '🟢 APTE' : '🔴 INAPTE' }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- 📜 PV & Reçu -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-purple-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <DocumentTextIcon class="h-6 w-6 mr-2 text-purple-600" />
                  Documents officiels
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Numéro PV</p>
                    <p class="text-base font-mono font-semibold text-gray-900">{{ visite.numero_pv || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Date PV</p>
                    <p class="text-base font-semibold text-gray-900">
                      {{ visite.date_pv ? formatDate(visite.date_pv) : '—' }}
                    </p>
                  </div>
                  <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-500 mb-2">Numéro de reçu</p>
                    <p class="text-base font-mono font-semibold text-gray-900">{{ visite.numero_recu || '—' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 🎫 Carte violette & Licence -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 border-b-2 border-violet-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <CreditCardIcon class="h-6 w-6 mr-2 text-violet-600" />
                  Carte violette & Licence
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">N° Carte violette</p>
                    <p class="text-base font-semibold text-gray-900">{{ visite.numero_carte_violette || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Date carte</p>
                    <p class="text-base font-semibold text-gray-900">
                      {{ visite.date_carte_violette ? formatDate(visite.date_carte_violette) : '—' }}
                    </p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">N° Licence</p>
                    <p class="text-base font-semibold text-gray-900">{{ visite.numero_licence || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Date licence</p>
                    <p class="text-base font-semibold text-gray-900">
                      {{ visite.date_licence ? formatDate(visite.date_licence) : '—' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- 📋 Autres informations -->
            <div v-if="visite.patente || visite.ani || visite.observations" class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b-2 border-gray-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <ClipboardDocumentListIcon class="h-6 w-6 mr-2 text-gray-600" />
                  Autres informations
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="space-y-4">
                  <div v-if="visite.patente">
                    <p class="text-sm font-medium text-gray-500 mb-2">Patente</p>
                    <p class="text-base font-semibold text-gray-900">{{ visite.patente }}</p>
                  </div>
                  <div v-if="visite.ani">
                    <p class="text-sm font-medium text-gray-500 mb-2">ANI</p>
                    <p class="text-base font-semibold text-gray-900">{{ visite.ani }}</p>
                  </div>
                  <div v-if="visite.observations">
                    <p class="text-sm font-medium text-gray-500 mb-2">Observations</p>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                      <p class="text-gray-700 whitespace-pre-line">{{ visite.observations }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Colonne latérale -->
          <div class="space-y-6">
            
            <!-- 💰 Récapitulatif financier -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b-2 border-yellow-500">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                  <CurrencyDollarIcon class="h-6 w-6 mr-2 text-yellow-600" />
                  Récapitulatif financier
                </h3>
              </div>
              <div class="px-6 py-6">
                <div class="space-y-3">
                  <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Droit</span>
                    <span class="font-semibold text-gray-900">{{ formatCurrency(visite.droit) }}</span>
                  </div>
                  <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Frais PV</span>
                    <span class="font-semibold text-gray-900">{{ formatCurrency(visite.pv_frais) }}</span>
                  </div>
                  <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Frais carte</span>
                    <span class="font-semibold text-gray-900">{{ formatCurrency(visite.carte_frais) }}</span>
                  </div>
                  <div class="flex justify-between items-center pb-3 border-b-2 border-gray-300">
                    <span class="text-base font-bold text-gray-700">Total HT</span>
                    <span class="text-lg font-bold text-gray-900">{{ formatCurrency(visite.tht) }}</span>
                  </div>
                  <div class="flex justify-between items-center pb-3 border-b-2 border-gray-300">
                    <span class="text-sm font-medium text-gray-600">TVA</span>
                    <span class="font-semibold text-gray-900">{{ formatCurrency(visite.tva) }}</span>
                  </div>
                  <div class="bg-gradient-to-r from-green-50 to-emerald-50 -mx-6 px-6 py-4 border-t-2 border-green-500 mt-4">
                    <div class="flex justify-between items-center">
                      <span class="text-lg font-bold text-green-700">TOTAL TTC</span>
                      <span class="text-2xl font-bold text-green-600">{{ formatCurrency(visite.total) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ℹ️ Métadonnées -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gray-50 border-b-2 border-gray-300">
                <h3 class="text-base font-bold text-gray-900">Informations système</h3>
              </div>
              <div class="px-6 py-6">
                <div class="space-y-4 text-sm">
                  <div>
                    <p class="text-gray-500 font-medium mb-1">Enregistré le</p>
                    <p class="font-semibold text-gray-900">{{ formatDateTime(visite.created_at) }}</p>
                  </div>
                  <div v-if="visite.created_at !== visite.updated_at" class="pt-3 border-t border-gray-200">
                    <p class="text-gray-500 font-medium mb-1">Modifié le</p>
                    <p class="font-semibold text-gray-900">{{ formatDateTime(visite.updated_at) }}</p>
                  </div>
                  <div v-if="daysUntilExpiry !== null" class="pt-3 border-t border-gray-200">
                    <p class="text-gray-500 font-medium mb-1">Jours restants</p>
                    <p :class="daysUntilExpiry < 0 ? 'text-red-600' : daysUntilExpiry <= 30 ? 'text-yellow-600' : 'text-green-600'" 
                       class="font-bold text-xl">
                      {{ daysUntilExpiry < 0 ? 'Expiré' : `${daysUntilExpiry} jours` }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions supplémentaires -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
              <div class="px-6 py-4 bg-gray-50 border-b-2 border-gray-300">
                <h3 class="text-base font-bold text-gray-900">Actions</h3>
              </div>
              <div class="px-6 py-6 space-y-3">
                <button class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                  <DocumentArrowDownIcon class="h-5 w-5 mr-2" />
                  Télécharger PDF
                </button>
                <button class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                  <DocumentArrowDownIcon class="h-5 w-5 mr-2" />
                  Exporter Excel
                </button>
                <button class="w-full flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                  <PrinterIcon class="h-5 w-5 mr-2" />
                  Imprimer
                </button>
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
  TruckIcon,
  CalendarIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  CreditCardIcon,
  ClipboardDocumentListIcon,
  CheckCircleIcon,
  XCircleIcon,
  DocumentArrowDownIcon,
  PrinterIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  visite: Object
})

// Computed pour le nom du propriétaire
const proprietaireNom = computed(() => {
  if (!props.visite.proprietaire) return 'N/A'
  
  // Si c'est une personne physique
  if (props.visite.proprietaire.type === 'personnel') {
    return `${props.visite.proprietaire.nom} ${props.visite.proprietaire.prenom}`
  }
  
  // Si c'est une entreprise
  if (props.visite.proprietaire.type === 'entreprise') {
    return props.visite.proprietaire.raison_sociale || props.visite.proprietaire.nom_commercial
  }
  
  return 'N/A'
})

const daysUntilExpiry = computed(() => {
  if (!props.visite.validite) return null
  const now = new Date()
  const validite = new Date(props.visite.validite)
  return Math.ceil((validite - now) / (1000 * 60 * 60 * 24))
})

const validityWarning = computed(() => {
  if (daysUntilExpiry.value === null) return null
  
  if (daysUntilExpiry.value < 0) {
    return {
      class: 'bg-red-100 text-red-800',
      text: '❌ Expiré'
    }
  }
  if (daysUntilExpiry.value <= 30) {
    return {
      class: 'bg-yellow-100 text-yellow-800',
      text: `⚠️ Expire dans ${daysUntilExpiry.value}j`
    }
  }
  return {
    class: 'bg-green-100 text-green-800',
    text: '✅ Valide'
  }
})

const aptitudeBannerClass = computed(() => {
  return props.visite.aptitude === 'APTE'
    ? 'bg-green-50 border-green-500 text-green-800'
    : 'bg-red-50 border-red-500 text-red-800'
})

const aptitudeBannerIcon = computed(() => {
  return props.visite.aptitude === 'APTE' ? CheckCircleIcon : XCircleIcon
})

const aptitudeBannerTitle = computed(() => {
  return props.visite.aptitude === 'APTE' ? '🟢 Véhicule APTE' : '🔴 Véhicule INAPTE'
})

const aptitudeBannerText = computed(() => {
  return props.visite.aptitude === 'APTE'
    ? 'Le véhicule a passé avec succès la visite technique et est autorisé à circuler'
    : 'Le véhicule n\'a pas passé la visite technique. Des corrections sont nécessaires avant remise en circulation'
})

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (datetime) => {
  if (!datetime) return '—'
  return new Date(datetime).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return '0,00 Ar'
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}

const formatNumber = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value)
}
</script>