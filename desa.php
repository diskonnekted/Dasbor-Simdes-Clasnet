<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Menggunakan kolom db_penduduk dari MySQL; tidak lagi membaca CSV

// Ambil data berita untuk perhitungan bintang
$newsContent = '';
if ($nRes = $db->query("SELECT judul, isi FROM berita WHERE published=1")) {
  while ($row = $nRes->fetch_assoc()) {
    $newsContent .= ' ' . strip_tags($row['judul'] . ' ' . $row['isi']);
  }
}
$newsContent = mb_strtolower($newsContent);

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
            // Hitung Bintang
            $desaNorm = mb_strtolower(preg_replace('/\s+/', ' ', preg_replace('/^\s*desa\s+/i', '', $r['nama_desa'])));
            $stars = 0;
            $hasWebsite = !empty($r['alamat_website']);
            $hasDb = isset($r['db_penduduk']) && strtoupper($r['db_penduduk']) === 'SUDAH ADA';
            $hasNews = false;
            if ($desaNorm !== '') {
                if (preg_match('/\b'.preg_quote($desaNorm, '/').'\b/', $newsContent)) {
                    $hasNews = true;
                }
            }
            if ($hasWebsite) {
                $stars = 1;
                if ($hasDb) {
                    $stars = 2;
                    if ($hasNews) {
                        $stars = 3;
                    }
                }
            }
            $starHtml = '';
            for($k=0; $k<$stars; $k++) {
                $starHtml .= '<svg class="w-3 h-3 text-yellow-500 inline-block ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }

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
            <td class="px-4 py-2 text-gray-800">
              <button onclick="openModal(this)"
                data-desa="<?= htmlspecialchars($r['nama_desa']) ?>"
                data-kec="<?= htmlspecialchars($r['nama_kecamatan']) ?>"
                data-website="<?= htmlspecialchars($r['alamat_website']) ?>"
                data-penduduk="<?= htmlspecialchars($jpFmt) ?>"
                data-db="<?= htmlspecialchars($dbpUpper) ?>"
                data-stars="<?= $stars ?>"
                class="text-blue-600 hover:text-blue-800 hover:underline font-medium text-left flex items-center">
                <?= htmlspecialchars($r['nama_desa']) ?>
                <span class="ml-2 flex items-center" title="<?= $stars ?> Bintang"><?= $starHtml ?></span>
              </button>
            </td>
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

<!-- Modal -->
<div id="desaModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white" id="modalTitle">Nama Desa</h3>
                    <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Kecamatan</div>
                            <div class="font-medium text-gray-900" id="modalKec"></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Jumlah Penduduk</div>
                            <div class="font-medium text-gray-900" id="modalPenduduk"></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Status DB</div>
                            <div class="font-medium" id="modalDb"></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Website</div>
                            <div class="truncate" id="modalWeb"></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                            Berita Terkait
                        </h4>
                        <div id="modalNews" class="space-y-3 min-h-[100px]"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function openModal(btn) {
    const desa = btn.dataset.desa;
    const kec = btn.dataset.kec;
    const web = btn.dataset.website;
    const pend = btn.dataset.penduduk;
    const db = btn.dataset.db;
    const stars = parseInt(btn.dataset.stars) || 0;

    let starHtml = '';
    for(let i=0; i<stars; i++) {
        starHtml += '<svg class="w-5 h-5 text-yellow-300 inline-block ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    document.getElementById('modalTitle').innerHTML = desa + '<span class="ml-3 inline-flex items-center" title="' + stars + ' Bintang">' + starHtml + '</span>';
    document.getElementById('modalKec').textContent = kec;
    document.getElementById('modalPenduduk').textContent = pend || '-';
    const dbEl = document.getElementById('modalDb');
    if(db === 'SUDAH ADA') dbEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Sudah Ada</span>';
    else if(db === 'BELUM ADA') dbEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Belum Ada</span>';
    else dbEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Diketahui</span>';
    const webEl = document.getElementById('modalWeb');
    if(web && web.startsWith('http')) webEl.innerHTML = `<a href="${web}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">${web} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg></a>`;
    else webEl.textContent = web || '-';
    const modal = document.getElementById('desaModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    const newsContainer = document.getElementById('modalNews');
    newsContainer.innerHTML = `<div class="flex flex-col items-center justify-center py-8 text-gray-500"><svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-sm">Memuat berita...</span></div>`;
    fetch(`peta.php?related=1&desa=${encodeURIComponent(desa)}&kec=${encodeURIComponent(kec)}&_t=${Date.now()}`)
        .then(res => res.json())
        .then(data => {
            if(data.items && data.items.length > 0) {
                let html = '';
                data.items.forEach(item => {
                    const img = item.gambar ? `<img src="${item.gambar}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0 bg-gray-200">` : `<div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-400"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg></div>`;
                    html += `<div class="flex gap-4 p-3 hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-100">${img}<div class="flex-1 min-w-0"><h5 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug mb-1">${item.judul}</h5><p class="text-xs text-gray-500 line-clamp-2 mb-2">${(item.isi||'').replace(/<[^>]*>/g, '').substring(0, 100)}...</p><div class="text-[10px] text-gray-400 flex items-center gap-2"><span>${new Date(item.dibuat_pada).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</span><span>•</span><span>${item.author || 'Admin'}</span></div></div></div>`;
                });
                newsContainer.innerHTML = html;
            } else {
                newsContainer.innerHTML = `<div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200"><svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg><p class="mt-2 text-sm text-gray-500">Belum ada berita terkait desa ini.</p></div>`;
            }
        })
        .catch(err => { console.error(err); newsContainer.innerHTML = `<div class="text-center py-4 text-red-500 text-sm">Gagal memuat berita.</div>`; });
}
function closeModal() {
    document.getElementById('desaModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(event) { if (event.key === "Escape") closeModal(); });
</script>
</body>
</html>
