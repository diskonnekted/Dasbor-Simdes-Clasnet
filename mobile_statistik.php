<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$agg = [];
$sql = "SELECT nama_kecamatan,
  COUNT(*) AS total,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,''))<>'' THEN 1 ELSE 0 END) AS with_sid,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,''))<>'' AND UPPER(TRIM(COALESCE(db_penduduk,'')))='SUDAH ADA' THEN 1 ELSE 0 END) AS db_with,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='update' THEN 1 ELSE 0 END) AS berita_update,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='tidak update' THEN 1 ELSE 0 END) AS berita_tidak_update,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='tidak ada' OR TRIM(COALESCE(berita_desa,''))='' THEN 1 ELSE 0 END) AS berita_tidak_ada,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(sosialisasi,'')))='sudah' THEN 1 ELSE 0 END) AS pelatihan_sudah,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(sosialisasi,'')))='belum' OR TRIM(COALESCE(sosialisasi,''))='' THEN 1 ELSE 0 END) AS pelatihan_belum
FROM desa GROUP BY nama_kecamatan ORDER BY nama_kecamatan";
if ($res = $db->query($sql)) { while ($r = $res->fetch_assoc()) { $agg[] = $r; } }
$labels = array_map(fn($r) => $r['nama_kecamatan'], $agg);
$withSid = array_map(fn($r) => (int)$r['with_sid'], $agg);
$withoutSid = array_map(fn($r) => max(0, (int)$r['total'] - (int)$r['with_sid']), $agg);
$selectedKec = $labels[0] ?? '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Statistik — SID Mobile</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="/clasnet.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen pb-16">
  <header class="fixed top-0 left-0 right-0 bg-blue-600 text-white z-20">
    <div class="px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo" class="w-8 h-8 rounded object-contain">
        <div class="font-semibold">Statistik SID</div>
      </div>
      <a href="statistik2.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4 space-y-3">
      <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center gap-2">
          <div class="text-sm text-gray-600">Pilih Kecamatan</div>
          <select id="kecSelectM" class="border rounded-lg px-3 py-2 text-sm">
            <?php foreach ($labels as $lab): ?>
              <option value="<?= htmlspecialchars($lab) ?>" <?= $lab===$selectedKec?'selected':'' ?>><?= htmlspecialchars($lab) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3 mt-4">
          <div>
            <div class="text-[11px] text-gray-600 mb-1 text-center">Memiliki SID vs Belum</div>
            <div class="relative h-40"><canvas id="pieSidM"></canvas></div>
          </div>
          <div>
            <div class="text-[11px] text-gray-600 mb-1 text-center">Database Penduduk</div>
            <div class="relative h-40"><canvas id="pieDbM"></canvas></div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-600 mb-2">Jumlah Desa: Memiliki SID vs Belum (per Kecamatan)</div>
        <div class="relative h-72"><canvas id="barM"></canvas></div>
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
  <?php include __DIR__ . '/partials/footer.php'; ?>
  <script>
    const rows = <?= json_encode($agg, JSON_UNESCAPED_UNICODE) ?>;
    const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
    const withSid = <?= json_encode($withSid) ?>;
    const withoutSid = <?= json_encode($withoutSid) ?>;
    const norm = (s) => String(s||'').trim().toLowerCase();
    const mapByLabel = {}; rows.forEach(r => { mapByLabel[norm(r.nama_kecamatan)] = r; });
    function getRow(label){ return mapByLabel[norm(label)] || null; }
    function makePie(ctx, labels, data, colors){
      return new Chart(ctx, {
        type: 'pie',
        data: { labels, datasets: [{ data, backgroundColor: colors }] },
        options: { responsive: true, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
    }
    function updatePie(chart, labels, data, colors){
      const total = (data||[]).reduce((a,b)=>a+(parseInt(b)||0),0);
      chart.data.labels = total>0 ? labels : ['Belum Ada Data'];
      chart.data.datasets[0].data = total>0 ? data : [1];
      chart.data.datasets[0].backgroundColor = total>0 ? colors : ['#d1d5db'];
      chart.options.plugins.legend.display = total>0;
      chart.update();
    }
    const sel = document.getElementById('kecSelectM');
    const pieSid = makePie(document.getElementById('pieSidM'), ['Memiliki SID','Belum Memiliki SID'], [0,0], ['#10b981','#f59e0b']);
    const pieDb = makePie(document.getElementById('pieDbM'), ['Sudah Ada di Website','Belum Ada di Website'], [0,0], ['#3b82f6','#ef4444']);
    function refreshSelected(){
      const row = getRow(sel.value);
      const w = row?parseInt(row.with_sid||0):0;
      const total = row?parseInt(row.total||0):0;
      const wo = Math.max(0, total - w);
      updatePie(pieSid, ['Memiliki SID','Belum Memiliki SID'], [w, wo], ['#10b981','#f59e0b']);
      const dw = row?parseInt(row.db_with||0):0;
      const dwo = Math.max(0, w - dw);
      updatePie(pieDb, ['Sudah Ada di Website','Belum Ada di Website'], [dw, dwo], ['#3b82f6','#ef4444']);
    }
    sel.addEventListener('change', refreshSelected);
    refreshSelected();
    const bar = new Chart(document.getElementById('barM'), {
      type: 'bar',
      data: { labels, datasets: [
        { label: 'Memiliki SID', data: withSid, backgroundColor: 'rgba(16, 185, 129, 0.7)' },
        { label: 'Belum Memiliki SID', data: withoutSid, backgroundColor: 'rgba(245, 158, 11, 0.7)' }
      ]},
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
  </script>
</body>
</html>
