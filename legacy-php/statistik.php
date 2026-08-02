<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Agregasi per kecamatan:
// - total desa
// - memiliki SID: alamat_website di DB tidak kosong (aktif/tidak aktif tetap dihitung)
// - database penduduk: kolom db_penduduk tidak kosong
// - total penduduk: SUM(jumlah_penduduk)
$agg = [];
if ($res = $db->query("SELECT nama_kecamatan,
  COUNT(*) AS total,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website, '')) <> '' THEN 1 ELSE 0 END) AS with_sid,
  SUM(CASE WHEN TRIM(COALESCE(db_penduduk, '')) <> '' THEN 1 ELSE 0 END) AS db_with,
  SUM(CASE WHEN COALESCE(jumlah_penduduk, 0) > 0 THEN 1 ELSE 0 END) AS penduduk_filled,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='update' THEN 1 ELSE 0 END) AS berita_update,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='tidak update' THEN 1 ELSE 0 END) AS berita_tidak_update,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(berita_desa,'')))='tidak ada' THEN 1 ELSE 0 END) AS berita_tidak_ada,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(sosialisasi,'')))='sudah' THEN 1 ELSE 0 END) AS pelatihan_sudah,
  SUM(CASE WHEN LOWER(TRIM(COALESCE(sosialisasi,'')))='belum' THEN 1 ELSE 0 END) AS pelatihan_belum,
  SUM(COALESCE(jumlah_penduduk, 0)) AS penduduk_total
FROM desa GROUP BY nama_kecamatan ORDER BY with_sid DESC, nama_kecamatan ASC")) {
  while ($r = $res->fetch_assoc()) { $agg[] = $r; }
}

$labels = array_map(fn($r) => $r['nama_kecamatan'], $agg);
$withSid = array_map(fn($r) => (int)$r['with_sid'], $agg);
$withoutSid = [];
foreach ($agg as $r) { $withoutSid[] = (int)$r['total'] - (int)$r['with_sid']; }
$dbWith = array_map(fn($r) => (int)$r['db_with'], $agg);
$dbWithout = [];
foreach ($agg as $r) { $dbWithout[] = (int)$r['total'] - (int)$r['db_with']; }
$pendudukTotals = array_map(fn($r) => (int)$r['penduduk_total'], $agg);
// Berita Desa: update/tidak update/tidak ada
$beritaUpdate = array_map(fn($r) => (int)$r['berita_update'], $agg);
$beritaTidakUpdate = array_map(fn($r) => (int)$r['berita_tidak_update'], $agg);
$beritaTidakAda = array_map(fn($r) => (int)$r['berita_tidak_ada'], $agg);
// Pelatihan: sudah/belum (gunakan kolom sosialisasi)
$pelatihanSudah = array_map(fn($r) => (int)$r['pelatihan_sudah'], $agg);
$pelatihanBelum = array_map(fn($r) => (int)$r['pelatihan_belum'], $agg);

// Total agregat untuk kartu angka (baris pertama)
$totalDesa = 0; $totalSid = 0; $totalDbWith = 0; $totalPenduduk = 0;
foreach ($agg as $r) {
  $totalDesa += (int)$r['total'];
  $totalSid += (int)$r['with_sid'];
  $totalDbWith += (int)$r['db_with'];
  $totalPenduduk += (int)$r['penduduk_total'];
}
$totalNoSid = $totalDesa - $totalSid;

// Kecamatan terpilih dan nilai awal pie
$selectedKec = isset($_GET['kec']) ? $_GET['kec'] : (count($labels) ? $labels[0] : '');
$idxSel = array_search($selectedKec, $labels);
$selectedCounts = [
  'with' => $idxSel !== false ? $withSid[$idxSel] : 0,
  'without' => $idxSel !== false ? $withoutSid[$idxSel] : 0,
];

