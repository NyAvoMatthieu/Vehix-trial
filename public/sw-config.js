// Configuration centralisée pour le Service Worker

const SW_CONFIG = {
    // Version du cache (incrémentez pour forcer la mise à jour)
    VERSION: '1.3.0',

    // Nom des caches
    CACHE_NAMES: {
        static: 'auto-manager-static-v1.3.0',
        dynamic: 'auto-manager-dynamic-v1.3.0',
        pending: 'auto-manager-pending-data',
    },

    // Ressources à mettre en cache immédiatement
    STATIC_ASSETS: [
        '/',
        '/dashboard',
        '/manifest.json',
        '/offline.html',
        '/icons/icon-192x192.svg',
        '/icons/icon-512x512.svg',
    ],

    // Routes de l'application
    APP_ROUTES: [
        '/dashboard',
        '/vehicules',
        '/trajets',
        '/maintenances',
        '/ravitaillements',
        '/assurances',
        '/recus',
        '/proprietaires',
    ],

    // Extensions de fichiers à mettre en cache
    CACHEABLE_EXTENSIONS: [
        '.js',
        '.css',
        '.png',
        '.jpg',
        '.jpeg',
        '.svg',
        '.webp',
        '.woff',
        '.woff2',
    ],

    // Durée de vie du cache (en millisecondes)
    CACHE_EXPIRATION: {
        static: 30 * 24 * 60 * 60 * 1000, // 30 jours
        dynamic: 7 * 24 * 60 * 60 * 1000,  // 7 jours
        api: 5 * 60 * 1000,                 // 5 minutes
    },

    // Stratégies de cache par type de ressource
    STRATEGIES: {
        static: 'CacheFirst',    // Fichiers statiques
        dynamic: 'NetworkFirst', // Pages dynamiques
        api: 'NetworkFirst',     // Requêtes API
        images: 'CacheFirst',    // Images
    },
};

// Exporter pour utilisation dans le Service Worker
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SW_CONFIG;
}
