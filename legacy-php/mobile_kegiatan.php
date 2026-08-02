<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$db->query("CREATE TABLE IF NOT EXISTS berita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT NOT NULL,
  gambar VARCHAR(255) DEFAULT NULL,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  published TINYINT(1) NOT NULL DEFAULT 1,
  author VARCHAR(100) DEFAULT 'Clasnet Group'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalItems = 0;
if ($cnt = $db->query("SELECT COUNT(*) AS c FROM berita WHERE published=1")) { $r = $cnt->fetch_assoc(); $totalItems = (int)$r['c']; }
$totalPages = $perPage > 0 ? max(1, (int)ceil($totalItems / $perPage)) : 1;
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;
$berita = [];
$sql = sprintf("SELECT id, judul, isi, gambar, dibuat_pada, author FROM berita WHERE published=1 ORDER BY dibuat_pada DESC LIMIT %d OFFSET %d", $perPage, $offset);
if ($res = $db->query($sql)) { while ($r = $res->fetch_assoc()) { $berita[] = $r; } }
function excerpt($text, $len = 120) {
  $plain = strip_tags($text);
  if (mb_strlen($plain) <= $len) return $plain;
  return mb_substr($plain, 0, $len) . '…';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Kegiatan — SID Mobile</title>
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
        <div class="font-semibold">Kegiatan</div>
      </div>
      <a href="kegiatan.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4">
      <?php if (empty($berita)): ?>
        <div class="bg-white rounded-xl shadow p-4">
          <div class="text-center text-sm text-gray-700">Belum ada berita kegiatan.</div>
        </div>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($berita as $b): ?>
          <a href="mobile_berita.php?id=<?= (int)$b['id'] ?>" class="block">
            <article class="bg-white rounded-xl shadow overflow-hidden">
              <?php $imgRel = !empty($b['gambar']) ? $b['gambar'] : null; $imgOk = $imgRel && file_exists(__DIR__ . '/' . $imgRel); ?>
              <?php if ($imgOk): ?>
                <img src="<?= htmlspecialchars($imgRel) ?>" alt="<?= htmlspecialchars($b['judul']) ?>" class="w-full h-36 object-cover">
              <?php endif; ?>
              <div class="p-3">
                <div class="text-[11px] text-gray-500"><?= date('d M Y', strtotime($b['dibuat_pada'])) ?> • <?= htmlspecialchars($b['author'] ?: 'Clasnet Group') ?></div>
                <div class="text-base font-semibold mt-1"><?= htmlspecialchars($b['judul']) ?></div>
                <p class="text-sm text-gray-700 mt-1"><?= htmlspecialchars(excerpt($b['isi'])) ?></p>
              </div>
            </article>
          </a>
          <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
          <div class="mt-3 flex items-center justify-between">
            <?php $prev = max(1, $page - 1); $next = min($totalPages, $page + 1); ?>
            <a href="?page=<?= $prev ?>" class="px-3 py-2 rounded-lg border bg-white text-sm <?= $page===1?'pointer-events-none opacity-50':'' ?>">Prev</a>
            <a href="?page=<?= $next ?>" class="px-3 py-2 rounded-lg border bg-white text-sm <?= $page===$totalPages?'pointer-events-none opacity-50':'' ?>">Next</a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
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
      <a href="mobile_kegiatan.php" class="flex flex-col items-center justify-center py-2 text-blue-600">
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
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js');
    });
  }
  </script>
</body>
</html>
