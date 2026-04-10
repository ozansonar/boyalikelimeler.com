/**
 * Boyalı Kelimeler - Service Worker
 *
 * Strategy:
 *  - Static assets (css/js/fonts/images) -> cache-first
 *  - HTML navigation requests            -> network-first with offline fallback
 *  - POST / non-GET / admin / auth / api -> bypass (never cache)
 *  - Cross-origin requests               -> bypass
 */

const CACHE_VERSION = 'bk-v3';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;
const OFFLINE_URL = '/offline';

// Pre-cache only the offline shell and core icons (small, guaranteed static)
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/manifest.json',
];

// URL prefixes that must NEVER be cached (dynamic, authenticated, or sensitive)
const BYPASS_PREFIXES = [
    '/admin',
    '/login',
    '/logout',
    '/register',
    '/password',
    '/email',
    '/profile',
    '/myposts',
    '/api',
    '/livewire',
    '/broadcasting',
    '/sanctum',
];

// File extensions treated as static assets (cache-first)
const STATIC_EXTENSIONS = [
    '.css', '.js', '.mjs',
    '.woff', '.woff2', '.ttf', '.otf', '.eot',
    '.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg', '.ico',
];

// ----- INSTALL -----------------------------------------------------------
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// ----- ACTIVATE ----------------------------------------------------------
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => !key.startsWith(CACHE_VERSION))
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// ----- HELPERS -----------------------------------------------------------
const isStaticAsset = (url) => {
    const pathname = url.pathname.toLowerCase();
    return STATIC_EXTENSIONS.some((ext) => pathname.endsWith(ext));
};

const shouldBypass = (request, url) => {
    // Only handle GET requests
    if (request.method !== 'GET') return true;

    // Only same-origin
    if (url.origin !== self.location.origin) return true;

    // Never cache bypass routes
    if (BYPASS_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) return true;

    // Never cache requests with query strings that look like auth/csrf
    if (url.search.includes('_token')) return true;

    return false;
};

// ----- FETCH -------------------------------------------------------------
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (shouldBypass(request, url)) {
        return; // Let the browser handle it normally
    }

    // Static assets: cache-first
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Navigation / HTML: network-first with offline fallback
    if (request.mode === 'navigate' || (request.headers.get('accept') || '').includes('text/html')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Everything else: plain network (no caching side-effects)
});

// Cache-first for static assets
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response && response.status === 200 && response.type === 'basic') {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        // Fall back to anything we might have cached previously
        const fallback = await caches.match(request);
        if (fallback) return fallback;
        throw err;
    }
}

// Network-first for HTML navigations
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response && response.status === 200 && response.type === 'basic') {
            const cache = await caches.open(RUNTIME_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (err) {
        const cached = await caches.match(request);
        if (cached) return cached;
        return caches.match(OFFLINE_URL);
    }
}

// Allow page to trigger immediate activation of a new SW
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
