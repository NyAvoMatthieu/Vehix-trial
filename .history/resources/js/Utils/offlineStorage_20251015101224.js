// resources/js/utils/offlineStorage.js

/**
 * Gestionnaire de stockage hors ligne pour les données
 */
class OfflineStorage {
    constructor() {
        this.dbName = 'AutoManagerDB';
        this.version = 1;
        this.db = null;
    }

    /**
     * Initialiser IndexedDB
     */
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Store pour les véhicules
                if (!db.objectStoreNames.contains('vehicules')) {
                    db.createObjectStore('vehicules', { keyPath: 'id' });
                }

                // Store pour les trajets
                if (!db.objectStoreNames.contains('trajets')) {
                    db.createObjectStore('trajets', { keyPath: 'id' });
                }

                // Store pour les ravitaillements
                if (!db.objectStoreNames.contains('ravitaillements')) {
                    db.createObjectStore('ravitaillements', { keyPath: 'id' });
                }

                // Store pour les maintenances
                if (!db.objectStoreNames.contains('maintenances')) {
                    db.createObjectStore('maintenances', { keyPath: 'id' });
                }

                // Store pour les requêtes en attente
                if (!db.objectStoreNames.contains('pendingRequests')) {
                    const store = db.createObjectStore('pendingRequests', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                }

                console.log('✅ IndexedDB initialisé');
            };
        });
    }

    /**
     * Sauvegarder des données
     */
    async save(storeName, data) {
        if (!this.db) await this.init();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.put(data);

            request.onsuccess = () => {
                console.log(`✅ Données sauvegardées dans ${storeName}:`, data.id);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Sauvegarder plusieurs éléments
     */
    async saveAll(storeName, items) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction([storeName], 'readwrite');
        const store = transaction.objectStore(storeName);

        const promises = items.map(item => {
            return new Promise((resolve, reject) => {
                const request = store.put(item);
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        });

        await Promise.all(promises);
        console.log(`✅ ${items.length} éléments sauvegardés dans ${storeName}`);
    }

    /**
     * Récupérer un élément
     */
    async get(storeName, id) {
        if (!this.db) await this.init();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.get(id);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Récupérer tous les éléments
     */
    async getAll(storeName) {
        if (!this.db) await this.init();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Supprimer un élément
     */
    async delete(storeName, id) {
        if (!this.db) await this.init();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.delete(id);

            request.onsuccess = () => {
                console.log(`🗑️ Élément supprimé de ${storeName}:`, id);
                resolve();
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Ajouter une requête en attente
     */
    async addPendingRequest(url, method, data) {
        if (!this.db) await this.init();

        const request = {
            url,
            method,
            data,
            timestamp: Date.now()
        };

        return this.save('pendingRequests', request);
    }

    /**
     * Récupérer toutes les requêtes en attente
     */
    async getPendingRequests() {
        return this.getAll('pendingRequests');
    }

    /**
     * Supprimer une requête en attente
     */
    async deletePendingRequest(id) {
        return this.delete('pendingRequests', id);
    }

    /**
     * Vider un store
     */
    async clear(storeName) {
        if (!this.db) await this.init();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([storeName], 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.clear();

            request.onsuccess = () => {
                console.log(`🗑️ Store ${storeName} vidé`);
                resolve();
            };
            request.onerror = () => reject(request.error);
        });
    }
}

// Instance singleton
const offlineStorage = new OfflineStorage();

/**
 * Fonction helper pour faire des requêtes avec support hors ligne
 */
export async function offlineAwareRequest(url, options = {}) {
    const { method = 'GET', data = null } = options;

    // Si en ligne, faire la requête normalement
    if (navigator.onLine) {
        try {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: data ? JSON.stringify(data) : null,
            });

            if (response.ok) {
                return await response.json();
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            console.error('Erreur réseau:', error);

            // Si GET, essayer de récupérer du cache
            if (method === 'GET') {
                return getFromOfflineCache(url);
            }

            throw error;
        }
    } else {
        // Mode hors ligne
        if (method === 'GET') {
            return getFromOfflineCache(url);
        } else {
            // Pour POST/PUT/DELETE, sauvegarder pour synchronisation ultérieure
            await offlineStorage.addPendingRequest(url, method, data);

            return {
                success: true,
                offline: true,
                message: 'Requête enregistrée pour synchronisation ultérieure'
            };
        }
    }
}

/**
 * Récupérer les données du cache hors ligne
 */
async function getFromOfflineCache(url) {
    // Déterminer le store en fonction de l'URL
    let storeName;
    if (url.includes('/vehicules')) storeName = 'vehicules';
    else if (url.includes('/trajets')) storeName = 'trajets';
    else if (url.includes('/ravitaillements')) storeName = 'ravitaillements';
    else if (url.includes('/maintenances')) storeName = 'maintenances';

    if (storeName) {
        const data = await offlineStorage.getAll(storeName);
        console.log(`📦 Données récupérées du cache (${storeName}):`, data.length, 'éléments');
        return { data, fromCache: true };
    }

    throw new Error('Aucune donnée en cache disponible');
}

/**
 * Synchroniser les requêtes en attente
 */
export async function syncPendingRequests() {
    if (!navigator.onLine) {
        console.log('⚠️ Impossible de synchroniser : hors ligne');
        return;
    }

    const pendingRequests = await offlineStorage.getPendingRequests();

    if (pendingRequests.length === 0) {
        console.log('✅ Aucune requête en attente');
        return;
    }

    console.log(`🔄 Synchronisation de ${pendingRequests.length} requête(s)...`);

    for (const request of pendingRequests) {
        try {
            const response = await fetch(request.url, {
                method: request.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: request.data ? JSON.stringify(request.data) : null,
            });

            if (response.ok) {
                await offlineStorage.deletePendingRequest(request.id);
                console.log('✅ Requête synchronisée:', request.url);
            } else {
                console.error('❌ Échec de synchronisation:', response.status);
            }
        } catch (error) {
            console.error('❌ Erreur de synchronisation:', error);
        }
    }

    console.log('✅ Synchronisation terminée');
}

// Exporter l'instance
export default offlineStorage;

// Synchroniser automatiquement quand la connexion revient
window.addEventListener('online', () => {
    console.log('🌐 Connexion rétablie - Début de la synchronisation...');
    syncPendingRequests();
});
