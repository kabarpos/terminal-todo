import { precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { CacheableResponsePlugin } from 'workbox-cacheable-response';

// Pre-cache semua asset yang di-generate oleh build
precacheAndRoute(self.__WB_MANIFEST);

// Cache untuk font
registerRoute(
    ({url}) => url.origin === 'https://fonts.googleapis.com' || 
               url.origin === 'https://fonts.gstatic.com',
    new CacheFirst({
        cacheName: 'google-fonts',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 30,
                maxAgeSeconds: 60 * 60 * 24 * 365, // 1 tahun
            }),
            new CacheableResponsePlugin({
                statuses: [0, 200]
            }),
        ],
    })
);

// Cache untuk gambar
registerRoute(
    ({request}) => request.destination === 'image',
    new CacheFirst({
        cacheName: 'images',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 60,
                maxAgeSeconds: 60 * 60 * 24 * 30, // 30 hari
            }),
        ],
    })
);

// Cache untuk API requests
registerRoute(
    ({url}) => url.pathname.startsWith('/api/'),
    new NetworkFirst({
        cacheName: 'api-cache',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 100,
                maxAgeSeconds: 60 * 60 * 24, // 24 jam
            }),
        ],
    })
);

// Cache untuk static assets
registerRoute(
    ({request}) => 
        request.destination === 'script' ||
        request.destination === 'style',
    new CacheFirst({
        cacheName: 'static-resources',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 60,
                maxAgeSeconds: 60 * 60 * 24 * 7, // 1 minggu
            }),
        ],
    })
);

// Event listener untuk skip waiting
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
}); 