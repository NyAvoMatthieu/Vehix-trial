<template>
  <AppLayout title="Modifier le véhicule">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Modifier le véhicule
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Status Warning -->
        <div v-if="vehicule.status !== 'valide'" class="mb-6">
          <div :class="getAlertClass(vehicule.status)" class="rounded-xl p-4 shadow-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <component :is="getStatusIcon(vehicule.status)" class="h-6 w-6" :class="getAlertIconColor(vehicule.status)" />
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-semibold" :class="getAlertTextColor(vehicule.status)">
                  {{ getStatusTitle(vehicule.status) }}
                </h3>
                <div class="mt-2 text-sm" :class="getAlertTextColor(vehicule.status)">
                  <p>{{ getStatusMessage(vehicule.status) }}</p>
                  <div v-if="vehicule.validation_notes" class="mt-3 p-3 bg-white rounded-lg border-2 shadow-sm">
                    <p class="font-semibold text-gray-900">📝 Notes du validateur:</p>
                    <p class="mt-1 text-gray-700 whitespace-pre-line">{{ vehicule.validation_notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-8">
            
            <!-- Informations générales -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <TruckIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Informations générales</h3>
              </div>

              <div>
                <label for="proprietaire_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Propriétaire
                </label>
                <select
                    id="proprietaire_id"
                    v-model="form.proprietaire_id"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                    required
                >
                    <option value="" disabled>Sélectionner un propriétaire...</option>
                    <option
                    v-for="proprietaire in proprietaires"
                    :key="proprietaire.id"
                    :value="proprietaire.id"
                    >
                    {{ proprietaire.display_name }}
                    </option>
                </select>
                <p v-if="form.errors.proprietaire_id" class="mt-2 text-sm text-red-600">
                    {{ form.errors.proprietaire_id }}
                </p>
              </div>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <FormInput
                  id="make"
                  v-model="form.make"
                  label="Marque"
                  placeholder="Ex: Peugeot, Renault..."
                  :error="form.errors.make"
                />

                <FormInput
                  id="model"
                  v-model="form.model"
                  label="Modèle"
                  placeholder="Ex: 308, Clio..."
                  :error="form.errors.model"
                />

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
                    <option value="" disabled>Sélectionner...</option>
                    <option value="voiture">🚗 Voiture</option>
                    <option value="moto">🏍️ Moto</option>
                  </select>
                  <p v-if="form.errors.vehicule_type" class="mt-2 text-sm text-red-600">
                    {{ form.errors.vehicule_type }}
                  </p>
                </div>

                <FormInput
                  id="year"
                  v-model="form.year"
                  type="date"
                  label="Date 1ère mise en circulation"
                  :min="1900"
                  :max="new Date().getFullYear() + 1"
                  :error="form.errors.year"
                />

                <FormInput
                    id="alias"
                    v-model="form.alias"
                    label="Alias"
                    placeholder=""
                    :error="form.errors.alias"
                    />

                <FormInput
                  id="license_plate"
                  v-model="form.license_plate"
                  label="Plaque d'immatriculation"
                  placeholder="Ex: 0000-XXX-0000"
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
                    <option value="VP">VP - Voiture Particulière</option>
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
                  <div v-if="vinChanged" class="mt-2 p-3 bg-yellow-50 border-2 border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 flex items-center">
                      <ExclamationTriangleIcon class="h-5 w-5 mr-2" />
                      ⚠️ Attention: La modification du VIN déclenchera une nouvelle vérification de doublon.
                    </p>
                  </div>
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
                    <option value="" disabled>Sélectionner...</option>
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

                <FormInput
                id="average_consumption"
                v-model="form.average_consumption"
                type="number"
                step="0.01"
                min="0"
                max="99.99"
                label="Consommation moyenne (L/100km)"
                placeholder="Ex: 7.5"
                :error="form.errors.average_consumption"
                help="Laissez vide pour un calcul automatique basé sur les ravitaillements"
                />

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
                    <p v-if="needsRevalidation">
                      Après modification, votre véhicule sera soumis à nouveau pour validation.
                    </p>
                    <p v-else>
                      Les modifications seront enregistrées et votre véhicule restera dans son état actuel.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
              <Link
                :href="route('vehicules.show', vehicule.id)"
                class="inline-flex justify-center py-2 px-6 border-2 border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
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
                  Enregistrer les modifications
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
import { computed, ref, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'
import {
  InformationCircleIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentDuplicateIcon,
  TruckIcon,
  DocumentTextIcon,
  CogIcon,
  ScaleIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: Object,
  proprietaires: Array
})

const chargeUtileCalculated = ref(false)

const form = useForm({
  proprietaire_id: props.vehicule.proprietaire_id,
  make: props.vehicule.make,
  model: props.vehicule.model,
  alias: props.vehicule.alias,
  vehicule_type: props.vehicule.vehicule_type,
  year: props.vehicule.year,
  license_plate: props.vehicule.license_plate,
  vin: props.vehicule.vin,
  color: props.vehicule.color,
  fuel_type: props.vehicule.fuel_type,
  mileage: props.vehicule.mileage,
  average_consumption: props.vehicule.average_consumption,
  // Nouveaux champs
  categorie: props.vehicule.categorie,
  numero_serie_type: props.vehicule.numero_serie_type,
  carrosserie: props.vehicule.carrosserie,
  numero_moteur: props.vehicule.numero_moteur,
  cylindree: props.vehicule.cylindree,
  puissance_administrative: props.vehicule.puissance_administrative,
  places_assises: props.vehicule.places_assises,
  poids_total_charge: props.vehicule.poids_total_charge,
  poids_vide: props.vehicule.poids_vide,
  charge_utile: props.vehicule.charge_utile
})

const vinChanged = computed(() => {
  return form.vin !== props.vehicule.vin
})

const needsRevalidation = computed(() => {
  const statusValue = getStatusValue(props.vehicule.status)
  return ['refuse', 'a_corriger', 'doublon'].includes(statusValue)
})

const getStatusValue = (status) => {
  return typeof status === 'object' ? status.value : status
}

const getStatusIcon = (status) => {
  const statusValue = getStatusValue(status)
  const icons = {
    'en_attente': ClockIcon,
    'refuse': XCircleIcon,
    'a_corriger': ExclamationTriangleIcon,
    'doublon': DocumentDuplicateIcon
  }
  return icons[statusValue] || ClockIcon
}

const getAlertClass = (status) => {
  const statusValue = getStatusValue(status)
  const classes = {
    'en_attente': 'bg-yellow-50 border-2 border-yellow-200',
    'refuse': 'bg-red-50 border-2 border-red-200',
    'a_corriger': 'bg-orange-50 border-2 border-orange-200',
    'doublon': 'bg-purple-50 border-2 border-purple-200'
  }
  return classes[statusValue] || 'bg-gray-50 border-2 border-gray-200'
}

const getAlertIconColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-500',
    'refuse': 'text-red-500',
    'a_corriger': 'text-orange-500',
    'doublon': 'text-purple-500'
  }
  return colors[statusValue] || 'text-gray-400'
}

