<template>
  <AppLayout title="Détails du véhicule">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Détails du véhicule
        </h2>
        <div class="flex space-x-3">
          <Link
            :href="route('vehicules.selection')"
            class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Retour
          </Link>
          <Link
            v-if="canEdit"
            :href="route('vehicules.edit', vehicule.id)"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <PencilIcon class="h-4 w-4 mr-2" />
            Modifier
          </Link>
          <button
            v-if="canDelete"
            @click="confirmDelete"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            <TrashIcon class="h-4 w-4 mr-2" />
            Supprimer
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Status Alert -->
        <div v-if="vehicule.status !== 'valide'" class="mb-6">
          <div :class="getAlertClass(vehicule.status)" class="rounded-xl p-4 shadow-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <component :is="getStatusIcon(vehicule.status)" class="h-6 w-6" :class="getAlertIconColor(vehicule.status)" />
              </div>
              <div class="ml-3 flex-1">
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
          <div class="p-6 space-y-8">

            <!-- En-tête avec badge de statut -->
            <div class="flex items-center justify-between pb-6 border-b-2 border-gray-200">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">
                  {{ vehicule.make }} {{ vehicule.model }}
                </h3>
                <p class="mt-1 text-sm text-gray-600 font-mono">
                  {{ vehicule.license_plate }}
                </p>
              </div>
              <VehiculeStatusBadge :status="vehicule.status" />
            </div>

            <!--  Carte d'info du véhicule avec kilométrage actuel -->
            <VehiculeInfoCard
              :vehicule="vehicule"
              :proprietaire="vehicule.proprietaire"
              :currentKilometrage="currentKilometrage"
              :lastUpdate="lastUpdate"
            />

            <!-- Propriétaire du véhicule -->
            <div v-if="vehicule.proprietaire" class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <UserCircleIcon class="h-6 w-6 text-indigo-600 mr-2" />
                <label class="text-base font-semibold text-gray-800">
                  Propriétaire du véhicule
                </label>
              </div>

              <div class="p-4 bg-white border-2 border-indigo-200 rounded-lg shadow-sm">
                <div class="flex items-center space-x-3">
                  <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-md">
                    <UserIcon v-if="vehicule.proprietaire.type === 'personnel'" class="h-6 w-6 text-white" />
                    <BuildingOfficeIcon v-else class="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-900">
                      {{ getProprietaireDisplayName(vehicule.proprietaire) }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ vehicule.proprietaire.type === 'personnel' ? 'Personne physique' : 'Entreprise' }}
                    </p>
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
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Marque</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.make || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Modèle</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.model || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alias</label>
                   <p class="text-base font-semibold text-gray-900">{{ vehicule.alias || 'Non spécifié' }}</p>
              </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Type véhicule</label>
                  <p class="text-base font-semibold text-gray-900">{{ getVehiculeTypeLabel(vehicule.vehicule_type) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Date 1ère mise en circulation</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatYear(vehicule.year) }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Plaque d'immatriculation</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.license_plate || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Catégorie (Genre)</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.categorie || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Carrosserie</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.carrosserie || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Couleur</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.color || 'Non spécifié' }}</p>
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
                <div class="sm:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Numéro VIN (Châssis)</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.vin || 'Non spécifié' }}</p>
                  <p class="mt-1 text-xs text-gray-500">Le numéro VIN se trouve généralement sur le tableau de bord côté conducteur</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">N° dans la série du type</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.numero_serie_type || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Numéro moteur</label>
                  <p class="text-base font-semibold text-gray-900 font-mono">{{ vehicule.numero_moteur || 'Non spécifié' }}</p>
                </div>
              </div>
            </div>

            <!-- Caractéristiques moteur -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <CogIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Caractéristiques moteur</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Type de carburant</label>
                  <p class="text-base font-semibold text-gray-900">{{ getFuelTypeLabel(vehicule.fuel_type) }}</p>
                </div>

                <div v-if="vehicule.average_consumption" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border-2 border-green-200 shadow-sm">
                  <label class="block text-sm font-medium text-green-700 mb-1">Consommation moyenne</label>
                  <p class="text-2xl font-bold text-green-700">
                    {{ parseFloat(vehicule.average_consumption).toFixed(2) }} <span class="text-sm">L/100km</span>
                  </p>
                  <p class="mt-1 text-xs text-green-600">💡 Calculée automatiquement</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Cylindrée</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.cylindree ? `${formatNumber(vehicule.cylindree)} cm³` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Puissance administrative</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.puissance_administrative ? `${vehicule.puissance_administrative} CV` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Kilométrage actuel</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatNumber(currentKilometrage !== null ? currentKilometrage : vehicule.mileage) }} km</p>
                  <p v-if="lastUpdate" class="text-xs text-gray-500 mt-1">
                        Mis à jour le {{ formatDate(lastUpdate) }}
                    </p>
                </div>
              </div>
            </div>

            <!-- Capacités et poids -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <ScaleIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Capacités et poids</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Places assises</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.places_assises ? `${vehicule.places_assises} places` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Poids à vide</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.poids_vide ? `${formatNumber(vehicule.poids_vide)} kg` : 'Non spécifié' }}
                  </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1 flex items-center">
                    PTAC
                    <span class="ml-1 text-xs text-gray-400" title="Poids Total Autorisé en Charge">ⓘ</span>
                  </label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.poids_total_charge ? `${formatNumber(vehicule.poids_total_charge)} kg` : 'Non spécifié' }}
                  </p>
                  <p class="mt-1 text-xs text-gray-500">Poids Total Autorisé en Charge</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Charge utile</label>
                  <p class="text-base font-semibold text-gray-900">
                    {{ vehicule.charge_utile ? `${formatNumber(vehicule.charge_utile)} kg` : 'Non spécifié' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Informations propriétaire et validation -->
            <div class="space-y-6">
              <div class="flex items-center space-x-2 border-b-2 border-gray-200 pb-2">
                <InformationCircleIcon class="h-5 w-5 text-indigo-600" />
                <h3 class="text-lg font-semibold text-gray-800">Propriétaire et validation</h3>
              </div>

              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Propriétaire</label>
                  <p class="text-base font-semibold text-gray-900">{{ vehicule.user?.name || 'Non spécifié' }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Date d'ajout</label>
                  <p class="text-base font-semibold text-gray-900">{{ formatDate(vehicule.created_at) }}</p>
                </div>

                <div v-if="vehicule.validated_at" class="sm:col-span-2 bg-green-50 rounded-lg p-4 border-2 border-green-200">
                  <label class="block text-sm font-medium text-green-700 mb-1">✅ Validé par</label>
                  <p class="text-base font-semibold text-green-900">
                    {{ vehicule.validator?.name }} le {{ formatDate(vehicule.validated_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <ChartBarIcon class="h-6 w-6 text-blue-600 mr-2" />
                <h3 class="text-lg font-semibold text-gray-800">Statistiques rapides</h3>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <button
                  @click="toggleStatDetail('assurances')"
                  class="bg-white rounded-lg p-4 shadow-sm border border-blue-200 hover:border-indigo-400 hover:shadow-md transition-all duration-200 text-left group"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-200">🛡️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.assurances?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Assurances</p>
                </button>

                <button
                  @click="toggleStatDetail('maintenances')"
                  class="bg-white rounded-lg p-4 shadow-sm border border-blue-200 hover:border-indigo-400 hover:shadow-md transition-all duration-200 text-left group"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-200">⚙️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.maintenances?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Maintenances</p>
                </button>

                <button
                  @click="toggleStatDetail('ravitaillements')"
                  class="bg-white rounded-lg p-4 shadow-sm border border-blue-200 hover:border-indigo-400 hover:shadow-md transition-all duration-200 text-left group"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-200">⛽</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.ravitaillements?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Ravitaillements</p>
                </button>

                <button
                  @click="toggleStatDetail('trajets')"
                  class="bg-white rounded-lg p-4 shadow-sm border border-blue-200 hover:border-indigo-400 hover:shadow-md transition-all duration-200 text-left group"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-200">🗺️</span>
                    <span class="text-2xl font-bold text-indigo-600">{{ vehicule.trajets?.length || 0 }}</span>
                  </div>
                  <p class="mt-2 text-xs font-medium text-gray-600">Trajets</p>
                </button>
              </div>

              <!-- Detail Panels -->
              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 transform -translate-y-2"
                enter-to-class="opacity-100 transform translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 transform translate-y-0"
                leave-to-class="opacity-0 transform -translate-y-2"
              >
                <div v-if="activeStatDetail" class="mt-6 bg-white rounded-xl border-2 border-indigo-200 shadow-lg overflow-hidden">
                  <!-- Assurances Detail -->
                  <div v-if="activeStatDetail === 'assurances'" class="p-4">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-indigo-100">
                      <h4 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="text-xl mr-2">🛡️</span>
                        Assurances
                      </h4>
                      <button @click="activeStatDetail = null" class="text-gray-400 hover:text-gray-600">
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>
                    <div v-if="vehicule.assurances?.length > 0" class="space-y-3">
                      <div v-for="assurance in vehicule.assurances.slice(0, 3)" :key="assurance.id" class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <p class="text-sm font-semibold text-gray-900">{{ assurance.company }}</p>
                        <p class="text-xs text-gray-600 mt-1">Police N° {{ assurance.policy_number }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                          Expire le {{ formatDate(assurance.end_date) }}
                        </p>
                      </div>
                      <Link
                        :href="route('assurances.index')"
                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium mt-2"
                      >
                        Voir toutes les assurances →
                      </Link>
                    </div>
                    <p v-else class="text-sm text-gray-500 text-center py-3">Aucune assurance enregistrée</p>
                  </div>

                  <!-- Maintenances Detail -->
                  <div v-if="activeStatDetail === 'maintenances'" class="p-4">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-indigo-100">
                      <h4 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="text-xl mr-2">⚙️</span>
                        Maintenances
                      </h4>
                      <button @click="activeStatDetail = null" class="text-gray-400 hover:text-gray-600">
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>
                    <div v-if="vehicule.maintenances?.length > 0" class="space-y-3">
                      <div v-for="maintenance in vehicule.maintenances.slice(0, 3)" :key="maintenance.id" class="bg-gradient-to-r from-gray-50 to-orange-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <p class="text-sm font-semibold text-gray-900">{{ maintenance.nature_intervention }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ maintenance.garage_nom }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                          Le {{ formatDate(maintenance.date_debut) }}
                        </p>
                      </div>
                      <Link
                        :href="route('maintenances.index')"
                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium mt-2"
                      >
                        Voir toutes les maintenances →
                      </Link>
                    </div>
                    <p v-else class="text-sm text-gray-500 text-center py-3">Aucune maintenance enregistrée</p>
                  </div>

                  <!-- Ravitaillements Detail -->
                  <div v-if="activeStatDetail === 'ravitaillements'" class="p-4">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-indigo-100">
                      <h4 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="text-xl mr-2">⛽</span>
                        Ravitaillements
                      </h4>
                      <button @click="activeStatDetail = null" class="text-gray-400 hover:text-gray-600">
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>
                    <div v-if="vehicule.ravitaillements?.length > 0" class="space-y-3">
                      <div v-for="ravitaillement in vehicule.ravitaillements.slice(0, 3)" :key="ravitaillement.id" class="bg-gradient-to-r from-gray-50 to-green-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <p class="text-sm font-semibold text-gray-900">{{ ravitaillement.station_service }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ ravitaillement.liters_purchased }} L - {{ formatNumber(ravitaillement.amount_paid) }} Ar</p>
                        <p class="text-xs text-gray-500 mt-1">
                          Le {{ formatDate(ravitaillement.ravitaillement_date) }}
                        </p>
                      </div>
                      <Link
                        :href="route('ravitaillements.index')"
                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium mt-2"
                      >
                        Voir tous les ravitaillements →
                      </Link>
                    </div>
                    <p v-else class="text-sm text-gray-500 text-center py-3">Aucun ravitaillement enregistré</p>
                  </div>

                  <!-- Trajets Detail -->
                  <div v-if="activeStatDetail === 'trajets'" class="p-4">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-indigo-100">
                      <h4 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="text-xl mr-2">🗺️</span>
                        Trajets
                      </h4>
                      <button @click="activeStatDetail = null" class="text-gray-400 hover:text-gray-600">
                        <XMarkIcon class="h-5 w-5" />
                      </button>
                    </div>
                    <div v-if="vehicule.trajets?.length > 0" class="space-y-3">
                      <div v-for="trajet in vehicule.trajets.slice(0, 3)" :key="trajet.id" class="bg-gradient-to-r from-gray-50 to-purple-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <p class="text-sm font-semibold text-gray-900">{{ trajet.departure }} → {{ trajet.destination }}</p>
                        <p class="text-xs text-gray-600 mt-1">Distance: {{ trajet.distance }} km</p>
                        <p class="text-xs text-gray-500 mt-1">
                          Le {{ formatDate(trajet.heure_depart) }}
                        </p>
                      </div>
                      <Link
                        :href="route('trajets.index')"
                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium mt-2"
                      >
                        Voir tous les trajets →
                      </Link>
                    </div>
                    <p v-else class="text-sm text-gray-500 text-center py-3">Aucun trajet enregistré</p>
                  </div>
                </div>
              </transition>
            </div>

            <!-- Validation Section (for validators/admins) -->
            <div v-if="canValidate && vehicule.status === 'en_attente'" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6 shadow-sm">
              <div class="flex items-center mb-4">
                <CheckCircleIcon class="h-6 w-6 text-green-600 mr-2" />
                <h3 class="text-lg font-semibold text-gray-800">Validation du véhicule</h3>
              </div>
              <ValidationForm :vehicule="vehicule" @validated="handleValidation" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VehiculeInfoCard from '@/Components/VehiculeInfoCard.vue'
import VehiculeStatusBadge from '@/Components/VehiculeStatusBadge.vue'
import ValidationForm from '@/Components/ValidationForm.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  TrashIcon,
  ClockIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  DocumentDuplicateIcon,
  TruckIcon,
  DocumentTextIcon,
  CogIcon,
  ScaleIcon,
  UserCircleIcon,
  ChartBarIcon,
  CheckCircleIcon,
  InformationCircleIcon,
  UserIcon,
  BuildingOfficeIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  vehicule: Object,
  canValidate: Boolean
})

// Calcul de kilométrage actuel
const currentKilometrage = computed(() => {
  // Priorité 1 : Dernier trajet
  const lastTrajet = props.vehicule.trajets?.[0]
  if (lastTrajet?.odo_end) {
    return lastTrajet.odo_end
  }

  // Priorité 2 : Dernière maintenance
  const lastMaintenance = props.vehicule.maintenances?.[0]
  if (lastMaintenance?.kilometrage_actuel) {
    return lastMaintenance.kilometrage_actuel
  }

  // Priorité 3 : Dernier ravitaillement
  const lastRavitaillement = props.vehicule.ravitaillements?.[0]
  if (lastRavitaillement?.odo_station) {
    return lastRavitaillement.odo_station
  }

  //  Kilométrage initial du véhicule
  return props.vehicule.mileage
})

// Date de dernière mise à jour
const lastUpdate = computed(() => {
  const lastTrajet = props.vehicule.trajets?.[0]
  const lastMaintenance = props.vehicule.maintenances?.[0]
  const lastRavitaillement = props.vehicule.ravitaillements?.[0]

  const dates = [
    lastTrajet?.heure_depart,
    lastMaintenance?.date_debut,
    lastRavitaillement?.ravitaillement_date
  ].filter(Boolean)

  if (dates.length === 0) return props.vehicule.created_at

  // Retourner la date la plus récente
  return dates.sort((a, b) => new Date(b) - new Date(a))[0]
})


const activeStatDetail = ref(null)

const toggleStatDetail = (type) => {
  if (activeStatDetail.value === type) {
    activeStatDetail.value = null
  } else {
    activeStatDetail.value = type
  }
}

const canEdit = computed(() => {
  const statusValue = typeof props.vehicule.status === 'object'
    ? props.vehicule.status.value
    : props.vehicule.status

  // ✅ Option 1 : Autoriser la modification pour tous les statuts
  return true

  // ✅ Option 2 : Autoriser uniquement certains statuts (incluant "valide")
  // return ['en_attente', 'refuse', 'a_corriger', 'doublon', 'valide'].includes(statusValue)
})

const canDelete = computed(() => {
  return canEdit.value
})

const getStatusValue = (status) => {
  return typeof status === 'object' ? status.value : status
}

const getProprietaireDisplayName = (proprietaire) => {
  if (!proprietaire) return 'Non spécifié'

  if (proprietaire.type === 'personnel') {
    return `${proprietaire.prenom || ''} ${proprietaire.nom || ''}`.trim() || 'Non spécifié'
  }

  return proprietaire.nom_commercial || proprietaire.raison_sociale || 'Non spécifié'
}

const getVehiculeTypeLabel = (type) => {
  const labels = {
    'voiture': '🚗 Voiture',
    'moto': '🏍️ Moto',
    'utilitaire': '🚚 Véhicule utilitaire',
    'camion': '🚛 Camion',
    'bus': '🚌 Bus',
    'autre': '🔧 Autre'
  }
  return labels[type] || type || 'Non spécifié'
}

const getFuelTypeLabel = (type) => {
  const labels = {
    'essence': '⛽ Essence',
    'diesel': '🛢️ Diesel',
    'hybride': '🔋 Hybride',
    'electrique': '⚡ Électrique',
    'gpl': '💨 GPL'
  }
  return labels[type] || type || 'Non spécifié'
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
    'en_attente': 'Ce véhicule est en cours de validation par notre équipe.',
    'refuse': 'Ce véhicule a été refusé. Consultez les notes ci-dessous.',
    'a_corriger': 'Des corrections sont nécessaires avant validation.',
    'doublon': 'Ce véhicule semble déjà exister dans le système.'
  }
  return messages[statusValue] || ''
}

const formatDate = (date) => {
  if (!date) return 'Non spécifié'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatYear = (date) => {
  if (!date) return 'Non spécifié'
  return new Date(date).getFullYear()
}

const formatNumber = (num) => {
  if (num === null || num === undefined) return '0'
  return new Intl.NumberFormat('fr-FR').format(num)
}

const confirmDelete = () => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')) {
    router.delete(route('vehicules.destroy', props.vehicule.id), {
      onSuccess: () => {
        router.visit(route('vehicules.selection'))
      }
    })
  }
}

const handleValidation = () => {
  router.reload({ only: ['vehicule'] })
}
</script>
