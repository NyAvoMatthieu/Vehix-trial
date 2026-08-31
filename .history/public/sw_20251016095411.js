const CACHE_VERSION = 'v1.4.0';
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
  '/offline.html'
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
        // Mettre en cache les ressources une par une pour éviter les erreurs
        return Promise.allSettled(
          STATIC_ASSETS.map(url => 
            cache.add(url).catch(err => console.log('Erreur cache:', url, err))
          )
        );
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

  // Ignorer les requêtes externes (sauf fonts et CDN)
  if (url.origin !== location.origin && !url.hostname.includes('fonts') && !url.hostname.includes('cdn')) {
    event.respondWith(fetch(request));
    return;
  }

  // Ignorer les requêtes CSRF
  if (url.pathname.includes('/sanctum/csrf-cookie')) {
    event.respondWith(fetch(request));
    return;
  }

  // Stratégie Cache First pour les assets statiques
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  // Stratégie Network First pour les pages et API
  event.respondWith(networkFirst(request));
});

// Stratégie Cache First
async function cacheFirst(request) {
  const cachedResponse = await caches.match(request);
  if (cachedResponse) {
    return cachedResponse;
  }

  try {
    const networkResponse = await fetch(request);
    if (networkResponse && networkResponse.status === 200) {
      const cache = await caches.open(DYNAMIC_CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('Erreur réseau (cache first):', error);
    return createOfflineResponse();
  }
}

// Stratégie Network First
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);
    
    // Ne mettre en cache que les réponses réussies
    if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
      const cache = await caches.open(DYNAMIC_CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    // Si le réseau échoue, essayer le cache
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
      console.log('📦 Réponse du cache:', request.url);
      return cachedResponse;
    }

    // Si pas de cache et que c'est une route de l'app
    const url = new URL(request.url);
    if (APP_ROUTES.some(route => url.pathname.startsWith(route))) {
      const offlinePage = await caches.match('/offline.html');
      return offlinePage || createOfflineResponse();
    }

    return createOfflineResponse();
  }
}

// Vérifier si c'est un asset statique
function isStaticAsset(pathname) {
  const staticExtensions = ['.js', '.css', '.png', '.jpg', '.jpeg', '.svg', '.webp', '.woff', '.woff2', '.ttf', '.eot', '.ico'];
  return staticExtensions.some(ext => pathname.endsWith(ext));
}

// Gérer les requêtes hors ligne (POST, PUT, DELETE)
async function handleOfflineRequest(request) {
  try {
    const data = await request.clone().text();
    const cache = await caches.open(PENDING_CACHE_NAME);

    // Stocker la requête pour synchronisation ultérieure
    await cache.put(
      new Request(request.url + '?timestamp=' + Date.now(), {
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
      <title>Mode Hors Ligne - Vehix</title>
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
        .container { max-width: 500px; }
        h1 { font-size: 3rem; margin: 0; }
        p { font-size: 1.2rem; margin: 20px 0; opacity: 0.9; }
        .icon { font-size: 5rem; margin-bottom: 20px; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
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
      <script>
        window.addEventListener('online', () => window.location.reload());
      </script>
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

        // Extraire l'URL originale (sans le timestamp)
        const originalUrl = request.url.split('?timestamp=')[0];

        const response = await fetch(originalUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: data
        });

        if (response.ok) {
          await cache.delete(request);
          console.log('✅ Données synchronisées:', originalUrl);
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