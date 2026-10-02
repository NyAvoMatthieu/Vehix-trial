<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  settings: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  settings: props.settings.map((s) => ({
    type: s.type,
    seuil_jours: s.seuil_jours,
    periodicite_jours: s.periodicite_jours,
    // ⭐ NOUVEAU — Partie 4 : seuils maintenance (utilisés uniquement sur la ligne "maintenance")
    seuil_km: s.seuil_km,
    retard_jours: s.retard_jours,
    retard_km: s.retard_km,
    actif: s.actif,
  })),
});

const submit = () => {
  form.put(route("administrateur.alert-settings.update"), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout title="Seuils d'alerte">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Seuils d'alerte</h2>
    </template>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">
        <p class="text-sm text-gray-600 mb-6">
          Définissez, pour chaque type d'échéance, le nombre de jours avant la date limite
          à partir duquel une alerte "bientôt échue" doit être générée.
        </p>

        <form @submit.prevent="submit" class="space-y-6">
          <div
            v-for="(setting, index) in form.settings"
            :key="setting.type"
            class="flex flex-wrap items-center justify-between gap-4 border-b pb-4"
          >
            <div class="flex-1 min-w-[10rem]">
              <p class="font-medium text-gray-800">
                {{ props.settings[index].label }}
              </p>
            </div>

            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Seuil (jours)</label>
              <input
                type="number"
                min="1"
                max="365"
                v-model.number="setting.seuil_jours"
                class="w-20 border-gray-300 rounded-md shadow-sm text-sm"
              />
            </div>

            <div
              v-if="setting.type === 'visite_technique'"
              class="flex items-center gap-2"
            >
              <label
                class="text-sm text-gray-600"
                title="Utilisée pour estimer la prochaine visite quand la date officielle n'est pas renseignée"
              >
                Périodicité (jours)
              </label>
              <input
                type="number"
                min="1"
                max="3650"
                v-model.number="setting.periodicite_jours"
                placeholder="Non renseignée"
                class="w-24 border-gray-300 rounded-md shadow-sm text-sm"
              />
            </div>

            <!-- ⭐ NOUVEAU — Partie 4 : champs visibles uniquement sur la ligne "maintenance" -->
            <template v-if="setting.type === 'maintenance'">
              <div class="flex items-center gap-2">
                <label
                  class="text-sm text-gray-600"
                  title="Nombre de km avant l'échéance à partir duquel une alerte 'bientôt due' est générée"
                >
                  Seuil (km)
                </label>
                <input
                  type="number"
                  min="1"
                  max="50000"
                  v-model.number="setting.seuil_km"
                  placeholder="Non renseigné"
                  class="w-24 border-gray-300 rounded-md shadow-sm text-sm"
                />
              </div>

              <div class="flex items-center gap-2">
                <label
                  class="text-sm text-gray-600"
                  title="Jours au-delà de l'échéance à partir desquels le statut passe de 'due' à 'en retard'"
                >
                  Retard (jours)
                </label>
                <input
                  type="number"
                  min="1"
                  max="365"
                  v-model.number="setting.retard_jours"
                  placeholder="Non renseigné"
                  class="w-24 border-gray-300 rounded-md shadow-sm text-sm"
                />
              </div>

              <div class="flex items-center gap-2">
                <label
                  class="text-sm text-gray-600"
                  title="Km au-delà de l'échéance à partir desquels le statut passe de 'due' à 'en retard'"
                >
                  Retard (km)
                </label>
                <input
                  type="number"
                  min="1"
                  max="50000"
                  v-model.number="setting.retard_km"
                  placeholder="Non renseigné"
                  class="w-24 border-gray-300 rounded-md shadow-sm text-sm"
                />
              </div>
            </template>

            <div class="flex items-center gap-2">
              <label class="text-sm text-gray-600">Actif</label>
              <input
                type="checkbox"
                v-model="setting.actif"
                class="rounded border-gray-300"
              />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <PrimaryButton :disabled="form.processing"> Enregistrer </PrimaryButton>
            <span v-if="form.recentlySuccessful" class="text-sm text-green-600">
              Enregistré.
            </span>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
