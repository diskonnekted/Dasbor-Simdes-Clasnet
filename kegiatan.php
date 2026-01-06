<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Buat tabel berita jika belum ada
$db->query("CREATE TABLE IF NOT EXISTS berita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  isi TEXT NOT NULL,
  gambar VARCHAR(255) DEFAULT NULL,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  published TINYINT(1) NOT NULL DEFAULT 1,
  author VARCHAR(100) DEFAULT 'Clasnet Group'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Paginasi
$perPage = 9;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
// Hitung total published
$totalItems = 0;
if ($cnt = $db->query("SELECT COUNT(*) AS c FROM berita WHERE published=1")) { $r = $cnt->fetch_assoc(); $totalItems = (int)$r['c']; }
$totalPages = $perPage > 0 ? max(1, (int)ceil($totalItems / $perPage)) : 1;
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;

// Ambil data halaman aktif
$berita = [];
$sql = sprintf(
  "SELECT id, judul, isi, gambar, dibuat_pada, author FROM berita WHERE published=1 ORDER BY dibuat_pada DESC LIMIT %d OFFSET %d",
  $perPage,
  $offset
);
if ($res = $db->query($sql)) { while ($r = $res->fetch_assoc()) { $berita[] = $r; } }

function excerpt($text, $len = 180) {
  $plain = strip_tags($text);
  if (mb_strlen($plain) <= $len) return $plain;
  return mb_substr($plain, 0, $len) . '…';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kegiatan SID — Clasnet Group</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo Clasnet Group" class="w-10 h-10 md:w-12 md:h-12 rounded object-contain">
        <div>
          <div class="font-semibold">Dasbor SID</div>
          <div class="text-xs text-gray-500">Developed by Clasnet Group</div>
        </div>
      </div>
      <?php $activeSlug = 'kegiatan'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-xl md:text-2xl font-semibold">Kegiatan SID oleh Clasnet Group</h1>
          <p class="text-xs md:text-sm mt-1 opacity-90">Berita dan dokumentasi aktivitas pengembangan dan pendampingan SID.</p>
          <p class="text-[11px] md:text-xs mt-2 opacity-80">Statistik dan konten dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="p-3 rounded-lg bg-white/10 self-start md:self-auto">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 md:w-8 md:h-8 opacity-90"><path d="M12 2l4 4H8l4-4zm8 8H4v10h16V10zm-2 2v6H6v-6h12z"/></svg>
        </div>
      </div>
    </div>

    <?php if (empty($berita)): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6">
        <div class="text-center">
          <div class="text-lg font-semibold">Belum ada berita kegiatan.</div>
          <div class="text-sm text-gray-600 mt-1">Silakan tambahkan melalui halaman admin khusus.</div>
        </div>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
        <?php foreach ($berita as $b): ?>
          <a href="berita.php?id=<?= (int)$b['id'] ?>" class="block group" title="Buka berita: <?= htmlspecialchars($b['judul']) ?>">
          <article class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 overflow-hidden group-hover:shadow-xl transition">
            <?php 
              $imgRel = !empty($b['gambar']) ? $b['gambar'] : null;
              $imgOk = $imgRel && file_exists(__DIR__ . '/' . $imgRel);
            ?>
            <?php if ($imgOk): ?>
              <div class="aspect-[16/9] bg-gray-100">
                <img src="<?= htmlspecialchars($imgRel) ?>" alt="<?= htmlspecialchars($b['judul']) ?>" class="w-full h-full object-cover" loading="lazy" onerror="this.closest('.aspect-\\[16/9\\]').innerHTML='';">
              </div>
            <?php else: ?>
              <div class="aspect-[16/9] bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14l4-4h12l4 4zM7 9h10v2H7V9zm0 4h7v2H7v-2z"/></svg>
              </div>
            <?php endif; ?>
            <div class="p-4">
              <div class="text-[11px] md:text-xs text-gray-500 flex items-center justify-between">
                <span><?= date('d M Y', strtotime($b['dibuat_pada'])) ?></span>
                <span class="font-medium"><?= htmlspecialchars($b['author'] ?: 'Clasnet Group') ?></span>
              </div>
              <h2 class="text-base md:text-lg font-semibold mt-2 text-gray-900"><?= htmlspecialchars($b['judul']) ?></h2>
              <p class="text-sm md:text-sm text-gray-700 mt-1"><?= htmlspecialchars(excerpt($b['isi'])) ?></p>
            </div>
          </article>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if ($totalPages > 1): ?>
        <div class="mt-6 flex items-center justify-center gap-2 flex-wrap">
          <?php $prev = max(1, $page - 1); $next = min($totalPages, $page + 1); ?>
          <a href="?page=<?= $prev ?>" class="px-3 py-2 rounded-lg text-sm <?= $page===1 ? 'bg-gray-100 text-gray-400 pointer-events-none' : 'bg-white ring-1 ring-gray-200 hover:bg-gray-50' ?>">Prev</a>
          <?php 
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            if ($start > 1) {
              echo '<a href=?page=1 class="px-3 py-2 rounded-lg text-sm bg-white ring-1 ring-gray-200 hover:bg-gray-50">1</a>';
              if ($start > 2) echo '<span class="px-2 text-gray-400">…</span>';
            }
            for ($i=$start; $i<=$end; $i++) {
              $cls = $i===$page ? 'bg-blue-600 text-white' : 'bg-white ring-1 ring-gray-200 hover:bg-gray-50';
              echo '<a href=?page='.$i.' class="px-3 py-2 rounded-lg text-sm '.$cls.'">'.$i.'</a>';
            }
            if ($end < $totalPages) {
              if ($end < $totalPages-1) echo '<span class="px-2 text-gray-400">…</span>';
              echo '<a href=?page='.$totalPages.' class="px-3 py-2 rounded-lg text-sm bg-white ring-1 ring-gray-200 hover:bg-gray-50">'.$totalPages.'</a>';
            }
          ?>
          <a href="?page=<?= $next ?>" class="px-3 py-2 rounded-lg text-sm <?= $page===$totalPages ? 'bg-gray-100 text-gray-400 pointer-events-none' : 'bg-white ring-1 ring-gray-200 hover:bg-gray-50' ?>">Next</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
