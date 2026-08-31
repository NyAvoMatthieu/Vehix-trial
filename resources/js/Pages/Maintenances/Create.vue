<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    vehicule: Object,
    lastKilometrage: Number,
});

const form = useForm({
    vehicule_id: props.vehicule.id,
    nature_intervention: '',
    kilometrage_actuel: props.lastKilometrage || 0,
    date_debut: new Date().toISOString().split('T')[0],
    date_fin: null,
    observation_generale: '',
    cout_main_oeuvre: 0,
    // Informations du garage
    garage_nom: '',
    garage_lieu: '',
    garage_contact: '',
    pieces: [],
});

const showFloatingButton = ref(true);

const addPiece = () => {
    form.pieces.push({
        nom_piece: '',
        marque_piece: '',
        reference_code: '',
        emplacement: '',
        quantite: 1,
        prix_unitaire: 0,
        date_installation: new Date().toISOString().split('T')[0],
        etat_piece: 'neuf',
        utilisation_actuelle: 0,
        limite_utilisation: 0,
        unite_mesure: 'km',
        vendeur: '',
        observation: '',
    });
    
    // Scroll vers la dernière pièce ajoutée après un court délai
    setTimeout(() => {
        const pieces = document.querySelectorAll('[data-piece-index]');
        if (pieces.length > 0) {
            pieces[pieces.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 100);
};

const removePiece = (index) => {
    form.pieces.splice(index, 1);
};

const calculatePrixTotal = (piece) => {
    return (piece.quantite * piece.prix_unitaire).toFixed(2);
};

const calculatePotentielRestant = (piece) => {
    return (piece.limite_utilisation - piece.utilisation_actuelle).toFixed(2);
};

const coutTotalPieces = computed(() => {
    return form.pieces.reduce((total, piece) => {
        return total + (piece.quantite * piece.prix_unitaire);
    }, 0).toFixed(2);
});

const coutTotal = computed(() => {
    return (parseFloat(form.cout_main_oeuvre || 0) + parseFloat(coutTotalPieces.value)).toFixed(2);
});

const submit = () => {
    form.post(route('maintenances.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Nouvelle Maintenance">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nouvelle Maintenance
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Vehicle Info -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2">Véhicule sélectionné</h3>
                        <p class="text-gray-700">
                            <span class="font-medium">{{ vehicule.alias }} :</span>
                            <span class="text-gray-500 ml-2">{{ vehicule.make }} - {{ vehicule.license_plate }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Dernier kilométrage: <span class="font-medium">{{ lastKilometrage }} km</span>
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">

                         <!-- Garage Information -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Informations du garage</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="garage_nom" value="Nom du garage" />
                                    <TextInput
                                        id="garage_nom"
                                        v-model="form.garage_nom"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder=""
                                    />
                                    <InputError :message="form.errors.garage_nom" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="garage_lieu" value="Lieu du garage" />
                                    <TextInput
                                        id="garage_lieu"
                                        v-model="form.garage_lieu"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder=""
                                    />
                                    <InputError :message="form.errors.garage_lieu" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="garage_contact" value="Contact du garage" />
                                    <TextInput
                                        id="garage_contact"
                                        v-model="form.garage_contact"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder=""
                                    />
                                    <InputError :message="form.errors.garage_contact" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>

                            <div class="mt-6">
                                <InputLabel for="nature_intervention" value="Nature de l'intervention *" />
                                <TextInput
                                    id="nature_intervention"
                                    v-model="form.nature_intervention"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    placeholder="Ex: Vidange moteur, Changement plaquettes..."
                                />
                                <InputError :message="form.errors.nature_intervention" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                <div>
                                    <InputLabel for="kilometrage_actuel" value="Kilométrage actuel" />
                                    <TextInput
                                        id="kilometrage_actuel"
                                        v-model="form.kilometrage_actuel"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.kilometrage_actuel" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="date_debut" value="Date de début " />
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
                                        required
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
                                    placeholder="Notes, observations, recommandations..."
                                ></textarea>
                                <InputError :message="form.errors.observation_generale" class="mt-2" />
                            </div>

                            <div class="mt-6">
                                <InputLabel for="cout_main_oeuvre" value="Coût main d'œuvre (Ar)" />
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
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Pièces remplacées / inspectées</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium text-indigo-600">{{ form.pieces.length }}</span> pièce(s) ajoutée(s)
                                    </p>
                                </div>
                            </div>

                            <div v-if="form.pieces.length === 0" class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg mb-2">Aucune pièce ajoutée</p>
                                    <p class="text-sm text-gray-400 mb-6">Utilisez le bouton flottant en bas à droite pour ajouter des pièces</p>
                                    <button
                                        type="button"
                                        @click="addPiece"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold shadow-lg transition-all duration-200"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Ajouter une pièce
                                    </button>
                                </div>
                            </div>

                            <div v-for="(piece, index) in form.pieces" :key="index" :data-piece-index="index" class="mb-6 p-5 bg-gradient-to-br from-gray-50 to-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <span class="text-lg font-bold text-indigo-600">{{ index + 1 }}</span>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 text-lg">Pièce {{ index + 1 }}</h4>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removePiece(index)"
                                        class="flex items-center space-x-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Supprimer</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <InputLabel :for="`piece_nom_${index}`" value="Nom de la pièce" />
                                        <TextInput
                                            :id="`piece_nom_${index}`"
                                            v-model="piece.nom_piece"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="Ex: Filtre à huile"
                                        />
                                        <InputError :message="form.errors[`pieces.${index}.nom_piece`]" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_marque_${index}`" value="Marque de la pièce" />
                                        <TextInput
                                            :id="`piece_marque_${index}`"
                                            v-model="piece.marque_piece"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="Ex: Bosch, Mann-Filter"
                                        />
                                        <InputError :message="form.errors[`pieces.${index}.marque_piece`]" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_ref_${index}`" value="Référence / Code" />
                                        <TextInput
                                            :id="`piece_ref_${index}`"
                                            v-model="piece.reference_code"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Ex: FLT-12345"
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <InputLabel :for="`piece_emp_${index}`" value="Emplacement" />
                                        <TextInput
                                            :id="`piece_emp_${index}`"
                                            v-model="piece.emplacement"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Ex: Compartiment moteur"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_etat_${index}`" value="État de la pièce" />
                                        <select
                                            :id="`piece_etat_${index}`"
                                            v-model="piece.etat_piece"
                                            @change="piece.utilisation_actuelle = piece.etat_piece === 'neuf' ? 0 : piece.utilisation_actuelle"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="neuf">Neuf</option>
                                            <option value="occasion">Occasion</option>
                                        </select>
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_vendeur_${index}`" value="Vendeur" />
                                        <TextInput
                                            :id="`piece_vendeur_${index}`"
                                            v-model="piece.vendeur"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            placeholder="Ex: Boutique Auto Plus / Station Total"
                                        />
                                        <InputError :message="form.errors[`pieces.${index}.vendeur`]" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                    <div>
                                        <InputLabel :for="`piece_qte_${index}`" value="Quantité" />
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
                                        <InputLabel :for="`piece_prix_${index}`" value="Prix unitaire (Ar)" />
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
                                        <InputLabel :for="`piece_date_${index}`" value="Date installation" />
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
                                        <InputLabel :for="`piece_limite_${index}`" value="Limite utilisation" />
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
                                            :readonly="piece.etat_piece === 'neuf'"
                                            :class="{ 'bg-gray-100': piece.etat_piece === 'neuf' }"
                                        />
                                        <p v-if="piece.etat_piece === 'neuf'" class="text-xs text-gray-500 mt-1">
                                            Automatiquement à 0 pour les pièces neuves
                                        </p>
                                    </div>

                                    <div>
                                        <InputLabel value="Potentiel restant" />
                                        <div class="mt-1 px-3 py-2 bg-green-50 border border-green-300 rounded-md text-green-900 font-medium">
                                            {{ calculatePotentielRestant(piece) }}
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel :for="`piece_unite_${index}`" value="Unité de mesure" />
                                        <select
                                            :id="`piece_unite_${index}`"
                                            v-model="piece.unite_mesure"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="km">Kilomètres (km)</option>
                                            <option value="heures">Heures (h)</option>
                                            <option value="tours">Tours</option>
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
                                        placeholder="Notes sur cette pièce..."
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
                                :href="route('maintenances.index')"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Annuler
                            </a>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enregistrer
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Floating Button pour ajouter une pièce -->
        <Transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="translate-y-16 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200 transform"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-16 opacity-0"
        >
            <div v-if="showFloatingButton" class="fixed bottom-8 right-8 z-50 flex flex-col items-end space-y-3">
                <!-- Bouton principal -->
                <button
                    type="button"
                    @click="addPiece"
                    class="group relative flex items-center space-x-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-2.5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 active:scale-95"
                    title="Ajouter une pièce"
                >
                    <!-- Icon -->
                    <div class="flex items-center justify-center w-5 h-5">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    
                    <!-- Text -->
                    <span class="font-semibold text-sm whitespace-nowrap">
                        Ajouter une pièce
                    </span>
                    
                    <!-- Badge avec le nombre de pièces -->
                    <div v-if="form.pieces.length > 0" class="absolute -top-2 -right-2 flex items-center justify-center w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full border-2 border-white shadow-md">
                        {{ form.pieces.length }}
                    </div>
                </button>

                <!-- Bouton pour scroll vers le haut -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-0"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-0"
                >
                    <button
                        v-if="form.pieces.length > 2"
                        type="button"
                        @click="() => window.scrollTo({ top: 0, behavior: 'smooth' })"
                        class="flex items-center justify-center w-11 h-11 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 active:scale-95"
                        title="Retour en haut"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </button>
                </Transition>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
/* Animation pulse pour le bouton flottant */
@keyframes pulse-ring {
    0% {
        transform: scale(0.95);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.7;
    }
    100% {
        transform: scale(0.95);
        opacity: 1;
    }
}

.group:hover {
    animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}
</style>