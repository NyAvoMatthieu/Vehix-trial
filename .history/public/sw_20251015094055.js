const CACHE_VERSION = 'v1.3.0';
const STATIC_CACHE_NAME = `auto-manager-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `auto-manager-dynamic-${CACHE_VERSION}`;
const PENDING_CACHE_NAME = 'auto-manager-pending-data';

// Ressources statiques à mettre en cache
const STATIC_ASSETS = [
  '/',
  '/dashboard',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  '/offline.html' // Page de fallback hors ligne
];

// Routes importantes de votre application
const APP_ROUTES = [
  '/dashboard',
  '/vehicules',
  '/assurances',
  '/maintenances',
  '/ravitaillements',
  '/trajets',
  '/recus',
  '/proprietaires'
];

// Installation du Service Worker
self.addEventListener('install', (event) => {
  console.log('🔧 Service Worker: Installation en cours...');

  event.waitUntil(
    caches.open(STATIC_CACHE_NAME)
      .then((cache) => {
        console.log('📦 Mise en cache des ressources statiques');
        return cache.addAll(STATIC_ASSETS.map(url => new Request(url, { cache: 'reload' })));
      })
      .then(() => {
        console.log('✅ Installation terminée');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('❌ Erreur lors de l\'installation:', error);
      })
  );
});

// Activation du Service Worker
self.addEventListener('activate', (event) => {
  console.log('🚀 Service Worker: Activation en cours...');

  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (
              cacheName !== STATIC_CACHE_NAME &&
              cacheName !== DYNAMIC_CACHE_NAME &&
              cacheName !== PENDING_CACHE_NAME
            ) {
              console.log('🗑️ Suppression ancien cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('✅ Activation terminée');
        return self.clients.claim();
      })
  );
});

// Gestion des requêtes (stratégie Network First avec Cache Fallback)
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Ignorer les requêtes non-GET
  if (request.method !== 'GET') {
    // Pour les requêtes POST/PUT/DELETE en mode hors ligne
    if (!navigator.onLine) {
      event.respondWith(handleOfflineRequest(request));
      return;
    }
    return;
  }

  // Ignorer les requêtes externes
  if (url.origin !== location.origin) {
    event.respondWith(fetch(request));
    return;
  }

  // Ignorer les requêtes CSRF
  if (url.pathname.includes('/sanctum/csrf-cookie')) {
    event.respondWith(fetch(request));
    return;
  }

  // Stratégie Network First avec Cache Fallback
  event.respondWith(
    fetch(request)
      .then((response) => {
        // Ne mettre en cache que les réponses réussies
        if (response && response.status === 200 && response.type === 'basic') {
          const responseClone = response.clone();

          caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
            cache.put(request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Si le réseau échoue, essayer le cache
        return caches.match(request)
          .then((cachedResponse) => {
            if (cachedResponse) {
              console.log('📦 Réponse du cache:', request.url);
              return cachedResponse;
            }

            // Si pas de cache et que c'est une route de l'app
            if (APP_ROUTES.some(route => url.pathname.startsWith(route))) {
              return caches.match('/offline.html')
                .then(offlinePage => offlinePage || createOfflineResponse());
            }

            return createOfflineResponse();
          });
      })
  );
});

// Gérer les requêtes hors ligne (POST, PUT, DELETE)
async function handleOfflineRequest(request) {
  try {
    const data = await request.clone().text();
    const cache = await caches.open(PENDING_CACHE_NAME);

    // Stocker la requête pour synchronisation ultérieure
    await cache.put(
      new Request(request.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      }),
      new Response(data, {
        headers: { 'Content-Type': 'application/json' }
      })
    );

    console.log('💾 Requête mise en file d\'attente:', request.url);

    return new Response(
      JSON.stringify({
        success: true,
        queued: true,
        message: 'Données enregistrées. Elles seront synchronisées automatiquement.'
      }),
      {
        status: 202,
        headers: { 'Content-Type': 'application/json' }
      }
    );
  } catch (error) {
    return new Response(
      JSON.stringify({
        success: false,
        error: 'Impossible d\'enregistrer hors ligne'
      }),
      {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      }
    );
  }
}

// Créer une réponse hors ligne
function createOfflineResponse() {
  return new Response(
    `<!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Mode Hors Ligne - AutoManager</title>
      <style>
        body {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
          margin: 0;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          text-align: center;
          padding: 20px;
        }
        .container {
          max-width: 500px;
        }
        h1 { font-size: 3rem; margin: 0; }
        p { font-size: 1.2rem; margin: 20px 0; opacity: 0.9; }
        .icon { font-size: 5rem; margin-bottom: 20px; }
        button {
          background: white;
          color: #667eea;
          border: none;
          padding: 15px 30px;
          font-size: 1rem;
          font-weight: 600;
          border-radius: 8px;
          cursor: pointer;
          margin-top: 20px;
          transition: transform 0.2s;
        }
        button:hover { transform: scale(1.05); }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="icon">📡</div>
        <h1>Mode Hors Ligne</h1>
        <p>Vous êtes actuellement hors ligne. Certaines fonctionnalités peuvent être limitées.</p>
        <p>Vos modifications seront synchronisées automatiquement dès que vous serez de nouveau en ligne.</p>
        <button onclick="window.location.reload()">Réessayer</button>
      </div>
    </body>
    </html>`,
    {
      headers: { 'Content-Type': 'text/html' }
    }
  );
}

// Synchronisation en arrière-plan
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-data') {
    event.waitUntil(syncPendingData());
  }
});

async function syncPendingData() {
  try {
    const cache = await caches.open(PENDING_CACHE_NAME);
    const requests = await cache.keys();

    for (const request of requests) {
      try {
        const cachedResponse = await cache.match(request);
        const data = await cachedResponse.text();

        const response = await fetch(request.url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: data
        });

        if (response.ok) {
          await cache.delete(request);
          console.log('✅ Données synchronisées:', request.url);
        }
      } catch (error) {
        console.error('❌ Erreur de synchronisation:', error);
      }
    }
  } catch (error) {
    console.error('❌ Erreur lors de la synchronisation:', error);
  }
}

// Gestion des messages
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'SYNC_NOW') {
    syncPendingData();
  }
});
