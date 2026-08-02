<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null; $gallery = []; $relatedDesaList = [];
if ($id > 0) {
  if ($res = $db->prepare("SELECT id, judul, isi, gambar, dibuat_pada, author, tags, related_desa FROM berita WHERE published=1 AND id=?")) {
    $res->bind_param('i', $id);
    $res->execute();
    $result = $res->get_result();
    $post = $result->fetch_assoc();
    $res->close();
  }
  if ($post) {
    // Ambil data desa terkait
    if (!empty($post['related_desa'])) {
      $ids = array_filter(array_map('intval', explode(',', $post['related_desa'])));
      if (!empty($ids)) {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $types = str_repeat('i', count($ids));
        if ($stmtD = $db->prepare("SELECT id, nama_desa, nama_kecamatan FROM desa WHERE id IN ($in) ORDER BY nama_kecamatan, nama_desa")) {
          $stmtD->bind_param($types, ...$ids);
          $stmtD->execute();
          $resD = $stmtD->get_result();
          while ($rd = $resD->fetch_assoc()) { $relatedDesaList[] = $rd; }
          $stmtD->close();
        }
      }
    }

    if ($stmtG = $db->prepare('SELECT id, path FROM berita_foto WHERE berita_id=? ORDER BY urutan ASC, id ASC')) {
      $stmtG->bind_param('i', $id);
      $stmtG->execute();
      $resG = $stmtG->get_result();
      while ($row = $resG->fetch_assoc()) { $gallery[] = $row; }
      $stmtG->close();
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title><?= $post ? htmlspecialchars($post['judul']) : 'Berita Tidak Ditemukan' ?> — SID Mobile</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <link rel="apple-touch-icon" href="/clasnet.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>.content img{max-width:100%;height:auto}</style>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen pb-16">
  <header class="fixed top-0 left-0 right-0 bg-blue-600 text-white z-20">
    <div class="px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="mobile_kegiatan.php" class="inline-flex items-center justify-center w-9 h-9 rounded bg-white/20" aria-label="Kembali">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        </a>
        <div class="font-semibold">Detail Kegiatan</div>
      </div>
      <a href="kegiatan.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4">
      <?php if (!$post): ?>
        <div class="bg-white rounded-xl shadow p-4">
          <div class="text-center text-sm">Berita tidak ditemukan</div>
          <div class="mt-3 text-center">
            <a href="mobile_kegiatan.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-600 text-white text-sm">Kembali</a>
          </div>
        </div>
      <?php else: ?>
        <article class="bg-white rounded-xl shadow overflow-hidden">
          <?php if (!empty($post['gambar'])): ?>
            <img src="<?= htmlspecialchars($post['gambar']) ?>" alt="<?= htmlspecialchars($post['judul']) ?>" class="w-full h-44 object-cover">
          <?php endif; ?>
          <div class="p-3">
            <div class="text-[11px] text-gray-500 flex items-center justify-between">
              <span><?= date('d M Y', strtotime($post['dibuat_pada'])) ?></span>
              <span class="font-medium"><?= htmlspecialchars($post['author'] ?: 'Clasnet Group') ?></span>
            </div>
            <h1 class="text-base font-semibold mt-1 text-gray-900"><?= htmlspecialchars($post['judul']) ?></h1>
            <div class="content prose prose-sm max-w-none mt-2 text-gray-800"><?= $post['isi'] ?></div>
            
            <?php if (!empty($relatedDesaList)): ?>
              <div class="mt-4 pt-3 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-900 mb-2 flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Desa Terkait
                </div>
                <div class="flex flex-wrap gap-2">
                  <?php foreach ($relatedDesaList as $rd): ?>
                    <a href="mobile_desa.php?q=<?= urlencode($rd['nama_desa']) ?>" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-xs text-gray-700 active:bg-blue-50 active:border-blue-200 active:text-blue-700 transition-colors">
                      <span class="font-medium"><?= htmlspecialchars($rd['nama_desa']) ?></span>
                      <span class="text-[10px] text-gray-400 border-l border-gray-300 pl-1.5 ml-1"><?= htmlspecialchars($rd['nama_kecamatan']) ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($gallery)): ?>
          <div class="px-3 pb-3">
            <div class="text-xs text-gray-600 mb-2">Foto Pendukung</div>
            <div class="grid grid-cols-3 gap-2">
              <?php foreach ($gallery as $g): ?>
                <img src="<?= htmlspecialchars($g['path']) ?>" alt="Foto pendukung" class="w-full h-20 object-cover rounded">
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </article>
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
