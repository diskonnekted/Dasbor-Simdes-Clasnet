<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Menggunakan kolom db_penduduk dari MySQL; tidak lagi membaca CSV

// Daftar kecamatan untuk filter
$kecamatanList = [];
if ($res = $db->query("SELECT DISTINCT nama_kecamatan FROM desa WHERE TRIM(COALESCE(nama_kecamatan,''))<>'' ORDER BY nama_kecamatan ASC")) {
  while ($row = $res->fetch_assoc()) { $kecamatanList[] = $row['nama_kecamatan']; }
}

$perPage = isset($_GET['per']) ? max(1, min(200, (int)$_GET['per'])) : 25;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Filter params
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$kec = isset($_GET['kec']) ? trim($_GET['kec']) : '';
$sid = isset($_GET['sid']) ? trim($_GET['sid']) : '';
$berita = isset($_GET['berita']) ? trim($_GET['berita']) : '';
$dbf = isset($_GET['db']) ? trim($_GET['db']) : '';

$where = [];
$params = [];
$types = '';
if ($q !== '') { $where[] = "nama_desa LIKE ?"; $params[] = '%'.$q.'%'; $types .= 's'; }
if ($kec !== '') { $where[] = "nama_kecamatan = ?"; $params[] = $kec; $types .= 's'; }
if ($sid === 'with') { $where[] = "TRIM(COALESCE(alamat_website,'')) <> ''"; }
elseif ($sid === 'without') { $where[] = "TRIM(COALESCE(alamat_website,'')) = ''"; }
if ($berita === 'ada') { $where[] = "(LOWER(TRIM(COALESCE(berita_desa,''))) IN ('update','tidak update'))"; }
elseif ($berita === 'tidak_ada') { $where[] = "(LOWER(TRIM(COALESCE(berita_desa,''))) IN ('tidak ada',''))"; }
if ($dbf === 'sudah') { $where[] = "UPPER(TRIM(COALESCE(db_penduduk,''))) = 'SUDAH ADA'"; }
elseif ($dbf === 'belum') { $where[] = "(UPPER(TRIM(COALESCE(db_penduduk,''))) = 'BELUM ADA' OR TRIM(COALESCE(db_penduduk,'')) = '')"; }

// Hitung total baris dengan filter
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

