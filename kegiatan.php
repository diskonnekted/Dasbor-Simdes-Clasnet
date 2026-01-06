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
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Hitung total published
$totalItems = 0;
$where = "published=1";
$params = [];
$types = "";

if ($q !== '') {
    $where .= " AND (judul LIKE ? OR isi LIKE ? OR tags LIKE ?)";
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if ($stmt = $db->prepare("SELECT COUNT(*) AS c FROM berita WHERE $where")) {
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $totalItems = (int)$row['c'];
    }
    $stmt->close();
}

$totalPages = $perPage > 0 ? max(1, (int)ceil($totalItems / $perPage)) : 1;
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;

// Ambil data halaman aktif
$berita = [];
$sql = "SELECT id, judul, isi, gambar, dibuat_pada, author, tags FROM berita WHERE $where ORDER BY dibuat_pada DESC LIMIT ? OFFSET ?";
if ($stmt = $db->prepare($sql)) {
    $limitParams = array_merge($params, [$perPage, $offset]);
    $limitTypes = $types . "ii";
    $stmt->bind_param($limitTypes, ...$limitParams);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $berita[] = $r;
    }
    $stmt->close();
}

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
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl md:text-2xl font-semibold">Kegiatan SID oleh Clasnet Group</h1>
          <p class="text-xs md:text-sm mt-1 opacity-90">Berita dan dokumentasi aktivitas pengembangan dan pendampingan SID.</p>
          <p class="text-[11px] md:text-xs mt-2 opacity-80">Statistik dan konten dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        
        <div class="w-full md:w-auto min-w-[300px]">
           <form method="get" action="" class="relative text-gray-700">
             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
               <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                 <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
               </svg>
             </div>
             <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" 
                    class="block w-full pl-10 pr-20 py-2.5 rounded-lg border-0 bg-white/10 text-white placeholder-white/60 ring-1 ring-white/20 focus:ring-2 focus:ring-white/50 focus:bg-white/20 sm:text-sm transition backdrop-blur-sm" 
                    placeholder="Cari kegiatan...">
             <?php if($q !== ''): ?>
             <a href="kegiatan.php" class="absolute inset-y-0 right-16 flex items-center px-2 text-white/70 hover:text-white">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
             </a>
             <?php endif; ?>
             <button type="submit" class="absolute inset-y-1 right-1 px-4 text-xs font-medium text-blue-700 bg-white rounded-md hover:bg-blue-50 focus:outline-none shadow-sm transition-colors">
               Cari
             </button>
           </form>
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
              <?php if (!empty($b['tags'])): ?>
                <div class="mt-3 flex flex-wrap gap-2">
                  <?php foreach(explode(' ', str_replace([',', '#'], [' ', ''], $b['tags'])) as $tag): ?>
                    <?php if(trim($tag) !== ''): ?>
                      <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600">
                        #<?= htmlspecialchars(trim($tag)) ?>
                      </span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
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
