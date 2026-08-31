<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3'; // Importez usePage pour accéder aux props

// Récupérez les props Inertia
const page = usePage();
const swPath = ref(page.props.pwa_paths.sw); // Obtenez le chemin du SW depuis Inertia

const showInstallPrompt = ref(false);
let deferredPrompt = null; // Utilisez let ici pour pouvoir le modifier
const isIOS = ref(false);
const isStandalone = ref(false);

onMounted(() => {
    // Détecter si c'est iOS
    isIOS.value = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    // Détecter si déjà installé
    isStandalone.value = window.matchMedia('(display-mode: standalone)').matches ||
                         window.navigator.standalone === true;

    // Enregistrer le Service Worker en utilisant le chemin fourni par Inertia
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(swPath.value) // Utilisez swPath.value
            .then(registration => {
                console.log('✅ Service Worker enregistré:', registration);

                // Vérifier les mises à jour
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    console.log('🔄 Nouvelle version du Service Worker détectée');

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('✨ Nouvelle version disponible - Rechargez la page');
                        }
                    });
                });
            })
            .catch(error => {
                console.error('❌ Échec de l\'enregistrement du Service Worker:', error);
            });
    }

    // Écouter l'événement beforeinstallprompt
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);

    // Écouter l'événement appinstalled
    window.addEventListener('appinstalled', handleAppInstalled);
});

onUnmounted(() => {
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
    window.removeEventListener('appinstalled', handleAppInstalled);
});

const handleBeforeInstallPrompt = (e) => {
    e.preventDefault();
    deferredPrompt = e; // Stockez dans la variable let

    // Afficher le prompt après 3 secondes
    setTimeout(() => {
        if (!isStandalone.value) {
            showInstallPrompt.value = true;
        }
    }, 3000);
};

const handleAppInstalled = () => {
    console.log('✅ PWA installée avec succès!');
    showInstallPrompt.value = false;
    deferredPrompt = null;
};

const installApp = async () => {
    if (!deferredPrompt) return; // Utilisez la variable let

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    console.log(`Installation ${outcome === 'accepted' ? 'acceptée' : 'refusée'}`);

    if (outcome === 'accepted') {
        showInstallPrompt.value = false;
    }

    deferredPrompt = null;
};

const dismissPrompt = () => {
    showInstallPrompt.value = false;
    // Stocker dans localStorage pour ne pas afficher pendant 7 jours
    localStorage.setItem('pwa-install-dismissed', Date.now().toString());
};

// Vérifier si le prompt a été fermé récemment
const checkDismissed = () => {
    const dismissed = localStorage.getItem('pwa-install-dismissed');
    if (dismissed) {
        const daysSinceDismissed = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60 * 24);
        return daysSinceDismissed < 7;
    }
    return false;
};
</script>

<template>
    <!-- Votre template reste le même -->
    <Transition name="slide-up">
        <div
            v-if="showInstallPrompt && !isStandalone && !isIOS && !checkDismissed()"
            class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-4 border-indigo-600 z-50 p-4 md:p-6"
        >
            <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-3 flex-shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">Installer Vehix</h3>
                        <p class="text-sm text-gray-600">
                            Accédez rapidement à l'app, même hors ligne !
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    <button
                        @click="dismissPrompt"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                    >
                        Plus tard
                    </button>
                    <button
                        @click="installApp"
                        class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg"
                    >
                        Installer
                    </button>
                </div>
            </div>
        </div>
    </Transition>

    <Transition name="slide-up">
        <div
            v-if="isIOS && !isStandalone && showInstallPrompt && !checkDismissed()"
            class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-4 border-indigo-600 z-50 p-4 md:p-6"
        >
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Installer AutoManager sur iOS</h3>
                    <button
                        @click="dismissPrompt"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs">1</span>
                        <p>Appuyez sur le bouton <strong>Partager</strong>
                            <svg class="inline w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.11 0-2-.9-2-2V10c0-1.11.89-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .89 2 2z"/>
                            </svg>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs">2</span>
                        <p>Sélectionnez <strong>"Sur l'écran d'accueil"</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs">3</span>
                        <p>Appuyez sur <strong>"Ajouter"</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </Transition>

    <Transition name="fade">
        <div
            v-if="isStandalone"
            class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 z-50"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium">Mode App</span>
        </div>
    </Transition>
</template>

<style scoped>
/* Vos styles restent les mêmes */
.slide-up-enter-active,
.slide-up-leave-active {
    transition: transform 0.3s ease-out;
}

.slide-up-enter-from {
    transform: translateY(100%);
}

.slide-up-leave-to {
    transform: translateY(100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>