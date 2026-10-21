const CACHE_NAME = 'pwa-cache-v3';
const urlsToCache = [
    '/',
    // '/css/app.css',
    // '/js/app.js',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
];

// Instalar el Service Worker y almacenar los archivos en caché
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache);
        })
    );
});

// Activar el Service Worker y eliminar los caches antiguos
self.addEventListener('activate', (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Estrategia de "Cache then Network" para archivos importantes
self.addEventListener('fetch', (event) => {
    // Verificar si el recurso está en la lista de caché
    if (urlsToCache.includes(event.request.url)) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                return cachedResponse || fetch(event.request).then((response) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, response.clone());
                        return response;
                    });
                });
            })
        );
    } else {
        // Para otros recursos, siempre realiza la solicitud a la red
        event.respondWith(fetch(event.request));
    }
});

// Forzar la activación del nuevo Service Worker (mensaje de 'SKIP_WAITING')
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
