import { precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst, StaleWhileRevalidate } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { CacheableResponsePlugin } from 'workbox-cacheable-response';

// Pre-cache semua asset yang di-generate oleh build
precacheAndRoute(self.__WB_MANIFEST);

// Cache untuk static assets (images, styles, scripts)
registerRoute(
    ({ request }) => request.destination === 'image' ||
                     request.destination === 'style' ||
                     request.destination === 'script' ||
                     request.destination === 'font',
    new CacheFirst({
        cacheName: 'static-assets',
        plugins: [
            new CacheableResponsePlugin({
                statuses: [0, 200]
            }),
            new ExpirationPlugin({
                maxEntries: 60,
                maxAgeSeconds: 30 * 24 * 60 * 60 // 30 hari
            })
        ]
    })
);

// Cache untuk API requests
registerRoute(
    ({ request }) => request.url.includes('/api/'),
    new NetworkFirst({
        cacheName: 'api-cache',
        plugins: [
            new CacheableResponsePlugin({
                statuses: [0, 200]
            }),
            new ExpirationPlugin({
                maxEntries: 100,
                maxAgeSeconds: 24 * 60 * 60 // 24 jam
            })
        ]
    })
);

// Cache untuk halaman HTML
registerRoute(
    ({ request }) => request.mode === 'navigate',
    new StaleWhileRevalidate({
        cacheName: 'pages-cache',
        plugins: [
            new CacheableResponsePlugin({
                statuses: [0, 200]
            })
        ]
    })
);

// Offline fallback
self.addEventListener('install', (event) => {
    const offlinePage = new Request('/offline.html');
    event.waitUntil(
        fetch(offlinePage).then((response) => {
            return caches.open('offline-cache').then((cache) => {
                return cache.put(offlinePage, response);
            });
        })
    );
});

// Event listener untuk skip waiting
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
}); 