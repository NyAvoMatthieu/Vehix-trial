<template>
  <AppLayout title="Nouvelle assurance">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nouvelle assurance véhicule
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
          <form @submit.prevent="submit" class="divide-y divide-gray-200">
            
            <!-- En-tête du formulaire -->
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
                    {{ form.policy_number || '___________' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Sélection du véhicule -->
            <div class="px-6 py-6 bg-gray-50">
              <div class="max-w-md">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Véhicule assuré *
                </label>
                <select
                  v-model="form.vehicule_id"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                >
                  <option value="">Sélectionner un véhicule...</option>
                  <option v-for="vehicule in vehicules" :key="vehicule.id" :value="vehicule.id">
                    {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }} - {{ vehicule.license_plate }}
                  </option>
                </select>
                <p v-if="form.errors.vehicule_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.vehicule_id }}
                </p>
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
                        Assureur *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Nom de la compagnie (ex: MAMA, Ny Havana, ARO)
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.assureur"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          required
                        />
                        <p v-if="form.errors.assureur" class="mt-1 text-xs text-red-600">
                          {{ form.errors.assureur }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Agence
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Nom ou code de l'agence / courtier
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.agence"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Police d'assurance *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Numéro unique du contrat
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.policy_number"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                          required
                        />
                        <p v-if="form.errors.policy_number" class="mt-1 text-xs text-red-600">
                          {{ form.errors.policy_number }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de délivrance
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Date d'émission du contrat/attestation
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.date_delivrance"
                          type="date"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de début *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Début de la couverture d'assurance
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.start_date"
                          type="date"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          required
                        />
                        <p v-if="form.errors.start_date" class="mt-1 text-xs text-red-600">
                          {{ form.errors.start_date }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de fin *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Date d'expiration de la couverture
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.end_date"
                          type="date"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          required
                        />
                        <p v-if="form.errors.end_date" class="mt-1 text-xs text-red-600">
                          {{ form.errors.end_date }}
                        </p>
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
                        CP *
                      </td>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300">
                        Corps propre / RC
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Prime de base obligatoire (dommages à autrui)
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model.number="form.prime_cp"
                          type="number"
                          step="0.01"
                          min="0"
                          class="block w-full text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-semibold"
                          required
                        />
                        <p v-if="form.errors.prime_cp" class="mt-1 text-xs text-red-600">
                          {{ form.errors.prime_cp }}
                        </p>
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
                      <td class="px-4 py-4">
                        <input
                          v-model.number="form.prime_de"
                          type="number"
                          step="0.01"
                          min="0"
                          class="block w-full text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
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
                      <td class="px-4 py-4">
                        <input
                          v-model.number="form.prime_ca"
                          type="number"
                          step="0.01"
                          min="0"
                          class="block w-full text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
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
                      <td class="px-4 py-4">
                        <input
                          v-model.number="form.prime_div"
                          type="number"
                          step="0.01"
                          min="0"
                          class="block w-full text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                      </td>
                    </tr>

                    <tr class="bg-green-50 border-t-2 border-green-500">
                      <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-900 text-lg border-r border-gray-300">
                        TOTAL
                      </td>
                      <td class="px-4 py-4">
                        <div class="text-right text-2xl font-bold text-green-600">
                          {{ formatCurrency(totalPrime) }}
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
                <input
                  v-model.number="form.deductible"
                  type="number"
                  step="0.01"
                  min="0"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Montant restant à charge en cas de sinistre
                </p>
              </div>
            </div>

            <!-- Détails de couverture -->
            <div class="px-6 py-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Détails de couverture / Notes
              </label>
              <textarea
                v-model="form.coverage_details"
                rows="4"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Détails complémentaires sur les garanties, exclusions, conditions particulières..."
              ></textarea>
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
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      📅 Date de signature
                    </label>
                    <input
                      v-model="form.date_signature"
                      type="date"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      📍 Lieu de signature
                    </label>
                    <input
                      v-model="form.lieu_signature"
                      type="text"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Ex: Antananarivo"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      🧾 Nom de l'agent / assureur
                    </label>
                    <input
                      v-model="form.agent_nom"
                      type="text"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Nom complet de l'agent"
                    />
                  </div>
                </div>

                <!-- Zone de signature visuelle -->
                <div class="space-y-4">
                  <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                    <div class="text-center">
                      <p class="text-sm font-medium text-gray-700 mb-2">✅ Signature de l'assuré</p>
                      <div class="h-24 flex items-center justify-center">
                        <span class="text-gray-400 text-sm italic">Zone réservée à la signature</span>
                      </div>
                    </div>
                  </div>

                  <div class="border-2 border-dashed border-indigo-300 rounded-lg p-6 bg-indigo-50">
                    <div class="text-center">
                      <p class="text-sm font-medium text-indigo-700 mb-2">🧾 Cachet et signature agent</p>
                      <div class="h-24 flex items-center justify-center">
                        <span class="text-indigo-400 text-sm italic">Zone réservée cachet + signature</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notes de signature -->
              <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Notes complémentaires
                </label>
                <textarea
                  v-model="form.notes_signature"
                  rows="3"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Remarques, conditions particulières de signature..."
                ></textarea>
              </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-6 bg-gray-50 flex justify-end space-x-3">
              <Link
                :href="route('assurances.index')"
                class="inline-flex justify-center py-2 px-6 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrement...
                </span>
                <span v-else>
                  Enregistrer le contrat
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  vehicules: Array,
  selectedVehiculeId: Number
})

const form = useForm({
  vehicule_id: props.selectedVehiculeId || '',
  company: '',
  assureur: '',
  agence: '',
  policy_number: '',
  start_date: '',
  end_date: '',
  date_delivrance: new Date().toISOString().split('T')[0],
  prime_cp: 0,
  prime_de: 0,
  prime_ca: 0,
  prime_div: 0,
  deductible: 0,
  coverage_details: '',
  lieu_signature: '',
  date_signature: new Date().toISOString().split('T')[0],
  agent_nom: '',
  notes_signature: ''
})

const totalPrime = computed(() => {
  return (form.prime_cp || 0) + (form.prime_de || 0) + (form.prime_ca || 0) + (form.prime_div || 0)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}

const submit = () => {
  form.post(route('assurances.store'))
}
</script>