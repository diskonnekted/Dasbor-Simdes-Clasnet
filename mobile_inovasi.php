<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$items = [];
try {
  $db->query("CREATE TABLE IF NOT EXISTS inovasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    published TINYINT(1) NOT NULL DEFAULT 1,
    dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  if ($res = $db->query("SELECT judul, deskripsi, gambar FROM inovasi WHERE published=1 ORDER BY dibuat_pada DESC")) {
    while ($r = $res->fetch_assoc()) {
      $items[] = ['file'=>$r['gambar'] ?: '', 'title'=>$r['judul'] ?: '', 'desc'=>$r['deskripsi'] ?: ''];
    }
  }
} catch (Throwable $e) {}
if (empty($items)) {
  $items = [
    ['file'=>'images/an1.jpg','title'=>'Integrasi LoRa untuk Desa','desc'=>'Jaringan LoRa untuk konektivitas jarak jauh berdaya rendah.'],
    ['file'=>'images/an2.jpg','title'=>'Sensor IoT Lingkungan','desc'=>'Pemantauan kualitas udara, banjir, dan cuaca lokal.'],
    ['file'=>'images/an3.jpg','title'=>'Dashboard Kinerja Desa','desc'=>'Visualisasi data pelayanan, statistik penduduk, dan aktivitas.'],
    ['file'=>'images/an4.jpg','title'=>'Anjungan Pelayanan Mandiri','desc'=>'Terminal layanan warga mandiri dengan integrasi antrean.'],
  ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Inovasi — SID Mobile</title>
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
        <div class="font-semibold">Inovasi</div>
      </div>
      <a href="inovasi.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4 space-y-3">
      <?php foreach ($items as $it): $path = __DIR__ . '/' . $it['file']; $hasImg = file_exists($path); ?>
        <article class="bg-white rounded-xl shadow overflow-hidden">
          <?php if ($hasImg): ?>
            <img src="<?= htmlspecialchars($it['file']) ?>" alt="<?= htmlspecialchars($it['title']) ?>" class="w-full h-40 object-cover">
          <?php endif; ?>
          <div class="p-3">
            <div class="text-base font-semibold text-gray-900"><?= htmlspecialchars($it['title']) ?></div>
            <p class="text-sm text-gray-700 mt-1"><?= htmlspecialchars($it['desc']) ?></p>
            <div class="mt-2">
              <a href="https://wa.me/6285117041846?text=Halo%20Clasnet%2C%20saya%20ingin%20mendalami%20inovasi%20<?= urlencode($it['title']) ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 text-sm">
                Konsultasi via WhatsApp
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      <div class="mt-2">
        <a href="mobile_kontak.php" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border bg-white text-sm">
          Lihat layanan & kontak
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
        </a>
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
      <a href="mobile_inovasi.php" class="flex flex-col items-center justify-center py-2 text-blue-600">
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
</body>
</html>
