/* global self, clients */
self.addEventListener('push', (event) => {
    let data = {
        title: 'MTsN 11 Majalengka',
        body: 'Ada informasi baru di website.',
        url: '/',
        icon: '/favicon.ico',
        image: null,
    };

    try {
        if (event.data) {
            data = { ...data, ...event.data.json() };
        }
    } catch (e) {
        // ignore malformed payload
    }

    const options = {
        body: data.body || '',
        icon: data.icon || '/favicon.ico',
        badge: '/favicon.ico',
        data: { url: data.url || '/' },
        vibrate: [120, 60, 120],
    };

    // Gambar besar (cover berita/galeri) — didukung Chrome/Android; diabaikan browser lain.
    if (data.image) {
        options.image = data.image;
    }

    event.waitUntil(
        self.registration.showNotification(data.title || 'Notifikasi', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(target);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(target);
            }
        })
    );
});