// Ambil data dengan filter + paginate
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Desa SID</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
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
      <?php $activeSlug = 'desa'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Daftar Desa dan Keterangan</h1>
          <p class="text-sm mt-1 opacity-90">Tabel ringkas desa, website, cek terakhir, dan data penduduk.</p>
          <p class="text-xs mt-2 opacity-80">Statistik dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90">
            <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
          </svg>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div class="text-sm text-gray-600 whitespace-nowrap flex-shrink-0">Menampilkan <?= count($rows) ?> dari <?= $totalRows ?> desa • Halaman <?= $page ?> dari <?= $totalPages ?></div>
      <form method="get" class="flex flex-wrap items-center gap-2 justify-end">
        <input type="hidden" name="page" value="1">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="text-sm border rounded-lg px-2 py-1 bg-white w-48" placeholder="Cari nama desa...">
        <select name="kec" class="text-sm border rounded-lg px-2 py-1 bg-white min-w-[170px]">
          <option value="">Semua Kecamatan</option>
          <?php foreach ($kecamatanList as $k): ?>
            <option value="<?= htmlspecialchars($k) ?>" <?= ($kec===$k?'selected':'') ?>><?= htmlspecialchars($k) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="sid" class="text-sm border rounded-lg px-2 py-1 bg-white min-w-[170px]">
          <option value="">Website/SID: Semua</option>
          <option value="with" <?= ($sid==='with'?'selected':'') ?>>Punya Website/SID</option>
          <option value="without" <?= ($sid==='without'?'selected':'') ?>>Belum Punya Website/SID</option>
        </select>
        <select name="berita" class="text-sm border rounded-lg px-2 py-1 bg-white min-w-[170px]">
          <option value="">Berita: Semua</option>
          <option value="ada" <?= ($berita==='ada'?'selected':'') ?>>Ada Berita</option>
          <option value="tidak_ada" <?= ($berita==='tidak_ada'?'selected':'') ?>>Tidak Ada Berita</option>
        </select>
        <select name="db" class="text-sm border rounded-lg px-2 py-1 bg-white min-w-[170px]">
          <option value="">DB Penduduk: Semua</option>
          <option value="sudah" <?= ($dbf==='sudah'?'selected':'') ?>>Sudah Ada</option>
          <option value="belum" <?= ($dbf==='belum'?'selected':'') ?>>Belum Ada</option>
        </select>
        <label for="per" class="text-sm text-gray-600 whitespace-nowrap">Per halaman</label>
        <select id="per" name="per" class="text-sm border rounded-lg px-2 py-1 bg-white w-24">
          <option value="25" <?= $perPage==25?'selected':'' ?>>25</option>
          <option value="50" <?= $perPage==50?'selected':'' ?>>50</option>
          <option value="100" <?= $perPage==100?'selected':'' ?>>100</option>
          <option value="200" <?= $perPage==200?'selected':'' ?>>200</option>
        </select>
        <button type="submit" class="text-sm px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50">Terapkan</button>
        <a href="desa.php" class="text-sm px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50">Reset</a>
      </form>
    </div>

    <div class="overflow-x-auto bg-white shadow-lg rounded-xl ring-1 ring-gray-100">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">#</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Kecamatan</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Desa</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Website</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Cek Terakhir</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Jml Penduduk</th>
            <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">DB Penduduk</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = $offset + 1; foreach ($rows as $r):
            $dbpLabel = ($r['db_penduduk'] === null || trim($r['db_penduduk']) === '') ? 'Tidak diketahui' : $r['db_penduduk'];
            $url = $r['alamat_website'];
            $validUrl = ($url && preg_match('/^(http|https):\/\//', $url));
            $link = $validUrl ? '<a class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100" href="'.htmlspecialchars($url).'" target="_blank">'.htmlspecialchars($url).'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg></a>' : htmlspecialchars($url);
            $jp = $r['jumlah_penduduk'];
            $jpFmt = is_numeric($jp) ? number_format((int)$jp, 0, ',', '.') : '';
            $dbpUpper = strtoupper(trim($dbpLabel));
            if ($dbpUpper === 'SUDAH ADA') {
              $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Sudah Ada</span>';
            } elseif ($dbpUpper === 'BELUM ADA') {
              $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Belum Ada</span>';
            } else {
              $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>';
            }
          ?>
          <tr class="border-t odd:bg-gray-50 hover:bg-blue-50/30">
            <td class="px-4 py-2 text-gray-500"><?= $i++ ?></td>
            <td class="px-4 py-2 font-medium text-gray-900"><?= htmlspecialchars($r['nama_kecamatan']) ?></td>
            <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($r['nama_desa']) ?></td>
            <td class="px-4 py-2 whitespace-nowrap"><?= $link ?></td>
            <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($r['last_checked_at'] ?? '') ?></td>
            <td class="px-4 py-2 font-semibold text-gray-900"><?= htmlspecialchars($jpFmt) ?></td>
            <td class="px-4 py-2"><?= $dbBadge ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-600">Navigasi halaman</div>
      <div class="flex items-center gap-1">
        <?php
          $per = $perPage;
          $prev = max(1, $page-1);
          $next = min($totalPages, $page+1);
          $qs = http_build_query([
            'per' => $per,
            'q' => $q,
            'kec' => $kec,
            'sid' => $sid,
            'berita' => $berita,
            'db' => $dbf,
          ]);
        ?>
        <a href="?page=<?= $prev ?>&<?= $qs ?>" class="px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50 <?= $page==1? 'pointer-events-none opacity-50':'' ?>">Prev</a>
        <?php
          $start = max(1, $page-2);
          $end = min($totalPages, $page+2);
          for ($p=$start; $p<=$end; $p++):
        ?>
          <a href="?page=<?= $p ?>&<?= $qs ?>" class="px-3 py-1 rounded-lg border bg-white shadow-sm <?= $p==$page? 'bg-blue-600 text-white border-blue-600 shadow':'' ?> hover:bg-gray-50"><?= $p ?></a>
        <?php endfor; ?>
        <a href="?page=<?= $next ?>&<?= $qs ?>" class="px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50 <?= $page==$totalPages? 'pointer-events-none opacity-50':'' ?>">Next</a>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
