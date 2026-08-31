<template>
  <AppLayout title="Ajouter un propriétaire">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter un nouveau propriétaire
      </h2>

      <!-- Indication de redirection -->
      <div v-if="redirectToVehicule" class="flex items-center space-x-2 text-sm text-indigo-600 bg-indigo-50 px-4 py-2 rounded-md">
        <ArrowRightIcon class="h-4 w-4" />
        <span>Ensuite: Création du véhicule</span>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">


        
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-8">

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

            <!-- Formulaire Personne Physique -->
            <div v-if="form.type === 'personnel'" class="space-y-6">
              <!-- Informations personnelles -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <UserIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Informations personnelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <FormInput
                    id="nom"
                    v-model="form.nom"
                    label="Nom"
                    required
                    :error="form.errors.nom"
                  />
                  <FormInput
                    id="prenom"
                    v-model="form.prenom"
                    label="Prénom"
                    required
                    :error="form.errors.prenom"
                  />
                  <FormInput
                    id="date_naissance"
                    v-model="form.date_naissance"
                    type="date"
                    label="Date de naissance"
                    required
                    :error="form.errors.date_naissance"
                  />
                  <FormInput
                    id="lieu_naissance"
                    v-model="form.lieu_naissance"
                    label="Lieu de naissance"
                    required
                    :error="form.errors.lieu_naissance"
                  />
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Sexe *
                    </label>
                    <select
                      v-model="form.sexe"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      required
                    >
                      <option value="">Sélectionner...</option>
                      <option value="masculin">Masculin</option>
                      <option value="feminin">Féminin</option>
                    </select>
                    <p v-if="form.errors.sexe" class="mt-1 text-sm text-red-600">
                      {{ form.errors.sexe }}
                    </p>
                  </div>
                  <FormInput
                    id="nationalite"
                    v-model="form.nationalite"
                    label="Nationalité"
                    required
                    :error="form.errors.nationalite"
                  />
                  <FormInput
                    id="numero_piece_identite"
                    v-model="form.numero_piece_identite"
                    label="N° pièce d'identité"
                    required
                    :error="form.errors.numero_piece_identite"
                  />
                  <FormInput
                    id="date_delivrance_piece"
                    v-model="form.date_delivrance_piece"
                    type="date"
                    label="Date de délivrance"
                    required
                    :error="form.errors.date_delivrance_piece"
                  />
                  <FormInput
                    id="situation_familiale"
                    v-model="form.situation_familiale"
                    label="Situation familiale"
                    :error="form.errors.situation_familiale"
                  />
                  <FormInput
                    id="profession"
                    v-model="form.profession"
                    label="Profession"
                    :error="form.errors.profession"
                  />
                </div>
              </div>

              <!-- Permis de conduire -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <IdentificationIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Permis de conduire
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <FormInput
                    id="numero_permis"
                    v-model="form.numero_permis"
                    label="N° Permis"
                    :error="form.errors.numero_permis"
                  />
                  <FormInput
                    id="categorie_permis"
                    v-model="form.categorie_permis"
                    label="Catégorie"
                    placeholder="Ex: B, C, D..."
                    :error="form.errors.categorie_permis"
                  />
                  <FormInput
                    id="date_delivrance_permis"
                    v-model="form.date_delivrance_permis"
                    type="date"
                    label="Date de délivrance"
                    :error="form.errors.date_delivrance_permis"
                  />
                </div>
              </div>
            </div>

            <!-- Formulaire Entreprise -->
            <div v-if="form.type === 'entreprise'" class="space-y-6">
              <!-- Informations entreprise -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <BuildingOfficeIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Informations sur l'entreprise
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <FormInput
                    id="raison_sociale"
                    v-model="form.raison_sociale"
                    label="Raison sociale"
                    required
                    :error="form.errors.raison_sociale"
                  />
                  <FormInput
                    id="nom_commercial"
                    v-model="form.nom_commercial"
                    label="Nom commercial"
                    :error="form.errors.nom_commercial"
                  />
                  <FormInput
                    id="forme_juridique"
                    v-model="form.forme_juridique"
                    label="Forme juridique"
                    placeholder="Ex: SARL, SA, EURL..."
                    required
                    :error="form.errors.forme_juridique"
                  />
                  <FormInput
                    id="nif"
                    v-model="form.nif"
                    label="Numéro NIF"
                    required
                    :error="form.errors.nif"
                  />
                  <FormInput
                    id="statistique"
                    v-model="form.statistique"
                    label="Numéro STAT"
                    :error="form.errors.statistique"
                  />
                  <FormInput
                    id="rcs"
                    v-model="form.rcs"
                    label="Registre du commerce (RCS)"
                    :error="form.errors.rcs"
                  />
                  <FormInput
                    id="date_creation"
                    v-model="form.date_creation"
                    type="date"
                    label="Date de création"
                    required
                    :error="form.errors.date_creation"
                  />
                  <FormInput
                    id="secteur_activite"
                    v-model="form.secteur_activite"
                    label="Secteur d'activité"
                    required
                    :error="form.errors.secteur_activite"
                  />
                </div>
              </div>

              <!-- Représentant légal -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <UserCircleIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Représentant légal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <FormInput
                    id="representant_nom"
                    v-model="form.representant_nom"
                    label="Nom"
                    required
                    :error="form.errors.representant_nom"
                  />
                  <FormInput
                    id="representant_prenom"
                    v-model="form.representant_prenom"
                    label="Prénom"
                    required
                    :error="form.errors.representant_prenom"
                  />
                  <FormInput
                    id="representant_fonction"
                    v-model="form.representant_fonction"
                    label="Fonction"
                    placeholder="Ex: Directeur Général, Gérant..."
                    required
                    :error="form.errors.representant_fonction"
                  />
                  <FormInput
                    id="representant_telephone"
                    v-model="form.representant_telephone"
                    label="Téléphone"
                    :error="form.errors.representant_telephone"
                  />
                  <FormInput
                    id="representant_email"
                    v-model="form.representant_email"
                    type="email"
                    label="Email"
                    :error="form.errors.representant_email"
                  />
                  <FormInput
                    id="representant_numero_piece"
                    v-model="form.representant_numero_piece"
                    label="N° pièce d'identité"
                    required
                    :error="form.errors.representant_numero_piece"
                  />
                  <FormInput
                    id="representant_date_delivrance"
                    v-model="form.representant_date_delivrance"
                    type="date"
                    label="Date de délivrance"
                    required
                    :error="form.errors.representant_date_delivrance"
                  />
                  <FormInput
                    id="representant_lieu_delivrance"
                    v-model="form.representant_lieu_delivrance"
                    label="Lieu de délivrance"
                    required
                    :error="form.errors.representant_lieu_delivrance"
                  />
                </div>
              </div>

              <!-- Contact administratif -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <PhoneIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Contact administratif (Responsable flotte)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <FormInput
                    id="responsable_flotte"
                    v-model="form.responsable_flotte"
                    label="Responsable flotte / parc auto"
                    :error="form.errors.responsable_flotte"
                  />
                  <FormInput
                    id="responsable_telephone"
                    v-model="form.responsable_telephone"
                    label="Téléphone"
                    :error="form.errors.responsable_telephone"
                  />
                  <FormInput
                    id="responsable_email"
                    v-model="form.responsable_email"
                    type="email"
                    label="Email"
                    :error="form.errors.responsable_email"
                  />
                </div>
              </div>

              <!-- Autorisations -->
              <div class="border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                  <DocumentCheckIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Autorisations de transport
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <FormInput
                    id="autorisation_transport"
                    v-model="form.autorisation_transport"
                    label="Autorisation de transport"
                    :error="form.errors.autorisation_transport"
                  />
                  <FormInput
                    id="autorisation_type"
                    v-model="form.autorisation_type"
                    label="Type d'autorisation"
                    placeholder="Ex: Marchandises, Voyageurs..."
                    :error="form.errors.autorisation_type"
                  />
                  <FormInput
                    id="autorisation_date_delivrance"
                    v-model="form.autorisation_date_delivrance"
                    type="date"
                    label="Date de délivrance"
                    :error="form.errors.autorisation_date_delivrance"
                  />
                  <FormInput
                    id="autorisation_validite"
                    v-model="form.autorisation_validite"
                    type="date"
                    label="Date de validité"
                    :error="form.errors.autorisation_validite"
                  />
                </div>
              </div>
            </div>

            <!-- Coordonnées (communes) -->
            <div class="border-b pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <MapPinIcon class="h-5 w-5 mr-2 text-indigo-600" />
                Coordonnées
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Adresse complète
                  </label>
                  <textarea
                    id="adresse_complete"
                    v-model="form.adresse_complete"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Numéro, rue, quartier..."
                  ></textarea>
                  <p v-if="form.errors.adresse_complete" class="mt-1 text-sm text-red-600">
                    {{ form.errors.adresse_complete }}
                  </p>
                </div>
                <FormInput
                  id="commune"
                  v-model="form.commune"
                  label="Commune"
                  :error="form.errors.commune"
                />
                <FormInput
                  id="fokontany"
                  v-model="form.fokontany"
                  label="Fokontany"
                  :error="form.errors.fokontany"
                />
                <FormInput
                  id="telephone_mobile"
                  v-model="form.telephone_mobile"
                  label="Téléphone mobile"
                  :error="form.errors.telephone_mobile"
                />
                <FormInput
                  id="telephone_fixe"
                  v-model="form.telephone_fixe"
                  label="Téléphone fixe"
                  :error="form.errors.telephone_fixe"
                />
                <FormInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  label="Email"
                  :error="form.errors.email"
                />
                <FormInput
                  v-if="form.type === 'entreprise'"
                  id="site_web"
                  v-model="form.site_web"
                  label="Site web"
                  placeholder="https://..."
                  :error="form.errors.site_web"
                />
              </div>
            </div>

            <!-- Observations -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Observations
              </label>
              <textarea
                id="observations"
                v-model="form.observations"
                rows="4"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Informations complémentaires..."
              ></textarea>
              <p v-if="form.errors.observations" class="mt-1 text-sm text-red-600">
                {{ form.errors.observations }}
              </p>
            </div>

            <!-- Actions -->
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
  DocumentCheckIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  redirectToVehicule: {
    type: Boolean,
    default: false
  }
})

const form = useForm({
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
