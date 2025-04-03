const CACHE_NAME = 'uac-inspection-v1';
const urlsToCache = [
    '/uacinspectionapp/',
    '/uacinspectionapp/auth/login.php',
    '/uacinspectionapp/assets/css/style.css',
    '/uacinspectionapp/assets/js/main.js',
    '/uacinspectionapp/assets/js/jquery-3.5.1.min.js',
    '/uacinspectionapp/assets/js/bootstrap.min.js',
    '/uacinspectionapp/assets/css/bootstrap.min.css',
    // Add your app icons
    '/uacinspectionapp/assets/icons/icon-72x72.png',
    '/uacinspectionapp/assets/icons/icon-96x96.png',
    '/uacinspectionapp/assets/icons/icon-128x128.png',
    '/uacinspectionapp/assets/icons/icon-144x144.png',
    '/uacinspectionapp/assets/icons/icon-152x152.png',
    '/uacinspectionapp/assets/icons/icon-192x192.png',
    '/uacinspectionapp/assets/icons/icon-384x384.png',
    '/uacinspectionapp/assets/icons/icon-512x512.png'
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch Event
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version or fetch new
                return response || fetch(event.request);
            })
    );
});

// Activate Event
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});