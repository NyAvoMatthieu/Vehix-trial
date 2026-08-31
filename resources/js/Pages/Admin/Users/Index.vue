<template>
  <AppLayout title="Gestion des utilisateurs">
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des utilisateurs
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Gérez les utilisateurs, leurs rôles et leurs accès
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-sm text-gray-600">
            Total: <span class="font-semibold text-gray-900">{{ users.total }}</span> utilisateurs
          </div>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtres améliorés -->
        <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center gap-4 mb-4">
            <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">Filtres de recherche</h3>
              <p class="text-sm text-gray-500">Affinez votre recherche d'utilisateurs</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Rechercher un utilisateur
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  v-model="searchForm.search"
                  type="text"
                  placeholder="Nom, email..."
                  class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                  @input="debouncedSearch"
                />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Filtrer par rôle
              </label>
              <select
                v-model="searchForm.role"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                @change="search"
              >
                <option value="all">Tous les rôles</option>
                <option v-for="role in roles" :key="role.value" :value="role.value">
                  {{ role.label }}
                </option>
              </select>
            </div>
          </div>

          <!-- Statistiques rapides -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-3 gap-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ getFilteredCount('client') }}</div>
                <div class="text-xs text-gray-600">Utilisateurs</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ getFilteredCount('validator') }}</div>
                <div class="text-xs text-gray-600">Validateurs</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ getFilteredCount('administrateur') }}</div>
                <div class="text-xs text-gray-600">Administrateurs</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des utilisateurs avec design amélioré -->
        <div class="space-y-4">
          <div
            v-for="user in users.data"
            :key="user.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200"
          >
            <!-- En-tête utilisateur redessiné -->
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4 flex-1">
                  <!-- Avatar avec statut -->
                  <div class="relative flex-shrink-0">
                    <img
                      :src="user.profile_photo_url"
                      :alt="user.name"
                      class="h-14 w-14 rounded-xl ring-2 ring-gray-100"
                    />
                    <div
                      v-if="user.blocked_until"
                      class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-2 border-white flex items-center justify-center"
                    >
                      <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>

                  <!-- Infos utilisateur -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                      <h3 class="text-lg font-semibold text-gray-900 truncate">
                        {{ user.name }}
                      </h3>
                      <span
                        :class="getRoleBadgeClass(user.role)"
                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                      >
                        {{ getRoleLabel(user.role) }}
                      </span>
                      <span
                        v-if="user.blocked_until"
                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800"
                      >
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                        </svg>
                        Bloqué
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 flex items-center">
                      <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      {{ user.email }}
                    </p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 ml-4">
                  <button
                    @click="toggleUserDetails(user.id)"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    :title="expandedUsers.includes(user.id) ? 'Masquer les détails' : 'Voir les détails'"
                  >
                    <ChevronDownIcon
                      :class="[
                        'h-5 w-5 transition-transform duration-200',
                        expandedUsers.includes(user.id) ? 'transform rotate-180' : ''
                      ]"
                    />
                  </button>

                  <Menu as="div" class="relative inline-block text-left">
                    <MenuButton
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    >
                      <EllipsisVerticalIcon class="h-5 w-5" />
                    </MenuButton>
                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <MenuItems
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                      >
                        <div class="py-1">
                          <MenuItem v-slot="{ active }">
                            <button
                              @click="openRoleModal(user)"
                              :class="[
                                active ? 'bg-indigo-50 text-indigo-900' : 'text-gray-700',
                                'block w-full text-left px-4 py-2.5 text-sm flex items-center transition-colors'
                              ]"
                            >
                              <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                              Changer le rôle
                            </button>
                          </MenuItem>
                          <MenuItem v-if="!user.blocked_until" v-slot="{ active }">
                            <button
                              @click="blockUser(user.id)"
                              :class="[
                                active ? 'bg-orange-50 text-orange-900' : 'text-gray-700',
                                'block w-full text-left px-4 py-2.5 text-sm flex items-center transition-colors'
                              ]"
                            >
                              <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                              </svg>
                              Bloquer l'utilisateur
                            </button>
                          </MenuItem>
                          <MenuItem v-else v-slot="{ active }">
                            <button
                              @click="unblockUser(user.id)"
                              :class="[
                                active ? 'bg-green-50 text-green-900' : 'text-gray-700',
                                'block w-full text-left px-4 py-2.5 text-sm flex items-center transition-colors'
                              ]"
                            >
                              <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              Débloquer l'utilisateur
                            </button>
                          </MenuItem>
                          <div class="border-t border-gray-100"></div>
                          <MenuItem v-slot="{ active }">
                            <button
                              @click="confirmDeleteUser(user)"
                              :class="[
                                active ? 'bg-red-50 text-red-900' : 'text-red-700',
                                'block w-full text-left px-4 py-2.5 text-sm flex items-center transition-colors'
                              ]"
                            >
                              <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                              Supprimer l'utilisateur
                            </button>
                          </MenuItem>
                        </div>
                      </MenuItems>
                    </transition>
                  </Menu>
                </div>
              </div>

              <!-- Statistiques utilisateur redessinées -->
              <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-medium text-blue-700 mb-1">Véhicules</p>
                      <p class="text-2xl font-bold text-blue-900">{{ user.vehicules_count || 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                      <TruckIcon class="h-5 w-5 text-blue-700" />
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-medium text-green-700 mb-1">Maintenances</p>
                      <p class="text-2xl font-bold text-green-900">{{ user.maintenances_count || 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-200 rounded-lg flex items-center justify-center">
                      <svg class="h-5 w-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Liste des véhicules (collapsible) améliorée -->
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div v-if="expandedUsers.includes(user.id)" class="bg-gradient-to-br from-gray-50 to-gray-100 border-t border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                  <h4 class="text-sm font-semibold text-gray-900 flex items-center">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                      <TruckIcon class="h-4 w-4 text-indigo-600" />
                    </div>
                    Véhicules de {{ user.name }}
                    <span class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium">
                      {{ user.vehicules?.length || 0 }}
                    </span>
                  </h4>
                </div>

                <div v-if="user.vehicules && user.vehicules.length > 0" class="space-y-3">
                  <div
                    v-for="vehicule in user.vehicules"
                    :key="vehicule.id"
                    class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200"
                  >


                  <div class="flex items-center justify-between">
                      <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                          <h5 class="text-base font-semibold text-gray-900">
                            {{ vehicule.alias || `${vehicule.year} ${vehicule.make} ${vehicule.model}` }}
                          </h5>
                          <VehiculeStatusBadge :status="vehicule.status" />
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                          <div class="flex items-center text-sm">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-2">
                              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                            </div>
                            <div>
                              <span class="text-xs text-gray-500 block">Véhicule</span>
                              <span class="font-medium text-gray-900">{{ vehicule.make }} {{ vehicule.model }}</span>
                            </div>
                          </div>
                          <div class="flex items-center text-sm">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-2">
                              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                              </svg>
                            </div>
                            <div>



                    
                              <span class="text-xs text-gray-500 block">Immatriculation</span>
                              <span class="font-medium text-gray-900">{{ vehicule.license_plate || 'N/A' }}</span>
                            </div>
                          </div>
                          <div class="flex items-center text-sm">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-2">
                              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                            </div>
                            <div>
                              <span class="text-xs text-gray-500 block">Propriétaire</span>
                              <span class="font-medium text-gray-900">
                                {{ getProprietaireDisplay(vehicule.proprietaire) }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <Link
                        :href="route('vehicules.show', vehicule.id)"
                        class="ml-4 inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow"
                      >
                        <EyeIcon class="h-4 w-4 mr-2" />
                        Voir détails
                      </Link>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center py-12 bg-white rounded-xl border-2 border-dashed border-gray-300">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <TruckIcon class="h-8 w-8 text-gray-400" />
                  </div>
                  <p class="text-sm font-medium text-gray-900 mb-1">Aucun véhicule enregistré</p>
                  <p class="text-xs text-gray-500">Cet utilisateur n'a pas encore ajouté de véhicule</p>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="users.data.length > 0" class="mt-6">
          <Pagination :links="users.links" />
        </div>

        <!-- Empty state amélioré -->
        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
          <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <UserGroupIcon class="h-10 w-10 text-gray-400" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun utilisateur trouvé</h3>
          <p class="text-sm text-gray-500 mb-6">Essayez d'ajuster vos filtres de recherche</p>
          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Réinitialiser les filtres
          </button>
        </div>
      </div>
    </div>

    <!-- Modal changement de rôle amélioré -->
    <TransitionRoot as="template" :show="showRoleModal">
      <Dialog as="div" class="relative z-10" @close="showRoleModal = false">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <div class="fixed inset-0 z-10 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <DialogPanel
                class="relative transform overflow-hidden rounded-2xl bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
              >
                <form @submit.prevent="updateUserRole">
                  <div class="flex items-center justify-center w-12 h-12 mx-auto bg-indigo-100 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <div>
                    <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900 mb-2 text-center">
                      Changer le rôle
                    </DialogTitle>
                    <p class="text-sm text-gray-500 text-center mb-6">
                      Modifier le rôle de <span class="font-semibold text-gray-900">{{ selectedUser?.name }}</span>
                    </p>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Nouveau rôle
                        </label>
                        <select
                          v-model="roleForm.role"
                          class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                          required
                        >
                          <option v-for="role in roles" :key="role.value" :value="role.value">
                            {{ role.label }}
                          </option>
                        </select>
                      </div>

                      <!-- Info sur les rôles -->
                      <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex">
                          <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                          </div>
                          <div class="ml-3">
                            <p class="text-xs text-blue-800">
                              <strong>Utilisateur:</strong> Accès standard<br>
                              <strong>Validateur:</strong> Peut valider des véhicules<br>
                              <strong>Administrateur:</strong> Accès complet
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="mt-6 flex space-x-3 justify-end">
                    <button
                      type="button"
                      @click="showRoleModal = false"
                      class="inline-flex justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                    >
                      Annuler
                    </button>
                    <button
                      type="submit"
                      :disabled="roleForm.processing"
                      class="inline-flex justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                      <svg v-if="roleForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Enregistrer
                    </button>
                  </div>
                </form>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal confirmation suppression amélioré -->
    <ConfirmationModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteUser"
    >
      <template #title>
        <div class="flex items-center">
          <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-xl mr-3">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <span>Supprimer l'utilisateur</span>
        </div>
      </template>
      <template #content>
        <div class="space-y-4">
          <p class="text-sm text-gray-600">
            Êtes-vous sûr de vouloir supprimer <strong class="text-gray-900">{{ selectedUser?.name }}</strong> ?
          </p>
          <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800 mb-1">Action irréversible</h4>
                <p class="text-xs text-red-700">
                  Cette action supprimera définitivement tous les véhicules, maintenances, réparations et données associés à cet utilisateur.
                </p>
              </div>
            </div>
          </div>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems
} from '@headlessui/vue'
import {
  TruckIcon,
  UserGroupIcon,
  ChevronDownIcon,
  EllipsisVerticalIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  users: Object,
  filters: Object,
  roles: Array
})

const expandedUsers = ref([])
const showRoleModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

const searchForm = reactive({
  search: props.filters.search || '',
  role: props.filters.role || 'all'
})

const roleForm = useForm({
  role: ''
})

let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    search()
  }, 300)
}

