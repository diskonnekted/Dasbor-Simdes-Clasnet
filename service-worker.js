const CACHE_NAME = 'sid-pwa-v2';
const URLS_TO_CACHE = ['/', '/index.php', '/mobile.php', '/mobile_desa.php', '/mobile_kegiatan.php', '/mobile_inovasi.php', '/mobile_kontak.php', '/mobile_peta.php', '/mobile_statistik.php', '/clasnet.png', '/footer.png'];
self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE_NAME).then(c => c.addAll(URLS_TO_CACHE)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
  if (e.request.method !== 'GET') return;
  const url = new URL(e.request.url);
  const isHTML = e.request.mode === 'navigate' || (e.request.headers.get('accept') || '').includes('text/html');
  if (url.origin === self.location.origin && isHTML) {
    e.respondWith(fetch(e.request).then(resp => {
      const copy = resp.clone();
      caches.open(CACHE_NAME).then(c => c.put(e.request, copy));
      return resp;
    }).catch(() => caches.match(e.request).then(r => r || caches.match('/mobile.php') || caches.match('/index.php'))));
    return;
  }
  if (url.origin === self.location.origin) {
    e.respondWith(caches.match(e.request).then(r => r || fetch(e.request).then(resp => {
      const copy = resp.clone();
      caches.open(CACHE_NAME).then(c => c.put(e.request, copy));
      return resp;
    }).catch(() => caches.match('/mobile.php') || caches.match('/index.php'))));
  }
});
