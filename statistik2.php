<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Agregasi segar per kecamatan (tanpa ketergantungan halaman lama)
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
$dbWith = array_map(fn($r) => (int)$r['db_with'], $agg);
$beritaUpdate = array_map(fn($r) => (int)$r['berita_update'], $agg);
$beritaTidakUpdate = array_map(fn($r) => (int)$r['berita_tidak_update'], $agg);
$beritaTidakAda = array_map(fn($r) => (int)$r['berita_tidak_ada'], $agg);
$pelatihanSudah = array_map(fn($r) => (int)$r['pelatihan_sudah'], $agg);
$pelatihanBelum = array_map(fn($r) => (int)$r['pelatihan_belum'], $agg);
// Hitung desa tanpa SID per kecamatan (total - with_sid)
$withoutSid = array_map(fn($r) => max(0, (int)$r['total'] - (int)$r['with_sid']), $agg);

// Default kecamatan terpilih: pertama dalam daftar
$selectedKec = $labels[0] ?? '';
$selectedRow = null;
foreach ($agg as $r) { if ($r['nama_kecamatan'] === $selectedKec) { $selectedRow = $r; break; } }
if (!$selectedRow && count($agg)) { $selectedRow = $agg[0]; }

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistik2 SID — Clasnet Group</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    .chart-wrap { height: 220px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo Clasnet Group" class="w-10 h-10 rounded object-contain">
        <div>
          <div class="font-semibold">Dasbor SID (Halaman Baru)</div>
          <div class="text-xs text-gray-500">Statistik v2, skrip baru</div>
        </div>
      </div>
      <?php $activeSlug = 'statistik2'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5 mb-6">
      <div class="flex items-center gap-3">
        <div class="text-sm text-gray-600">Pilih Kecamatan</div>
        <select id="kecSelect2" class="border rounded-lg px-3 py-2 text-sm">
          <?php foreach ($labels as $lab): ?>
            <option value="<?= htmlspecialchars($lab) ?>" <?= $lab === $selectedKec ? 'selected' : '' ?>><?= htmlspecialchars($lab) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <div>
          <div class="text-xs text-gray-600 mb-2 text-center">Memiliki SID vs Belum</div>
          <div class="chart-wrap"><canvas id="pieSid2" width="360" height="220" style="display:block"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2 text-center">Database Penduduk: Sudah vs Belum</div>
          <div class="chart-wrap"><canvas id="pieDb2" width="360" height="220" style="display:block"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2 text-center">Berita Desa: Ada vs Tidak Ada</div>
          <div class="chart-wrap"><canvas id="pieBerita2" width="360" height="220" style="display:block"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2 text-center">Pelatihan: Sudah vs Belum</div>
          <div class="chart-wrap"><canvas id="piePelatihan2" width="360" height="220" style="display:block"></canvas></div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
      <div class="text-sm text-gray-600 mb-3">Jumlah Desa: Memiliki SID vs Belum (per Kecamatan)</div>
      <div class="overflow-x-auto">
        <div class="relative h-[420px]">
          <canvas id="bar2"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Data dari PHP ke JS
    const rows = <?= json_encode($agg, JSON_UNESCAPED_UNICODE) ?>;
    const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
    const withSid = <?= json_encode($withSid) ?>;
    const withoutSid = <?= json_encode($withoutSid) ?>;

    // Pemetaan label -> nilai baris, tahan terhadap spasi/kapital
    const norm = (s) => String(s||'').trim().toLowerCase();
    const mapByLabel = {};
    rows.forEach(r => { mapByLabel[norm(r.nama_kecamatan)] = r; });

    function getRow(label) { return mapByLabel[norm(label)] || null; }
    function makePie(ctx, labels, data, colors) {
      return new Chart(ctx, {
        type: 'pie',
        data: { labels, datasets: [{ data, backgroundColor: colors }] },
        options: { responsive: false, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
    }
    function updatePie(chart, labels, data, colors) {
      const total = (data||[]).reduce((a,b)=>a+(parseInt(b)||0),0);
      if (total > 0) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.data.datasets[0].backgroundColor = colors;
        chart.options.plugins.legend.display = true;
      } else {
        chart.data.labels = ['Belum Ada Data'];
        chart.data.datasets[0].data = [1];
        chart.data.datasets[0].backgroundColor = ['#d1d5db'];
        chart.options.plugins.legend.display = false;
      }
      chart.update();
    }

    // Inisialisasi chart
    const sidCtx = document.getElementById('pieSid2').getContext('2d');
    const dbCtx = document.getElementById('pieDb2').getContext('2d');
    const berCtx = document.getElementById('pieBerita2').getContext('2d');
    const pelCtx = document.getElementById('piePelatihan2').getContext('2d');
    const barCtx = document.getElementById('bar2').getContext('2d');

    const sel = document.getElementById('kecSelect2');
    const r0 = getRow(sel.value);

    const pieSid = makePie(sidCtx, ['Memiliki SID','Belum Memiliki SID'], [r0?parseInt(r0.with_sid||0):0, r0?Math.max(0,parseInt(r0.total||0)-parseInt(r0.with_sid||0)):0], ['#10b981','#f59e0b']);
    const pieDb = makePie(dbCtx, ['Sudah Ada di Website','Belum Ada di Website'], [r0?parseInt(r0.db_with||0):0, r0?Math.max(0,parseInt(r0.with_sid||0)-parseInt(r0.db_with||0)):0], ['#3b82f6','#ef4444']);
    const initBerAda = r0 ? (parseInt(r0.berita_update||0) + parseInt(r0.berita_tidak_update||0)) : 0;
    const initBerTidakAda = r0 ? parseInt(r0.berita_tidak_ada||0) : 0;
    const pieBer = makePie(berCtx, ['Ada','Tidak Ada'], [initBerAda, initBerTidakAda], ['#3b82f6','#9ca3af']);
    const piePel = makePie(pelCtx, ['Sudah','Belum'], [r0?parseInt(r0.pelatihan_sudah||0):0, r0?parseInt(r0.pelatihan_belum||0):0], ['#22c55e','#ef4444']);

    // Bar chart semua kecamatan
    const bar = new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          { label: 'Memiliki SID', data: withSid, backgroundColor: 'rgba(16, 185, 129, 0.7)' },
          { label: 'Belum Memiliki SID', data: withoutSid, backgroundColor: 'rgba(245, 158, 11, 0.7)' }
        ]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Handler dropdown
    sel.addEventListener('change', () => {
      const row = getRow(sel.value);
      const w = row?parseInt(row.with_sid||0):0;
      const total = row?parseInt(row.total||0):0;
      const wo = Math.max(0, total - w);
      updatePie(pieSid, ['Memiliki SID','Belum Memiliki SID'], [w, wo], ['#10b981','#f59e0b']);
      const dw = row?parseInt(row.db_with||0):0;
      const dwo = Math.max(0, (row?parseInt(row.with_sid||0):0) - dw);
      updatePie(pieDb, ['Sudah Ada di Website','Belum Ada di Website'], [dw, dwo], ['#3b82f6','#ef4444']);
      const berAda = row ? (parseInt(row.berita_update||0) + parseInt(row.berita_tidak_update||0)) : 0;
      const berTidakAda = row ? parseInt(row.berita_tidak_ada||0) : 0;
      updatePie(pieBer, ['Ada','Tidak Ada'], [berAda, berTidakAda], ['#3b82f6','#9ca3af']);
      const ps = row?parseInt(row.pelatihan_sudah||0):0;
      const pb = row?parseInt(row.pelatihan_belum||0):0;
      updatePie(piePel, ['Sudah','Belum'], [ps, pb], ['#22c55e','#ef4444']);
    });
  </script>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