const getAlertTextColor = (status) => {
  const statusValue = getStatusValue(status)
  const colors = {
    'en_attente': 'text-yellow-900',
    'refuse': 'text-red-900',
    'a_corriger': 'text-orange-900',
    'doublon': 'text-purple-900'
  }
  return colors[statusValue] || 'text-gray-800'
}

const getStatusTitle = (status) => {
  const statusValue = getStatusValue(status)
  const titles = {
    'en_attente': '⏳ Véhicule en attente de validation',
    'refuse': '❌ Véhicule refusé',
    'a_corriger': '✏️ Corrections requises',
    'doublon': '📋 Doublon détecté'
  }
  return titles[statusValue] || 'Information'
}

const getStatusMessage = (status) => {
  const statusValue = getStatusValue(status)
  const messages = {
    'en_attente': 'Vous pouvez modifier les informations de votre véhicule.',
    'refuse': 'Corrigez les informations selon les notes du validateur ci-dessous.',
    'a_corriger': 'Veuillez apporter les corrections demandées par le validateur.',
    'doublon': 'Vérifiez le VIN et corrigez-le si nécessaire. Si ce véhicule est réellement un doublon, vous pouvez le supprimer.'
  }
  return messages[statusValue] || ''
}

const calculateChargeUtile = () => {
  if (form.poids_total_charge && form.poids_vide) {
    form.charge_utile = (form.poids_total_charge - form.poids_vide).toFixed(2)
    chargeUtileCalculated.value = true
  } else {
    form.charge_utile = null
    chargeUtileCalculated.value = false
  }
}

onMounted(() => {
  // Vérifier si la charge utile existe déjà
  if (form.poids_total_charge && form.poids_vide && form.charge_utile) {
    chargeUtileCalculated.value = true
  }
})

const submit = () => {
  form.put(route('vehicules.update', props.vehicule.id), {
    onSuccess: () => {
      // Redirection handled by controller
    }
  })
}
</script>