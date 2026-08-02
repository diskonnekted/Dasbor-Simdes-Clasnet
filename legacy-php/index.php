<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (isset($_GET['view']) && $_GET['view'] === 'desktop') {
  setcookie('force_desktop', '1', time() + 60*60*24*30, '/');
}
if (!isset($_COOKIE['force_desktop'])) {
  $isMobile = preg_match('/Android.*Mobile|iPhone|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i', $ua);
  $isTablet = preg_match('/iPad|Tablet/i', $ua);
  if ($isMobile && !$isTablet) {
    header('Location: mobile.php');
    exit;
  }
}

$total = 0; $withWebsite = 0; $withoutWebsite = 0; $active = 0; $inactive = 0; $unknown = 0;
$q1 = $db->query("SELECT COUNT(*) AS c FROM desa");
if ($q1) { $r = $q1->fetch_assoc(); $total = (int)$r['c']; }
$q2 = $db->query("SELECT 
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) <> '' THEN 1 ELSE 0 END) AS w,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) = '' THEN 1 ELSE 0 END) AS nw
FROM desa");
if ($q2) { $r = $q2->fetch_assoc(); $withWebsite = (int)$r['w']; $withoutWebsite = (int)$r['nw']; }
$q3 = $db->query("SELECT 
  SUM(LOWER(TRIM(COALESCE(website_status,'')))='active') AS a, 
  SUM(LOWER(TRIM(COALESCE(website_status,'')))='inactive') AS i, 
  SUM(website_status IS NULL OR TRIM(COALESCE(website_status,''))='') AS u 
FROM desa");
if ($q3) { $r = $q3->fetch_assoc(); $active = (int)$r['a']; $inactive = (int)$r['i']; $unknown = (int)$r['u']; }

// Inisialisasi variabel penduduk agar tidak undefined
$dbPendudukSudah = 0; $dbPendudukBelum = 0;

// Total desa (dinamis) mengikuti data di database
$kabupatenTotal = $total;

// Hitung persentase untuk tampilan UI (mengacu ke total dinamis)
$websitePct = $total > 0 ? round(($withWebsite / $total) * 100) : 0;
$activePct  = ($withWebsite + $withoutWebsite) > 0 ? round(($active / ($withWebsite + $withoutWebsite)) * 100) : 0;
$inactivePct= ($withWebsite + $withoutWebsite) > 0 ? round(($inactive / ($withWebsite + $withoutWebsite)) * 100) : 0;
// Persentase berbasis URL (definisi: memiliki website jika URL tidak kosong)
$websiteActivePct = $total > 0 ? round(($withWebsite / $total) * 100) : 0;
$websiteInactivePct = $total > 0 ? round(($withoutWebsite / $total) * 100) : 0;

// Statistik adopsi SID per kecamatan
$kecamatanStats = [];
$sidTotal = 0; $totalDesaKabu = 0;
$q4 = $db->query("SELECT nama_kecamatan AS kec,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) <> '' THEN 1 ELSE 0 END) AS sid,
  SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) = '' THEN 1 ELSE 0 END) AS nonsid,
  COUNT(*) AS total
FROM desa GROUP BY nama_kecamatan ORDER BY nama_kecamatan");
if ($q4) { while ($row = $q4->fetch_assoc()) { 
  $kecamatanStats[] = [
    'kec' => $row['kec'] ?? 'Tidak diketahui',
    'sid' => (int)$row['sid'],
    'nonsid' => (int)$row['nonsid'],
    'total' => (int)$row['total']
  ];
  $sidTotal += (int)$row['sid'];
  $totalDesaKabu += (int)$row['total'];
} }

$desaKeys = [];
$q5 = $db->query("SELECT nama_kecamatan, nama_desa FROM desa");
if ($q5) { while ($row = $q5->fetch_assoc()) { $desaKeys[$row['nama_kecamatan'].'|'.$row['nama_desa']] = true; } }

