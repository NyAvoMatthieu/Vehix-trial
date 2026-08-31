<template>
  <AppLayout title="Assurances">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Mes Assurances
        </h2>
        <Link
          :href="route('assurances.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition"
        >
          <PlusIcon class="h-4 w-4 mr-2" />
          Nouvelle assurance
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Véhicule sélectionné -->
        <div v-if="selectedVehicule" class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                <TruckIcon class="h-6 w-6 text-indigo-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm text-gray-500">Véhicule sélectionné</p>
                <p class="font-semibold text-gray-900">
                  {{ selectedVehicule.year }} {{ selectedVehicule.make }} {{ selectedVehicule.model }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des assurances -->
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              Contrats d'assurance
            </h3>
          </div>

          <div v-if="assurances.data.length === 0" class="px-6 py-12 text-center">
            <ShieldExclamationIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune assurance</h3>
            <p class="mt-1 text-sm text-gray-500">
              Commencez par ajouter votre premier contrat d'assurance.
            </p>
            <div class="mt-6">
              <Link
                :href="route('assurances.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Nouvelle assurance
              </Link>
            </div>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Assureur
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Police
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Véhicule
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Période
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Prime totale
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="assurance in assurances.data" :key="assurance.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <ShieldCheckIcon class="h-6 w-6 text-indigo-600" />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ assurance.assureur }}
                        </div>
                        <div v-if="assurance.agence" class="text-sm text-gray-500">
                          {{ assurance.agence }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono font-medium text-gray-900">
                      {{ assurance.policy_number }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ assurance.vehicule.make }} {{ assurance.vehicule.model }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ assurance.vehicule.license_plate }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ formatDate(assurance.start_date) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      → {{ formatDate(assurance.end_date) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">
                      {{ formatCurrency(assurance.prime_total) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(assurance)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ getStatusText(assurance) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link
                        :href="route('assurances.show', assurance.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Voir
                      </Link>
                      <Link
                        :href="route('assurances.edit', assurance.id)"
                        class="text-gray-600 hover:text-gray-900"
                      >
                        Modifier
                      </Link>
                      <button
                        @click="confirmDelete(assurance)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

            <!-- Pagination -->
                <div v-if="assurances.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                    Affichage de <span class="font-medium">{{ assurances.from }}</span> à 
                    <span class="font-medium">{{ assurances.to }}</span> sur 
                    <span class="font-medium">{{ assurances.total }}</span> résultats
                    </div>
                    
                    <!-- Boutons de pagination -->
                <div class="flex space-x-2">
                <Link
                    v-for="link in assurances.links"
                    :key="link.label"
                    :href="link.url || '#'" 
                    v-html="link.label"
                    :class="[
                    'px-3 py-2 text-sm rounded-md',
                    !link.url 
                        ? 'text-gray-400 cursor-not-allowed pointer-events-none'  <!-- Grisé + désactivé -->
                        : link.active 
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                    ]"
                    :disabled="!link.url"
                />
                </div>
            </div>
            </div>
          <!-- Fin Pagination -->
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { 
  PlusIcon, 
  TruckIcon, 
  ShieldCheckIcon, 
  ShieldExclamationIcon 
} from '@heroicons/vue/24/outline'

defineProps({
  assurances: Object,
  selectedVehicule: Object
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value) + ' Ar'
}

const getStatusText = (assurance) => {
  const now = new Date()
  const endDate = new Date(assurance.end_date)
  const daysUntilExpiry = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))

  if (daysUntilExpiry < 0) return 'Expiré'
  if (daysUntilExpiry <= 30) return `Expire dans ${daysUntilExpiry}j`
  return 'Actif'
}

const getStatusBadgeClass = (assurance) => {
  const now = new Date()
  const endDate = new Date(assurance.end_date)
  const daysUntilExpiry = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24))

  if (daysUntilExpiry < 0) return 'bg-red-100 text-red-800'
  if (daysUntilExpiry <= 30) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const confirmDelete = (assurance) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette assurance ?')) {
    router.delete(route('assurances.destroy', assurance.id))
  }
}
</script>