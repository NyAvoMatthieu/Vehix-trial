<template>
  <AppLayout title="Ajouter un propriétaire">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Ajouter un nouveau propriétaire
        </h2>
        
        <!-- Indication de redirection -->
        <div v-if="redirectToVehicule" class="flex items-center space-x-2 text-sm text-indigo-600 bg-indigo-50 px-4 py-2 rounded-md">
          <ArrowRightIcon class="h-4 w-4" />
          <span>Ensuite: Création du véhicule</span>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Message d'information si redirection -->
        <div v-if="redirectToVehicule" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Création d'un véhicule en cours
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <p>
                  Vous devez d'abord créer un propriétaire avant de pouvoir enregistrer votre véhicule.
                  Après avoir enregistré ce propriétaire, vous serez redirigé vers le formulaire de création de véhicule.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-8">
            
            <!-- Champ caché pour la redirection -->
            <input type="hidden" v-model="form.redirect_to_vehicule" />
            
            <!-- Sélection du type -->
            <div class="bg-gray-50 p-6 rounded-lg border-2 border-gray-200">
              <label class="block text-sm font-medium text-gray-700 mb-4">
                Type de propriétaire *
              </label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label
                  :class="[
                    'relative flex cursor-pointer rounded-lg border p-6 shadow-sm focus:outline-none',
                    form.type === 'personnel'
                      ? 'border-indigo-600 ring-2 ring-indigo-600'
                      : 'border-gray-300'
                  ]"
                >
                  <input
                    type="radio"
                    v-model="form.type"
                    value="personnel"
                    class="sr-only"
                  />
                  <div class="flex flex-1">
                    <div class="flex flex-col">
                      <span class="block text-lg font-medium text-gray-900 mb-2">
                        👤 Personne physique
                      </span>
                      <span class="block text-sm text-gray-500">
                        Propriétaire individuel avec permis de conduire
                      </span>
                    </div>
                  </div>
                </label>

                <label
                  :class="[
                    'relative flex cursor-pointer rounded-lg border p-6 shadow-sm focus:outline-none',
                    form.type === 'entreprise'
                      ? 'border-indigo-600 ring-2 ring-indigo-600'
                      : 'border-gray-300'
                  ]"
                >
                  <input
                    type="radio"
                    v-model="form.type"
                    value="entreprise"
                    class="sr-only"
                  />
                  <div class="flex flex-1">
                    <div class="flex flex-col">
                      <span class="block text-lg font-medium text-gray-900 mb-2">
                        🏢 Entreprise
                      </span>
                      <span class="block text-sm text-gray-500">
                        Société avec représentant légal et parc automobile
                      </span>
                    </div>
                  </div>
                </label>
              </div>
              <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">
                {{ form.errors.type }}
              </p>
            </div>

            <!-- Le reste du formulaire reste identique (Informations personnelles/entreprise, etc.) -->
            <!-- ... (copier tout le contenu du formulaire original) ... -->

            <!-- Actions avec bouton Cancel modifié -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
              <Link
                :href="redirectToVehicule ? route('vehicules.create') : route('proprietaires.index')"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
              >
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrement...
                </span>
                <span v-else>
                  {{ redirectToVehicule ? 'Enregistrer et continuer' : 'Enregistrer' }}
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
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FormInput from '@/Components/FormInput.vue'
import {
  UserIcon,
  BuildingOfficeIcon,
  UserCircleIcon,
  PhoneIcon,
  MapPinIcon,
  IdentificationIcon,
  DocumentCheckIcon,
  InformationCircleIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  redirectToVehicule: {
    type: Boolean,
    default: false
  }
})

const form = useForm({
  redirect_to_vehicule: props.redirectToVehicule,
  type: 'personnel',
  // Personnel
  nom: '',
  prenom: '',
  date_naissance: '',
  lieu_naissance: '',
  sexe: '',
  nationalite: '',
  numero_piece_identite: '',
  date_delivrance_piece: '',
  situation_familiale: '',
  profession: '',
  // Entreprise
  raison_sociale: '',
  nom_commercial: '',
  forme_juridique: '',
  nif: '',
  statistique: '',
  rcs: '',
  date_creation: '',
  secteur_activite: '',
  // Représentant
  representant_nom: '',
  representant_prenom: '',
  representant_fonction: '',
  representant_telephone: '',
  representant_email: '',
  representant_numero_piece: '',
  representant_date_delivrance: '',
  representant_lieu_delivrance: '',
  // Contact admin
  responsable_flotte: '',
  responsable_telephone: '',
  responsable_email: '',
  // Permis
  numero_permis: '',
  categorie_permis: '',
  date_delivrance_permis: '',
  // Autorisations
  autorisation_transport: '',
  autorisation_date_delivrance: '',
  autorisation_validite: '',
  autorisation_type: '',
  // Coordonnées
  adresse_complete: '',
  commune: '',
  fokontany: '',
  telephone_mobile: '',
  telephone_fixe: '',
  email: '',
  site_web: '',
  // Observations
  observations: ''
})

const submit = () => {
  form.post(route('proprietaires.store'))
}
</script>