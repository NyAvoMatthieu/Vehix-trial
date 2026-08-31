<template>
  <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
            <TruckIcon class="h-10 w-10 text-white" />
          </div>
          <div>
            <h3 class="text-2xl font-bold text-white">
              {{ vehicule.year }} {{ vehicule.make }} {{ vehicule.model }}
            </h3>
            <p class="text-indigo-100 text-sm mt-1">
              Véhicule Sélectionné
            </p>
          </div>
        </div>
        <button
          @click="deselectVehicle"
          class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-md text-sm font-medium transition-colors duration-200"
          title="Changer de véhicule"
        >
          Changer
        </button>
      </div>
    </div>

    <div class="px-6 py-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Immatriculation -->
        <div class="flex items-center space-x-3">
          <div class="h-12 w-12 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500">Immatriculation</p>
            <p class="text-lg font-bold text-gray-900 font-mono">
              {{ vehicule.license_plate }}
            </p>
          </div>
        </div>

        <!-- Type de Carburant -->
        <div class="flex items-center space-x-3">
          <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500">Carburant</p>
            <p class="text-lg font-semibold text-gray-900">
              {{ formatFuelType(vehicule.fuel_type) }}
            </p>
          </div>
        </div>

        <!-- Kilométrage -->
        <div class="flex items-center space-x-3">
          <div class="h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center">
            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500">Kilométrage</p>
            <p class="text-lg font-semibold text-gray-900">
              {{ formatNumber(vehicule.mileage) }} km
            </p>
          </div>
        </div>
      </div>

      <!-- Informations supplémentaires -->
      <div v-if="vehicule.color" class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
          <span class="font-medium">Couleur:</span>
          <span>{{ vehicule.color }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { TruckIcon } from '@heroicons/vue/24/outline';

defineProps({
  vehicule: {
    type: Object,
    required: true
  }
});

const deselectVehicle = () => {
  if (confirm('Voulez-vous vraiment changer de véhicule ? Vous serez redirigé vers la page de sélection.')) {
    router.post(route('vehicules.deselect'));
  }
};

const formatFuelType = (type) => {
  const types = {
    'essence': 'Essence',
    'diesel': 'Diesel',
    'electrique': 'Électrique',
    'hybride': 'Hybride',
    'gpl': 'GPL'
  };
  return types[type] || type;
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('fr-FR').format(num);
};
</script>
