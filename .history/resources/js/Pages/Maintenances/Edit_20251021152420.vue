<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    maintenance: Object,
    techniciens: Array,
    maintenanceTypes: Array,
});

// Vérifier que les données existent avant de les utiliser
const form = useForm({
    technicien_id: props.maintenance?.technicien_id || null,
    type: props.maintenance?.type || 'preventive',
    nature_intervention: props.maintenance?.nature_intervention || '',
    kilometrage_actuel: props.maintenance?.kilometrage_actuel || 0,
    date_debut: props.maintenance?.date_debut || new Date().toISOString().split('T')[0],
    date_fin: props.maintenance?.date_fin || null,
    observation_generale: props.maintenance?.observation_generale || '',
    cout_main_oeuvre: props.maintenance?.cout_main_oeuvre || 0,
    status: props.maintenance?.status || 'en_attente',
    pieces: Array.isArray(props.maintenance?.pieces) ? props.maintenance.pieces.map(piece => ({
        id: piece.id,
        nom_piece: piece.nom_piece || '',
        reference_code: piece.reference_code || '',
        emplacement: piece.emplacement || '',
        quantite: piece.quantite || 1,
        prix_unitaire: piece.prix_unitaire || 0,
        date_installation: piece.date_installation || new Date().toISOString().split('T')[0],
        limite_utilisation: piece.limite_utilisation || 0,
        utilisation_actuelle: piece.utilisation_actuelle || 0,
        unite_mesure: piece.unite_mesure || 'km',
        observation: piece.observation || '',
    })) : [],
});

const addPiece = () => {
    form.pieces.push({
        nom_piece: '',
        reference_code: '',
        emplacement: '',
        quantite: 1,
        prix_unitaire: 0,
        date_installation: new Date().toISOString().split('T')[0],
        limite_utilisation: 0,
        utilisation_actuelle: 0,
        unite_mesure: 'km',
        observation: '',
    });
};

const removePiece = (index) => {
    form.pieces.splice(index, 1);
};

const calculatePrixTotal = (piece) => {
    const quantite = parseFloat(piece.quantite) || 0;
    const prixUnitaire = parseFloat(piece.prix_unitaire) || 0;
    return (quantite * prixUnitaire).toFixed(2);
};

const calculatePotentielRestant = (piece) => {
    const limite = parseFloat(piece.limite_utilisation) || 0;
    const utilisation = parseFloat(piece.utilisation_actuelle) || 0;
    return (limite - utilisation).toFixed(2);
};

const coutTotalPieces = computed(() => {
    return form.pieces.reduce((total, piece) => {
        const quantite = parseFloat(piece.quantite) || 0;
        const prixUnitaire = parseFloat(piece.prix_unitaire) || 0;
        return total + (quantite * prixUnitaire);
    }, 0).toFixed(2);
});

const coutTotal = computed(() => {
    const mainOeuvre = parseFloat(form.cout_main_oeuvre) || 0;
    const pieces = parseFloat(coutTotalPieces.value) || 0;
    return (mainOeuvre + pieces).toFixed(2);
});

const submit = () => {
    form.put(route('maintenances.update', props.maintenance.id), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Erreurs de validation:', errors);
        }
    });
};
</script>

