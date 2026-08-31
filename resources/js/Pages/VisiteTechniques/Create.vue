<template>
  <AppLayout title="Nouvelle visite technique">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nouvelle visite technique
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        <!-- Bande d'état aptitude -->
        <div :class="aptitudeBannerClass" class="mb-6 rounded-lg p-4 border-l-4">
          <div class="flex items-center">
            <component :is="aptitudeBannerIcon" class="h-6 w-6 mr-3" />
            <div>
              <h3 class="font-semibold text-lg">{{ aptitudeBannerTitle }}</h3>
              <p class="text-sm">{{ aptitudeBannerText }}</p>
            </div>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">

          <!-- 🚗 Véhicule -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-indigo-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <TruckIcon class="h-6 w-6 mr-2 text-indigo-600" />
                Véhicule
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Alias & Marque</label>
                  <input
                    type="text"
                    :value="`${selectedVehicule.alias} - ${selectedVehicule.make}`"
                    class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                    disabled
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Immatriculation</label>
                  <input
                    type="text"
                    :value="selectedVehicule.license_plate"
                    class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 font-mono"
                    disabled
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Kilométrage</label>
                  <input
                    v-model.number="form.kilometrage"
                    type="number"
                    min="0"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="124500"
                  />
                  <p v-if="form.errors.kilometrage" class="mt-1 text-sm text-red-600">{{ form.errors.kilometrage }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- 📅 Visite -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-green-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <CalendarIcon class="h-6 w-6 mr-2 text-green-600" />
                Informations de visite
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date de visite</label>
                  <input
                    v-model="form.date_visite"
                    type="date"
                    required
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="form.errors.date_visite" class="mt-1 text-sm text-red-600">{{ form.errors.date_visite }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Validité jusqu'au</label>
                  <input
                    v-model="form.validite"
                    type="date"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p v-if="form.errors.validite" class="mt-1 text-sm text-red-600">{{ form.errors.validite }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Centre de visite</label>
                  <input
                    v-model="form.centre"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder=""
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Opérateur</label>
                  <input
                    v-model="form.operateur"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder=""
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">VTA</label>
                  <input
                    v-model="form.vta"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder=""
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Vérificateur</label>
                  <input
                    v-model="form.verificateur"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Type de visite</label>
                  <select
                    v-model="form.type_visite"
                    required
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="Initiale">Initiale</option>
                    <option value="Périodique">Périodique</option>
                    <option value="Contre-visite">Contre-visite</option>
                  </select>
                  <p v-if="form.errors.type_visite" class="mt-1 text-sm text-red-600">{{ form.errors.type_visite }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Aptitude</label>
                  <select
                    v-model="form.aptitude"
                    required
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="APTE">🟢 APTE</option>
                    <option value="INAPTE">🔴 INAPTE</option>
                  </select>
                  <p v-if="form.errors.aptitude" class="mt-1 text-sm text-red-600">{{ form.errors.aptitude }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- 📜 PV & Reçu -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-purple-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <DocumentTextIcon class="h-6 w-6 mr-2 text-purple-600" />
                PV & Reçu
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Numéro PV</label>
                  <input
                    v-model="form.numero_pv"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                    placeholder=""
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date PV</label>
                  <input
                    v-model="form.date_pv"
                    type="date"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de reçu</label>
                  <input
                    v-model="form.numero_recu"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                    placeholder=""
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- 💰 Paiement -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b-2 border-yellow-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <CurrencyDollarIcon class="h-6 w-6 mr-2 text-yellow-600" />
                Informations de paiement
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Droit (Ar)</label>
                  <input
                    v-model.number="form.droit"
                    type="number"
                    step="0.01"
                    min="0"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right"
                    placeholder="36000"
                    @input="calculateTotals"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Frais PV (Ar)</label>
                  <input
                    v-model.number="form.pv_frais"
                    type="number"
                    step="0.01"
                    min="0"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right"
                    placeholder="5000"
                    @input="calculateTotals"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Frais carte (Ar)</label>
                  <input
                    v-model.number="form.carte_frais"
                    type="number"
                    step="0.01"
                    min="0"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right"
                    placeholder="5000"
                    @input="calculateTotals"
                  />
                </div>
              </div>

              <div class="mt-6 border-t pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total HT (Ar)</label>
                    <div class="text-2xl font-bold text-gray-900">
                      {{ formatCurrency(totalHT) }}
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">TVA (Ar)</label>
                    <input
                      v-model.number="form.tva"
                      type="number"
                      step="0.01"
                      min="0"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right"
                      placeholder="9200"
                      @input="calculateTotal"
                    />
                  </div>
                  <div class="bg-green-50 p-4 rounded-lg border-2 border-green-500">
                    <label class="block text-sm font-medium text-green-700 mb-2">TOTAL TTC (Ar)</label>
                    <div class="text-2xl font-bold text-green-600">
                      {{ formatCurrency(totalTTC) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 🎫 Carte violette & Licence -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 border-b-2 border-violet-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <CreditCardIcon class="h-6 w-6 mr-2 text-violet-600" />
                Carte violette & Licence
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">N° Carte violette</label>
                  <input
                    v-model="form.numero_carte_violette"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date carte violette</label>
                  <input
                    v-model="form.date_carte_violette"
                    type="date"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">N° Licence</label>
                  <input
                    v-model="form.numero_licence"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Date licence</label>
                  <input
                    v-model="form.date_licence"
                    type="date"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- 📋 Autres informations -->
          <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b-2 border-gray-500">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <ClipboardDocumentListIcon class="h-6 w-6 mr-2 text-gray-600" />
                Autres informations
              </h3>
            </div>
            <div class="px-6 py-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Patente</label>
                  <input
                    v-model="form.patente"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">ANI</label>
                  <input
                    v-model="form.ani"
                    type="text"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Observations</label>
                  <textarea
                    v-model="form.observations"
                    rows="4"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Véhicule conforme — RAS"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 bg-white p-6 rounded-lg shadow">
            <Link
              :href="route('visite-techniques.index')"
              class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              🔙 Annuler
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
            >
              <span v-if="form.processing">Enregistrement...</span>
              <span v-else>💾 Enregistrer</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  TruckIcon,
  CalendarIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  CreditCardIcon,
  ClipboardDocumentListIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  selectedVehicule: Object
})

const form = useForm({
  vehicule_id: props.selectedVehicule.id,
  kilometrage: null,
  date_visite: new Date().toISOString().split('T')[0],
  validite: null,
  centre: '',
  operateur: '',
  vta: '',
  verificateur: '',
  type_visite: 'Périodique',
  aptitude: 'APTE',
  numero_pv: '',
  date_pv: null,
  numero_recu: '',
  droit: 0,
  pv_frais: 0,
  carte_frais: 0,
  tht: 0,
  tva: 0,
  total: 0,
  numero_carte_violette: '',
  date_carte_violette: null,
  numero_licence: '',
  date_licence: null,
  patente: '',
  ani: '',
  observations: ''
})

const totalHT = computed(() => {
  return (form.droit || 0) + (form.pv_frais || 0) + (form.carte_frais || 0)
})

const totalTTC = computed(() => {
  return totalHT.value + (form.tva || 0)
})

const calculateTotals = () => {
  form.tht = totalHT.value
}

const calculateTotal = () => {
  // Le total se calcule automatiquement via computed
}

watch(totalHT, (newVal) => {
  form.tht = newVal
})

watch(totalTTC, (newVal) => {
  form.total = newVal
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}

const aptitudeBannerClass = computed(() => {
  return form.aptitude === 'APTE'
    ? 'bg-green-50 border-green-500 text-green-800'
    : 'bg-red-50 border-red-500 text-red-800'
})

const aptitudeBannerIcon = computed(() => {
  return form.aptitude === 'APTE' ? CheckCircleIcon : XCircleIcon
})

const aptitudeBannerTitle = computed(() => {
  return form.aptitude === 'APTE' ? '🟢 Véhicule APTE' : '🔴 Véhicule INAPTE'
})

const aptitudeBannerText = computed(() => {
  return form.aptitude === 'APTE'
    ? 'Le véhicule a passé avec succès la visite technique'
    : 'Le véhicule n\'a pas passé la visite technique et nécessite des corrections'
})

const submit = () => {
  form.post(route('visite-techniques.store'))
}
</script>
