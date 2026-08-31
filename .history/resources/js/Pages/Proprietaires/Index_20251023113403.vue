<template>
  <AppLayout title="Propriétaires">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Propriétaires
        </h2>
        <Link
          :href="route('proprietaires.create')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500"
        >
          <PlusIcon class="h-4 w-4 mr-2" />
          Ajouter un propriétaire
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres de recherche -->
        <SearchFilter
          placeholder="Rechercher par nom, raison sociale, NIF..."
          :search="filters.search"
          :filters="[
            {
              name: 'type',
              placeholder: 'Tous les types',
              options: [
                { value: 'personnel', label: 'Personne physique' },
                { value: 'entreprise', label: 'Entreprise' }
              ]
            }
          ]"
          :current-filters="filters"
        />

        <!-- Tableau -->
        <DataTable
          title="Liste des propriétaires"
          :columns="columns"
          :data="proprietaires"
        >
          <template #proprietaire="{ item }">
            <div v-if="item" class="flex items-center">
              <div class="h-10 w-10 flex-shrink-0">
                <div :class="[
                  'h-10 w-10 rounded-full flex items-center justify-center',
                  item.type === 'personnel' ? 'bg-blue-100' : 'bg-purple-100'
                ]">
                  <UserIcon v-if="item.type === 'personnel'" class="h-6 w-6 text-blue-600" />
                  <BuildingOfficeIcon v-else class="h-6 w-6 text-purple-600" />
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ getDisplayName(item) }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ item.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                </div>
              </div>
            </div>
          </template>

          <template #type="{ item }">
            <span
              v-if="item"
              :class="[
                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                item.type === 'personnel'
                  ? 'bg-blue-100 text-blue-800'
                  : 'bg-purple-100 text-purple-800'
              ]"
            >
              {{ item.type === 'personnel' ? '👤 Personnel' : '🏢 Entreprise' }}
            </span>
          </template>

          <template #identifiant="{ item }">
            <div v-if="item" class="text-sm text-gray-900">
              <div v-if="item.type === 'personnel'" class="font-mono">
                {{ item.numero_piece_identite || 'N/A' }}
              </div>
              <div v-else class="font-mono">
                NIF: {{ item.nif || 'N/A' }}
              </div>
            </div>
          </template>

          <template #contact="{ item }">
            <div v-if="item" class="text-sm text-gray-900">
              <div v-if="item.telephone_mobile">
                📱 {{ item.telephone_mobile }}
              </div>
              <div v-if="item.email" class="text-gray-500">
                ✉️ {{ item.email }}
              </div>
            </div>
          </template>

          <template #localisation="{ item }">
            <div v-if="item" class="text-sm text-gray-500">
              {{ item.commune || 'N/A' }}
            </div>
          </template>

          <template #actions="{ item }">
            <div class="flex space-x-2">
              <Link
                v-if="item?.id"
                :href="route('proprietaires.show', item.id)"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                Voir
              </Link>
              <Link
                v-if="item?.id"
                :href="route('proprietaires.edit', item.id)"
                class="text-gray-600 hover:text-gray-900 text-sm font-medium"
              >
                Modifier
              </Link>
            </div>
          </template>
        </DataTable>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import SearchFilter from '@/Components/SearchFilter.vue'
import { PlusIcon, UserIcon, BuildingOfficeIcon } from '@heroicons/vue/24/outline'

defineProps({
  proprietaires: Object,
  filters: Object
})

const columns = [
  { key: 'proprietaire', label: 'Propriétaire' },
  { key: 'type', label: 'Type' },
  { key: 'identifiant', label: 'Identifiant' },
  { key: 'contact', label: 'Contact' },
  { key: 'localisation', label: 'Localisation' }
]

const getDisplayName = (item) => {
  if (item.type === 'personnel') {
    return `${item.prenom || ''} ${item.nom || ''}`.trim() || 'N/A'
  }
  return item.nom_commercial || item.raison_sociale || 'N/A'
}
</script>