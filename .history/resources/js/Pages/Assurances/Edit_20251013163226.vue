<template>
  <AppLayout title="Modifier l'assurance">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Modifier le contrat d'assurance
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
          <form @submit.prevent="submit" class="divide-y divide-gray-200">
            
            <!-- En-tête du formulaire -->
            <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-blue-50 border-b-4 border-indigo-500">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-2xl font-bold text-gray-900">
                    Contrat d'Assurance Véhicule
                  </h3>
                  <p class="mt-1 text-sm text-gray-600">
                    Modification du document officiel
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-700">Référence:</p>
                  <p class="text-lg font-mono font-semibold text-indigo-600">
                    {{ form.policy_number }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Sélection du véhicule -->
            <div class="px-6 py-6 bg-gray-50">
              <div class="max-w-md">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Véhicule assuré *
                </label>
                <select
                  v-model="form.vehicule_id"
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                >
                  <option value="">Sélectionner un véhicule...</option>
                  <option v-for="vehicule in vehicules" :key="vehicule.id" :value="vehicule.id">
                    {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }} - {{ vehicule.license_plate }}
                  </option>
                </select>
                <p v-if="form.errors.vehicule_id" class="mt-1 text-sm text-red-600">
                  {{ form.errors.vehicule_id }}
                </p>
              </div>
            </div>

            <!-- 🏢 INFORMATIONS ADMINISTRATIVES DU CONTRAT -->
            <div class="px-6 py-6">
              <div class="mb-6">
                <h4 class="text-lg font-bold text-gray-900 flex items-center">
                  <span class="text-2xl mr-2">🏢</span>
                  Informations Administratives du Contrat
                </h4>
                <div class="mt-1 h-1 w-24 bg-indigo-500 rounded"></div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 w-1/4">
                        Champ
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 w-1/2">
                        Description / Exemple
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/4">
                        Valeur
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Assureur *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Nom de la compagnie (ex: Allianz, Ny Havana, ARO)
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.assureur"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          required
                        />
                        <p v-if="form.errors.assureur" class="mt-1 text-xs text-red-600">
                          {{ form.errors.assureur }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Agence
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Nom ou code de l'agence / courtier
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.agence"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Police d'assurance *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Numéro unique du contrat
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.policy_number"
                          type="text"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                          required
                        />
                        <p v-if="form.errors.policy_number" class="mt-1 text-xs text-red-600">
                          {{ form.errors.policy_number }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de délivrance
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Date d'émission du contrat/attestation
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.date_delivrance"
                          type="date"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de début *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Début de la couverture d'assurance
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.start_date"
                          type="date"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          required
                        />
                        <p v-if="form.errors.start_date" class="mt-1 text-xs text-red-600">
                          {{ form.errors.start_date }}
                        </p>
                      </td>
                    </tr>

                    <tr>
                      <td class="px-4 py-4 font-medium text-gray-900 border-r border-gray-300 bg-gray-50">
                        Date de fin *
                      </td>
                      <td class="px-4 py-4 text-sm text-gray-600 border-r border-gray-300">
                        Date d'expiration de la couverture
                      </td>
                      <td class="px-4 py-4">
                        <input
                          v-model="form.end_date"
                          type="date"
                          class="block w-full border-gray-300 rounded-m-->