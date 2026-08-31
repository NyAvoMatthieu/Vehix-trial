<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    trajet: Object,
    vehicules: Array,
});

const form = useForm({
    vehicule_id: props.trajet.vehicule_id,
    trajet_date: props.trajet.trajet_date,
    departure: props.trajet.departure,
    destination: props.trajet.destination,
    heure_depart: props.trajet.heure_depart,
    heure_arrivee: props.trajet.heure_arrivee,
    purpose: props.trajet.purpose,
    km_depart: props.trajet.km_depart,
    km_arrivee: props.trajet.km_arrivee,
    notes: props.trajet.notes || '',
});

const calculatedDistance = computed(() => {
    if (form.km_depart && form.km_arrivee) {
        const distance = parseFloat(form.km_arrivee) - parseFloat(form.km_depart);
        return distance > 0 ? distance.toFixed(2) : 0;
    }
    return 0;
});

const submit = () => {
    form.put(route('trajets.update', props.trajet.id));
};
</script>

<template>
    <AppLayout title="Modifier le Trajet">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Modifier le Trajet
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
                                        {{ vehicule.marque }} {{ vehicule.modele }} ({{ vehicule.immatriculation }})
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
                                    type="time"
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
                                    type="time"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.heure_arrivee" class="mt-2" />
                            </div>

                            <!-- Kilométrage de départ -->
                            <div>
                                <InputLabel for="km_depart" value="Kilométrage de départ (km) *" />
                                <TextInput
                                    id="km_depart"
                                    v-model="form.km_depart"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    placeholder="0.00"
                                    required
                                />
                                <InputError :message="form.errors.km_depart" class="mt-2" />
                            </div>

                            <!-- Kilométrage d'arrivée -->
                            <div>
                                <InputLabel for="km_arrivee" value="Kilométrage d'arrivée (km) *" />
                                <TextInput
                                    id="km_arrivee"
                                    v-model="form.km_arrivee"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    placeholder="0.00"
                                    required
                                />
                                <InputError :message="form.errors.km_arrivee" class="mt-2" />
                                <p v-if="calculatedDistance > 0" class="mt-1 text-sm text-green-600">
                                    Distance calculée: {{ calculatedDistance }} km
                                </p>
                            </div>

                            <!-- Motif du trajet -->
                            <div class="md:col-span-2">
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
                            <div class="md:col-span-2">
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
                                <span v-else>Mettre à jour le Trajet</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>