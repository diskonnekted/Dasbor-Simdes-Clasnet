<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();
$perPage = isset($_GET['per']) ? max(10, min(100, (int)$_GET['per'])) : 20;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$kec = isset($_GET['kec']) ? trim($_GET['kec']) : '';
$where = [];
$params = [];
$types = '';
if ($q !== '') { $where[] = "nama_desa LIKE ?"; $params[] = '%'.$q.'%'; $types .= 's'; }
if ($kec !== '') { $where[] = "nama_kecamatan = ?"; $params[] = $kec; $types .= 's'; }
$sqlCount = 'SELECT COUNT(*) AS total FROM desa';
if (!empty($where)) { $sqlCount .= ' WHERE ' . implode(' AND ', $where); }
$stmtCount = $db->prepare($sqlCount);
if ($stmtCount) {
  if (!empty($params)) { $stmtCount->bind_param($types, ...$params); }
  $stmtCount->execute();
  $rc = $stmtCount->get_result();
  $totalRows = (int)$rc->fetch_assoc()['total'];
  $stmtCount->close();
} else { $totalRows = 0; }
$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;
$rows = [];
$sql = 'SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk FROM desa';
if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' ORDER BY nama_kecamatan, nama_desa LIMIT ? OFFSET ?';
$stmt = $db->prepare($sql);
if ($stmt) {
  $bindTypes = $types . 'ii';
  $bindParams = $params;
  $bindParams[] = $perPage;
  $bindParams[] = $offset;
  $stmt->bind_param($bindTypes, ...$bindParams);
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
  }
  $stmt->close();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
  <title>Desa — SID Mobile</title>
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
        <div class="font-semibold">Desa</div>
      </div>
      <a href="index.php" class="text-white/90">Web</a>
    </div>
  </header>
  <main class="pt-16">
    <div class="px-4">
      <form method="get" class="bg-white rounded-xl shadow p-3 flex items-center gap-2">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="flex-1 text-sm border rounded-lg px-2 py-2" placeholder="Cari nama desa...">
        <select name="kec" class="text-sm border rounded-lg px-2 py-2">
          <option value="">Semua Kecamatan</option>
          <?php
            if ($kl = $db->query("SELECT DISTINCT nama_kecamatan FROM desa WHERE TRIM(COALESCE(nama_kecamatan,''))<>'' ORDER BY nama_kecamatan")) {
              while ($row = $kl->fetch_assoc()) {
                $nm = $row['nama_kecamatan'];
                echo '<option value="'.htmlspecialchars($nm).'" '.($kec===$nm?'selected':'').'>'.htmlspecialchars($nm).'</option>';
              }
            }
          ?>
        </select>
        <button class="text-sm px-3 py-2 rounded-lg bg-blue-600 text-white">Cari</button>
      </form>
      <div class="mt-3 text-xs text-gray-600">Menampilkan <?= count($rows) ?> dari <?= $totalRows ?> desa • Hal <?= $page ?>/<?= $totalPages ?></div>
      <div class="mt-3 space-y-2">
        <?php foreach ($rows as $r): 
          $url = $r['alamat_website'];
          $validUrl = ($url && preg_match('/^(http|https):\\/\\//', $url));
          $jp = $r['jumlah_penduduk'];
          $jpFmt = is_numeric($jp) ? number_format((int)$jp, 0, ',', '.') : '';
          $dbpLabel = ($r['db_penduduk'] === null || trim($r['db_penduduk']) === '') ? 'Tidak diketahui' : $r['db_penduduk'];
          $dbpUpper = strtoupper(trim($dbpLabel));
          $dbBadge = $dbpUpper === 'SUDAH ADA' ? 'bg-emerald-50 text-emerald-700' : ($dbpUpper === 'BELUM ADA' ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-700');
        ?>
        <article class="bg-white rounded-xl shadow p-3">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-xs text-gray-500"><?= htmlspecialchars($r['nama_kecamatan']) ?></div>
              <div class="text-base font-semibold"><?= htmlspecialchars($r['nama_desa']) ?></div>
              <div class="mt-1 text-xs text-gray-600">Penduduk: <span class="font-medium"><?= htmlspecialchars($jpFmt) ?></span></div>
              <div class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-[11px] <?= $dbBadge ?>"><?= htmlspecialchars($dbpLabel) ?></div>
            </div>
            <div class="text-right">
              <?php if ($validUrl): ?>
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs">Website
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
                </a>
              <?php else: ?>
                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs">Belum ada URL</span>
              <?php endif; ?>
              <div class="mt-1 text-[11px] text-gray-500"><?= htmlspecialchars($r['last_checked_at'] ?? '') ?></div>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="mt-3 flex items-center justify-between">
        <?php $prev = max(1, $page-1); $next = min($totalPages, $page+1); $qs = http_build_query(['q'=>$q,'kec'=>$kec,'per'=>$perPage]); ?>
        <a href="?page=<?= $prev ?>&<?= $qs ?>" class="px-3 py-2 rounded-lg border bg-white text-sm <?= $page==1?'pointer-events-none opacity-50':'' ?>">Prev</a>
        <a href="?page=<?= $next ?>&<?= $qs ?>" class="px-3 py-2 rounded-lg border bg-white text-sm <?= $page==$totalPages?'pointer-events-none opacity-50':'' ?>">Next</a>
      </div>
    </div>
  </main>
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t z-20">
    <div class="grid grid-cols-5 text-xs text-gray-600">
      <a href="mobile.php" class="flex flex-col items-center justify-center py-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Beranda</span>
      </a>
      <a href="mobile_desa.php" class="flex flex-col items-center justify-center py-2 text-blue-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        <span>Desa</span>
      </a>
      <a href="mobile_kegiatan.php" class="flex flex-col items-center justify-center py-2">
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
