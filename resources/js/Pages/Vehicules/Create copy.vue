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

            <!-- Informations générales -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <TruckIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Informations générales</h3>
              </div>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

                <div>
                  <label for="vehicule_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type véhicule
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
                    <!--<option value="utilitaire">🚚 Véhicule utilitaire</option>
                    <option value="camion">🚛 Camion</option>
                    <option value="bus">🚌 Bus</option>
                    <option value="autre">🔧 Autre</option>-->
                  </select>
                  <p v-if="form.errors.vehicule_type" class="mt-2 text-sm text-red-600">
                    {{ form.errors.vehicule_type }}
                  </p>
                </div>



                <FormInput
                  id="make"
                  v-model="form.make"
                  label="Marque"
                  placeholder="Ex: Toyota, Peugeot..."
                  :error="form.errors.make"
                />

                <FormInput
                  id="model"
                  v-model="form.model"
                  label="Modèle"
                  placeholder="Ex: 308, Clio..."
                  required
                  :error="form.errors.model"
                />

                

                <FormInput
                  id="year"
                  v-model="form.year"
                  type="date"
                  label="Date 1ère mise en circulation *"
                  required
                  :error="form.errors.year"
                />

                <FormInput
                  id="license_plate"
                  v-model="form.license_plate"
                  label="Plaque d'immatriculation *"
                  placeholder="Ex: 0000-XXX"
                  required
                  :error="form.errors.license_plate"
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
                    id="alias"
                    v-model="form.alias"
                    label="Alias"
                    placeholder="Ex: Ma petite voiture, Le camion rouge..."
                    :error="form.errors.alias"
                    help="Donnez un surnom pour identifier facilement votre véhicule"
                    />

              </div>
            </div>

            <!-- Identification technique -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <DocumentTextIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Identification technique</h3>
              </div>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <FormInput
                    id="vin"
                    v-model="form.vin"
                    label="Numéro VIN (Châssis)"
                    placeholder="17 caractères alphanumériques"
                    maxlength="17"
                    :error="form.errors.vin"
                    help="Le numéro VIN se trouve généralement sur le tableau de bord côté conducteur"
                  />
                </div>

                <FormInput
                  id="numero_serie_type"
                  v-model="form.numero_serie_type"
                  label="N° dans la série du type"
                  placeholder="Ex: 12345"
                  :error="form.errors.numero_serie_type"
                />

                <FormInput
                  id="numero_moteur"
                  v-model="form.numero_moteur"
                  label="Numéro moteur"
                  placeholder="Ex: ABC123456"
                  :error="form.errors.numero_moteur"
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
                    <option value="pickup">Pick-up</option>
                  </select>
                  <p v-if="form.errors.carrosserie" class="mt-2 text-sm text-red-600">
                    {{ form.errors.carrosserie }}
                  </p>
                </div>

                <FormInput
                  id="color"
                  v-model="form.color"
                  label="Couleur"
                  placeholder="Ex: Blanc, Noir..."
                  :error="form.errors.color"
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
                <div>
                  <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type de carburant
                  </label>
                  <select
                    id="fuel_type"
                    v-model="form.fuel_type"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="" disabled selected>Sélectionner...</option>
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

                 <div>
                  <label for="average_consumption" class="block text-sm font-medium text-gray-700 mb-1">
                    Consommation moyenne (L/100km)
                  </label>
                  <input
                    id="average_consumption"
                    v-model="form.average_consumption"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    :class="{ 'border-green-300 bg-green-50': suggestedConsumption }"
                    :placeholder="`Ex: ${suggestedConsumption || '7.50'}`"
                  />
                  <p v-if="form.errors.average_consumption" class="mt-2 text-sm text-red-600">
                    {{ form.errors.average_consumption }}
                  </p>
                  <p v-if="suggestedConsumption" class="mt-1 text-xs text-green-600">
                    💡 Suggestion pour {{ vehiculeTypeLabel }}: {{ suggestedConsumption }} L/100km
                  </p>
                  <p v-else class="mt-1 text-xs text-gray-500">
                    💡 Consommation moyenne de votre véhicule en litres pour 100 km
                  </p>
                </div>

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
                  label="Puissance admin. (CV)"
                  placeholder="Ex: 7"
                  min="0"
                  :error="form.errors.puissance_administrative"
                />

                <FormInput
                  id="mileage"
                  v-model.number="form.mileage"
                  type="number"
                  label="Kilométrage actuel (km)"
                  placeholder="Ex: 50000"
                  min="0"
                  :error="form.errors.mileage"
                />
              </div>
            </div>

            <!-- Capacités et poids -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <ScaleIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Capacités et poids</h3>
              </div>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <FormInput
                  id="places_assises"
                  v-model.number="form.places_assises"
                  type="number"
                  label="Places assises"
                  placeholder="Ex: 5"
                  min="1"
                  :error="form.errors.places_assises"
                />

                <FormInput
                  id="poids_vide"
                  v-model.number="form.poids_vide"
                  type="number"
                  step="0.01"
                  label="Poids à vide (kg)"
                  placeholder="Ex: 1200"
                  min="0"
                  :error="form.errors.poids_vide"
                  @input="calculateChargeUtile"
                />

                <FormInput
                  id="poids_total_charge"
                  v-model.number="form.poids_total_charge"
                  type="number"
                  step="0.01"
                  label="PTAC (kg)"
                  placeholder="Ex: 1700"
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
                    <p>
                      Votre véhicule sera soumis à validation par notre équipe avant d'être activé.
                      Assurez-vous que toutes les informations sont correctes.
                    </p>
                  </div>
                </div>
              </div>
            </div>

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
// 🚀 Valeurs par défaut de consommation selon le type de véhicule
const defaultConsumptions = {
  'voiture': 7.50,
  'moto': 4.00,
  /*'utilitaire': 10.00,
  'camion': 18.00,
  'bus': 25.00,
  'autre': 8.00*/
}

// 🚀 Suggestion de consommation selon le type de véhicule
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
    /*'utilitaire': 'utilitaire',
    'camion': 'camion',
    'bus': 'bus',
    'autre': 'autre'*/
  }
  return labels[form.vehicule_type] || 'véhicule'
})

// 🚀 Watch pour pré-remplir automatiquement la consommation
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