// Default selected kecamatan (pertama dalam daftar)
$selectedKec = $labels[0] ?? '';
$selectedCounts = ['with'=>0,'without'=>0];
foreach ($agg as $r) {
  if ($r['nama_kecamatan'] === $selectedKec) {
    $selectedCounts['with'] = (int)$r['with_sid'];
    $selectedCounts['without'] = (int)$r['total'] - (int)$r['with_sid'];
    break;
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistik SID — Clasnet Group</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    .chart-wrap { height: 220px; position: relative; overflow: hidden; }
  </style>
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
      <nav class="flex gap-4 text-sm">
        <a href="index.php" class="text-gray-700 hover:text-blue-600">Dashboard</a>
        <a href="desa.php" class="text-gray-700 hover:text-blue-600">Daftar Desa</a>
        <a href="peta.php" class="text-gray-700 hover:text-blue-600">Peta SID</a>
        <a href="kegiatan.php" class="text-gray-700 hover:text-blue-600">Kegiatan</a>
        <a href="statistik2.php" class="text-blue-600 font-medium">Statistik</a>
      </nav>
    </div>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Statistik SID</h1>
          <p class="text-sm mt-1 opacity-90">Visualisasi desa per kecamatan: memiliki SID vs belum.</p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        </div>
      </div>
    </div>

    <!-- Baris pertama: Kartu angka statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-xs text-gray-500">Total Desa</div>
        <div class="text-2xl font-semibold text-gray-800 mt-1"><?= number_format($totalDesa, 0, ',', '.') ?></div>
      </div>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-xs text-gray-500">Memiliki SID</div>
        <div class="text-2xl font-semibold text-emerald-600 mt-1"><?= number_format($totalSid, 0, ',', '.') ?></div>
      </div>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-xs text-gray-500">Belum Memiliki SID</div>
        <div class="text-2xl font-semibold text-amber-600 mt-1"><?= number_format($totalNoSid, 0, ',', '.') ?></div>
      </div>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-xs text-gray-500">Total Penduduk</div>
        <div class="text-2xl font-semibold text-blue-600 mt-1"><?= number_format($totalPenduduk, 0, ',', '.') ?></div>
      </div>
    </div>

    <!-- Baris kedua: Pie charts -->
    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5 mb-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="text-sm text-gray-600">Pilih Kecamatan</div>
        <select id="kecSelect" class="border rounded-lg px-3 py-2 text-sm">
          <?php foreach ($labels as $lab): ?>
            <option value="<?= htmlspecialchars($lab) ?>" <?= $lab === $selectedKec ? 'selected' : '' ?>><?= htmlspecialchars($lab) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <div class="text-xs text-gray-600 mb-2">Memiliki SID vs Belum</div>
          <div class="chart-wrap"><canvas id="pieSidChart"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2">Database Penduduk: Sudah vs Belum</div>
          <div class="chart-wrap"><canvas id="pieDbChart"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2">Berita Desa: Update vs Tidak Update vs Tidak Ada</div>
          <div class="chart-wrap"><canvas id="pieBeritaChart"></canvas></div>
        </div>
        <div>
          <div class="text-xs text-gray-600 mb-2">Pelatihan: Sudah vs Belum</div>
          <div class="chart-wrap"><canvas id="piePelatihanChart"></canvas></div>
        </div>
      </div>
    </div>

    <!-- Baris ketiga: Bar chart SID vs Belum per Kecamatan -->
    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
      <div class="text-sm text-gray-600 mb-3">Jumlah Desa: Memiliki SID vs Belum (per Kecamatan)</div>
      <div class="overflow-x-auto">
        <canvas id="barChart" height="260"></canvas>
      </div>
    </div>

    <div class="mt-6 text-xs text-gray-500">
      Statistik ini dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="text-blue-600 hover:underline">www.clasnet.co.id</a>
    </div>
  </div>

  <script>
    function setCanvasFixedSize(id) {
      const el = document.getElementById(id);
      if (!el) return;
      const parent = el.parentElement;
      const w = parent ? parent.clientWidth : 360;
      el.width = Math.max(320, w || 360);
      el.height = 220;
    }
    function setPieDataOrPlaceholder(chart, labels, data, colors) {
      const total = (data || []).reduce((a, b) => a + (Number.isFinite(b) ? b : (parseInt(b)||0)), 0);
      if (total > 0) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.data.datasets[0].backgroundColor = colors;
        chart.options.plugins = chart.options.plugins || {};
        chart.options.plugins.legend = chart.options.plugins.legend || {};
        chart.options.plugins.legend.display = true;
      } else {
        chart.data.labels = ['Belum Ada Data'];
        chart.data.datasets[0].data = [1];
        chart.data.datasets[0].backgroundColor = ['#d1d5db'];
        chart.options.plugins = chart.options.plugins || {};
        chart.options.plugins.legend = chart.options.plugins.legend || {};
        chart.options.plugins.legend.display = false;
      }
      chart.update();
    }
    function setBarDataOrPlaceholder(chart, labels, data, colorList, labelName) {
      const total = (data || []).reduce((a, b) => a + (Number.isFinite(b) ? b : (parseInt(b)||0)), 0);
      if (total > 0) {
        chart.data.labels = labels;
        chart.data.datasets[0].label = labelName || chart.data.datasets[0].label;
        chart.data.datasets[0].data = data;
        chart.data.datasets[0].backgroundColor = colorList;
      } else {
        chart.data.labels = ['Belum Ada Data'];
        chart.data.datasets[0].label = labelName || 'Data';
        chart.data.datasets[0].data = [1];
        chart.data.datasets[0].backgroundColor = ['#d1d5db'];
      }
      chart.update();
    }
    window.addEventListener('load', () => {
      const kecLabels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
      const withSid = <?= json_encode($withSid) ?>;
      const withoutSid = <?= json_encode($withoutSid) ?>;
      const dbWith = <?= json_encode($dbWith) ?>;
      const dbWithout = <?= json_encode($dbWithout) ?>;
      const pendudukTotals = <?= json_encode($pendudukTotals) ?>;
      const beritaUpdate = <?= json_encode($beritaUpdate) ?>;
      const beritaTidakUpdate = <?= json_encode($beritaTidakUpdate) ?>;
      const beritaTidakAda = <?= json_encode($beritaTidakAda) ?>;
      const pelatihanSudah = <?= json_encode($pelatihanSudah) ?>;
      const pelatihanBelum = <?= json_encode($pelatihanBelum) ?>;
      const aggRows = <?= json_encode($agg, JSON_UNESCAPED_UNICODE) ?>;
      const norm = (s) => String(s||'').trim().toLowerCase();
      const findRow = (label) => aggRows.find(r => norm(r.nama_kecamatan) === norm(label));

      // Pie chart: Memiliki SID vs Belum
      setCanvasFixedSize('pieSidChart');
      const pieSidCtx = document.getElementById('pieSidChart').getContext('2d');
      let pieSidChart = new Chart(pieSidCtx, {
        type: 'pie',
        data: {
          labels: ['Memiliki SID', 'Belum Memiliki SID'],
          datasets: [{ data: [<?= $selectedCounts['with'] ?>, <?= $selectedCounts['without'] ?>], backgroundColor: ['#10b981', '#f59e0b'] }]
        },
        options: { responsive: false, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
      setPieDataOrPlaceholder(pieSidChart, ['Memiliki SID','Belum Memiliki SID'], [<?= $selectedCounts['with'] ?>, <?= $selectedCounts['without'] ?>], ['#10b981','#f59e0b']);

      // Tentukan data awal berdasarkan nilai dropdown terpilih (tanpa bergantung pada indeks)
      const selectedInit = document.getElementById('kecSelect').value;
      const row0 = findRow(selectedInit);
      const initDbWith = row0 ? parseInt(row0.db_with||0) : 0;
      const initDbWithout = row0 ? Math.max(0, parseInt(row0.total||0) - parseInt(row0.db_with||0)) : 0;
      setCanvasFixedSize('pieDbChart');
      const pieDbCtx = document.getElementById('pieDbChart').getContext('2d');
      let pieDbChart = new Chart(pieDbCtx, {
        type: 'pie',
        data: {
          labels: ['Sudah Ada', 'Belum Ada'],
          datasets: [{ data: [initDbWith, initDbWithout], backgroundColor: ['#3b82f6', '#ef4444'] }]
        },
        options: { responsive: false, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
      setPieDataOrPlaceholder(pieDbChart, ['Sudah Ada','Belum Ada'], [initDbWith, initDbWithout], ['#3b82f6','#ef4444']);

      // Pie chart: Berita Desa (Update vs Tidak Update vs Tidak Ada)
      const initBerUpdate = row0 ? parseInt(row0.berita_update||0) : 0;
      const initBerTidakUpdate = row0 ? parseInt(row0.berita_tidak_update||0) : 0;
      const initBerTidakAda = row0 ? parseInt(row0.berita_tidak_ada||0) : 0;
      setCanvasFixedSize('pieBeritaChart');
      const pieBeritaCtx = document.getElementById('pieBeritaChart').getContext('2d');
      let pieBeritaChart = new Chart(pieBeritaCtx, {
        type: 'pie',
        data: {
          labels: ['Update', 'Tidak Update', 'Tidak Ada'],
          datasets: [{ data: [initBerUpdate, initBerTidakUpdate, initBerTidakAda], backgroundColor: ['#3b82f6', '#f59e0b', '#9ca3af'] }]
        },
        options: { responsive: false, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
      setPieDataOrPlaceholder(pieBeritaChart, ['Update','Tidak Update','Tidak Ada'], [initBerUpdate, initBerTidakUpdate, initBerTidakAda], ['#3b82f6','#f59e0b','#9ca3af']);

      // Pie chart: Pelatihan (Sudah vs Belum)
      const initPelSudah = row0 ? parseInt(row0.pelatihan_sudah||0) : 0;
      const initPelBelum = row0 ? parseInt(row0.pelatihan_belum||0) : 0;
      setCanvasFixedSize('piePelatihanChart');
      const piePelatihanCtx = document.getElementById('piePelatihanChart').getContext('2d');
      let piePelatihanChart = new Chart(piePelatihanCtx, {
        type: 'pie',
        data: {
          labels: ['Sudah', 'Belum'],
          datasets: [{ data: [initPelSudah, initPelBelum], backgroundColor: ['#22c55e', '#ef4444'] }]
        },
        options: { responsive: false, maintainAspectRatio: false, animation: false, plugins: { legend: { position: 'bottom' } } }
      });
      setPieDataOrPlaceholder(piePelatihanChart, ['Sudah','Belum'], [initPelSudah, initPelBelum], ['#22c55e','#ef4444']);

      // Dropdown perubahan kecamatan untuk kedua pie
      document.getElementById('kecSelect').addEventListener('change', (e) => {
        const row = findRow(e.target.value);
        const w = row ? parseInt(row.with_sid||0) : 0;
        const totalRow = row ? parseInt(row.total||0) : 0;
        const wo = Math.max(0, totalRow - w);
        const dw = row ? parseInt(row.db_with||0) : 0;
        const dwo = Math.max(0, totalRow - dw);
        setCanvasFixedSize('pieSidChart');
        setCanvasFixedSize('pieDbChart');
        setCanvasFixedSize('pieBeritaChart');
        setCanvasFixedSize('piePelatihanChart');
        // Paksa chart menyesuaikan ukuran baru jika diperlukan
        if (pieSidChart && pieSidChart.resize) pieSidChart.resize();
        if (pieDbChart && pieDbChart.resize) pieDbChart.resize();
        if (pieBeritaChart && pieBeritaChart.resize) pieBeritaChart.resize();
        if (piePelatihanChart && piePelatihanChart.resize) piePelatihanChart.resize();
        setPieDataOrPlaceholder(pieSidChart, ['Memiliki SID','Belum Memiliki SID'], [w, wo], ['#10b981','#f59e0b']);
        setPieDataOrPlaceholder(pieDbChart, ['Sudah Ada','Belum Ada'], [dw, dwo], ['#3b82f6','#ef4444']);
        const bu = row ? parseInt(row.berita_update||0) : 0;
        const btu = row ? parseInt(row.berita_tidak_update||0) : 0;
        const bta = row ? parseInt(row.berita_tidak_ada||0) : 0;
        setPieDataOrPlaceholder(pieBeritaChart, ['Update','Tidak Update','Tidak Ada'], [bu, btu, bta], ['#3b82f6','#f59e0b','#9ca3af']);
        const ps = row ? parseInt(row.pelatihan_sudah||0) : 0;
        const pb = row ? parseInt(row.pelatihan_belum||0) : 0;
        setPieDataOrPlaceholder(piePelatihanChart, ['Sudah','Belum'], [ps, pb], ['#22c55e','#ef4444']);
      });

      // Bar chart: jumlah desa Memiliki SID vs Belum per kecamatan (stacked)
      const barCtx = document.getElementById('barChart').getContext('2d');
      const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: kecLabels,
          datasets: [
            { label: 'Memiliki SID', data: withSid, backgroundColor: 'rgba(16, 185, 129, 0.7)' },
            { label: 'Belum Memiliki SID', data: withoutSid, backgroundColor: 'rgba(245, 158, 11, 0.7)' }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } },
          scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } }
        }
      });
    });

    // (Opsional) pendudukTotals disiapkan bila diperlukan untuk kartu atau chart lain
  </script>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
