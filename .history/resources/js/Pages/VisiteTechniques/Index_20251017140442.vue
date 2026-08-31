<template>
  <AppLayout title="Visites Techniques">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Visites Techniques
        </h2>
        <Link
          :href="route('visite-techniques.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500"
        >
          <PlusIcon class="h-4 w-4 mr-2" />
          Nouvelle visite
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Véhicule sélectionné -->
        <div v-if="selectedVehicule" class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
              <TruckIcon class="h-6 w-6 text-indigo-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-500">Véhicule sélectionné</p>
              <p class="font-semibold text-gray-900">
                {{ selectedVehicule.year }} {{ selectedVehicule.make }} {{ selectedVehicule.model }}
                - {{ selectedVehicule.license_plate }}
              </p>
            </div>
          </div>
        </div>

        <!-- Filtres -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher par N° PV, N° reçu, centre..."
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              @input="search"
            />
            <select
              v-model="aptitudeFilter"
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              @change="search"
            >
              <option value="all">Toutes les aptitudes</option>
              <option value="APTE">🟢 APTE</option>
              <option value="INAPTE">🔴 INAPTE</option>
            </select>
          </div>
        </div>

        <!-- Liste -->
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
          <div v-if="visites.data.length === 0" class="px-6 py-12 text-center">
            <ClipboardDocumentCheckIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune visite technique</h3>
            <p class="mt-1 text-sm text-gray-500">
              Commencez par enregistrer votre première visite technique.
            </p>
            <div class="mt-6">
              <Link
                :href="route('visite-techniques.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Nouvelle visite
              </Link>
            </div>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Date visite
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Véhicule
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    N° PV
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Centre
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Validité
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Aptitude
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="visite in visites.data" :key="visite.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatDate(visite.date_visite) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ visite.type_visite }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ selectedVehicule.make }}
                    </div>
                    <div class="text-sm text-gray-500 font-mono">
                      {{ visite.immatriculation }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono text-gray-900">
                      {{ visite.numero_pv || '—' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ visite.centre || '—' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ visite.validite ? formatDate(visite.validite) : '—' }}
                    </div>
                    <span v-if="visite.validite" :class="getValidityBadgeClass(visite)" class="text-xs px-2 py-1 rounded-full">
                      {{ getValidityText(visite) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="visite.aptitude === 'APTE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                          class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                      {{ visite.aptitude === 'APTE' ? '🟢 APTE' : '🔴 INAPTE' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <Link :href="route('visite-techniques.show', visite.id)" class="text-indigo-600 hover:text-indigo-900">
                        Voir
                      </Link>
                      <Link :href="route('visite-techniques.edit', visite.id)" class="text-gray-600 hover:text-gray-900">
                        Modifier
                      </Link>
                      <button @click="confirmDelete(visite)" class="text-red-600 hover:text-red-900">
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="visites.data.length > 0" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ visites.from }}</span> à 
                <span class="font-medium">{{ visites.to }}</span> sur 
                <span class="font-medium">{{ visites.total }}</span> résultats
              </div>
              <div class="flex space-x-2">
                <Link
                  v-for="link in visites.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  :class="getPaginationLinkClass(link)"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { PlusIcon, TruckIcon, ClipboardDocumentCheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  visites: Object,
  selectedVehicule: Object,
  filters: Object
})

const searchQuery = ref(props.filters?.search || '')
const aptitudeFilter = ref(props.filters?.aptitude || 'all')

const search = () => {
  router.get(route('visite-techniques.index'), {
    search: searchQuery.value,
    aptitude: aptitudeFilter.value
  }, {
    preserveState: true,
    replace: true
  })
}

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getValidityText = (visite) => {
  if (!visite.validite) return ''
  const now = new Date()
  const validite = new Date(visite.validite)
  const days = Math.ceil((validite - now) / (1000 * 60 * 60 * 24))
  
  if (days < 0) return 'Expiré'
  if (days <= 30) return `${days}j restants`
  return 'Valide'
}

const getValidityBadgeClass = (visite) => {
  if (!visite.validite) return ''
  const now = new Date()
  const validite = new Date(visite.validite)
  const days = Math.ceil((validite - now) / (1000 * 60 * 60 * 24))
  
  if (days < 0) return 'bg-red-100 text-red-800'
  if (days <= 30) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const confirmDelete = (visite) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette visite technique ?')) {
    router.delete(route('visite-techniques.destroy', visite.id))
  }
}

const getPaginationLinkClass = (link) => {
  if (!link.url) {
    return 'px-3 py-2 text-sm rounded-md text-gray-400 cursor-not-allowed'
  }
  if (link.active) {
    return 'px-3 py-2 text-sm rounded-md bg-indigo-600 text-white'
  }
  return 'px-3 py-2 text-sm rounded-md bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
}
</script>