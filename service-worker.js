// Nom du cache
const CACHE_NAME = "lavache-pwa-v1";

// Fichiers statiques à mettre en cache (uniquement l'App Shell)
const ASSETS = [
    "/",
    "/index.html",
    "/dashboard.html",
    "/manifest.json",
    "/icon-512.png",
    "/icon-192.png",
    "https://jsdelivr.net",
    "https://jsdelivr.net"
];

// Installation du service worker
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(ASSETS);
        })
    );
    self.skipWaiting(); // Mise à jour immédiate
});

// Activation et nettoyage des anciens caches
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Interception des requêtes
self.addEventListener("fetch", event => {
    const url = new URL(event.request.url);

    // SÉCURITÉ SÉRIEUSE : Ne jamais intercepter ni mettre en cache le PHP ou les requêtes de soumission POST
    if (event.request.method !== "GET" || url.pathname.endsWith(".php")) {
        // On laisse la requête aller directement au serveur XAMPP sans passer par le cache
        return; 
    }

    event.respondWith(
        caches.match(event.request).then(response => {
            // 1. Retourne le cache si disponible (HTML, CSS, Icônes)
            if (response) return response;

            // 2. Sinon, fetch réseau + cache dynamique pour les autres éléments statiques
            return fetch(event.request)
                .then(networkResponse => {
                    // On vérifie que la réponse est valide avant de la cloner dans le cache
                    if (!networkResponse || networkResponse.status !== 200) {
                        return networkResponse;
                    }
                    
                    return caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                })
                .catch(() => {
                    // Fallback si l'utilisateur est complètement hors-ligne
                    if (event.request.destination === "document") {
                        return caches.match("/index.html");
                    }
                });
        })
    );
});
