// Nom du cache
const CACHE_NAME = "lavache-pwa-v1";

// Fichiers à mettre en cache (App Shell)
const ASSETS = [
    "/",
    "/index.html",
    "/dashboard.html",
    "/offer.php",
    "/reserve.php",
    "/update.php",
    "/delete.php",
    "/list.php",
    "/manifest.json",
    "/icon-512.png",
    "/icon-192.png",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
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

// Activation
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
    event.respondWith(
        caches.match(event.request).then(response => {
            // Retourne le cache si disponible
            if (response) return response;

            // Sinon, fetch réseau + cache dynamique
            return fetch(event.request)
                .then(networkResponse => {
                    return caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                })
                .catch(() => {
                    // Fallback offline
                    if (event.request.destination === "document") {
                        return caches.match("/index.html");
                    }
                });
        })
    );
});

<script>
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker.register("/service-worker.js");
    })
};
</script>