const search = () => {
  router.get(route('administrateur.users.index'), searchForm, {
    preserveState: true,
    preserveScroll: true
  })
}

const resetFilters = () => {
  searchForm.search = ''
  searchForm.role = 'all'
  search()
}

const toggleUserDetails = (userId) => {
  const index = expandedUsers.value.indexOf(userId)
  if (index > -1) {
    expandedUsers.value.splice(index, 1)
  } else {
    expandedUsers.value.push(userId)
  }
}

const openRoleModal = (user) => {
  selectedUser.value = user
  roleForm.role = user.role
  showRoleModal.value = true
}

const updateUserRole = () => {
  roleForm.put(route('administrateur.users.role', selectedUser.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showRoleModal.value = false
      selectedUser.value = null
    }
  })
}

const confirmDeleteUser = (user) => {
  selectedUser.value = user
  showDeleteModal.value = true
}

const deleteUser = () => {
  router.delete(route('administrateur.users.delete', selectedUser.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      selectedUser.value = null
    }
  })
}

const blockUser = (userId) => {
  router.post(route('administrateur.users.block', userId), {}, {
    preserveScroll: true
  })
}

const unblockUser = (userId) => {
  router.post(route('administrateur.users.unblock', userId), {}, {
    preserveScroll: true
  })
}

const getRoleLabel = (role) => {
  const labels = {
    'client': 'Utilisateur',
    'validator': 'Validateur',
    'administrateur': 'Administrateur'
  }
  return labels[role] || role
}

const getRoleBadgeClass = (role) => {
  const classes = {
    'client': 'bg-blue-100 text-blue-800 border border-blue-200',
    'validator': 'bg-green-100 text-green-800 border border-green-200',
    'administrateur': 'bg-purple-100 text-purple-800 border border-purple-200'
  }
  return classes[role] || 'bg-gray-100 text-gray-800 border border-gray-200'
}

const getProprietaireDisplay = (proprietaire) => {
  if (!proprietaire) return 'N/A'

  if (proprietaire.type === 'personnel') {
    return `${proprietaire.prenom} ${proprietaire.nom}`
  } else {
    return proprietaire.raison_sociale
  }
}

const getFilteredCount = (role) => {
  return props.users.data.filter(user => user.role === role).length
}
</script>
