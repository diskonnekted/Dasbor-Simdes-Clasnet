<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!isset($_COOKIE['force_desktop'])) {
  if (preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua)) {
    header('Location: mobile_berita.php?id=' . $id);
    exit;
  }
}
$post = null;
$gallery = [];
$relatedDesaList = [];
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $post ? htmlspecialchars($post['judul']) : 'Berita Tidak Ditemukan' ?> — Clasnet Group</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
  <style> .prose img { max-width: 100%; height: auto; } </style>
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
        <a href="kegiatan.php" class="text-blue-600 font-medium">Kegiatan</a>
        <a href="statistik2.php" class="text-gray-700 hover:text-blue-600">Statistik</a>
      </nav>
    </div>
  </header>

  <div class="max-w-3xl mx-auto px-4 py-8">
    <?php if (!$post): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6">
        <div class="text-center">
          <div class="text-lg font-semibold">Berita tidak ditemukan</div>
          <div class="text-sm text-gray-600 mt-1">Berita yang Anda cari tidak tersedia atau belum dipublikasikan.</div>
          <a href="kegiatan.php" class="inline-block mt-4 px-3 py-2 rounded-lg bg-blue-600 text-white text-sm">Kembali ke Kegiatan</a>
        </div>
      </div>
    <?php else: ?>
      <article class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 overflow-hidden">
        <?php if (!empty($post['gambar'])): ?>
          <button type="button" class="w-full group" onclick="openLightbox('<?= htmlspecialchars($post['gambar']) ?>')">
            <img src="<?= htmlspecialchars($post['gambar']) ?>" alt="<?= htmlspecialchars($post['judul']) ?>" class="w-full max-h-[60vh] object-cover group-hover:opacity-95">
          </button>
        <?php endif; ?>
        <div class="p-6">
          <div class="text-xs text-gray-500 flex items-center justify-between">
            <span><?= date('d M Y', strtotime($post['dibuat_pada'])) ?></span>
            <span class="font-medium"><?= htmlspecialchars($post['author'] ?: 'Clasnet Group') ?></span>
          </div>
          <h1 class="text-2xl font-semibold mt-2 text-gray-900"><?= htmlspecialchars($post['judul']) ?></h1>
          <div class="prose prose-sm max-w-none mt-3 text-gray-800">
            <?= $post['isi'] ?>
          </div>

          <?php if (!empty($relatedDesaList)): ?>
            <div class="mt-6 pt-4 border-t border-gray-100">
              <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Desa Terkait
              </h3>
              <div class="flex flex-wrap gap-2">
                <?php foreach ($relatedDesaList as $rd): ?>
                  <a href="desa.php?q=<?= urlencode($rd['nama_desa']) ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-200 shadow-sm hover:border-blue-300 hover:ring-1 hover:ring-blue-300 transition-all text-sm text-gray-700 group">
                    <span class="font-medium text-blue-600 group-hover:text-blue-700"><?= htmlspecialchars($rd['nama_desa']) ?></span>
                    <span class="text-xs text-gray-400 group-hover:text-gray-500 border-l pl-2"><?= htmlspecialchars($rd['nama_kecamatan']) ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="mt-8">
            <a href="kegiatan.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white shadow-sm hover:bg-gray-50 text-sm text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
              Kembali ke Halaman Berita
            </a>
          </div>
        </div>
      </article>
      <?php if (!empty($gallery)): ?>
        <div class="mt-6">
          <div class="text-sm text-gray-600 mb-2">Foto Pendukung</div>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <?php foreach ($gallery as $g): ?>
              <button type="button" class="block rounded overflow-hidden ring-1 ring-gray-200 hover:ring-gray-400" onclick="openLightbox('<?= htmlspecialchars($g['path']) ?>')">
                <img src="<?= htmlspecialchars($g['path']) ?>" alt="Foto pendukung" class="w-full h-32 object-cover">
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div id="lightbox" class="hidden fixed inset-0 z-50 bg-black/80 items-center justify-center p-4">
    <img id="lightboxImage" src="" alt="Foto" class="max-w-[90vw] max-h-[90vh] object-contain rounded shadow-2xl">
  </div>

  <script>
    function openLightbox(src) {
      const lb = document.getElementById('lightbox');
      const img = document.getElementById('lightboxImage');
      img.src = src;
      lb.classList.remove('hidden');
      lb.classList.add('flex');
    }
    function closeLightbox() {
      const lb = document.getElementById('lightbox');
      lb.classList.add('hidden');
      lb.classList.remove('flex');
    }
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });
    document.getElementById('lightbox').addEventListener('click', (e) => { if (e.target.id === 'lightbox') closeLightbox(); });
  </script>

  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
