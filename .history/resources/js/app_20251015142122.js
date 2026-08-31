
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

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

// Enregistrer le Service Worker (décommenter cette section)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/gestion_auto/public/sw.js')
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
    });
}

// Gérer l'installation de la PWA
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    console.log('💡 Événement beforeinstallprompt déclenché');

    // Vous pouvez afficher un bouton d'installation personnalisé ici
    showInstallButton();
});

window.addEventListener('appinstalled', () => {
    console.log('✅ PWA installée avec succès');
    deferredPrompt = null;
});

function showInstallButton() {
    // Créer un bouton d'installation si nécessaire
    const installButton = document.createElement('button');
    installButton.textContent = '📱 Installer AutoManager';
    installButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 24px;
        background: #4f46e5;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 9999;
    `;

    installButton.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`Installation ${outcome === 'accepted' ? 'acceptée' : 'refusée'}`);
            deferredPrompt = null;
            installButton.remove();
        }
    });

    document.body.appendChild(installButton);
}

// Gestion des erreurs globales
window.addEventListener('unhandledrejection', event => {
    console.error('Unhandled promise rejection:', event.reason);
});

// Détection de l'état en ligne/hors ligne
window.addEventListener('online', () => {
    console.log('✅ Connexion rétablie - Synchronisation...');
    syncPendingData();
});

window.addEventListener('offline', () => {
    console.log('⚠️ Mode hors ligne activé');
    showOfflineNotification();
});

// Fonction pour synchroniser les données en attente
async function syncPendingData() {
    try {
        const cache = await caches.open('auto-manager-pending-data');
        const requests = await cache.keys();

        for (const request of requests) {
            try {
                const response = await cache.match(request);
                const data = await response.json();

                // Envoyer les données au serveur
                await fetch(request, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                // Supprimer du cache après envoi réussi
                await cache.delete(request);
                console.log('✅ Données synchronisées:', request.url);
            } catch (error) {
                console.error('Erreur de synchronisation:', error);
            }
        }
    } catch (error) {
        console.error('Erreur lors de la synchronisation:', error);
    }
}

function showOfflineNotification() {
    const notification = document.createElement('div');
    notification.textContent = '⚠️ Mode hors ligne - Les modifications seront synchronisées automatiquement';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 24px;
        background: #f59e0b;
        color: white;
        border-radius: 8px;
        z-index: 9999;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}
