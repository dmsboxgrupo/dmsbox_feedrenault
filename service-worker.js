// service-worker.js

const CACHE_NAME = 'feed-renault-cache-v1';
const FILES_TO_CACHE = [
    '/',
    '/index.html',
    '/login.html',
    '/dashboard.html',
    '/assets/css/login/custom.css',
    '/assets/css/dashboard/custom.css',
    '/assets/css/dashboard/cartao.css',
    '/assets/css/libraries/slick.css',
    '/assets/css/libraries/normalize.css',
    '/assets/css/libraries/cropper.css',
    '/assets/js/config.js',
    '/assets/js/login/classes.js',
    '/assets/js/login/actions.js',
    '/assets/js/dashboard/classes.js',
    '/assets/js/dashboard/actions.js',
    '/assets/js/parts/home.js',
    '/assets/js/parts/materiais.js',
    '/assets/js/parts/campanhas.js',
    '/assets/js/parts/salvos.js',
    '/assets/js/parts/notificacoes.js',
    '/assets/js/parts/configuracoes.js',
    '/assets/js/parts/busca.js',
    '/assets/js/parts/ajuda.js',
    '/assets/images/favicon/android-icon-36x36.png',
    '/assets/images/favicon/android-icon-48x48.png',
    '/assets/images/favicon/android-icon-72x72.png',
    '/assets/images/favicon/android-icon-96x96.png',
    '/assets/images/favicon/android-icon-144x144.png',
    '/assets/images/favicon/android-icon-192x192.png',
    '/assets/images/favicon/screenshot-1.jpg',
    '/assets/images/favicon/screenshot-2.jpg',
    '/assets/images/favicon/screenshot-3.jpg',
    '/assets/images/favicon/screenshot-4.jpg',
    '/assets/images/favicon/screenshot-5.jpg'
];

// Instalar o Service Worker
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Instalando service worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Cacheando arquivos...');
                return cache.addAll(FILES_TO_CACHE);
            })
    );
});

// Ativar o Service Worker
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Ativando service worker...');
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

// Recuperar arquivos do cache
self.addEventListener('fetch', (event) => {
    console.log('[Service Worker] Recuperando: ', event.request.url);
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            // Retorna a resposta do cache se existir
            if (cachedResponse) {
                return cachedResponse;
            }
            // Caso contrário, faz a requisição à rede
            return fetch(event.request);
        })
    );
});
