<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    fuelPrice: Object,
});

const form = useForm({
    fuel_type: props.fuelPrice.fuel_type,
    price_per_liter: props.fuelPrice.price_per_liter,
    effective_date: props.fuelPrice.effective_date,
    notes: props.fuelPrice.notes || '',
});

const submit = () => {
    form.put(route('admin.fuel-prices.update', props.fuelPrice.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Modifier Prix Carburant">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier Prix Carburant
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Fuel Type -->
                        <div>
                            <InputLabel for="fuel_type" value="Type de carburant *" />
                            <select
                                id="fuel_type"
                                v-model="form.fuel_type"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="diesel">Diesel</option>
                                <option value="essence">Essence</option>
                                <option value="gpl">GPL</option>
                                <option value="electrique">Électrique</option>
                            </select>
                            <InputError :message="form.errors.fuel_type" class="mt-2" />
                        </div>

                        <!-- Price Per Liter -->
                        <div>
                            <InputLabel for="price_per_liter" value="Prix par litre (Ar) *" />
                            <TextInput
                                id="price_per_liter"
                                v-model="form.price_per_liter"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="mt-1 block w-full"
                                required
                                placeholder="Ex: 5000.00"
                            />
                            <InputError :message="form.errors.price_per_liter" class="mt-2" />
                        </div>

                        <!-- Effective Date -->
                        <div>
                            <InputLabel for="effective_date" value="Date d'application *" />
                            <TextInput
                                id="effective_date"
                                v-model="form.effective_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.effective_date" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" value="Notes (optionnel)" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Raison du changement de prix, notes additionnelles..."
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Info Box -->
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Attention
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>La modification de ce prix affectera tous les futurs ravitaillements pour ce type de carburant.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4">
                            <a
                                :href="route('admin.fuel-prices.index')"
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