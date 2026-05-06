const CACHE = 'minipos-v2';
const OFFLINE_URL = '/offline';

const PRECACHE = [
    '/',
    '/login',
    '/offline',
    '/pos',
    '/css/app.css',
    '/js/alpine.min.js',
];

/* ── Install: pre-cache critical assets ─────────────────────── */
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE).then(cache =>
            cache.addAll(PRECACHE).catch(err => console.warn('[SW] Pre-cache failed:', err))
        )
    );
    self.skipWaiting();
});

/* ── Activate: purge old caches ─────────────────────────────── */
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

/* ── Fetch strategy ──────────────────────────────────────────── */
self.addEventListener('fetch', event => {
    const req = event.request;
    const url = new URL(req.url);

    // Only handle same-origin GET requests
    if (req.method !== 'GET' || url.origin !== location.origin) return;

    // Static assets (CSS/JS/fonts/images): cache-first
    if (/\.(css|js|woff2?|ttf|svg|png|jpg|ico|webp)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(req).then(cached => cached || fetchAndCache(req))
        );
        return;
    }

    // HTML pages: network-first, fallback to cache then offline page
    if (req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req)
                .then(res => { cacheResponse(req, res.clone()); return res; })
                .catch(() =>
                    caches.match(req).then(cached =>
                        cached || caches.match(OFFLINE_URL)
                    )
                )
        );
    }
});

function fetchAndCache(req) {
    return fetch(req).then(res => { cacheResponse(req, res.clone()); return res; });
}

function cacheResponse(req, res) {
    if (res && res.status === 200) {
        caches.open(CACHE).then(c => c.put(req, res));
    }
}