<template>
    <AppLayout title="Modifier Maintenance">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier Maintenance - {{ maintenance?.reference || 'N/A' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Vehicle Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Véhicule</h3>
                        <div v-if="maintenance?.vehicule">
                            <p class="text-gray-700">
                                <span class="font-medium">{{ maintenance.vehicule.make }} {{ maintenance.vehicule.model }}</span>
                                <span class="text-gray-500 ml-2">{{ maintenance.vehicule.license_plate }}</span>
                            </p>
                        </div>
                        <div v-else>
                            <p class="text-gray-500 italic">Véhicule non disponible</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="type" value="Type de maintenance *" />
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option v-for="type in maintenanceTypes" :key="type.value" :value="type.value">
                                            {{ type.icon }} {{ type.label }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.type" class="mt-2" />
                                </div>

                                /*<div>
                                    <InputLabel for="status" value="Statut *" />
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="en_attente">En attente</option>
                                        <option value="en_cours">En cours</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="technicien_id" value="Technicien responsable" />
                                    <select
                                        id="technicien_id"
                                        v-model="form.technicien_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option :value="null">Moi-même ({{ $page.props.auth.user.name }})</option>
                                        <option v-for="tech in techniciens" :key="tech.id" :value="tech.id">
                                            {{ tech.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.technicien_id" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6">
                                <InputLabel for="nature_intervention" value="Nature de l'intervention *" />
                                <TextInput
                                    id="nature_intervention"
                                    v-model="form.nature_intervention"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.nature_intervention" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                <div>
                                    <InputLabel for="kilometrage_actuel" value="Kilométrage actuel *" />
                                    <TextInput
                                        id="kilometrage_actuel"
                                        v-model="form.kilometrage_actuel"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.kilometrage_actuel" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="date_debut" value="Date de début *" />
                                    <TextInput
                                        id="date_debut"
                                        v-model="form.date_debut"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.date_debut" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="date_fin" value="Date de fin" />
                                    <TextInput
                                        id="date_fin"
                                        v-model="form.date_fin"
                                        type="date"
                                        class="mt-1 block w-full"
                                        :min="form.date_debut"
                                    />
                                    <InputError :message="form.errors.date_fin" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6">
                                <InputLabel for="observation_generale" value="Observation générale" />
                                <textarea
                                    id="observation_generale"
                                    v-model="form.observation_generale"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                ></textarea>
                                <InputError :message="form.errors.observation_generale" class="mt-2" />
                            </div>

                            <div class="mt-6">
                                <InputLabel for="cout_main_oeuvre" value="Coût main d'œuvre (Ar) *" />
                                <TextInput
                                    id="cout_main_oeuvre"
                                    v-model="form.cout_main_oeuvre"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.cout_main_oeuvre" class="mt-2" />
                            </div>
                        </div>

                        <!-- Pièces Section -->
                        <div class="border-b pb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Pièces remplacées / inspectées</h3>
                                <button
                                    type="button"
                                    @click="addPiece"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
                                >
                                    + Ajouter une pièce
                                </button>
                            </div>

                            <div v-if="form.pieces.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">Aucune pièce ajoutée.</p>
                            </div>

                            <div v-for="(piece, index) in form.pieces" :key="index" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="font-medium text-gray-900">Pièce {{ index + 1 }}</h4>
                                    <button
                                        type="button"
                                        @click="removePiece(index)"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Supprimer
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel :for="`piece_nom_${index}`" value="Nom de la pièce *" />
                                        <TextInput
                                            :id="`piece_nom_${index}`"
                                            v-model="piece.nom_piece"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_ref_${index}`" value="Référence / Code" />
                                        <TextInput
                                            :id="`piece_ref_${index}`"
                                            v-model="piece.reference_code"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_emp_${index}`" value="Emplacement" />
                                        <TextInput
                                            :id="`piece_emp_${index}`"
                                            v-model="piece.emplacement"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                    <div>
                                        <InputLabel :for="`piece_qte_${index}`" value="Quantité *" />
                                        <TextInput
                                            :id="`piece_qte_${index}`"
                                            v-model="piece.quantite"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_prix_${index}`" value="Prix unitaire (Ar) *" />
                                        <TextInput
                                            :id="`piece_prix_${index}`"
                                            v-model="piece.prix_unitaire"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <InputLabel value="Prix total (Ar)" />
                                        <div class="mt-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900">
                                            {{ calculatePrixTotal(piece) }}
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_date_${index}`" value="Date installation *" />
                                        <TextInput
                                            :id="`piece_date_${index}`"
                                            v-model="piece.date_installation"
                                            type="date"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                    <div>
                                        <InputLabel :for="`piece_limite_${index}`" value="Limite utilisation *" />
                                        <TextInput
                                            :id="`piece_limite_${index}`"
                                            v-model="piece.limite_utilisation"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_util_${index}`" value="Utilisation actuelle" />
                                        <TextInput
                                            :id="`piece_util_${index}`"
                                            v-model="piece.utilisation_actuelle"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel value="Potentiel restant" />
                                        <div class="mt-1 px-3 py-2 bg-green-50 border border-green-300 rounded-md text-green-900 font-medium">
                                            {{ calculatePotentielRestant(piece) }}
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_unite_${index}`" value="Unité de mesure *" />
                                        <select
                                            :id="`piece_unite_${index}`"
                                            v-model="piece.unite_mesure"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="km">Kilomètres (km)</option>
                                            <option value="heures">Heures (h)</option>
                                            <option value="cycles">Cycles</option>
                                            <option value="jours">Jours</option>
                                            <option value="mois">Mois</option>
                                            <option value="annees">Années</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <InputLabel :for="`piece_obs_${index}`" value="Observation" />
                                    <textarea
                                        :id="`piece_obs_${index}`"
                                        v-model="piece.observation"
                                        rows="2"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Summary -->
                        <div class="p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3">Récapitulatif des coûts</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-gray-600">Coût main d'œuvre:</span>
                                    <div class="text-xl font-bold text-gray-900">
                                        {{ parseFloat(form.cout_main_oeuvre || 0).toFixed(2) }} Ar
                                    </div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Coût des pièces:</span>
                                    <div class="text-xl font-bold text-gray-900">
                                        {{ coutTotalPieces }} Ar
                                    </div>
                                </div>
                                <div class="bg-white p-3 rounded-lg">
                                    <span class="text-sm text-gray-600">Coût total:</span>
                                    <div class="text-2xl font-bold text-indigo-600">
                                        {{ coutTotal }} Ar
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6">
                            <a
                                :href="route('maintenances.show', maintenance.id)"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Annuler
                            </a>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Mettre à jour
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>