// Hitung data penduduk dari database (tanpa CSV)
$q6 = $db->query("SELECT 
  SUM(CASE WHEN UPPER(COALESCE(db_penduduk,''))='SUDAH ADA' THEN 1 ELSE 0 END) AS sudah,
  SUM(CASE WHEN UPPER(COALESCE(db_penduduk,''))='BELUM ADA' THEN 1 ELSE 0 END) AS belum
FROM desa");
if ($q6) { $r = $q6->fetch_assoc(); $dbPendudukSudah = (int)$r['sudah']; $dbPendudukBelum = (int)$r['belum']; }

// Hitung total penduduk dari database
$dbPendudukTotal = $dbPendudukSudah + $dbPendudukBelum;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="generator" content="Clasnet CMS <?= htmlspecialchars(APP_VERSION) ?>">
  <title>Statistik SID</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="/clasnet.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      <?php include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Statistik SID Kabupaten</h1>
          <p class="text-sm mt-1 opacity-90">Gambaran cepat status website desa dan data penduduk.</p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 opacity-80">
          <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
        </svg>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-xl transition">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">Total Desa (DB)</div>
            <div class="text-4xl font-bold mt-1"><?= $kabupatenTotal ?></div>
            <div class="text-xs text-gray-500 mt-1">Terdata di DB SID: <?= $kabupatenTotal ?> desa</div>
          </div>
          <div class="p-2 rounded-lg bg-blue-50 text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-xl transition">
        <div class="text-xs uppercase tracking-wide text-gray-500">Memiliki Website</div>
        <div class="flex items-center justify-between mt-1">
          <div class="text-3xl font-bold"><?= $withWebsite ?></div>
          <span class="text-sm text-gray-600"><?= $websitePct ?>%</span>
        </div>
        <div class="mt-3 h-2 bg-gray-200 rounded-full">
          <div class="h-2 bg-blue-600 rounded-full" style="width: <?= $websitePct ?>%"></div>
        </div>
        <div class="text-xs text-gray-500 mt-2">Belum memiliki website: <?= $withoutWebsite ?></div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-xl transition">
        <div class="text-xs uppercase tracking-wide text-gray-500">Website Aktif (URL Aktif)</div>
        <div class="flex items-center justify-between mt-1">
          <div class="text-3xl font-bold text-emerald-600"><?= $withWebsite ?></div>
          <span class="text-sm text-gray-600"><?= $websiteActivePct ?>%</span>
        </div>
        <div class="mt-3 h-2 bg-emerald-100 rounded-full">
          <div class="h-2 bg-emerald-500 rounded-full" style="width: <?= $websiteActivePct ?>%"></div>
        </div>
        <div class="text-xs text-gray-500 mt-2">Belum aktif (tanpa URL): <?= $withoutWebsite ?> (<?= $websiteInactivePct ?>%)</div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-5 hover:shadow-xl transition">
        <div class="text-xs uppercase tracking-wide text-gray-500">Database Penduduk</div>
        <div class="mt-1">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Sudah Ada</span>
            <span class="font-semibold text-gray-900"><?= $dbPendudukSudah ?></span>
          </div>
          <div class="flex items-center justify-between mt-1">
            <span class="text-sm text-gray-600">Belum Ada</span>
            <span class="font-semibold text-gray-900"><?= $dbPendudukBelum ?></span>
          </div>
        </div>
        <div class="mt-3 h-2 bg-gray-200 rounded-full">
          <?php $dbPct = $dbPendudukTotal>0 ? round(($dbPendudukSudah/$dbPendudukTotal)*100) : 0; ?>
          <div class="h-2 bg-indigo-600 rounded-full" style="width: <?= $dbPct ?>%"></div>
        </div>
        <div class="text-xs text-gray-500 mt-2">Sumber: basis data MySQL SID</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
      <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-5 ring-1 ring-gray-100">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="text-sm text-gray-500">Adopsi Website per Kecamatan</div>
            <div class="text-xs text-gray-400">Jumlah desa yang sudah dan belum memiliki website (memiliki URL)</div>
          </div>
          <div class="flex items-center gap-2">
            <label for="kecSelect" class="text-xs text-gray-500">Filter kecamatan:</label>
            <select id="kecSelect" class="text-sm border rounded-lg px-2 py-1">
              <option value="all">Semua kecamatan</option>
            </select>
          </div>
        </div>
        <canvas id="sidChart" class="mt-4"></canvas>
        <div id="kecAnalytics" class="mt-4 text-sm text-gray-700"></div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-5 ring-1 ring-gray-100">
        <div class="text-sm text-gray-500">Ringkasan Cepat</div>
        <div class="mt-3 space-y-2">
          <div class="flex items-center justify-between p-3 bg-emerald-50 text-emerald-700 rounded-lg">
            <span>Website aktif (berdasarkan URL)</span>
            <span class="font-semibold"><?= $withWebsite ?></span>
          </div>
          <div class="flex items-center justify-between p-3 bg-rose-50 text-rose-700 rounded-lg">
            <span>Belum memiliki website</span>
            <span class="font-semibold"><?= $withoutWebsite ?></span>
          </div>
          <div class="flex items-center justify-between p-3 bg-blue-50 text-blue-700 rounded-lg">
            <span>Memiliki Website</span>
            <span class="font-semibold"><?= $withWebsite ?></span>
          </div>
          <div class="flex items-center justify-between p-3 bg-indigo-50 text-indigo-700 rounded-lg">
            <span>Coverage website terhadap total desa</span>
            <span class="font-semibold"><?= $withWebsite ?> / <?= $kabupatenTotal ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-6 text-xs text-gray-500">
      Statistik ini dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="text-blue-600 hover:underline">www.clasnet.co.id</a>
    </div>
  </div>

  <script>
    const kecStats = <?= json_encode($kecamatanStats, JSON_UNESCAPED_UNICODE) ?>;
    const kabAdopt = {
      sidTotal: <?= json_encode($sidTotal) ?>,
      totalDesa: <?= json_encode($totalDesaKabu) ?>
    };
    const selectEl = document.getElementById('kecSelect');
    const analyticsEl = document.getElementById('kecAnalytics');

    const labelsAll = kecStats.map(s => s.kec);
    const sidCountsAll = kecStats.map(s => s.sid);
    const nonSidCountsAll = kecStats.map(s => s.nonsid);

    // Populate dropdown
    labelsAll.forEach(k => {
      const opt = document.createElement('option');
      opt.value = k; opt.textContent = k; selectEl.appendChild(opt);
    });

    const ctx = document.getElementById('sidChart').getContext('2d');
    const chartConfig = {
      type: 'bar',
      data: {
        labels: labelsAll,
        datasets: [
          {
            label: 'Sudah Website',
            data: sidCountsAll,
            backgroundColor: 'rgba(16, 185, 129, 0.7)',
            borderColor: 'rgb(5, 150, 105)',
            borderWidth: 1
          },
          {
            label: 'Belum Website',
            data: nonSidCountsAll,
            backgroundColor: 'rgba(148, 163, 184, 0.7)',
            borderColor: 'rgb(100, 116, 139)',
            borderWidth: 1
          }
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: {
          x: { stacked: true, ticks: { maxRotation: 45, minRotation: 0 } },
          y: { stacked: true, beginAtZero: true }
        }
      }
    };
    const chart = new Chart(ctx, chartConfig);

    function renderAnalytics(selected) {
      const totalKec = kecStats.length;
      const kabRate = kabAdopt.totalDesa > 0 ? (kabAdopt.sidTotal / kabAdopt.totalDesa) : 0;
      if (!selected || selected === 'all') {
        analyticsEl.innerHTML = `Rata-rata adopsi kabupaten: ${(kabRate*100).toFixed(1)}%. Total desa: ${kabAdopt.totalDesa}, sudah SID: ${kabAdopt.sidTotal}.`;
        return;
      }
      const s = kecStats.find(x => x.kec === selected);
      if (!s) { analyticsEl.textContent = ''; return; }
      const rate = s.total > 0 ? (s.sid / s.total) : 0;
      // Ranking berdasarkan persentase adopsi
      const sorted = [...kecStats].sort((a,b) => (b.total>0?b.sid/b.total:0) - (a.total>0?a.sid/a.total:0));
      const rank = sorted.findIndex(x => x.kec === selected) + 1;
      const diff = rate - kabRate;
      const trend = diff >= 0 ? 'di atas' : 'di bawah';
      analyticsEl.innerHTML = `Kecamatan <span class="font-semibold">${selected}</span>: ${s.sid} dari ${s.total} desa sudah SID (<span class="font-semibold">${(rate*100).toFixed(1)}%</span>). Peringkat adopsi: <span class="font-semibold">#${rank}</span> dari ${totalKec} kecamatan, ${trend} rata-rata kabupaten sebesar ${(Math.abs(diff)*100).toFixed(1)}%.`;
    }

    function updateChartFor(selected) {
      if (!selected || selected === 'all') {
        chart.data.labels = labelsAll;
        chart.data.datasets[0].data = sidCountsAll;
        chart.data.datasets[1].data = nonSidCountsAll;
      } else {
        const idx = labelsAll.indexOf(selected);
        if (idx >= 0) {
          chart.data.labels = [selected];
          chart.data.datasets[0].data = [sidCountsAll[idx]];
          chart.data.datasets[1].data = [nonSidCountsAll[idx]];
        }
      }
      chart.update();
      renderAnalytics(selected);
    }

    selectEl.addEventListener('change', function(){
      updateChartFor(selectEl.value);
    });

    // Initial render
    updateChartFor('all');
  </script>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>

