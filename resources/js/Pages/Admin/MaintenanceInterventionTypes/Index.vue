<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    types: Array,
});

const showForm = ref(false);
const editingId = ref(null);

const emptyForm = () => ({
    nom: '',
    description: '',
    intervalle_km: null,
    intervalle_jours: null,
    actif: true,
});

const form = ref(emptyForm());

const startCreate = () => {
    editingId.value = null;
    form.value = emptyForm();
    showForm.value = true;
};

const startEdit = (type) => {
    editingId.value = type.id;
    form.value = {
        nom: type.nom,
        description: type.description,
        intervalle_km: type.intervalle_km,
        intervalle_jours: type.intervalle_jours,
        actif: type.actif,
    };
    showForm.value = true;
};

const cancel = () => {
    showForm.value = false;
    editingId.value = null;
};

const submit = () => {
    if (editingId.value) {
        router.put(route('administrateur.maintenance-types.update', editingId.value), form.value, {
            onSuccess: cancel,
        });
    } else {
        router.post(route('administrateur.maintenance-types.store'), form.value, {
            onSuccess: cancel,
        });
    }
};

const desactiver = (type) => {
    if (confirm(`Désactiver « ${type.nom} » ? Il n'apparaîtra plus dans les nouveaux suivis véhicule.`)) {
        router.delete(route('administrateur.maintenance-types.destroy', type.id));
    }
};
</script>

<template>
    <AppLayout title="Catalogue de maintenance">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🔧 Catalogue des interventions de maintenance
                </h2>
                <PrimaryButton @click="startCreate">+ Nouveau type</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Formulaire création / édition -->
                <div v-if="showForm" class="bg-white shadow-lg rounded-xl p-6 border-2 border-indigo-200">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        {{ editingId ? "Modifier le type d'intervention" : "Nouveau type d'intervention" }}
                    </h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="form.nom" type="text" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="form.description" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Intervalle (km)</label>
                                <input v-model.number="form.intervalle_km" type="number" min="0"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Intervalle (jours)</label>
                                <input v-model.number="form.intervalle_jours" type="number" min="0"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Au moins un des deux intervalles doit être renseigné.</p>
                        <div class="flex gap-3">
                            <PrimaryButton type="submit">{{ editingId ? 'Enregistrer' : 'Créer' }}</PrimaryButton>
                            <button type="button" @click="cancel" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Liste -->
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervalle km</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervalle jours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="type in types" :key="type.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ type.nom }}</div>
                                    <div v-if="type.description" class="text-xs text-gray-500">{{ type.description }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ type.intervalle_km ? `${type.intervalle_km.toLocaleString('fr-FR')} km` : '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ type.intervalle_jours ? `${type.intervalle_jours} j` : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-3 py-1 text-xs font-semibold rounded-full',
                                        type.actif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                                    ]">
                                        {{ type.actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <button @click="startEdit(type)" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Modifier
                                    </button>
                                    <button v-if="type.actif" @click="desactiver(type)" class="text-red-600 hover:text-red-900">
                                        Désactiver
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
