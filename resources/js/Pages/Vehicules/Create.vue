<template>
  <AppLayout title="Ajouter un véhicule">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter un nouveau véhicule
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-8">

            <!-- Sélection du propriétaire -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <UserCircleIcon class="h-6 w-6 text-indigo-600 mr-2" />
                <label class="text-base font-semibold text-gray-800">
                  Propriétaire du véhicule *
                </label>
              </div>

              <!-- Si aucun propriétaire n'existe -->
              <div v-if="!proprietaires || proprietaires.length === 0" class="space-y-3">
                <div class="flex items-center space-x-2 text-amber-600 bg-amber-50 p-3 rounded-lg">
                  <ExclamationTriangleIcon class="h-5 w-5" />
                  <span class="text-sm font-medium">
                    Vous devez d'abord créer un propriétaire
                  </span>
                </div>
                <Link
                  :href="route('proprietaires.create', { redirect_to_vehicule: true })"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  Créer un propriétaire
                </Link>
              </div>

              <!-- Si des propriétaires existent -->
              <div v-else class="space-y-3">
                <select
                  id="proprietaire_id"
                  v-model="form.proprietaire_id"
                  class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                  required
                >
                  <option value="" disabled selected>Sélectionner un propriétaire...</option>
                  <option
                    v-for="prop in proprietaires"
                    :key="prop.id"
                    :value="prop.id"
                  >
                    {{ prop.display_name }} ({{ prop.type === 'personnel' ? '👤 Personnel' : '🏢 Entreprise' }})
                  </option>
                </select>

                <div class="flex items-center justify-between">
                  <p v-if="form.errors.proprietaire_id" class="text-sm text-red-600">
                    {{ form.errors.proprietaire_id }}
                  </p>
                  <Link
                    :href="route('proprietaires.create', { redirect_to_vehicule: true })"
                    class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors duration-200"
                  >
                    <PlusIcon class="h-4 w-4 mr-1" />
                    Ajouter un nouveau propriétaire
                  </Link>
                </div>

                <!-- Affichage du propriétaire sélectionné -->
                <div v-if="selectedProprietaire" class="mt-3 p-4 bg-white border-2 border-indigo-200 rounded-lg shadow-sm">
                  <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-md">
                      <UserIcon v-if="selectedProprietaire.type === 'personnel'" class="h-6 w-6 text-white" />
                      <BuildingOfficeIcon v-else class="h-6 w-6 text-white" />
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-gray-900">
                        {{ selectedProprietaire.display_name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ selectedProprietaire.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Informations principales -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <TruckIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Informations principales</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Type de véhicule -->
                <div>
                  <label for="vehicule_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type de véhicule
                  </label>
                  <select
                    id="vehicule_type"
                    v-model="form.vehicule_type"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                    required
                  >
                    <option value="" disabled selected>Sélectionner...</option>
                    <option value="voiture">🚗 Voiture</option>
                    <option value="moto">🏍️ Moto</option>
                  </select>
                  <p v-if="form.errors.vehicule_type" class="mt-2 text-sm text-red-600">
                    {{ form.errors.vehicule_type }}
                  </p>
                </div>

                <!-- Marque -->
                <FormInput
                  id="make"
                  v-model="form.make"
                  label="Marque"
                  placeholder="Ex: Toyota, Peugeot..."
                  :error="form.errors.make"
                />

                <!-- Modèle -->
                <FormInput
                  id="model"
                  v-model="form.model"
                  label="Modèle"
                  placeholder="Ex: 308, Clio..."
                  :error="form.errors.model"
                />

                
                <!-- Alias -->
                <FormInput
                  id="alias"
                  v-model="form.alias"
                  label="Alias"
                  placeholder="Ex: Ma petite voiture..."
                  :error="form.errors.alias"
                  help="Surnom pour identifier facilement votre véhicule"
                />

                <!-- Plaque d'immatriculation -->
                <FormInput
                  id="license_plate"
                  v-model="form.license_plate"
                  label="Plaque d'immatriculation"
                  placeholder="Ex: 0000-XXX"

                  :error="form.errors.license_plate"
                />


                <!-- Couleur -->
                <FormInput
                  id="color"
                  v-model="form.color"
                  label="Couleur"
                  placeholder="Ex: Bleu, Rouge..."
                  :error="form.errors.color"
                />

                <!-- Type de carburant -->
                <div>
                  <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type de carburant
                  </label>
                  <select
                    id="fuel_type"
                    v-model="form.fuel_type"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">Sélectionner...</option>
                    <option value="essence">⛽ Essence</option>
                    <option value="diesel">🛢️ Diesel</option>
                    <option value="hybride">🔋 Hybride</option>
                    <option value="electrique">⚡ Électrique</option>
                    <option value="gpl">💨 GPL</option>
                  </select>
                  <p v-if="form.errors.fuel_type" class="mt-2 text-sm text-red-600">
                    {{ form.errors.fuel_type }}
                  </p>
                </div>

                <!-- Kilométrage -->
                <FormInput
                  id="mileage"
                  v-model.number="form.mileage"
                  type="number"
                  label="Kilométrage (km)"
                  placeholder="0"
                  min="0"
                  :error="form.errors.mileage"
                />

                
                <!-- Date 1ère mise en circulation -->
                <FormInput
                  id="year"
                  v-model="form.year"
                  type="date"
                  label="Date 1ère mise en circulation"
                  :error="form.errors.year"
                />


              </div>
            </div>

            <!-- Bouton pour afficher plus de détails -->
            <div class="flex justify-end">
              <button
                type="button"
                @click="showMoreDetails = !showMoreDetails"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border-2 border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all duration-200"
              >
                <CogIcon class="h-5 w-5 mr-2" />
                {{ showMoreDetails ? 'Masquer les détails' : 'Plus de détails' }}
                <svg
                  :class="{ 'rotate-180': showMoreDetails }"
                  class="ml-2 h-4 w-4 transition-transform duration-200"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </div>

            <!-- Détails supplémentaires (masqués par défaut) -->
            <transition name="slide-fade">
              <div v-show="showMoreDetails" class="space-y-8">

                <!-- Identification technique -->
                <div class="space-y-6">
                  <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                    <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
                    <h3 class="text-lg font-semibold text-gray-800">Identification technique</h3>
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <FormInput
                      id="vin"
                      v-model="form.vin"
                      label="Numéro de châssis (VIN)"
                      placeholder="Ex: 1HGBH41JXMN109186"
                      :error="form.errors.vin"
                      help="17 caractères"
                    />

                    <div>
                      <label for="categorie" class="block text-sm font-medium text-gray-700 mb-1">
                        Catégorie (Genre)
                      </label>
                      <select
                        id="categorie"
                        v-model="form.categorie"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option value="">Sélectionner...</option>
                        <option value="VP">VP - Véhicule Particulière</option>
                        <option value="CTTE">CTTE - Camionnette</option>
                        <option value="CAM">CAM - Camion</option>
                        <option value="TCP">TCP - Transport en commun</option>
                        <option value="MTL">MTL - Motocyclette légère</option>
                        <option value="MTT">MTT - Tricycle/Quadricycle</option>
                      </select>
                      <p v-if="form.errors.categorie" class="mt-2 text-sm text-red-600">
                        {{ form.errors.categorie }}
                      </p>
                    </div>

                    <FormInput
                      id="numero_serie_type"
                      v-model="form.numero_serie_type"
                      label="N° de série ou type"
                      placeholder="Ex: Type mine"
                      :error="form.errors.numero_serie_type"
                    />

                    <div>
                      <label for="carrosserie" class="block text-sm font-medium text-gray-700 mb-1">
                        Carrosserie
                      </label>
                      <select
                        id="carrosserie"
                        v-model="form.carrosserie"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option value="">Sélectionner...</option>
                        <option value="berline">Berline</option>
                        <option value="break">Break</option>
                        <option value="coupe">Coupé</option>
                        <option value="cabriolet">Cabriolet</option>
                        <option value="suv">SUV</option>
                        <option value="monospace">Monospace</option>
                        <option value="utilitaire">Utilitaire</option>
                      </select>
                      <p v-if="form.errors.carrosserie" class="mt-2 text-sm text-red-600">
                        {{ form.errors.carrosserie }}
                      </p>
                    </div>

                    <FormInput
                      id="numero_moteur"
                      v-model="form.numero_moteur"
                      label="Numéro moteur"
                      placeholder="Ex: ABC123456"
                      :error="form.errors.numero_moteur"
                    />
                  </div>
                </div>

                <!-- Caractéristiques moteur -->
                <div class="space-y-6">
                  <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                    <CogIcon class="h-5 w-5 text-indigo-600" />
                    <h3 class="text-lg font-semibold text-gray-800">Caractéristiques moteur</h3>
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <FormInput
                      id="cylindree"
                      v-model.number="form.cylindree"
                      type="number"
                      label="Cylindrée (cm³)"
                      placeholder="Ex: 1600"
                      min="0"
                      :error="form.errors.cylindree"
                    />

                    <FormInput
                      id="puissance_administrative"
                      v-model.number="form.puissance_administrative"
                      type="number"
                      label="Puissance administrative (CV)"
                      placeholder="Ex: 7"
                      min="0"
                      :error="form.errors.puissance_administrative"
                    />

                    <div>
                      <label for="average_consumption" class="block text-sm font-medium text-gray-700 mb-1">
                        Consommation moyenne (L/100km)
                      </label>
                      <input
                        id="average_consumption"
                        v-model.number="form.average_consumption"
                        type="number"
                        step="0.1"
                        min="0"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :placeholder="suggestedConsumption ? `Suggéré: ${suggestedConsumption}` : 'Ex: 7.5'"
                      />
                      <p v-if="suggestedConsumption" class="mt-1 text-xs text-gray-500">
                        💡 Suggestion pour {{ vehiculeTypeLabel }}: {{ suggestedConsumption }} L/100km
                      </p>
                      <p v-if="form.errors.average_consumption" class="mt-2 text-sm text-red-600">
                        {{ form.errors.average_consumption }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Poids et charge -->
                <div class="space-y-6">
                  <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                    <ScaleIcon class="h-5 w-5 text-indigo-600" />
                    <h3 class="text-lg font-semibold text-gray-800">Poids et charge</h3>
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <FormInput
                      id="places_assises"
                      v-model.number="form.places_assises"
                      type="number"
                      label="Nombre de places assises"
                      placeholder="Ex: 5"
                      min="1"
                      :error="form.errors.places_assises"
                    />

                    <FormInput
                      id="poids_vide"
                      v-model.number="form.poids_vide"
                      type="number"
                      label="Poids à vide (kg)"
                      placeholder="Ex: 1200"
                      step="0.01"
                      min="0"
                      :error="form.errors.poids_vide"
                      help="Poids du véhicule sans chargement"
                      @input="calculateChargeUtile"
                    />

                    <FormInput
                      id="poids_total_charge"
                      v-model.number="form.poids_total_charge"
                      type="number"
                      label="PTAC (kg)"
                      placeholder="Ex: 1800"
                      step="0.01"
                      min="0"
                      :error="form.errors.poids_total_charge"
                      help="Poids Total Autorisé en Charge"
                      @input="calculateChargeUtile"
                    />

                    <div>
                      <label for="charge_utile" class="block text-sm font-medium text-gray-700 mb-1">
                        Charge utile (kg)
                      </label>
                      <input
                        id="charge_utile"
                        v-model.number="form.charge_utile"
                        type="number"
                        step="0.01"
                        min="0"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50"
                        :class="{ 'bg-green-50': chargeUtileCalculated }"
                        placeholder="Calculé auto"
                        readonly
                      />
                      <p v-if="chargeUtileCalculated" class="mt-1 text-xs text-green-600">
                        ✓ Calculé automatiquement
                      </p>
                      <p v-if="form.errors.charge_utile" class="mt-2 text-sm text-red-600">
                        {{ form.errors.charge_utile }}
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </transition>


            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
              <Link
                :href="route('vehicules.index')"
                class="inline-flex justify-center py-2 px-6 border-2 border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="form.processing || (!proprietaires || proprietaires.length === 0)"
                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrement...
                </span>
                <span v-else class="flex items-center">
                  <CheckCircleIcon class="h-5 w-5 mr-2" />
                  Enregistrer
                </span>
              </button>
            </div>

            <!-- Information -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl p-4 shadow-sm">
              <div class="flex">
                <div class="flex-shrink-0">
                  <InformationCircleIcon class="h-6 w-6 text-blue-500" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-semibold text-blue-900">
                    ℹ️ Information importante
                  </h3>
                  <div class="mt-2 text-sm text-blue-800">
                                      
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Vos données sont strictement confidentielles dans Vehix</li>
                            <li>Votre véhicule sera soumis à validation par notre équipe avant d'être activé.
                                Assurez-vous que toutes les informations sont correctes.
                            </li>
                        </ul>
                    </div>

                  </div>
                </div>
              </div>
            </div>


          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'

import {
  InformationCircleIcon,
  PlusIcon,
  ExclamationTriangleIcon,
  UserIcon,
  BuildingOfficeIcon,
  UserCircleIcon,
  TruckIcon,
  DocumentTextIcon,
  CogIcon,
  ScaleIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  proprietaires: Array,
  selectedProprietaireId: Number
})

const defaultYear = new Date().getFullYear()
const defaultDate = `${defaultYear}-01-01`
const chargeUtileCalculated = ref(false)
const showMoreDetails = ref(false)

const form = useForm({
  proprietaire_id: props.selectedProprietaireId || '',
  make: '',
  model: '',
  alias: '',
  vehicule_type: '',
  year: defaultDate,
  license_plate: '',
  vin: '',
  color: '',
  fuel_type: '',
  mileage: 0,
  average_consumption: '',
  // Nouveaux champs
  categorie: '',
  numero_serie_type: '',
  carrosserie: '',
  numero_moteur: '',
  cylindree: null,
  puissance_administrative: null,
  places_assises: null,
  poids_total_charge: null,
  poids_vide: null,
  charge_utile: null
})

const selectedProprietaire = computed(() => {
  if (!form.proprietaire_id || !props.proprietaires) return null
  return props.proprietaires.find(p => p.id === form.proprietaire_id)
})

const calculateChargeUtile = () => {
  if (form.poids_total_charge && form.poids_vide) {
    form.charge_utile = (form.poids_total_charge - form.poids_vide).toFixed(2)
    chargeUtileCalculated.value = true
  } else {
    form.charge_utile = null
    chargeUtileCalculated.value = false
  }
}

// Valeurs par défaut de consommation selon le type de véhicule
const defaultConsumptions = {
  'voiture': 7.50,
  'moto': 4.00,
}

// Suggestion de consommation selon le type de véhicule
const suggestedConsumption = computed(() => {
  if (form.vehicule_type && !form.average_consumption) {
    return defaultConsumptions[form.vehicule_type] || 7.50
  }
  return null
})

const vehiculeTypeLabel = computed(() => {
  const labels = {
    'voiture': 'voiture',
    'moto': 'moto',
  }
  return labels[form.vehicule_type] || 'véhicule'
})

// Watch pour pré-remplir automatiquement la consommation
watch(() => form.vehicule_type, (newType) => {
  if (newType && !form.average_consumption) {
    form.average_consumption = defaultConsumptions[newType] || 7.50
  }
})

const submit = () => {
  form.post(route('vehicules.store'), {
    onSuccess: () => {
      // Redirection handled by the controller
    }
  })
}
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
