<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
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
$websitePct = $total > 0 ? round(($withWebsite / $total) * 100) : 0;
$inactivePct= $total > 0 ? round(($withoutWebsite / $total) * 100) : 0;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Dasbor SID Mobile</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="/clasnet.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen pb-16">
  <header class="fixed top-0 left-0 right-0 bg-blue-600 text-white z-20">
    <div class="px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo" class="w-8 h-8 rounded object-contain">
        <div class="font-semibold">Dasbor SID</div>
      </div>
      <div class="flex items-center gap-3">
        <a href="index.php" class="text-white/90">Web</a>
      </div>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4">
      <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs text-gray-500">Total Desa</div>
            <div class="text-3xl font-bold"><?= $total ?></div>
          </div>
          <div class="p-2 rounded-lg bg-blue-50 text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl shadow p-4">
          <div class="text-xs text-gray-500">Memiliki Website</div>
          <div class="flex items-center justify-between mt-1">
            <div class="text-2xl font-bold text-emerald-600"><?= $withWebsite ?></div>
            <span class="text-xs text-gray-600"><?= $websitePct ?>%</span>
          </div>
          <div class="mt-2 h-2 bg-emerald-100 rounded-full"><div class="h-2 bg-emerald-500 rounded-full" style="width: <?= $websitePct ?>%"></div></div>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
          <div class="text-xs text-gray-500">Belum Website</div>
          <div class="flex items-center justify-between mt-1">
            <div class="text-2xl font-bold text-rose-600"><?= $withoutWebsite ?></div>
            <span class="text-xs text-gray-600"><?= $inactivePct ?>%</span>
          </div>
          <div class="mt-2 h-2 bg-rose-100 rounded-full"><div class="h-2 bg-rose-500 rounded-full" style="width: <?= $inactivePct ?>%"></div></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="text-sm font-medium">Akses Cepat</div>
        <div class="grid grid-cols-3 gap-3 mt-3">
          <a href="desa.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 text-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 9-1.5 1.5L18 12.5V20h-5v-4H11v4H6v-7.5L4.5 13.5 3 12l9-9z"/></svg>
            <span class="text-xs mt-1">Desa</span>
          </a>
          <a href="peta.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-emerald-50 text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.14 2 5 5.14 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.86-3.14-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
            <span class="text-xs mt-1">Peta</span>
          </a>
          <a href="statistik2.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-indigo-50 text-indigo-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            <span class="text-xs mt-1">Statistik</span>
          </a>
          <a href="kegiatan.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-yellow-50 text-yellow-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14l4-4h12c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
            <span class="text-xs mt-1">Kegiatan</span>
          </a>
          <a href="inovasi.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-rose-50 text-rose-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9 21h6v-1c0-1.66 1.34-3 3-3v-4c0-3.31-2.69-6-6-6S6 9.69 6 13v4c1.66 0 3 1.34 3 3v1z"/></svg>
            <span class="text-xs mt-1">Inovasi</span>
          </a>
          <a href="kontak.php" class="flex flex-col items-center justify-center p-3 rounded-lg bg-gray-50 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v16l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            <span class="text-xs mt-1">Kontak</span>
          </a>
        </div>
      </div>
    </div>
  </main>
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t z-20">
    <div class="grid grid-cols-5 text-xs text-gray-600">
      <a href="mobile.php" class="flex flex-col items-center justify-center py-2 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Beranda</span>
      </a>
      <a href="desa.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        <span>Desa</span>
      </a>
      <a href="peta.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.14 2 5 5.14 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.86-3.14-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
        <span>Peta</span>
      </a>
      <a href="statistik2.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        <span>Statistik</span>
      </a>
      <a href="kontak.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v16l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        <span>Kontak</span>
      </a>
    </div>
  </nav>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
