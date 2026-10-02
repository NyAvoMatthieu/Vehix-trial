<script setup>
import { ref, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    expired: Boolean,
});

const isSharing = ref(false);
const lastSentAt = ref(null);
const errorMessage = ref('');
const coords = ref(null);

let watchId = null;
let lastSendAt = 0;
const SEND_INTERVAL_MS = 5000;

const sendPosition = async (lat, lng) => {
    try {
        await fetch(`/api/position-share/${props.token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ lat, lng }),
        });
        lastSentAt.value = new Date();
    } catch (e) {
        errorMessage.value = "Erreur d'envoi de la position. Vérifiez votre connexion.";
    }
};

const startSharing = () => {
    if (!navigator.geolocation) {
        errorMessage.value = "La géolocalisation n'est pas disponible sur cet appareil.";
        return;
    }

    errorMessage.value = '';
    isSharing.value = true;

    watchId = navigator.geolocation.watchPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            coords.value = { lat: latitude, lng: longitude };

            const now = Date.now();
            if (now - lastSendAt >= SEND_INTERVAL_MS) {
                lastSendAt = now;
                sendPosition(latitude, longitude);
            }
        },
        () => {
            errorMessage.value = 'Position refusée ou indisponible. Autorisez la géolocalisation pour continuer.';
            isSharing.value = false;
        },
        { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
    );
};

const stopSharing = () => {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
    isSharing.value = false;
};

onBeforeUnmount(() => {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
    }
});

</script>

<template>
    <Head title="Partage de position" />

    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
            <div v-if="expired">
                <p class="text-5xl mb-4">⌛</p>
                <h1 class="text-lg font-bold text-gray-900 mb-2">Lien expiré</h1>
                <p class="text-sm text-gray-600">
                    Ce lien de partage de position n'est plus valide. Demandez un nouveau lien.
                </p>
            </div>

            <div v-else>
                <p class="text-5xl mb-4">🚗</p>
                <h1 class="text-lg font-bold text-gray-900 mb-2">Partage de position — Vehix</h1>
                <p class="text-sm text-gray-600 mb-6">
                    Partagez votre position pour renseigner automatiquement le point de départ du trajet.
                </p>

                <button
                    v-if="!isSharing"
                    type="button"
                    @click="startSharing"
                    class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700"
                >
                    📡 Démarrer le partage
                </button>

                <div v-else class="space-y-3">
                    <div class="flex items-center justify-center gap-2 text-green-700 font-medium">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Partage actif
                    </div>

                    <p v-if="coords" class="text-xs text-gray-500">
                        {{ coords.lat.toFixed(5) }}, {{ coords.lng.toFixed(5) }}
                    </p>
                    <p v-if="lastSentAt" class="text-xs text-gray-400">
                        Dernier envoi : {{ lastSentAt.toLocaleTimeString() }}
                    </p>

                    <button
                        type="button"
                        @click="stopSharing"
                        class="w-full py-3 bg-red-50 text-red-700 font-semibold rounded-lg hover:bg-red-100"
                    >
                        Arrêter le partage
                    </button>

                    <p class="text-xs text-gray-400 pt-2">
                        Gardez cette page ouverte tant que le trajet n'est pas enregistré.
                    </p>
                </div>

                <p v-if="errorMessage" class="mt-4 text-sm text-red-600">{{ errorMessage }}</p>
            </div>
        </div>
    </div>
</template>
