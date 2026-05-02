const CACHE_NAME = 'qlnuoc-cache-v1';
const urlsToCache = [
  './',
  './index.php',
  './css/style.css',
  './icons/icon-192.png',
  './icons/icon-512.png'
];

// Cài đặt service worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

// Kích hoạt và dọn cache cũ
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
      )
    )
  );
});

// Xử lý fetch offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response =>
      response || fetch(event.request)
    )
  );
});
