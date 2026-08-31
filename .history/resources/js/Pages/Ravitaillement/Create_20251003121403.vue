<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    vehicules: Array,
    selectedVehiculeId: Number,
    lastRavitaillement: Object,
    currentTrajet: Object,
});

const form = useForm({
    vehicule_id: props.selectedVehiculeId || null,
    ravitaillement_date: new Date().toISOString().split('T')[0],
    station_name: '',
    liters: '',
    price_per_liter: '',
    total_cost: '',
    odo_station: '',
    odo_arrival: '',
    fuel_type: 'essence',
    payment_method: 'carte',
    receipt_number: '',
    is_full_tank: true,
    average_consumption: '',
    fuel_left: '',
    notes: '',
});

// Véhicule sélectionné
const selectedVehicule = computed(() => {
    return props.vehicules.find(v => v.id === form.vehicule_id);
});

// Auto-remplir le type de carburant selon le véhicule
watch(() => form.vehicule_id, (newVehiculeId) => {
    const vehicule = props.vehicules.find(v => v.id === newVehiculeId);
    if (vehicule) {
        form.fuel_type = vehicule.fuel_type || 'essence';
        
        // Auto-remplir odo_station si vide
        if (!form.odo_station) {
            if (props.currentTrajet && props.currentTrajet.vehicule_id === newVehiculeId) {
                form.odo_station = props.currentTrajet.start_mileage;
            } else {
                form.odo_station = vehicule.mileage;
            }
        }
    }
});

// Calcul automatique du coût total
watch([() => form.liters, () => form.price_per_liter], ([liters, price]) => {
    if (liters && price) {
        form.total_cost = (parseFloat(liters) * parseFloat(price)).toFixed(2);
    }
});

// Calcul du carburant restant
const calculateFuelLeft = () => {
    if (!form.liters || !form.odo_station) return;

    const litersAdded = parseFloat(form.liters) || 0;
    const currentOdo = parseInt(form.odo_station) || 0;
    const avgConsumption = parseFloat(form.average_consumption) || 8; // Par défaut 8L/100km

    if (props.lastRavitaillement) {
        const lastOdo = props.lastRavitaillement.odo_arrival || props.lastRavitaillement.odo_station;
        const lastFuelLeft = parseFloat(props.lastRavitaillement.fuel_left) || 0;
        
        const distanceTraveled = currentOdo - lastOdo;
        
        if (distanceTraveled >= 0) {
            const fuelConsumed = (distanceTraveled / 100) * avgConsumption;
            const fuelBeforeRefill = Math.max(0, lastFuelLeft - fuelConsumed);
            form.fuel_left = (fuelBeforeRefill + litersAdded).toFixed(2);
        } else {
            form.fuel_left = litersAdded.toFixed(2);
        }
    } else {
        form.fuel_left = litersAdded.toFixed(2);
    }
};

// Déclencher le calcul
watch([() => form.liters, () => form.odo_station, () => form.average_consumption], () => {
    calculateFuelLeft();
});

