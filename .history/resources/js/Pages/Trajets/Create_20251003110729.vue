<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    vehicules: Array,
    lastOdometers: Object,
});

const form = useForm({
    vehicule_id: '',
    trajet_date: '',
    departure: '',
    destination: '',
    heure_depart: '',
    heure_arrivee: '',
    purpose: '',
    kilometrage_mode: 'odometer',
    km_depart: '',
    km_arrivee: '',
    odo_start: '',
    odo_end: '',
    notes: '',
});

const lastKnownOdo = ref(0);

// Watch for vehicle change to update last known odometer
watch(() => form.vehicule_id, (newVehicleId) => {
    if (newVehicleId && props.lastOdometers[newVehicleId] !== undefined) {
        lastKnownOdo.value = props.lastOdometers[newVehicleId];
        
        // If in trajet mode, update odo_start
        if (form.kilometrage_mode === 'trajet') {
            form.odo_start = lastKnownOdo.value;
            updateOdoEnd();
        }
    }
});

// Watch mode change
watch(() => form.kilometrage_mode, (newMode) => {
    if (newMode === 'odometer') {
        // Clear trajet mode fields
        form.km_depart = 0;
        form.km_arrivee = calculatedDistance.value;
    } else {
        // Clear odometer mode fields and set defaults
        form.km_depart = '';
        form.km_arrivee = '';
        form.odo_start = lastKnownOdo.value;
        updateOdoEnd();
    }
});

const calculatedDistance = computed(() => {
    if (form.kilometrage_mode === 'odometer') {
        if (form.odo_start && form.odo_end) {
            const distance = parseFloat(form.odo_end) - parseFloat(form.odo_start);
            return distance > 0 ? distance.toFixed(2) : 0;
        }
    } else {
        if (form.km_depart !== '' && form.km_arrivee !== '') {
            const distance = parseFloat(form.km_arrivee) - parseFloat(form.km_depart);
            return distance > 0 ? distance.toFixed(2) : 0;
        }
    }
    return 0;
});

// Auto-update km fields when in odometer mode
watch([() => form.odo_start, () => form.odo_end], () => {
    if (form.kilometrage_mode === 'odometer') {
        form.km_depart = 0;
        form.km_arrivee = calculatedDistance.value;
    }
});

// Auto-update odo fields when in trajet mode
const updateOdoEnd = () => {
    if (form.kilometrage_mode === 'trajet' && form.km_depart !== '' && form.km_arrivee !== '') {
        const distance = parseFloat(calculatedDistance.value);
        form.odo_end = (parseFloat(form.odo_start) + distance).toFixed(2);
    }
};

watch([() => form.km_depart, () => form.km_arrivee], () => {
    if (form.kilometrage_mode === 'trajet') {
        updateOdoEnd();
    }
});

const submit = () => {
    form.post(route('trajets.store'));
};
</script>

