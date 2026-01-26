const CACHE_NAME = 'qanaah-v5';
const ASSETS_TO_CACHE = [
    '/',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap',
    'https://cdn-icons-png.flaticon.com/512/2344/2344132.png'
];

// Install Event: specific static assets caching
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// Activate Event: Cleanup old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    return caches.delete(key);
                }
            }));
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    // Strategy 1: HTML pages (Navigation) -> Network First
    if (event.request.mode === 'navigate' || event.request.destination === 'document') {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                })
                .catch(() => {
                    return caches.match(event.request)
                        .then((cachedResponse) => {
                            if (cachedResponse) {
                                return cachedResponse;
                            }
                            return caches.match('/');
                        });
                })
        );
        return;
    }

    // Strategy 2: Static Assets (Styles, Scripts, Images, Fonts) -> Cache First
    const staticDestinations = ['style', 'script', 'image', 'font'];
    if (staticDestinations.includes(event.request.destination)) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    // Don't cache partial/error responses
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic' && networkResponse.type !== 'cors') {
                        return networkResponse;
                    }
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Strategy 3: Default (API calls, etc.) -> Network First
    // We do NOT cache API calls to ensure data is fresh.
    event.respondWith(
        fetch(event.request).catch(() => {
            // Optional: return cached if available (stale-while-revalidate concept) or just fail
            return caches.match(event.request);
        })
    );
});
