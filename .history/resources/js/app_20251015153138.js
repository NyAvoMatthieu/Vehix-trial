// resources/js/app.js
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PwaInstallPrompt from './Components/PwaInstallPrompt.vue';
const appName = import.meta.env.VITE_APP_NAME || 'Automanager';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
        showSpinner: true,
    },
});

// Enregistrer le Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('✅ Service Worker enregistré:', registration);

                // Vérifier les mises à jour
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    console.log('🔄 Nouvelle version du Service Worker détectée');

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('✨ Nouvelle version disponible - Rechargez la page');
                            // Optionnel : afficher une notification
                            showUpdateNotification();
                        }
                    });
                });
            })
            .catch(error => {
                console.error('❌ Échec de l\'enregistrement du Service Worker:', error);
            });
    });
}

// Gérer l'installation de la PWA
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    console.log('💡 beforeinstallprompt déclenché');
    e.preventDefault();
    deferredPrompt = e;
    
    // Déclencher un événement personnalisé pour le composant Vue
    window.dispatchEvent(new CustomEvent('pwa-install-available', { detail: e }));
});

window.addEventListener('appinstalled', () => {
    console.log('✅ PWA installée avec succès');
    deferredPrompt = null;
    
    // Déclencher un événement personnalisé
    window.dispatchEvent(new CustomEvent('pwa-installed'));
});

// Fonction globale pour installer la PWA (appelée depuis le composant Vue)
window.installPWA = async () => {
    if (!deferredPrompt) {
        console.log('❌ Pas de prompt d\'installation disponible');
        return false;
    }

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`Installation ${outcome === 'accepted' ? 'acceptée' : 'refusée'}`);
    
    deferredPrompt = null;
    return outcome === 'accepted';
};

// Gestion des erreurs globales
window.addEventListener('unhandledrejection', event => {
    console.error('Unhandled promise rejection:', event.reason);
});

// Détection de l'état en ligne/hors ligne
window.addEventListener('online', () => {
    console.log('✅ Connexion rétablie - Synchronisation...');
    syncPendingData();
    showNotification('✅ Connexion rétablie - Synchronisation en cours...', 'success');
});

window.addEventListener('offline', () => {
    console.log('⚠️ Mode hors ligne activé');
    showNotification('⚠️ Mode hors ligne - Les modifications seront synchronisées automatiquement', 'warning');
});

// Fonction pour synchroniser les données en attente
async function syncPendingData() {
    try {
        const cache = await caches.open('auto-manager-pending-data');
        const requests = await cache.keys();

        if (requests.length === 0) {
            console.log('Aucune donnée à synchroniser');
            return;
        }

        console.log(`🔄 Synchronisation de ${requests.length} requête(s)...`);

        for (const request of requests) {
            try {
                const response = await cache.match(request);
                const data = await response.json();

                // Envoyer les données au serveur
                const fetchResponse = await fetch(request, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                if (fetchResponse.ok) {
                    // Supprimer du cache après envoi réussi
                    await cache.delete(request);
                    console.log('✅ Données synchronisées:', request.url);
                }
            } catch (error) {
                console.error('Erreur de synchronisation:', error);
            }
        }

        showNotification('✅ Synchronisation terminée', 'success');
    } catch (error) {
        console.error('Erreur lors de la synchronisation:', error);
    }
}

function showNotification(message, type = 'info') {
    // Déclencher un événement personnalisé pour afficher la notification dans Vue
    window.dispatchEvent(new CustomEvent('show-notification', { 
        detail: { message, type }
    }));
}

function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            background: #4f46e5;
            color: white;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            <span>✨ Nouvelle version disponible</span>
            <button onclick="window.location.reload()" style="
                background: white;
                color: #4f46e5;
                border: none;
                padding: 6px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 600;
            ">Recharger</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 10000);
}