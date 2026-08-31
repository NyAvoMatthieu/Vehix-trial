<template>
  <AppLayout title="Véhicules">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Mes Véhicules
        </h2>
        <Link
          :href="route('vehicules.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition"
        >
          <PlusIcon class="h-4 w-4 mr-2" />
          Ajouter un véhicule
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <SearchFilter
          placeholder="Rechercher par marque, modèle, plaque, utilisateur ou propriétaire..."
          :search="filters.search"
          :filters="[
            {
              name: 'status',
              placeholder: 'Tous les statuts',
              options: [
                { value: 'en_attente', label: 'En attente' },
                { value: 'valide', label: 'Validé' },
                { value: 'refuse', label: 'Refusé' },
                { value: 'a_corriger', label: 'À corriger' },
                { value: 'doublon', label: 'Doublon' }
              ]
            }
          ]"
          :current-filters="filters"
        />

        <DataTable
          title="Liste des véhicules"
          :columns="columns"
          :data="vehicules"
        >
          <template #vehicule="{ item }">
            <div v-if="item" class="flex items-center">
              <div class="h-10 w-10 flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                  <TruckIcon class="h-6 w-6 text-indigo-600" />
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ item.alias || `${item.year || ''} ${item.make || 'N/A'}`.trim() }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ item.make }}
                  {{ item.license_plate || 'Sans plaque'}}
                </div>
              </div>
            </div>
          </template>

          <template #user_info="{ item }">
            <div v-if="item?.user" class="text-sm">
              <div class="flex items-center">
                <UserIcon class="h-4 w-4 text-gray-400 mr-2 flex-shrink-0" />
                <div>
                  <div class="font-medium text-gray-900">{{ item.user.name }}</div>
                  <div class="text-gray-500 text-xs">{{ item.user.email }}</div>
                </div>
              </div>
            </div>
            <span v-else class="text-sm text-gray-500">Utilisateur non défini</span>
          </template>

          <template #proprietaire_info="{ item }">
            <div v-if="item?.proprietaire" class="text-sm">
              <div class="flex items-center">
                <component
                  :is="item.proprietaire.type === 'personnel' ? UserIcon : BuildingOfficeIcon"
                  class="h-4 w-4 text-gray-400 mr-2 flex-shrink-0"
                />
                <div>
                  <div class="font-medium text-gray-900">
                    {{ getProprietaireDisplayName(item.proprietaire) }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ item.proprietaire.type === 'personnel' ? '👤 Personnel' : '🏢 Entreprise' }}
                  </div>
                </div>
              </div>
            </div>
            <span v-else class="text-sm text-gray-500 italic">Propriétaire non défini</span>
          </template>

          <template #year="{ item }">
            <span v-if="item?.year" class="text-sm text-gray-900">
              {{ formatYear(item.year) }}
            </span>
            <span v-else class="text-sm text-gray-500">-</span>
          </template>

          <template #vin="{ item }">
            <span v-if="item?.vin" class="text-sm text-gray-900 font-mono">
              {{ item.vin }}
            </span>
            <span v-else class="text-sm text-gray-500">Non renseigné</span>
          </template>

          <template #status="{ item }">
            <VehiculeStatusBadge v-if="item" :status="item.status" />
          </template>

          <template #validation_info="{ item }">
            <div v-if="item && item.validated_at" class="text-sm text-gray-900">
              <div class="font-medium">{{ item.validator?.name || 'N/A' }}</div>
              <div class="text-gray-500 text-xs">{{ formatDate(item.validated_at) }}</div>
            </div>
            <span v-else class="text-sm text-gray-500">En attente</span>
          </template>

          <template #actions="{ item }">
            <div class="flex space-x-2">
              <Link
                v-if="item?.id"
                :href="route('vehicules.show', item.id)"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                Voir
              </Link>
              <Link
                v-if="item?.id && canEdit(item.status)"
                :href="route('vehicules.edit', item.id)"
                class="text-gray-600 hover:text-gray-900 text-sm font-medium"
              >
                Modifier
              </Link>
              <button
                v-if="canValidate && item?.status === 'en_attente'"
                @click="openValidationModal(item)"
                class="text-green-600 hover:text-green-900 text-sm font-medium"
              >
                Valider
              </button>
            </div>
          </template>
        </DataTable>
      </div>
    </div>

    <!-- Validation Modal -->
    <ValidationModal
      :show="showValidationModal"
      :vehicule="selectedVehicule"
      @close="closeValidationModal"
      @validated="handleValidation"
    />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import SearchFilter from '@/Components/SearchFilter.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ValidationModal from '@/Components/ValidationModal.vue'
import { PlusIcon, TruckIcon, UserIcon, BuildingOfficeIcon } from '@heroicons/vue/24/outline'

defineProps({
  vehicules: Object,
  filters: Object,
  canValidate: Boolean
})

const columns = [
  { key: 'vehicule', label: 'Véhicule' },
  { key: 'user_info', label: 'Utilisateur' },
  { key: 'proprietaire_info', label: 'Propriétaire' },
  { key: 'year', label: 'Année' },
  { key: 'vin', label: 'VIN' },
  { key: 'status', label: 'Statut' },
  { key: 'validation_info', label: 'Validation' }
]

const showValidationModal = ref(false)
const selectedVehicule = ref(null)

const openValidationModal = (vehicule) => {
  selectedVehicule.value = vehicule
  showValidationModal.value = true
}

const closeValidationModal = () => {
  showValidationModal.value = false
  selectedVehicule.value = null
}

const handleValidation = () => {
  closeValidationModal()
  window.location.reload()
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatYear = (year) => {
  if (!year) return '-'
  return new Date(year).getFullYear()
}

const getProprietaireDisplayName = (proprietaire) => {
  if (!proprietaire) return 'N/A'

  if (proprietaire.type === 'personnel') {
    return `${proprietaire.prenom || ''} ${proprietaire.nom || ''}`.trim() || 'N/A'
  }

  return proprietaire.nom_commercial || proprietaire.raison_sociale || 'N/A'
}

const canEdit = (status) => {
  return ['en_attente', 'a_corriger', 'refuse', 'doublon'].includes(status)
}
</script>