const submit = () => {
    form.post(route('ravitaillements.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Nouveau Ravitaillement">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouveau Ravitaillement
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <FormSection @submitted="submit">
                    <template #title>
                        Informations du Ravitaillement
                    </template>

                    <template #description>
                        Enregistrez les détails de votre ravitaillement. Le coût total et le carburant restant seront calculés automatiquement.
                    </template>

                    <template #form>
                        <!-- Véhicule -->
                        <div class="col-span-6">
                            <InputLabel for="vehicule_id" value="Véhicule *" />
                            <select
                                id="vehicule_id"
                                v-model="form.vehicule_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option :value="null">Sélectionner un véhicule</option>
                                <option
                                    v-for="vehicule in vehicules"
                                    :key="vehicule.id"
                                    :value="vehicule.id"
                                >
                                    {{ vehicule.make }} {{ vehicule.model }} - {{ vehicule.license_plate }}
                                </option>
                            </select>
                            <InputError :message="form.errors.vehicule_id" class="mt-2" />
                        </div>

                        <!-- Date -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="ravitaillement_date" value="Date du ravitaillement *" />
                            <TextInput
                                id="ravitaillement_date"
                                v-model="form.ravitaillement_date"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.ravitaillement_date" class="mt-2" />
                        </div>

                        <!-- Station-service -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="station_name" value="Station-service *" />
                            <TextInput
                                id="station_name"
                                v-model="form.station_name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ex: Total, Shell..."
                            />
                            <InputError :message="form.errors.station_name" class="mt-2" />
                        </div>

                        <!-- Type de carburant -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="fuel_type" value="Type de carburant *" />
                            <select
                                id="fuel_type"
                                v-model="form.fuel_type"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="essence">Essence</option>
                                <option value="diesel">Diesel</option>
                                <option value="gpl">GPL</option>
                                <option value="electrique">Électrique</option>
                                <option value="hybride">Hybride</option>
                            </select>
                            <InputError :message="form.errors.fuel_type" class="mt-2" />
                        </div>

                        <!-- Plein complet -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="is_full_tank" value="Plein complet *" />
                            <select
                                id="is_full_tank"
                                v-model="form.is_full_tank"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option :value="true">Oui</option>
                                <option :value="false">Non</option>
                            </select>
                            <InputError :message="form.errors.is_full_tank" class="mt-2" />
                        </div>

                        <!-- Litres -->
                        <div class="col-span-6 sm:col-span-2">
                            <InputLabel for="liters" value="Litres *" />
                            <TextInput
                                id="liters"
                                v-model="form.liters"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                placeholder="0.00"
                            />
                            <InputError :message="form.errors.liters" class="mt-2" />
                        </div>

                        <!-- Prix par litre -->
                        <div class="col-span-6 sm:col-span-2">
                            <InputLabel for="price_per_liter" value="Prix/litre (Ar) *" />
                            <TextInput
                                id="price_per_liter"
                                v-model="form.price_per_liter"
                                type="number"
                                step="0.001"
                                class="mt-1 block w-full"
                                placeholder="0.000"
                            />
                            <InputError :message="form.errors.price_per_liter" class="mt-2" />
                        </div>

                        <!-- Coût total -->
                        <div class="col-span-6 sm:col-span-2">
                            <InputLabel for="total_cost" value="Coût total (Ar) *" />
                            <TextInput
                                id="total_cost"
                                v-model="form.total_cost"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full bg-gray-50"
                                placeholder="Auto-calculé"
                                readonly
                            />
                            <InputError :message="form.errors.total_cost" class="mt-2" />
                        </div>

                        <!-- Kilométrage à la station -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="odo_station" value="Kilométrage à la station" />
                            <TextInput
                                id="odo_station"
                                v-model="form.odo_station"
                                type="number"
                                class="mt-1 block w-full"
                                placeholder="Auto-rempli si vide"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Laissez vide pour utiliser le km du trajet en cours ou du véhicule
                            </p>
                            <InputError :message="form.errors.odo_station" class="mt-2" />
                        </div>

                        <!-- Kilométrage d'arrivée -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="odo_arrival" value="Kilométrage d'arrivée" />
                            <TextInput
                                id="odo_arrival"
                                v-model="form.odo_arrival"
                                type="number"
                                class="mt-1 block w-full"
                                placeholder="Optionnel"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Sera utilisé pour calculer la consommation
                            </p>
                            <InputError :message="form.errors.odo_arrival" class="mt-2" />
                        </div>

                        <!-- Consommation moyenne -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="average_consumption" value="Consommation moy. (L/100km)" />
                            <TextInput
                                id="average_consumption"
                                v-model="form.average_consumption"
                                type="number"
                                step="0.1"
                                class="mt-1 block w-full"
                                placeholder="8.0"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Pour calculer le carburant restant (défaut: 8 L/100km)
                            </p>
                            <InputError :message="form.errors.average_consumption" class="mt-2" />
                        </div>

                        <!-- Carburant restant -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="fuel_left" value="Carburant restant (L)" />
                            <TextInput
                                id="fuel_left"
                                v-model="form.fuel_left"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full bg-gray-50"
                                placeholder="Auto-calculé"
                                readonly
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                Calculé automatiquement
                            </p>
                            <InputError :message="form.errors.fuel_left" class="mt-2" />
                        </div>

                        <!-- Mode de paiement -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="payment_method" value="Mode de paiement *" />
                            <select
                                id="payment_method"
                                v-model="form.payment_method"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="especes">Espèces</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="mobile">Mobile Money</option>
                                <option value="bon">Bon carburant</option>
                            </select>
                            <InputError :message="form.errors.payment_method" class="mt-2" />
                        </div>

                        <!-- Numéro de reçu -->
                        <div class="col-span-6 sm:col-span-3">
                            <InputLabel for="receipt_number" value="Numéro de reçu" />
                            <TextInput
                                id="receipt_number"
                                v-model="form.receipt_number"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Optionnel"
                            />
                            <InputError :message="form.errors.receipt_number" class="mt-2" />
                        </div>

                        <!-- Remarques -->
                        <div class="col-span-6">
                            <InputLabel for="notes" value="Remarques" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Notes additionnelles..."
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Info dernier ravitaillement -->
                        <div v-if="lastRavitaillement" class="col-span-6 bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-sm text-blue-900 mb-2">
                                📊 Dernier ravitaillement
                            </h4>
                            <div class="grid grid-cols-2 gap-2 text-sm text-blue-800">
                                <div>Date: {{ new Date(lastRavitaillement.ravitaillement_date).toLocaleDateString() }}</div>
                                <div>Litres: {{ lastRavitaillement.liters }} L</div>
                                <div>Kilométrage: {{ lastRavitaillement.odo_station }} km</div>
                                <div>Restant: {{ lastRavitaillement.fuel_left || 'N/A' }} L</div>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <SecondaryButton @click="$inertia.visit(route('ravitaillements.index'))">
                            Annuler
                        </SecondaryButton>

                        <PrimaryButton
                            class="ml-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Enregistrer
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>