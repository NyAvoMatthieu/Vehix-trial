<template>
  <AppLayout title="Détails du propriétaire">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails du propriétaire
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('proprietaires.index')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            :href="route('proprietaires.edit', proprietaire.id)"
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Informations principales -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- En-tête avec type -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-indigo-500 to-purple-600">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center">
                      <UserIcon v-if="proprietaire.type === 'personnel'" class="h-10 w-10 text-indigo-600" />
                      <BuildingOfficeIcon v-else class="h-10 w-10 text-purple-600" />
                    </div>
                    <div class="ml-4">
                      <h3 class="text-2xl font-bold text-white">
                        {{ getDisplayName() }}
                      </h3>
                      <p class="text-indigo-100">
                        {{ proprietaire.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                      </p>
                    </div>
                  </div>
                  <span
                    :class="[
                      'px-3 py-1 text-sm font-semibold rounded-full',
                      proprietaire.type === 'personnel'
                        ? 'bg-blue-100 text-blue-800'
                        : 'bg-purple-100 text-purple-800'
                    ]"
                  >
                    {{ proprietaire.type === 'personnel' ? '👤 Personnel' : '🏢 Entreprise' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Informations personnelles / entreprise -->
            <div v-if="proprietaire.type === 'personnel'" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <UserIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Informations personnelles
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Nom complet" :value="`${proprietaire.prenom} ${proprietaire.nom}`" />
                  <DetailRow label="Date de naissance" :value="formatDate(proprietaire.date_naissance)" odd />
                  <DetailRow label="Lieu de naissance" :value="proprietaire.lieu_naissance" />
                  <DetailRow label="Sexe" :value="proprietaire.sexe === 'masculin' ? 'Masculin' : 'Féminin'" odd />
                  <DetailRow label="Nationalité" :value="proprietaire.nationalite" />
                  <DetailRow label="N° pièce d'identité" :value="proprietaire.numero_piece_identite" odd />
                  <DetailRow label="Date de délivrance" :value="formatDate(proprietaire.date_delivrance_piece)" />
                  <DetailRow label="Situation familiale" :value="proprietaire.situation_familiale" odd />
                  <DetailRow label="Profession" :value="proprietaire.profession" />
                </dl>
              </div>
            </div>

            <!-- Informations entreprise -->
            <div v-if="proprietaire.type === 'entreprise'" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <BuildingOfficeIcon class="h-5 w-5 mr-2 text-purple-600" />
                  Informations sur l'entreprise
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Raison sociale" :value="proprietaire.raison_sociale" />
                  <DetailRow label="Nom commercial" :value="proprietaire.nom_commercial" odd />
                  <DetailRow label="Forme juridique" :value="proprietaire.forme_juridique" />
                  <DetailRow label="NIF" :value="proprietaire.nif" odd mono />
                  <DetailRow label="Statistique" :value="proprietaire.statistique" />
                  <DetailRow label="RCS" :value="proprietaire.rcs" odd />
                  <DetailRow label="Date de création" :value="formatDate(proprietaire.date_creation)" />
                  <DetailRow label="Secteur d'activité" :value="proprietaire.secteur_activite" odd />
                  <DetailRow label="Site web" :value="proprietaire.site_web" :link="proprietaire.site_web" />
                </dl>
              </div>
            </div>

            <!-- Permis de conduire (Personnel) -->
            <div v-if="proprietaire.type === 'personnel' && proprietaire.numero_permis" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <IdentificationIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Permis de conduire
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="N° Permis" :value="proprietaire.numero_permis" mono />
                  <DetailRow label="Catégorie" :value="proprietaire.categorie_permis" odd />
                  <DetailRow label="Date de délivrance" :value="formatDate(proprietaire.date_delivrance_permis)" />
                </dl>
              </div>
            </div>

            <!-- Représentant légal (Entreprise) -->
            <div v-if="proprietaire.type === 'entreprise'" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <UserCircleIcon class="h-5 w-5 mr-2 text-purple-600" />
                  Représentant légal
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Nom" :value="`${proprietaire.representant_prenom} ${proprietaire.representant_nom}`" />
                  <DetailRow label="Fonction" :value="proprietaire.representant_fonction" odd />
                  <DetailRow label="Téléphone" :value="proprietaire.representant_telephone" />
                  <DetailRow label="Email" :value="proprietaire.representant_email" odd />
                  <DetailRow label="N° pièce d'identité" :value="proprietaire.representant_numero_piece" mono />
                  <DetailRow label="Date de délivrance" :value="formatDate(proprietaire.representant_date_delivrance)" odd />
                  <DetailRow label="Lieu de délivrance" :value="proprietaire.representant_lieu_delivrance" />
                </dl>
              </div>
            </div>

            <!-- Contact administratif (Entreprise) -->
            <div v-if="proprietaire.type === 'entreprise' && proprietaire.responsable_flotte" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <PhoneIcon class="h-5 w-5 mr-2 text-purple-600" />
                  Contact administratif
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Responsable flotte" :value="proprietaire.responsable_flotte" />
                  <DetailRow label="Téléphone" :value="proprietaire.responsable_telephone" odd />
                  <DetailRow label="Email" :value="proprietaire.responsable_email" />
                </dl>
              </div>
            </div>

            <!-- Autorisations (Entreprise) -->
            <div v-if="proprietaire.type === 'entreprise' && proprietaire.autorisation_transport" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <DocumentCheckIcon class="h-5 w-5 mr-2 text-purple-600" />
                  Autorisations de transport
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Autorisation" :value="proprietaire.autorisation_transport" />
                  <DetailRow label="Type" :value="proprietaire.autorisation_type" odd />
                  <DetailRow label="Date de délivrance" :value="formatDate(proprietaire.autorisation_date_delivrance)" />
                  <DetailRow label="Date de validité" :value="formatDate(proprietaire.autorisation_validite)" odd>
                    <template v-if="proprietaire.autorisation_validite">
                      <span :class="[
                        'ml-2 px-2 py-1 text-xs font-semibold rounded-full',
                        isValidAutorisation() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]">
                        {{ isValidAutorisation() ? '✓ Valide' : '✗ Expirée' }}
                      </span>
                    </template>
                  </DetailRow>
                </dl>
              </div>
            </div>

            <!-- Coordonnées -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <MapPinIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Coordonnées
                </h3>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <DetailRow label="Adresse complète" :value="proprietaire.adresse_complete" />
                  <DetailRow label="Commune" :value="proprietaire.commune" odd />
                  <DetailRow label="Fokontany" :value="proprietaire.fokontany" />
                  <DetailRow label="Téléphone mobile" :value="proprietaire.telephone_mobile" odd />
                  <DetailRow label="Téléphone fixe" :value="proprietaire.telephone_fixe" />
                  <DetailRow label="Email" :value="proprietaire.email" odd />
                </dl>
              </div>
            </div>

            <!-- Observations -->
            <div v-if="proprietaire.observations" class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                  <DocumentTextIcon class="h-5 w-5 mr-2 text-indigo-600" />
                  Observations
                </h3>
              </div>
              <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <p class="text-sm text-gray-700 whitespace-pre-line">
                  {{ proprietaire.observations }}
                </p>
              </div>
            </div>

          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            
            <!-- Carte récapitulatif -->
            <div class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Récapitulatif
                </h3>
                <dl class="space-y-3">
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Type</dt>
                    <dd class="text-sm font-medium text-gray-900">
                      {{ proprietaire.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                    </dd>
                  </div>
                  <div v-if="proprietaire.type === 'personnel'" class="flex justify-between">
                    <dt class="text-sm text-gray-500">Âge</dt>
                    <dd class="text-sm font-medium text-gray-900">
                      {{ getAge() }} ans
                    </dd>
                  </div>
                  <div v-if="proprietaire.type === 'entreprise'" class="flex justify-between">
                    <dt class="text-sm text-gray-500">Ancienneté</dt>
                    <dd class="text-sm font-medium text-gray-900">
                      {{ getAnciennete() }} ans
                    </dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Créé le</dt>
                    <dd class="text-sm font-medium text-gray-900">
                      {{ formatDate(proprietaire.created_at) }}
                    </dd>
                  </div>
                  <div v-if="proprietaire.updated_at !== proprietaire.created_at" class="flex justify-between">
                    <dt class="text-sm text-gray-500">Modifié le</dt>
                    <dd class="text-sm font-medium text-gray-900">
                      {{ formatDate(proprietaire.updated_at) }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Statuts -->
            <div class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Statuts
                </h3>
                <dl class="space-y-3">
                  <div v-if="proprietaire.type === 'personnel'" class="flex items-center justify-between">
                    <dt class="text-sm text-gray-500">Permis de conduire</dt>
                    <dd>
                      <span :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        proprietaire.numero_permis ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ proprietaire.numero_permis ? '✓ Possède' : '✗ Aucun' }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="proprietaire.type === 'entreprise'" class="flex items-center justify-between">
                    <dt class="text-sm text-gray-500">Autorisation</dt>
                    <dd>
                      <span :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        isValidAutorisation() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]">
                        {{ isValidAutorisation() ? '✓ Valide' : '✗ Invalide/Expirée' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white shadow sm:rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Actions rapides
                </h3>
                <div class="space-y-3">
                  <Link
                    :href="route('proprietaires.edit', proprietaire.id)"
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <PencilIcon class="h-4 w-4 mr-2" />
                    Modifier
                  </Link>
                  <button
                    @click="confirmDelete"
                    class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                  >
                    <TrashIcon class="h-4 w-4 mr-2" />
                    Supprimer
                  </button>
                </div>
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
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  UserIcon,
  BuildingOfficeIcon,
  UserCircleIcon,
  PhoneIcon,
  MapPinIcon,
  IdentificationIcon,
  DocumentCheckIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  proprietaire: Object
})

// Composant pour afficher une ligne de détail
const DetailRow = {
  props: {
    label: String,
    value: [String, Number],
    odd: Boolean,
    mono: Boolean,
    link: String
  },
  template: `
    <div :class="[odd ? 'bg-gray-50' : 'bg-white', 'px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6']">
      <dt class="text-sm font-medium text-gray-500">{{ label }}</dt>
      <dd :class="['mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2', mono ? 'font-mono' : '']">
        <a v-if="link" :href="link" target="_blank" class="text-indigo-600 hover:text-indigo-900">
          {{ value || 'N/A' }}
        </a>
        <span v-else>{{ value || 'N/A' }}</span>
        <slot></slot>
      </dd>
    </div>
  `
}

const getDisplayName = () => {
  if (props.proprietaire.type === 'personnel') {
    return `${props.proprietaire.prenom || ''} ${props.proprietaire.nom || ''}`.trim()
  }
  return props.proprietaire.nom_commercial || props.proprietaire.raison_sociale
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getAge = () => {
  if (!props.proprietaire.date_naissance) return 'N/A'
  const today = new Date()
  const birthDate = new Date(props.proprietaire.date_naissance)
  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }
  return age
}

const getAnciennete = () => {
  if (!props.proprietaire.date_creation) return 'N/A'
  const today = new Date()
  const creationDate = new Date(props.proprietaire.date_creation)
  return today.getFullYear() - creationDate.getFullYear()
}

const isValidAutorisation = () => {
  if (!props.proprietaire.autorisation_validite) return false
  return new Date(props.proprietaire.autorisation_validite) > new Date()
}

const confirmDelete = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce propriétaire ? Cette action est irréversible.')) {
    router.delete(route('proprietaires.destroy', props.proprietaire.id), {
      onSuccess: () => {
        router.visit(route('proprietaires.index'))
      }
    })
  }
}
</script>