<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$desaData = [];
$newsContent = '';
if ($nRes = $db->query("SELECT judul, isi FROM berita WHERE published=1")) {
  while ($row = $nRes->fetch_assoc()) {
    $newsContent .= ' ' . strip_tags($row['judul'] . ' ' . $row['isi']);
  }
}
$newsContent = mb_strtolower($newsContent);

if ($res = $db->query("SELECT id, nama_kecamatan, nama_desa, alamat_website, jumlah_penduduk, db_penduduk FROM desa")) {
  while ($r = $res->fetch_assoc()) {
    $desaRaw = trim($r['nama_desa'] ?? '');
    $kecRaw = trim($r['nama_kecamatan'] ?? '');
    $desaNorm = mb_strtolower(preg_replace('/\s+/', ' ', preg_replace('/^\s*desa\s+/i', '', $desaRaw)));
    $kecNorm = mb_strtolower(preg_replace('/\s+/', ' ', $kecRaw));
    
    // Hitung Bintang
    $stars = 0;
    $hasWebsite = !empty($r['alamat_website']);
    $hasDb = isset($r['db_penduduk']) && strtoupper($r['db_penduduk']) === 'SUDAH ADA';
    $hasNews = false;
    
    if ($desaNorm !== '') {
        // Cek apakah nama desa ada di berita (whole word match)
        if (preg_match('/\b'.preg_quote($desaNorm, '/').'\b/', $newsContent)) {
            $hasNews = true;
        }
    }

    if ($hasWebsite) {
        $stars = 1;
        if ($hasDb) {
            $stars = 2;
            if ($hasNews) {
                $stars = 3;
            }
        }
    }
    $r['stars'] = $stars;

    $key = $kecNorm . '|' . $desaNorm;
    if ($desaNorm !== '') { $desaData[$key] = $r; }
  }
}
if (isset($_GET['related'])) {
  header('Content-Type: application/json');
  $desaQ = trim($_GET['desa'] ?? '');
  $kecQ = trim($_GET['kec'] ?? '');
  if ($desaQ === '') { echo json_encode(['items'=>[]]); exit; }
  $like = '%' . $desaQ . '%';
  $items = [];
  if ($stmt = $db->prepare("SELECT id, judul, isi, gambar, dibuat_pada, author FROM berita WHERE published=1 AND (judul LIKE ? OR isi LIKE ?) ORDER BY dibuat_pada DESC LIMIT 5")) {
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($b = $res->fetch_assoc()) {
      $plain = strip_tags($b['isi'] ?? '');
      $excerpt = mb_strlen($plain) <= 120 ? $plain : mb_substr($plain, 0, 120) . '…';
      $items[] = [
        'id' => (int)$b['id'],
        'judul' => $b['judul'] ?? '',
        'dibuat_pada' => $b['dibuat_pada'] ?? '',
        'author' => $b['author'] ?? '',
        'gambar' => $b['gambar'] ?? '',
        'excerpt' => $excerpt
      ];
    }
    $stmt->close();
  }
  echo json_encode(['items'=>$items]);
  exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Peta Sebaran SID (v2)</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
  <style>
    .map-wrap { height: 70vh; }
    .ol-popup { position: absolute; background: white; padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05); min-width: 200px; }
    .ol-popup:after, .ol-popup:before { top: 100%; border: solid transparent; content: " "; height: 0; width: 0; position: absolute; pointer-events: none; }
    .ol-popup:after { border-top-color: white; border-width: 10px; left: 48px; margin-left: -10px; }
    .ol-popup:before { border-top-color: #e5e7eb; border-width: 11px; left: 48px; margin-left: -11px; }
  </style>
  <script>
    window.desaData = <?= json_encode($desaData, JSON_UNESCAPED_UNICODE) ?>;
  </script>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo Clasnet Group" class="w-12 h-12 rounded object-contain">
        <div>
          <div class="font-semibold">Dasbor SID</div>
          <div class="text-xs text-gray-500">Developed by Clasnet Group</div>
        </div>
      </div>
      <?php $activeSlug = 'peta'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Peta Sebaran SID</h1>
          <p class="text-sm mt-1 opacity-90">Peta interaktif berbasis OpenLayers menggunakan data GeoJSON.</p>
          <p class="text-xs mt-2 opacity-80">Statistik dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M3 5h18v14H3V5zm2 2v10h14V7H5zm3 2h8v2H8V9zm0 4h5v2H8v-2z"/></svg>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-100">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-4 py-3 border-b gap-2">
          <div class="text-xs text-gray-400">Sumber data: GeoJSON lokal</div>
          <div class="flex flex-wrap gap-3 text-xs font-medium text-gray-600">
             <div class="flex items-center gap-1"><span class="text-amber-500 text-base">★</span> Memiliki SID</div>
             <div class="flex items-center gap-1"><span class="text-amber-500 text-base">★★</span> + Database</div>
             <div class="flex items-center gap-1"><span class="text-amber-500 text-base">★★★</span> + Update Berita</div>
          </div>
        </div>
        <div class="p-3">
          <div class="relative w-full map-wrap rounded-lg overflow-hidden ring-1 ring-gray-100">
            <div id="map" class="absolute inset-0 w-full h-full"></div>
            <div id="popup" class="ol-popup" style="display:none;"></div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-sm text-gray-600">Detail Desa</div>
        <div id="desaPanel" class="mt-3 space-y-2 text-sm text-gray-700">
          <div class="rounded-xl border border-dashed border-gray-300 p-4 bg-gradient-to-br from-gray-50 to-white">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
              </div>
              <div>
                <div class="text-sm font-semibold text-gray-900">Belum ada desa dipilih</div>
                <div class="text-xs text-gray-500">Klik sebuah desa pada peta untuk melihat informasi.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5">
          <div class="text-sm text-gray-600">Berita Terkait</div>
          <div id="relatedPanel" class="mt-3 space-y-2 text-sm text-gray-700">
            <div class="rounded-xl border border-dashed border-gray-300 p-4 bg-gradient-to-br from-gray-50 to-white">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 text-white flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14l4-4h12l4 4zM7 9h10v2H7V9zm0 4h7v2H7v-2z"/></svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-gray-900">Tidak ada desa aktif</div>
                  <div class="text-xs text-gray-500">Pilih desa pada peta untuk melihat berita terkait.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
  <script>
    const vectorSource = new ol.source.Vector({
      format: new ol.format.GeoJSON({ dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857' }),
      url: '/peta_desa.geojson'
    });
    const vectorLayer = new ol.layer.Vector({
      declutter: true,
      source: vectorSource,
      style: function(feature) {
        const name = feature.get('Nama_Desa_') || feature.get('nama') || feature.get('Name') || '';
        const kec = feature.get('Nama_Kec') || feature.get('kecamatan') || '';
        const normDesa = (name||'').toLowerCase().trim().replace(/^desa\s+/,'').replace(/\s+/g,' ');
        const normKec = (kec||'').toLowerCase().trim().replace(/\s+/g,' ');
        const key = normKec + '|' + normDesa;
        let hasWebsite = false;
        let starCount = 0;
        const dataMap = window.desaData || {};
        let data = dataMap[key];
        
        if (!data) {
          const keys = Object.keys(dataMap);
          for (let i=0;i<keys.length;i++) {
            const k = keys[i];
            if (k.endsWith('|'+normDesa)) {
              data = dataMap[k];
              break;
            }
          }
        }
        
        if (data) {
            const url = (data.alamat_website || '').trim();
            hasWebsite = url !== '';
            starCount = parseInt(data.stars || 0, 10);
        }

        const fillColor = hasWebsite ? 'rgba(16, 185, 129, 0.15)' : 'rgba(244, 63, 94, 0.15)';
        const strokeColor = hasWebsite ? '#10b981' : '#ef4444';
        
        let labelText = name;
        if (starCount > 0) labelText += ' ' + '★'.repeat(starCount);

        return new ol.style.Style({
          fill: new ol.style.Fill({ color: fillColor }),
          stroke: new ol.style.Stroke({ color: strokeColor, width: 1 }),
          text: name ? new ol.style.Text({
            text: labelText,
            font: '11px sans-serif',
            fill: new ol.style.Fill({ color: '#111827' }),
            stroke: new ol.style.Stroke({ color: '#ffffff', width: 2 })
          }) : null
        });
      }
    });
    const map = new ol.Map({
      target: 'map',
      layers: [
        new ol.layer.Tile({ source: new ol.source.OSM() }),
        vectorLayer
      ],
      view: new ol.View({ center: ol.proj.fromLonLat([110, -7.4]), zoom: 10 })
    });
    const popupEl = document.getElementById('popup');
    const popup = new ol.Overlay({ element: popupEl, positioning: 'bottom-center', stopEvent: true, offset: [0, -10] });
    map.addOverlay(popup);
    vectorSource.once('change', function() {
      if (vectorSource.getState() === 'ready') {
        const extent = vectorSource.getExtent();
        if (extent) map.getView().fit(extent, { padding: [40,40,40,40], duration: 500 });
      }
    });
    function normName(s) {
      return (s||'').toLowerCase().trim().replace(/^desa\s+/,'').replace(/\s+/g,' ');
    }
    function normKec(s) {
      return (s||'').toLowerCase().trim().replace(/\s+/g,' ');
    }
    function fmt(n) {
      const x = parseInt(n,10); if (isNaN(x)) return '';
      return x.toLocaleString('id-ID');
    }
    function esc(s) {
      return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function updateSidebar(name, kecHint) {
      const panel = document.getElementById('desaPanel');
      const composite = normKec(kecHint||'') + '|' + normName(name);
      let data = window.desaData && window.desaData[composite] ? window.desaData[composite] : null;
      if (!data) {
        const onlyName = normName(name);
        const keys = Object.keys(window.desaData||{});
        for (let i=0;i<keys.length;i++) {
          const k = keys[i];
          if (k.endsWith('|'+onlyName)) { data = window.desaData[k]; break; }
        }
      }
      if (!data) {
        panel.innerHTML = '<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-700"><div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11 15h2v2h-2zm0-8h2v6h-2z"/><path d="M1 21h22L12 2 1 21z"/></svg><div>Data desa tidak ditemukan untuk: <span class="font-semibold">'+esc(name)+'</span>.</div></div></div>';
        const rp = document.getElementById('relatedPanel');
        rp.innerHTML = '<div class="rounded-xl border border-gray-200 bg-white p-4 text-gray-600">Tidak ada berita terkait.</div>';
        return;
      }
      const url = data.alamat_website || '';
      const valid = /^https?:\/\//i.test(url);
      const urlHtml = valid
        ? '<a class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow hover:opacity-90" href="'+esc(url)+'" target="_blank">Buka Website<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg></a>'
        : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak ada website</span>';
      const dbp = (data.db_penduduk||'').toString().trim();
      const dbpUpper = dbp.toUpperCase();
      let dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>';
      if (dbpUpper === 'SUDAH ADA') dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Sudah Ada</span>';
      else if (dbpUpper === 'BELUM ADA') dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Belum Ada</span>';
      const linkDesa = 'desa.php?q=' + encodeURIComponent(data.nama_desa || name);
      const stars = parseInt(data.stars||0, 10);
      const starStr = stars > 0 ? ' <span class="text-amber-500 text-sm">'+'★'.repeat(stars)+'</span>' : '';
      panel.innerHTML =
        '<div class="space-y-3">'+
          '<div class="flex items-start gap-3">'+
            '<div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-indigo-500 text-white flex items-center justify-center">'+
              '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>'+
            '</div>'+
            '<div class="flex-1">'+
              '<div class="text-base font-semibold text-gray-900">'+esc(data.nama_desa || name)+starStr+'</div>'+
              '<div class="text-xs text-gray-600">'+esc(data.nama_kecamatan || kecHint || '')+'</div>'+
              '<div class="mt-2 flex items-center gap-2">'+dbBadge+'</div>'+
            '</div>'+
          '</div>'+
          '<div class="rounded-xl ring-1 ring-gray-100 bg-white p-3">'+
            '<div class="flex items-center justify-between">'+
              '<div class="text-sm text-gray-600">Jumlah Penduduk</div>'+
              '<div class="text-xl font-bold text-gray-900">'+esc(fmt(data.jumlah_penduduk))+'</div>'+
            '</div>'+
            '<div class="mt-3 flex items-center justify-between">'+
              '<div class="text-sm text-gray-600">Website</div>'+
              '<div>'+urlHtml+'</div>'+
            '</div>'+
          '</div>';
      const rp = document.getElementById('relatedPanel');
      rp.innerHTML =
        '<div class="space-y-2">'+
          '<div class="rounded-xl border bg-white p-3 animate-pulse">'+
            '<div class="flex gap-3 items-start">'+
              '<div class="w-16 h-16 rounded bg-gray-200"></div>'+
              '<div class="flex-1 space-y-2">'+
                '<div class="h-3 bg-gray-200 rounded w-1/2"></div>'+
                '<div class="h-3 bg-gray-200 rounded w-3/4"></div>'+
                '<div class="h-3 bg-gray-200 rounded w-2/3"></div>'+
              '</div>'+
            '</div>'+
          '</div>'+
          '<div class="rounded-xl border bg-white p-3 animate-pulse">'+
            '<div class="flex gap-3 items-start">'+
              '<div class="w-16 h-16 rounded bg-gray-200"></div>'+
              '<div class="flex-1 space-y-2">'+
                '<div class="h-3 bg-gray-200 rounded w-1/2"></div>'+
                '<div class="h-3 bg-gray-200 rounded w-3/4"></div>'+
                '<div class="h-3 bg-gray-200 rounded w-2/3"></div>'+
              '</div>'+
            '</div>'+
          '</div>'+
        '</div>';
      fetch('peta.php?related=1&desa='+encodeURIComponent(data.nama_desa || name)+'&kec='+encodeURIComponent(data.nama_kecamatan || kecHint || ''))
        .then(r => r.json())
        .then(j => {
          const items = j && j.items ? j.items : [];
          if (!items.length) { rp.innerHTML = '<div class="rounded-xl border border-gray-200 bg-white p-4 text-gray-600">Tidak ada berita terkait.</div>'; return; }
          let html = '';
          for (let i=0;i<items.length;i++) {
            const it = items[i];
            const dateStr = it.dibuat_pada ? new Date(it.dibuat_pada.replace(' ', 'T')).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
            html += '<a href="berita.php?id='+it.id+'" class="block group rounded-xl ring-1 ring-gray-100 bg-white p-3 hover:shadow-md transition">'+
                      '<div class="flex gap-3 items-start">'+
                        (it.gambar ? '<img src="'+esc(it.gambar)+'" class="w-16 h-16 rounded object-cover flex-shrink-0" alt="">' : '<div class="w-16 h-16 rounded bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14l4-4h12l4 4zM7 9h10v2H7V9zm0 4h7v2H7v-2z"/></svg></div>')+
                        '<div class="flex-1">'+
                          '<div class="text-[11px] text-gray-500">'+dateStr+' • '+esc(it.author || 'Clasnet Group')+'</div>'+
                          '<div class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600">'+esc(it.judul)+'</div>'+
                          '<div class="text-xs text-gray-700">'+esc(it.excerpt)+'</div>'+
                        '</div>'+
                      '</div>'+
                    '</a>';
          }
          rp.innerHTML = html;
        })
        .catch(() => { rp.innerHTML = '<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-700">Gagal memuat berita terkait.</div>'; });
    }
    map.on('singleclick', function(evt) {
      const feature = map.forEachFeatureAtPixel(evt.pixel, f => f);
      if (feature) {
        const props = feature.getProperties();
        const name = props['Nama_Desa_'] || props['nama'] || props['Name'] || 'Desa';
        const kec = props['Nama_Kec'] || props['kecamatan'] || '';
        const kab = props['Nama_Kab'] || props['kabupaten'] || '';
        
        const normDesa = (name||'').toLowerCase().trim().replace(/^desa\s+/,'').replace(/\s+/g,' ');
        const normKecVal = (kec||'').toLowerCase().trim().replace(/\s+/g,' ');
        const composite = normKecVal + '|' + normDesa;
        let data = window.desaData && window.desaData[composite] ? window.desaData[composite] : null;
        if (!data) {
             const keys = Object.keys(window.desaData||{});
             for (let i=0;i<keys.length;i++) {
               if (keys[i].endsWith('|'+normDesa)) { data = window.desaData[keys[i]]; break; }
             }
        }
        const stars = data ? parseInt(data.stars||0, 10) : 0;
        const starStr = stars > 0 ? ' <span class="text-amber-500">'+'★'.repeat(stars)+'</span>' : '';

        popupEl.innerHTML = '<div class="text-sm font-semibold text-gray-900">'+name+starStr+'</div>' +
                            '<div class="text-xs text-gray-600">'+[kec,kab].filter(Boolean).join(' · ')+'</div>';
        popupEl.style.display = 'block';
        popup.setPosition(evt.coordinate);
        updateSidebar(name, kec);
      } else {
        popupEl.style.display = 'none';
      }
    });
  </script>
</body>
</html>