<template>
    <AppLayout title="Nouveau Trajet">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouveau Trajet
                </h2>
                <Link
                    :href="route('trajets.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Retour à la liste
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Véhicule -->
                            <div>
                                <InputLabel for="vehicule_id" value="Véhicule *" />
                                <select
                                    id="vehicule_id"
                                    v-model="form.vehicule_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Sélectionner un véhicule</option>
                                    <option v-for="vehicule in vehicules" :key="vehicule.id" :value="vehicule.id">
                                        {{ vehicule.make }} {{ vehicule.model }} ({{ vehicule.license_plate }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.vehicule_id" class="mt-2" />
                            </div>

                            <!-- Date du trajet -->
                            <div>
                                <InputLabel for="trajet_date" value="Date du trajet *" />
                                <TextInput
                                    id="trajet_date"
                                    v-model="form.trajet_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.trajet_date" class="mt-2" />
                            </div>

                            <!-- Lieu de départ -->
                            <div>
                                <InputLabel for="departure" value="Lieu de départ *" />
                                <TextInput
                                    id="departure"
                                    v-model="form.departure"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: Antananarivo"
                                    required
                                />
                                <InputError :message="form.errors.departure" class="mt-2" />
                            </div>

                            <!-- Lieu d'arrivée -->
                            <div>
                                <InputLabel for="destination" value="Lieu d'arrivée *" />
                                <TextInput
                                    id="destination"
                                    v-model="form.destination"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: Antsirabe"
                                    required
                                />
                                <InputError :message="form.errors.destination" class="mt-2" />
                            </div>

                            <!-- Heure de départ -->
                            <div>
                                <InputLabel for="heure_depart" value="Heure de départ *" />
                                <TextInput
                                    id="heure_depart"
                                    v-model="form.heure_depart"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.heure_depart" class="mt-2" />
                            </div>

                            <!-- Heure d'arrivée -->
                            <div>
                                <InputLabel for="heure_arrivee" value="Heure d'arrivée *" />
                                <TextInput
                                    id="heure_arrivee"
                                    v-model="form.heure_arrivee"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.heure_arrivee" class="mt-2" />
                            </div>
                        </div>

                        <!-- Mode de saisie kilométrage -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mode de saisie du kilométrage</h3>
                            
                            <div class="space-y-3 mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.kilometrage_mode"
                                        value="odometer"
                                        class="mr-3 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <div>
                                        <span class="font-medium text-gray-900">Mode Odomètre</span>
                                        <p class="text-sm text-gray-600">Saisir les valeurs du compteur du véhicule (tableau de bord)</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.kilometrage_mode"
                                        value="trajet"
                                        class="mr-3 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <div>
                                        <span class="font-medium text-gray-900">Mode Kilométrage du trajet</span>
                                        <p class="text-sm text-gray-600">Saisir les km parcourus durant ce trajet spécifique</p>
                                    </div>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <!-- Mode Odomètre -->
                                <div class="space-y-4" :class="{ 'opacity-50': form.kilometrage_mode !== 'odometer' }">
                                    <h4 class="font-semibold text-gray-700 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Odomètre (Compteur)
                                    </h4>
                                    
                                    <div>
                                        <InputLabel for="odo_start" value="Odomètre départ (km) *" />
                                        <TextInput
                                            id="odo_start"
                                            v-model="form.odo_start"
                                            type="number"
                                            step="0.01"
                                            class="mt-1 block w-full"
                                            placeholder="0.00"
                                            :disabled="form.kilometrage_mode !== 'odometer'"
                                            :readonly="form.kilometrage_mode !== 'odometer'"
                                            :required="form.kilometrage_mode === 'odometer'"
                                        />
                                        <InputError :message="form.errors.odo_start" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="odo_end" value="Odomètre arrivée (km) *" />
                                        <TextInput
                                            id="odo_end"
                                            v-model="form.odo_end"
                                            type="number"
                                            step="0.01"
                                            class="mt-1 block w-full"
                                            placeholder="0.00"
                                            :disabled="form.kilometrage_mode !== 'odometer'"
                                            :readonly="form.kilometrage_mode !== 'odometer'"
                                            :required="form.kilometrage_mode === 'odometer'"
                                        />
                                        <InputError :message="form.errors.odo_end" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Mode Kilométrage du trajet -->
                                <div class="space-y-4" :class="{ 'opacity-50': form.kilometrage_mode !== 'trajet' }">
                                    <h4 class="font-semibold text-gray-700 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        Kilométrage du trajet
                                    </h4>
                                    
                                    <div>
                                        <InputLabel for="km_depart" value="Km départ *" />
                                        <TextInput
                                            id="km_depart"
                                            v-model="form.km_depart"
                                            type="number"
                                            step="0.01"
                                            class="mt-1 block w-full"
                                            placeholder="0.00"
                                            :disabled="form.kilometrage_mode !== 'trajet'"
                                            :readonly="form.kilometrage_mode !== 'trajet'"
                                            :required="form.kilometrage_mode === 'trajet'"
                                        />
                                        <InputError :message="form.errors.km_depart" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="km_arrivee" value="Km arrivée *" />
                                        <TextInput
                                            id="km_arrivee"
                                            v-model="form.km_arrivee"
                                            type="number"
                                            step="0.01"
                                            class="mt-1 block w-full"
                                            placeholder="0.00"
                                            :disabled="form.kilometrage_mode !== 'trajet'"
                                            :readonly="form.kilometrage_mode !== 'trajet'"
                                            :required="form.kilometrage_mode === 'trajet'"
                                        />
                                        <InputError :message="form.errors.km_arrivee" class="mt-2" />
                                    </div>

                                    <div v-if="form.vehicule_id && form.kilometrage_mode === 'trajet'" class="text-sm text-blue-600 bg-blue-50 p-2 rounded">
                                        📍 Dernier odomètre connu: {{ lastKnownOdo }} km
                                    </div>
                                </div>
                            </div>

                            <!-- Distance calculée -->
                            <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700 uppercase">Distance calculée</span>
                                    <span class="text-3xl font-bold text-green-600">{{ calculatedDistance }} km</span>
                                </div>
                            </div>
                        </div>

                        <!-- Motif du trajet -->
                        <div class="mt-6">
                            <InputLabel for="purpose" value="Motif du trajet *" />
                            <TextInput
                                id="purpose"
                                v-model="form.purpose"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ex: Déplacement professionnel, livraison, etc."
                                required
                            />
                            <InputError :message="form.errors.purpose" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <InputLabel for="notes" value="Observations / Notes" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Notes supplémentaires sur le trajet..."
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex items-center justify-end mt-6 gap-4">
                            <Link
                                :href="route('trajets.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition"
                            >
                                Annuler
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
                            >
                                <span v-if="form.processing">Enregistrement...</span>
                                <span v-else>Enregistrer le Trajet</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>