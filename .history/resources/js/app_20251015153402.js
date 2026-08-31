// resources/js/app.js
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Importez votre composant PWA
import PwaInstallPrompt from './Components/PwaInstallPrompt.vue'; // Ajustez le chemin si nécessaire

const appName = import.meta.env.VITE_APP_NAME || 'Automanager';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue);

        // Enregistrez le composant PWA globalement
        app.component('PwaInstallPrompt', PwaInstallPrompt);

        return app.mount(el);
    },
    progress: {
        color: '#4f46e5',
        showSpinner: true,
    },
});

// --- SUPPRIMEZ CETTE SECTION ENTIERE ---
// if ('serviceWorker' in navigator) {
//     window.addEventListener('load', () => {
//         navigator.serviceWorker.register('/gestion_auto/public/sw.js') // Ancien chemin dur
//             .then(registration => {
//                 console.log('✅ Service Worker enregistré:', registration);
//                 // ... gestion mise à jour
//             })
//             .catch(error => {
//                 console.error('❌ Échec de l\'enregistrement du Service Worker:', error);
//             });
//     });
// }
// --- FIN DE LA SECTION À SUPPRIMER ---

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

// Note: La logique d'installation PWA native a été supprimée d'ici.
// Elle est gérée dans le composant Vue PwaInstallPrompt.