<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import MaintenanceEcheanceStatutBadge from "@/Components/MaintenanceEcheanceStatutBadge.vue";
import EcheancePulseDot from "@/Components/EcheancePulseDot.vue";

const props = defineProps({
  vehicule: Object,
  suivis: Array,
});

const formatDate = (date) => {
  if (!date) return null;
  return new Date(date).toLocaleDateString("fr-FR");
};

console.log(props.suivis);
</script>

<template>
  <AppLayout title="Suivi maintenance">
    <template #header>
      <div class="flex items-center gap-3">
        <span class="text-2xl">🔧</span>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Suivi maintenance — {{ vehicule.alias }}
          <span class="text-gray-500 font-normal text-base"
            >({{ vehicule.license_plate }})</span
          >
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Intervention
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Dernier passage
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Prochaine échéance
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Restant
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Statut
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="suivis.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <div
                      class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                    >
                      <span class="text-2xl">🔧</span>
                    </div>
                    <p class="text-gray-500 font-medium">
                      Aucun type d'intervention actif dans le catalogue
                    </p>
                  </div>
                </td>
              </tr>
              <tr
                v-for="s in suivis"
                :key="s.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ s.type }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  <div v-if="s.derniere_date_effectuee">
                    📅 {{ formatDate(s.derniere_date_effectuee) }}
                  </div>
                  <div v-if="s.dernier_km_effectue">
                    🛣️ {{ s.dernier_km_effectue.toLocaleString("fr-FR") }} km
                  </div>
                  <div
                    v-if="!s.derniere_date_effectuee && !s.dernier_km_effectue"
                    class="text-gray-400 italic"
                  >
                    Jamais effectuée
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  <div v-if="s.prochaine_date">📅 {{ formatDate(s.prochaine_date) }}</div>
                  <div v-if="s.prochain_km">
                    🛣️ {{ s.prochain_km.toLocaleString("fr-FR") }} km
                  </div>
                  <div v-if="!s.prochaine_date && !s.prochain_km" class="text-gray-400">
                    —
                  </div>
                </td>
                <td class="px-6 py-4 text-sm">
                  <div
                    v-if="s.jours_restants !== null"
                    :class="
                      s.jours_restants < 0
                        ? 'text-red-600 font-semibold'
                        : 'text-gray-700'
                    "
                  >
                    {{
                      s.jours_restants >= 0
                        ? `${s.jours_restants} j`
                        : `${Math.abs(s.jours_restants)} j de retard`
                    }}
                  </div>
                  <div
                    v-if="s.km_restants !== null"
                    :class="
                      s.km_restants < 0 ? 'text-red-600 font-semibold' : 'text-gray-700'
                    "
                  >
                    {{
                      s.km_restants >= 0
                        ? `${s.km_restants.toLocaleString("fr-FR")} km`
                        : `${Math.abs(s.km_restants).toLocaleString(
                            "fr-FR"
                          )} km de retard`
                    }}
                  </div>
                </td>
                <td class="px-6 py-4 flex items-center gap-2">
                  <EcheancePulseDot :statut="s.statut" />
                  <MaintenanceEcheanceStatutBadge
                    :statut="s.statut"
                    :label="s.statut_label"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
