<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Peta — SID Mobile</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="/clasnet.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen pb-16">
  <header class="fixed top-0 left-0 right-0 bg-blue-600 text-white z-20">
    <div class="px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo" class="w-8 h-8 rounded object-contain">
        <div class="font-semibold">Peta SID</div>
      </div>
      <a href="peta.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4">
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <div>
            <div class="text-xs text-gray-400">Data: GeoJSON dari repo Clasnet</div>
          </div>
          <a href="https://github.com/Clasnet/clasnet-peta-desa" target="_blank" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg border bg-white text-sm">
            Repo Peta
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
          </a>
        </div>
        <div class="p-3">
          <div class="relative w-full h-[60vh] rounded-lg overflow-hidden ring-1 ring-gray-100">
            <div id="mapm" class="absolute inset-0 w-full h-full"></div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t z-20">
    <div class="grid grid-cols-5 text-xs text-gray-600">
      <a href="mobile.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Beranda</span>
      </a>
      <a href="mobile_desa.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        <span>Desa</span>
      </a>
      <a href="mobile_kegiatan.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14l4-4h12c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
        <span>Kegiatan</span>
      </a>
      <a href="mobile_inovasi.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9 21h6v-1c0-1.66 1.34-3 3-3v-4c0-3.31-2.69-6-6-6S6 9.69 6 13v4c1.66 0 3 1.34 3 3v1z"/></svg>
        <span>Inovasi</span>
      </a>
      <a href="mobile_kontak.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v16l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        <span>Kontak</span>
      </a>
    </div>
  </nav>
  <script>
  </script>
  <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
  <script>
    const vsrc = new ol.source.Vector({
      format: new ol.format.GeoJSON({ dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857' }),
      url: '/peta_desa.geojson'
    });
    const vlyr = new ol.layer.Vector({
      declutter: true,
      source: vsrc,
      style: function(feature) {
        const name = feature.get('Nama_Desa_') || feature.get('nama') || feature.get('Name') || '';
        const kec = feature.get('Nama_Kec') || feature.get('kecamatan') || '';
        const normDesa = (name||'').toLowerCase().trim().replace(/^desa\s+/,'').replace(/\s+/g,' ');
        const normKec = (kec||'').toLowerCase().trim().replace(/\s+/g,' ');
        const key = normKec + '|' + normDesa;
        let hasWebsite = false;
        const dataMap = window.desaData || {};
        if (dataMap[key]) {
          const url = (dataMap[key].alamat_website || '').trim();
          hasWebsite = url !== '';
        } else {
          const keys = Object.keys(dataMap);
          for (let i=0;i<keys.length;i++) {
            const k = keys[i];
            if (k.endsWith('|'+normDesa)) {
              const url = (dataMap[k].alamat_website || '').trim();
              hasWebsite = url !== '';
              break;
            }
          }
        }
        const fillColor = hasWebsite ? 'rgba(16, 185, 129, 0.15)' : 'rgba(244, 63, 94, 0.15)';
        const strokeColor = hasWebsite ? '#10b981' : '#ef4444';
        return new ol.style.Style({
          fill: new ol.style.Fill({ color: fillColor }),
          stroke: new ol.style.Stroke({ color: strokeColor, width: 1 }),
          text: name ? new ol.style.Text({
            text: name,
            font: '10px sans-serif',
            fill: new ol.style.Fill({ color: '#111827' }),
            stroke: new ol.style.Stroke({ color: '#ffffff', width: 2 })
          }) : null
        });
      }
    });
    const mmap = new ol.Map({
      target: 'mapm',
      layers: [
        new ol.layer.Tile({ source: new ol.source.OSM() }),
        vlyr
      ],
      view: new ol.View({ center: ol.proj.fromLonLat([110, -7.4]), zoom: 10 })
    });
    vsrc.once('change', function() {
      if (vsrc.getState() === 'ready') {
        const extent = vsrc.getExtent();
        if (extent) mmap.getView().fit(extent, { padding: [20,20,20,20], duration: 500 });
      }
    });
  </script>
  <script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js');
    });
  }
  </script>
</body>
</html>
