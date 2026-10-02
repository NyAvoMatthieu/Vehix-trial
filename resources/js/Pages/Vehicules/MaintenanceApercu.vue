<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceEcheanceStatutBadge from '@/Components/MaintenanceEcheanceStatutBadge.vue';

defineProps({
    apercu: Array,
});
</script>

<template>
    <AppLayout title="Maintenance préventive">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🔧 Maintenance préventive — vue d'ensemble
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link
                        v-for="item in apercu"
                        :key="item.vehicule.id"
                        :href="route('vehicules.maintenance.show', item.vehicule.id)"
                        class="bg-white shadow-lg rounded-xl p-5 border border-gray-200 hover:border-indigo-300 hover:shadow-xl transition-all"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-gray-900">{{ item.vehicule.alias }}</div>
                                <div class="text-sm text-gray-500">{{ item.vehicule.make }} — {{ item.vehicule.license_plate }}</div>
                            </div>
                            <MaintenanceEcheanceStatutBadge
                                v-if="item.pire_statut"
                                :statut="item.pire_statut"
                                :label="item.pire_statut_label"
                            />
                            <span v-else class="text-xs text-gray-400 italic">Aucune donnée</span>
                        </div>
                        <div v-if="item.nb_alertes > 0" class="text-sm text-orange-600 font-medium">
                            ⚠️ {{ item.nb_alertes }} intervention(s) à surveiller
                        </div>
                        <div v-else class="text-sm text-green-600 font-medium">
                            ✅ Tout est à jour
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
