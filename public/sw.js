self.addEventListener('install', (event) => {
    event.waitUntil(caches.open('mtsn11-v1').then((cache) => cache.addAll(['/'])));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            const fetched = fetch(event.request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open('mtsn11-v1').then((cache) => cache.put(event.request, copy)).catch(() => {});
                    return response;
                })
                .catch(() => cached);

            return cached || fetched;
        })
    );
});
