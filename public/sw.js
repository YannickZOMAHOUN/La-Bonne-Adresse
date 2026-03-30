/**
 * Service Worker — Bonnes Adresses Bénin
 * Stratégie : Cache-first pour assets statiques, Network-first pour pages
 */

const CACHE_NAME   = 'bonnes-adresses-v1';
const CACHE_STATIC = 'bonnes-adresses-static-v1';

// Assets à mettre en cache immédiatement à l'installation
const PRECACHE_URLS = [
    '/',
    '/css/app.css',
    '/manifest.json',
    '/offline.html',
];

// ── INSTALLATION : précache les assets essentiels ─────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_STATIC).then(cache => {
            return cache.addAll(PRECACHE_URLS);
        }).then(() => self.skipWaiting())
    );
});

// ── ACTIVATION : supprime les anciens caches ──────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME && key !== CACHE_STATIC)
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── FETCH : stratégie selon le type de ressource ─────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignore les requêtes non-GET et les APIs externes
    if (request.method !== 'GET') return;
    if (!url.origin === location.origin) return;

    // Assets statiques (CSS, JS, fonts, images de storage) → Cache-first
    if (
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/storage/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com')
    ) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (!response || response.status !== 200) return response;
                    const clone = response.clone();
                    caches.open(CACHE_STATIC).then(cache => cache.put(request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // Pages HTML → Network-first, fallback cache puis offline
    if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (!response || response.status !== 200) return response;
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(cached =>
                        cached || caches.match('/offline.html')
                    )
                )
        );
        return;
    }

    // Tout le reste → Network avec fallback cache
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
