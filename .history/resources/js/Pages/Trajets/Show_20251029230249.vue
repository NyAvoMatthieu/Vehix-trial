<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    trajet: Object,
});

const deleteTrajet = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?')) {
        router.delete(route('trajets.destroy', props.trajet.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    return time.substring(0, 5);
};

const formatDateTime = (dateTime) => {
    
    if (!dateTime) {
        return 'n/a'; // ou 'N/A', ou '--/--/---- --:--',
    }
    
    const dateObj = new Date(dateTime);
   
    if (isNaN(dateObj.getTime())) {
        return 'Date invalide';
    }
   
    return dateObj.toLocaleString('fr-FR');
};

const calculateDuration = () => {
    const [startH, startM] = props.trajet.heure_depart.split(':').map(Number);
    const [endH, endM] = props.trajet.heure_arrivee.split(':').map(Number);
    
    const startMinutes = startH * 60 + startM;
    const endMinutes = endH * 60 + endM;
    const diffMinutes = endMinutes - startMinutes;
    
    const hours = Math.floor(diffMinutes / 60);
    const minutes = diffMinutes % 60;
    
    if (hours > 0 && minutes > 0) {
        return `${hours}h ${minutes}min`;
    } else if (hours > 0) {
        return `${hours}h`;
    } else {
        return `${minutes}min`;
    }
};

const getModeLabel = () => {
    return props.trajet.kilometrage_mode === 'odometer' ? 'Odomètre' : 'Kilométrage du trajet';
};
</script>

<template>
    <AppLayout title="Détails du Trajet">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails du Trajet
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
                    <div class="p-6">
                        <!-- En-tête avec actions -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                                    <span>{{ trajet.departure }}</span>
                                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    <span>{{ trajet.destination }}</span>
                                </h3>
                                <p class="text-gray-500 mt-1">{{ formatDate(trajet.trajet_date) }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    :href="route('trajets.edit', trajet.id)"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </Link>
                                <button
                                    @click="deleteTrajet"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>

                        <!-- Carte résumé avec distance -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 mb-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-indigo-100 text-sm uppercase font-semibold">Distance Parcourue</p>
                                    <p class="text-4xl font-bold mt-1">{{ trajet.distance }} km</p>
                                    <p class="text-indigo-100 text-xs mt-1">Mode: {{ getModeLabel() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-indigo-100 text-sm uppercase font-semibold">Durée du trajet</p>
                                    <p class="text-2xl font-bold mt-1">{{ calculateDuration() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informations principales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Véhicule -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-indigo-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-1">Véhicule</h4>
                                        <p class="text-lg font-medium text-gray-900">
                                            {{ trajet.vehicule.make }} {{ trajet.vehicule.model }}
                                        </p>
                                        <p class="text-sm text-gray-600 font-mono">{{ trajet.vehicule.license_plate }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Chauffeur -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-green-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-1">Chauffeur</h4>
                                        <p class="text-lg font-medium text-gray-900">{{ trajet.user.name }}</p>
                                        <p class="text-sm text-gray-600">{{ trajet.user.email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Horaires -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-blue-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Horaires</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Départ:</span>
                                                <span class="font-semibold text-gray-900 text-lg">{{ formatTime(trajet.heure_depart) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Arrivée:</span>
                                                <span class="font-semibold text-gray-900 text-lg">{{ formatTime(trajet.heure_arrivee) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kilométrage -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-orange-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Kilométrage</h4>
                                        <div class="space-y-2">
                                            <div v-if="trajet.kilometrage_mode === 'odometer' && trajet.odo_start">
                                                <p class="text-xs text-gray-500 mb-1">Odomètre:</p>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600">Départ:</span>
                                                    <span class="font-medium text-gray-900">{{ trajet.odo_start }} km</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600">Arrivée:</span>
                                                    <span class="font-medium text-gray-900">{{ trajet.odo_end }} km</span>
                                                </div>
                                            </div>
                                            <div v-if="trajet.kilometrage_mode === 'trajet' && trajet.km_depart !== null">
                                                <p class="text-xs text-gray-500 mb-1">Km du trajet:</p>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600">Départ:</span>
                                                    <span class="font-medium text-gray-900">{{ trajet.km_depart }} km</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600">Arrivée:</span>
                                                    <span class="font-medium text-gray-900">{{ trajet.km_arrivee }} km</span>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                                                <span class="font-semibold text-gray-700">Distance:</span>
                                                <span class="font-bold text-indigo-600 text-lg">{{ trajet.distance }} km</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Motif -->
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-purple-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase mb-1">Motif du trajet</h4>
                                        <p class="text-gray-900 text-lg">{{ trajet.purpose }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div v-if="trajet.notes" class="md:col-span-2 bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                <div class="flex items-start gap-3">
                                    <div class="bg-yellow-100 rounded-full p-2">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-700 uppercase mb-1">Observations / Notes</h4>
                                        <p class="text-gray-900 whitespace-pre-line">{{ trajet.notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de suivi -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="font-medium">Créé le:</span>
                                    <span>{{ new Date(trajet.created_at).toLocaleString('fr-FR') }}</span>
                                </div>
                                <div v-if="trajet.updated_at !== trajet.created_at" class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="font-medium">Modifié le:</span>
                                    <span>{{ new Date(trajet.updated_at).toLocaleString('fr-FR') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>