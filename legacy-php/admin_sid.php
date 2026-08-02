<?php
require_once __DIR__ . '/admin_auth.php';
admin_require();
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Pastikan kolom baru tersedia pada tabel desa (sosialisasi, berita_desa, developer)
function ensureColumn($db, $table, $column, $definition) {
  $column = $db->real_escape_string($column);
  $table = $db->real_escape_string($table);
  $sql = "SELECT COUNT(*) AS c FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='$table' AND column_name='$column'";
  if ($res = $db->query($sql)) {
    $row = $res->fetch_assoc();
    if ((int)$row['c'] === 0) { $db->query("ALTER TABLE `$table` ADD COLUMN $definition"); }
    $res->close();
  }
}
ensureColumn($db, 'desa', 'sosialisasi', "`sosialisasi` VARCHAR(20) NULL");
ensureColumn($db, 'desa', 'berita_desa', "`berita_desa` VARCHAR(20) NULL");
ensureColumn($db, 'desa', 'developer', "`developer` VARCHAR(50) NULL");

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = '';

// Endpoint AJAX untuk dropdown dan auto-load
if (isset($_GET['ajax'])) {
  header('Content-Type: application/json');
  $ajax = $_GET['ajax'];
  if ($ajax === 'desa_list') {
    $kec = trim($_GET['kecamatan'] ?? '');
    $out = [];
    $stmt = $db->prepare("SELECT nama_desa FROM desa WHERE nama_kecamatan=? ORDER BY nama_desa ASC");
    $stmt->bind_param('s', $kec);
    if ($stmt->execute()) {
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()) { $out[] = $row['nama_desa']; }
    }
    $stmt->close();
    echo json_encode(['ok' => true, 'desa' => $out]);
    exit;
  } elseif ($ajax === 'desa_get') {
    $kec = trim($_GET['kecamatan'] ?? '');
    $desa = trim($_GET['desa'] ?? '');
    $stmt = $db->prepare("SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer FROM desa WHERE nama_kecamatan=? AND nama_desa=? LIMIT 1");
    $stmt->bind_param('ss', $kec, $desa);
    $data = null;
    if ($stmt->execute()) {
      $res = $stmt->get_result();
      if ($row = $res->fetch_assoc()) { $data = $row; }
    }
    $stmt->close();
    echo json_encode(['ok' => (bool)$data, 'data' => $data]);
    exit;
  }
}

// Proses form (create/update/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $op = $_POST['op'] ?? '';
  if ($op === 'save') {
    $nama_kecamatan = trim($_POST['nama_kecamatan'] ?? '');
    $nama_desa = trim($_POST['nama_desa'] ?? '');
    $alamat_website = trim($_POST['alamat_website'] ?? '');
    $db_penduduk = trim($_POST['db_penduduk'] ?? '');
    $sosialisasi = strtolower(trim($_POST['sosialisasi'] ?? ''));
    $berita_desa = strtolower(trim($_POST['berita_desa'] ?? ''));
    $developer = strtolower(trim($_POST['developer'] ?? ''));
    $jumlah_penduduk_raw = $_POST['jumlah_penduduk'] ?? '';
    $jumlah_penduduk = ($jumlah_penduduk_raw === '' ? null : (int)$jumlah_penduduk_raw);
    $last_checked_at = trim($_POST['last_checked_at'] ?? '');

    if ($nama_kecamatan === '') { $errors[] = 'Nama kecamatan wajib diisi.'; }
    if ($nama_desa === '') { $errors[] = 'Nama desa wajib diisi.'; }
    if ($jumlah_penduduk !== null && $jumlah_penduduk < 0) { $errors[] = 'Jumlah penduduk tidak boleh negatif.'; }
    if ($db_penduduk !== '' && !in_array(strtoupper($db_penduduk), ['SUDAH ADA','BELUM ADA','TIDAK DIKETAHUI'])) {
      $errors[] = 'Nilai db_penduduk harus "Sudah Ada", "Belum Ada", atau "Tidak Diketahui".';
    }
    if ($sosialisasi !== '' && !in_array($sosialisasi, ['sudah','belum'])) { $errors[] = 'Nilai sosialisasi harus "sudah" atau "belum".'; }
    if ($berita_desa !== '' && !in_array($berita_desa, ['update','tidak update','tidak ada'])) { $errors[] = 'Nilai berita desa harus "update" / "tidak update" / "tidak ada".'; }
    if ($developer !== '' && !in_array($developer, ['clasnet','digitaldesa','opendesa','parso rtik','supri rtik','sraya','lainnya'])) {
      $errors[] = 'Nilai developer harus salah satu dari: Clasnet, Digitaldesa, OpenDesa, Parso RTIK, Supri RTIK, Sraya, atau Lainnya.';
    }

    if (empty($errors)) {
      // Siapkan variabel untuk bind_param (harus berupa variabel, bukan ekspresi)
      $lastCheckedParam = ($last_checked_at === '') ? null : $last_checked_at;
      $jumlahPendudukParam = ($jumlah_penduduk === null) ? null : (int)$jumlah_penduduk;

      // Cek duplikat (nama_kecamatan, nama_desa)
      $dupCount = 0;
      if ($action === 'edit' && $id > 0) {
        if ($stmtDup = $db->prepare("SELECT COUNT(*) AS c FROM desa WHERE nama_kecamatan=? AND nama_desa=? AND id<>?")) {
          $stmtDup->bind_param('ssi', $nama_kecamatan, $nama_desa, $id);
          if ($stmtDup->execute()) { $resDup = $stmtDup->get_result(); $dupCount = (int)$resDup->fetch_assoc()['c']; }
          $stmtDup->close();
        }
      } else {
        if ($stmtDup = $db->prepare("SELECT COUNT(*) AS c FROM desa WHERE nama_kecamatan=? AND nama_desa=?")) {
          $stmtDup->bind_param('ss', $nama_kecamatan, $nama_desa);
          if ($stmtDup->execute()) { $resDup = $stmtDup->get_result(); $dupCount = (int)$resDup->fetch_assoc()['c']; }
          $stmtDup->close();
        }
      }

      if ($dupCount > 0) {
        $errors[] = 'Duplikat terdeteksi: kombinasi kecamatan "'.htmlspecialchars($nama_kecamatan).'" dan desa "'.htmlspecialchars($nama_desa).'" sudah ada.';
      }

      if (empty($errors) && $action === 'edit' && $id > 0) {
        $stmt = $db->prepare("UPDATE desa SET nama_kecamatan=?, nama_desa=?, alamat_website=?, last_checked_at=?, jumlah_penduduk=?, db_penduduk=?, sosialisasi=?, berita_desa=?, developer=? WHERE id=?");
        $stmt->bind_param('ssssissssi', $nama_kecamatan, $nama_desa, $alamat_website, $lastCheckedParam, $jumlahPendudukParam, $db_penduduk, $sosialisasi, $berita_desa, $developer, $id);
        try {
          if ($stmt->execute()) { $success = 'Data desa berhasil diperbarui.'; $action = ''; $id = 0; }
          else { $errors[] = 'Gagal memperbarui data: ' . $stmt->error; }
        } catch (mysqli_sql_exception $ex) {
          $errors[] = 'Gagal memperbarui data (duplikat?): ' . $ex->getMessage();
        }
        $stmt->close();
      } elseif (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO desa (nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssissss', $nama_kecamatan, $nama_desa, $alamat_website, $lastCheckedParam, $jumlahPendudukParam, $db_penduduk, $sosialisasi, $berita_desa, $developer);
        try {
          if ($stmt->execute()) { $success = 'Data desa baru berhasil ditambahkan.'; $action = ''; }
          else { $errors[] = 'Gagal menambah data: ' . $stmt->error; }
        } catch (mysqli_sql_exception $ex) {
          $errors[] = 'Gagal menambah data (duplikat?): ' . $ex->getMessage();
        }
        $stmt->close();
      }
    }
  } elseif ($op === 'delete') {
    $delId = (int)($_POST['id'] ?? 0);
    if ($delId > 0) {
      $stmt = $db->prepare("DELETE FROM desa WHERE id=?");
      $stmt->bind_param('i', $delId);
      if ($stmt->execute()) { $success = 'Data desa berhasil dihapus.'; }
      else { $errors[] = 'Gagal menghapus data: ' . $stmt->error; }
      $stmt->close();
      $action = '';
      $id = 0;
    }
  }
}

// Data untuk edit jika diperlukan
$edit = null;
if ($action === 'edit' && $id > 0) {
  if ($res = $db->query("SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer FROM desa WHERE id=" . $id)) {
    $edit = $res->fetch_assoc();
  }
}

// Daftar kecamatan untuk dropdown
$kecamatanList = [];
if ($res = $db->query("SELECT DISTINCT nama_kecamatan FROM desa WHERE TRIM(COALESCE(nama_kecamatan,''))<>'' ORDER BY nama_kecamatan ASC")) {
  while ($row = $res->fetch_assoc()) { $kecamatanList[] = $row['nama_kecamatan']; }
}

// Jika diminta laporan duplikat, siapkan datanya
$dupPerKec = [];
$dupGlobal = [];
if ($action === 'dup') {
  if ($res = $db->query("SELECT nama_kecamatan, nama_desa, COUNT(*) AS jumlah, GROUP_CONCAT(id ORDER BY id SEPARATOR ', ') AS ids FROM desa GROUP BY nama_kecamatan, nama_desa HAVING COUNT(*)>1 ORDER BY nama_kecamatan ASC, nama_desa ASC")) {
    while ($r = $res->fetch_assoc()) { $dupPerKec[] = $r; }
  }
  if ($res = $db->query("SELECT nama_desa, COUNT(*) AS jumlah, GROUP_CONCAT(DISTINCT nama_kecamatan ORDER BY nama_kecamatan SEPARATOR ', ') AS kecamatan, GROUP_CONCAT(id ORDER BY id SEPARATOR ', ') AS ids FROM desa GROUP BY nama_desa HAVING COUNT(*)>1 ORDER BY jumlah DESC, nama_desa ASC")) {
    while ($r = $res->fetch_assoc()) { $dupGlobal[] = $r; }
  }
}

// Cross-check dengan daftar desa.csv
$csvInvalidInDb = [];
$csvMissingInDb = [];
if ($action === 'checkcsv') {
  $csvPath = __DIR__ . '/daftar desa.csv';
  $validPairs = [];
  $headers = null;
  if (file_exists($csvPath)) {
    if (($fh = fopen($csvPath, 'r')) !== false) {
      $rowIndex = 0;
      while (($cols = fgetcsv($fh)) !== false) {
        // Lewati baris kosong
        if (count($cols) === 0) { continue; }
        // Normalisasi BOM pada kolom pertama
        if ($rowIndex === 0) {
          $cols[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$cols[0]);
        }
        if ($rowIndex === 0) {
          // Deteksi header (kecamatan/desa) bila ada
          $h0 = strtolower(trim((string)($cols[0] ?? '')));
          $h1 = strtolower(trim((string)($cols[1] ?? '')));
          if (strpos($h0, 'kecamatan') !== false || strpos($h1, 'desa') !== false) {
            $headers = [$h0, $h1];
            $rowIndex++;
            continue; // jangan masukkan header sebagai data
          }
        }
        $kec = trim((string)($cols[0] ?? ''));
        $desa = trim((string)($cols[1] ?? ''));
        if ($kec === '' || $desa === '') { $rowIndex++; continue; }
        $key = strtolower($kec.'|'.$desa);
        $validPairs[$key] = ['kecamatan' => $kec, 'desa' => $desa];
        $rowIndex++;
      }
      fclose($fh);
    }

    // Ambil semua pasangan dari DB
    $dbPairs = [];
    if ($res = $db->query("SELECT id, nama_kecamatan, nama_desa FROM desa")) {
      while ($r = $res->fetch_assoc()) {
        $key = strtolower(trim((string)$r['nama_kecamatan']).'|'.trim((string)$r['nama_desa']));
        if (!isset($dbPairs[$key])) { $dbPairs[$key] = []; }
        $dbPairs[$key][] = (int)$r['id'];
      }
    }

    // DB yang tidak ada di CSV (invalid)
    foreach ($dbPairs as $key => $ids) {
      if (!isset($validPairs[$key])) {
        [$kec,$desa] = explode('|', $key, 2);
        $csvInvalidInDb[] = [
          'kecamatan' => $kec,
          'desa' => $desa,
          'ids' => implode(', ', $ids),
        ];
      }
    }

    // CSV yang tidak ada di DB (missing)
    foreach ($validPairs as $key => $pair) {
      if (!isset($dbPairs[$key])) {
        $csvMissingInDb[] = [
          'kecamatan' => $pair['kecamatan'],
          'desa' => $pair['desa'],
        ];
      }
    }
  }
}

// Cross-check jumlah desa per kecamatan sesuai angka acuan
$countsDb = [];
$expectedCounts = [
  'banjarmangu' => 17,
  'banjarnegara' => 4,
  'batur' => 8,
  'bawang' => 18,
  'kalibening' => 16,
  'karangkobar' => 13,
  'madukara' => 18,
  'mandiraja' => 16,
  'pagedongan' => 9,
  'pagentan' => 16,
  'pandanarum' => 8,
  'pejawaran' => 17,
  'punggelan' => 17,
  'purwanegara' => 13,
  'purworejo klampok' => 8,
  'rakit' => 11,
  'sigaluh' => 14,
  'susukan' => 15,
  'wanadadi' => 11,
  'wanayasa' => 17,
];
$mismatchCounts = [];
$extraDbCounts = [];
$missingExpectedCounts = [];
if ($action === 'checkcount') {
  if ($res = $db->query("SELECT nama_kecamatan, COUNT(*) AS total FROM desa GROUP BY nama_kecamatan ORDER BY nama_kecamatan ASC")) {
    while ($r = $res->fetch_assoc()) {
      $key = strtolower(trim((string)$r['nama_kecamatan']));
      $countsDb[$key] = (int)$r['total'];
    }
  }
  // Bandingkan
  foreach ($countsDb as $kec => $dbTotal) {
    if (array_key_exists($kec, $expectedCounts)) {
      $exp = (int)$expectedCounts[$kec];
      if ($exp !== $dbTotal) {
        $mismatchCounts[] = [
          'kecamatan' => $kec,
          'db' => $dbTotal,
          'expected' => $exp,
          'selisih' => $dbTotal - $exp,
        ];
      }
    } else {
      $extraDbCounts[] = [
        'kecamatan' => $kec,
        'db' => $dbTotal,
      ];
    }
  }
  foreach ($expectedCounts as $kec => $exp) {
    if (!array_key_exists($kec, $countsDb)) {
      $missingExpectedCounts[] = [
        'kecamatan' => $kec,
        'expected' => (int)$exp,
      ];
    }
  }
}

// Cross-check: nama kelurahan yang tidak boleh ada di daftar desa
$kelurahanList = [
  'argasoka','karangtengah','krandegan','kutabanjarnegara','parakancanggah','semampir','semarang','sokanandi','wangon'
];
$kelurahanRows = [];
if ($action === 'checkkelurahan') {
  $placeholders = implode(',', array_fill(0, count($kelurahanList), '?'));
  $typesKel = str_repeat('s', count($kelurahanList));
  $sqlKel = "SELECT id, nama_kecamatan, nama_desa FROM desa WHERE LOWER(TRIM(nama_desa)) IN ($placeholders) ORDER BY nama_kecamatan ASC, nama_desa ASC";
  if ($stmtKel = $db->prepare($sqlKel)) {
    $stmtKel->bind_param($typesKel, ...$kelurahanList);
    if ($stmtKel->execute()) {
      $resKel = $stmtKel->get_result();
      while ($row = $resKel->fetch_assoc()) { $kelurahanRows[] = $row; }
    }
    $stmtKel->close();
  }
}

// Ambil daftar desa untuk tabel dengan filter
$list = [];
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$kec = isset($_GET['kec']) ? trim($_GET['kec']) : '';
$sid = isset($_GET['sid']) ? trim($_GET['sid']) : ''; // with | without
$berita = isset($_GET['berita']) ? trim($_GET['berita']) : ''; // ada | tidak_ada
$dbf = isset($_GET['db']) ? trim($_GET['db']) : ''; // sudah | belum

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

$sql = "SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer FROM desa";
if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= " ORDER BY nama_kecamatan ASC, nama_desa ASC";

$stmt = $db->prepare($sql);
if ($stmt) {
  if (!empty($params)) { $stmt->bind_param($types, ...$params); }
  if ($stmt->execute()) {
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) { $list[] = $r; }
  }
  $stmt->close();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Data Desa SID</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Admin Data Desa SID</h1>
          <p class="text-sm mt-1 opacity-90">Tambah, edit, dan hapus data desa pada basis data SID.</p>
          <p class="text-xs mt-2 opacity-80">Dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="flex items-center gap-2">
          <a href="desa.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white/10 hover:bg-white/20 text-white text-sm">
            Lihat Daftar Desa
          </a>
          <a href="index.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white/10 hover:bg-white/20 text-white text-sm">
            Kembali ke Dashboard
          </a>
        </div>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-lg p-4 mb-4">
        <div class="font-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="list-disc ml-5 text-sm">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($action === 'checkcount'): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-lg font-semibold text-gray-900">Cross Check Jumlah Desa per Kecamatan</div>
          <a href="admin_sid.php" class="text-sm px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">Kembali</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div>
            <div class="text-sm font-medium text-gray-800 mb-2">Perbedaan jumlah (DB vs acuan)</div>
            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">DB</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Acuan</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Selisih</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($mismatchCounts)): ?>
                    <tr><td colspan="5" class="px-3 py-3 text-gray-600">Semua jumlah sesuai acuan.</td></tr>
                  <?php else: $i=1; foreach ($mismatchCounts as $d): ?>
                    <tr class="border-t odd:bg-gray-50">
                      <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                      <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars(ucwords($d['kecamatan'])) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['db'] ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['expected'] ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['selisih'] ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-800 mb-2">Ada di DB tapi tidak ada di acuan</div>
            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">DB</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($extraDbCounts)): ?>
                    <tr><td colspan="3" class="px-3 py-3 text-gray-600">Tidak ada kecamatan di DB yang di luar acuan.</td></tr>
                  <?php else: $i=1; foreach ($extraDbCounts as $d): ?>
                    <tr class="border-t odd:bg-gray-50">
                      <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                      <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars(ucwords($d['kecamatan'])) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['db'] ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="mt-6">
          <div class="text-sm font-medium text-gray-800 mb-2">Ada di acuan tapi belum ada di DB</div>
          <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                  <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Acuan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($missingExpectedCounts)): ?>
                  <tr><td colspan="3" class="px-3 py-3 text-gray-600">Semua kecamatan acuan sudah ada di DB.</td></tr>
                <?php else: $i=1; foreach ($missingExpectedCounts as $d): ?>
                  <tr class="border-t odd:bg-gray-50">
                    <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                    <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars(ucwords($d['kecamatan'])) ?></td>
                    <td class="px-3 py-2 text-gray-800"><?= (int)$d['expected'] ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($action === 'checkkelurahan'): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-lg font-semibold text-gray-900">Cross Check Kelurahan (tidak seharusnya ada di daftar desa)</div>
          <a href="admin_sid.php" class="text-sm px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">Kembali</a>
        </div>
        <p class="text-sm text-gray-600 mb-4">Menandai entri dengan nama: Argasoka, Karangtengah, Krandegan, Kutabanjarnegara, Parakancanggah, Semampir, Semarang, Sokanandi, Wangon.</p>
        <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Nama</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">ID</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($kelurahanRows)): ?>
                <tr><td colspan="5" class="px-3 py-3 text-gray-600">Tidak ada entri kelurahan yang terdeteksi di Database.</td></tr>
              <?php else: $i=1; foreach ($kelurahanRows as $r): ?>
                <tr class="border-t odd:bg-gray-50">
                  <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                  <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars($r['nama_kecamatan']) ?></td>
                  <td class="px-3 py-2 text-gray-800"><?= htmlspecialchars($r['nama_desa']) ?></td>
                  <td class="px-3 py-2 text-gray-800"><?= (int)$r['id'] ?></td>
                  <td class="px-3 py-2">
                    <form method="post" onsubmit="return confirm('Hapus entri ini dari daftar desa?');" class="inline-flex items-center gap-2">
                      <input type="hidden" name="op" value="delete">
                      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                      <button type="submit" class="px-3 py-1 rounded-lg border bg-white hover:bg-rose-50 text-rose-700">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 mb-4">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <?php if ($action === 'create' || $action === 'edit'): ?>
      <?php
        $v = [
          'nama_kecamatan' => $edit['nama_kecamatan'] ?? '',
          'nama_desa' => $edit['nama_desa'] ?? '',
          'alamat_website' => $edit['alamat_website'] ?? '',
          'last_checked_at' => $edit['last_checked_at'] ?? '',
          'jumlah_penduduk' => $edit['jumlah_penduduk'] ?? '',
          'db_penduduk' => $edit['db_penduduk'] ?? '',
          'sosialisasi' => $edit['sosialisasi'] ?? '',
          'berita_desa' => $edit['berita_desa'] ?? '',
          'developer' => $edit['developer'] ?? '',
        ];
      ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-lg font-semibold text-gray-900"><?= $action==='edit' ? 'Edit Data Desa' : 'Tambah Data Desa' ?></div>
          <a href="admin_sid.php" class="text-sm px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">Batal</a>
        </div>
        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="hidden" name="op" value="save">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Nama Kecamatan</label>
            <select id="nama_kecamatan" name="nama_kecamatan" class="w-full border rounded-lg px-3 py-2" required>
              <option value="">Pilih Kecamatan</option>
              <?php foreach ($kecamatanList as $kec): $sel = ($kec === ($v['nama_kecamatan'] ?? '')) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($kec) ?>" <?= $sel ?>><?= htmlspecialchars($kec) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Nama Desa</label>
            <select id="nama_desa" name="nama_desa" class="w-full border rounded-lg px-3 py-2" required>
              <?php if (!empty($v['nama_desa'])): ?>
                <option value="<?= htmlspecialchars($v['nama_desa']) ?>" selected><?= htmlspecialchars($v['nama_desa']) ?></option>
              <?php else: ?>
                <option value="">Pilih Kecamatan dulu</option>
              <?php endif; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Alamat Website</label>
            <input type="text" name="alamat_website" value="<?= htmlspecialchars($v['alamat_website']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="https://desa.example.go.id">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Status DB Penduduk</label>
            <select name="db_penduduk" class="w-full border rounded-lg px-3 py-2">
              <?php $opts = ['' => 'Tidak diketahui', 'Sudah Ada' => 'Sudah Ada', 'Belum Ada' => 'Belum Ada']; ?>
              <?php foreach ($opts as $k => $label): $sel = (strtoupper($v['db_penduduk']) === strtoupper($k)) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $sel ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Jumlah Penduduk</label>
            <input type="number" min="0" name="jumlah_penduduk" value="<?= htmlspecialchars((string)$v['jumlah_penduduk']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="contoh: 3456">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Last Checked At (YYYY-MM-DD HH:MM:SS)</label>
            <input type="text" name="last_checked_at" value="<?= htmlspecialchars($v['last_checked_at']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="2025-01-01 12:00:00">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Sosialisasi</label>
            <select name="sosialisasi" class="w-full border rounded-lg px-3 py-2">
              <?php $optsSos = ['' => 'Tidak diketahui', 'sudah' => 'Sudah', 'belum' => 'Belum']; ?>
              <?php foreach ($optsSos as $k => $label): $sel = (strtolower($v['sosialisasi']) === strtolower($k)) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $sel ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Berita Desa</label>
            <select name="berita_desa" class="w-full border rounded-lg px-3 py-2">
              <?php $optsBerita = ['' => 'Tidak diketahui', 'update' => 'Update', 'tidak update' => 'Tidak Update', 'tidak ada' => 'Tidak Ada']; ?>
              <?php foreach ($optsBerita as $k => $label): $sel = (strtolower($v['berita_desa']) === strtolower($k)) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $sel ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Developer</label>
            <select name="developer" class="w-full border rounded-lg px-3 py-2">
              <?php $optsDev = [
                '' => 'Tidak diketahui',
                'clasnet' => 'Clasnet',
                'digitaldesa' => 'Digitaldesa',
                'opendesa' => 'OpenDesa',
                'parso rtik' => 'Parso RTIK',
                'supri rtik' => 'Supri RTIK',
                'sraya' => 'Sraya',
                'lainnya' => 'Lainnya',
              ]; ?>
              <?php foreach ($optsDev as $k => $label): $sel = (strtolower($v['developer']) === strtolower($k)) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $sel ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="md:col-span-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Simpan</button>
          </div>
        </form>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          const kecEl = document.getElementById('nama_kecamatan');
          const desaEl = document.getElementById('nama_desa');
          const fields = {
            alamat_website: document.querySelector('input[name="alamat_website"]'),
            last_checked_at: document.querySelector('input[name="last_checked_at"]'),
            jumlah_penduduk: document.querySelector('input[name="jumlah_penduduk"]'),
            db_penduduk: document.querySelector('select[name="db_penduduk"]'),
            sosialisasi: document.querySelector('select[name="sosialisasi"]'),
            berita_desa: document.querySelector('select[name="berita_desa"]'),
            developer: document.querySelector('select[name="developer"]'),
          };

          async function loadDesaList(kec) {
            desaEl.innerHTML = '<option value="">Memuat daftar desa...</option>';
            try {
              const res = await fetch('admin_sid.php?ajax=desa_list&kecamatan=' + encodeURIComponent(kec));
              const j = await res.json();
              desaEl.innerHTML = '';
              desaEl.appendChild(new Option('Pilih Desa', ''));
              (j.desa || []).forEach(nama => desaEl.appendChild(new Option(nama, nama)));
            } catch (e) {
              desaEl.innerHTML = '<option value="">Gagal memuat desa</option>';
            }
          }

          async function loadDesaData(kec, desa) {
            if (!kec || !desa) return;
            try {
              const res = await fetch('admin_sid.php?ajax=desa_get&kecamatan=' + encodeURIComponent(kec) + '&desa=' + encodeURIComponent(desa));
              const j = await res.json();
              if (j && j.ok && j.data) {
                const d = j.data;
                fields.alamat_website.value = d.alamat_website || '';
                fields.last_checked_at.value = d.last_checked_at || '';
                fields.jumlah_penduduk.value = (d.jumlah_penduduk ?? '') === '' ? '' : d.jumlah_penduduk;
                const dbv = (d.db_penduduk || '').toString();
                // Cocokkan dengan opsi yang ada ('', 'Sudah Ada', 'Belum Ada')
                if (dbv.toUpperCase() === 'SUDAH ADA') fields.db_penduduk.value = 'Sudah Ada';
                else if (dbv.toUpperCase() === 'BELUM ADA') fields.db_penduduk.value = 'Belum Ada';
                else fields.db_penduduk.value = '';

                const sosv = (d.sosialisasi || '').toLowerCase();
                fields.sosialisasi.value = (sosv === 'sudah' || sosv === 'belum') ? sosv : '';

                const ber = (d.berita_desa || '').toLowerCase();
                fields.berita_desa.value = (ber === 'update' || ber === 'tidak update' || ber === 'tidak ada') ? ber : '';

                const dev = (d.developer || '').toLowerCase();
                const allowedDev = ['clasnet','digitaldesa','opendesa','parso rtik','supri rtik','sraya','lainnya'];
                fields.developer.value = allowedDev.includes(dev) ? dev : '';
              }
            } catch (e) {
              // silent fail
            }
          }

          kecEl?.addEventListener('change', (ev) => {
            const kec = ev.target.value;
            if (kec) { loadDesaList(kec); }
          });
          desaEl?.addEventListener('change', (ev) => {
            const desa = ev.target.value;
            const kec = kecEl?.value;
            if (desa && kec) { loadDesaData(kec, desa); }
          });

          // Jika sudah preselect (mode edit), isi ulang daftar desa agar lengkap
          if (kecEl && kecEl.value && desaEl && desaEl.value) {
            loadDesaList(kecEl.value).then(() => {
              // pastikan nilai desa terpilih tetap
              const current = desaEl.value;
              if (current) desaEl.value = current;
            });
          }
        });
      </script>
    <?php endif; ?>

    <?php if ($action === 'dup'): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-lg font-semibold text-gray-900">Laporan Duplikat Nama Desa</div>
          <a href="admin_sid.php" class="text-sm px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">Kembali</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div>
            <div class="text-sm font-medium text-gray-800 mb-2">Duplikat dalam kecamatan yang sama</div>
            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Desa</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Jumlah</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">IDs</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($dupPerKec)): ?>
                    <tr><td colspan="5" class="px-3 py-3 text-gray-600">Tidak ada duplikat dalam kecamatan yang sama.</td></tr>
                  <?php else: $i=1; foreach ($dupPerKec as $d): ?>
                    <tr class="border-t odd:bg-gray-50">
                      <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                      <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars($d['nama_kecamatan']) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= htmlspecialchars($d['nama_desa']) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['jumlah'] ?></td>
                      <td class="px-3 py-2 text-gray-600">
                        <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-xs">
                          <?= htmlspecialchars($d['ids']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-800 mb-2">Duplikat lintas kecamatan</div>
            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Desa</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Jumlah</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">IDs</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($dupGlobal)): ?>
                    <tr><td colspan="5" class="px-3 py-3 text-gray-600">Tidak ada duplikat lintas kecamatan.</td></tr>
                  <?php else: $i=1; foreach ($dupGlobal as $d): ?>
                    <tr class="border-t odd:bg-gray-50">
                      <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                      <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars($d['nama_desa']) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= htmlspecialchars($d['kecamatan']) ?></td>
                      <td class="px-3 py-2 text-gray-800"><?= (int)$d['jumlah'] ?></td>
                      <td class="px-3 py-2 text-gray-600">
                        <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-xs">
                          <?= htmlspecialchars($d['ids']) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($action === 'checkcsv'): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="text-lg font-semibold text-gray-900">Cross Check dengan daftar desa.csv</div>
          <a href="admin_sid.php" class="text-sm px-3 py-1 rounded-lg border bg-white hover:bg-gray-50">Kembali</a>
        </div>
        <?php if (!file_exists(__DIR__ . '/daftar desa.csv')): ?>
          <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-4">File <span class="font-mono">daftar desa.csv</span> tidak ditemukan di root proyek.</div>
        <?php else: ?>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <div class="text-sm font-medium text-gray-800 mb-2">Ada di Database tetapi tidak ada di CSV (perlu ditinjau)</div>
              <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
                <table class="min-w-full text-sm">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Desa</th>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">IDs</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($csvInvalidInDb)): ?>
                      <tr><td colspan="4" class="px-3 py-3 text-gray-600">Tidak ada perbedaan. Seluruh data DB cocok dengan CSV.</td></tr>
                    <?php else: $i=1; foreach ($csvInvalidInDb as $d): ?>
                      <tr class="border-t odd:bg-gray-50">
                        <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                        <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars($d['kecamatan']) ?></td>
                        <td class="px-3 py-2 text-gray-800"><?= htmlspecialchars($d['desa']) ?></td>
                        <td class="px-3 py-2 text-gray-600">
                          <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-xs"><?= htmlspecialchars($d['ids']) ?></span>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-800 mb-2">Ada di CSV tetapi belum ada di Database (perlu ditambah)</div>
              <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100">
                <table class="min-w-full text-sm">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Kecamatan</th>
                      <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Desa</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($csvMissingInDb)): ?>
                      <tr><td colspan="3" class="px-3 py-3 text-gray-600">Semua data CSV sudah ada di Database.</td></tr>
                    <?php else: $i=1; foreach ($csvMissingInDb as $d): ?>
                      <tr class="border-t odd:bg-gray-50">
                        <td class="px-3 py-2 text-gray-500"><?= $i++ ?></td>
                        <td class="px-3 py-2 font-medium text-gray-900"><?= htmlspecialchars($d['kecamatan']) ?></td>
                        <td class="px-3 py-2 text-gray-800"><?= htmlspecialchars($d['desa']) ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($action !== 'create' && $action !== 'edit' && $action !== 'dup'): ?>
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
          <div class="text-sm text-gray-600 whitespace-nowrap flex-shrink-0">Total desa: <?= count($list) ?></div>
          <form method="get" action="admin_sid.php" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" class="border rounded-lg px-3 py-1.5 text-sm w-48" placeholder="Cari nama desa...">
            <select name="kec" class="text-sm border rounded-lg px-2 py-1 bg-white">
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
            <button type="submit" class="text-sm px-3 py-1.5 rounded-lg bg-gray-800 text-white hover:bg-gray-900">Filter</button>
            <a href="admin_sid.php" class="text-sm px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Reset</a>
          </form>
        </div>
        <div class="flex items-center gap-2">
          <a href="admin_sid.php?action=dup" class="text-sm px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Cek Duplikat</a>
          <a href="admin_sid.php?action=checkcsv" class="text-sm px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Cross Check CSV</a>
          <a href="admin_sid.php?action=checkcount" class="text-sm px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Cross Check Jumlah</a>
          <a href="admin_sid.php?action=checkkelurahan" class="text-sm px-3 py-2 rounded-lg border bg-white hover:bg-gray-50">Cross Check Kelurahan</a>
          <a href="admin_sid.php?action=create" class="text-sm px-3 py-2 rounded-lg bg-blue-600 text-white shadow hover:bg-blue-700">Tambah Desa</a>
        </div>
      </div>
      <div class="overflow-x-auto bg-white rounded-xl shadow-lg ring-1 ring-gray-100">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">#</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Kecamatan</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Desa</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Website</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Cek Terakhir</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Jumlah Penduduk</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">DB Penduduk</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Sosialisasi</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Berita Desa</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Developer</th>
              <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($list as $r): ?>
              <?php
                $link = '-';
                if (trim((string)$r['alamat_website']) !== '') {
                  $url = htmlspecialchars($r['alamat_website']);
                  $link = '<a href="'.$url.'" target="_blank" class="text-blue-600 hover:underline">'.htmlspecialchars($r['alamat_website']).'</a>';
                }
                $jpFmt = ($r['jumlah_penduduk'] === null || $r['jumlah_penduduk'] === '') ? '-' : number_format((int)$r['jumlah_penduduk'], 0, ',', '.');
                $dbpUpper = strtoupper(trim((string)$r['db_penduduk']));
                if ($dbpUpper === 'SUDAH ADA') {
                  $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Sudah Ada</span>';
                } elseif ($dbpUpper === 'BELUM ADA') {
                  $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Belum Ada</span>';
                } else {
                  $dbBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>';
                }
                $sos = strtolower(trim((string)$r['sosialisasi'] ?? ''));
                if ($sos === 'sudah') { $sosBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Sudah</span>'; }
                elseif ($sos === 'belum') { $sosBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">Belum</span>'; }
                else { $sosBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>'; }

                $ber = strtolower(trim((string)$r['berita_desa'] ?? ''));
                if ($ber === 'update') { $berBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Update</span>'; }
                elseif ($ber === 'tidak update') { $berBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Tidak Update</span>'; }
                elseif ($ber === 'tidak ada') { $berBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700">Tidak Ada</span>'; }
                else { $berBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>'; }

                $dev = strtolower(trim((string)$r['developer'] ?? ''));
                if ($dev === 'clasnet') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700">Clasnet</span>'; }
                elseif ($dev === 'digitaldesa') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Digitaldesa</span>'; }
                elseif ($dev === 'opendesa') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">OpenDesa</span>'; }
                elseif ($dev === 'parso rtik') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-50 text-cyan-700">Parso RTIK</span>'; }
                elseif ($dev === 'supri rtik') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-50 text-teal-700">Supri RTIK</span>'; }
                elseif ($dev === 'sraya') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Sraya</span>'; }
                elseif ($dev === 'lainnya') { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-700">Lainnya</span>'; }
                else { $devBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Tidak diketahui</span>'; }
              ?>
              <tr class="border-t odd:bg-gray-50">
                <td class="px-4 py-2 text-gray-500"><?= $i++ ?></td>
                <td class="px-4 py-2 font-medium text-gray-900"><?= htmlspecialchars($r['nama_kecamatan']) ?></td>
                <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($r['nama_desa']) ?></td>
                <td class="px-4 py-2 whitespace-nowrap"><?= $link ?></td>
                <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($r['last_checked_at'] ?? '') ?></td>
                <td class="px-4 py-2 font-semibold text-gray-900"><?= htmlspecialchars($jpFmt) ?></td>
                <td class="px-4 py-2"><?= $dbBadge ?></td>
                <td class="px-4 py-2"><?= $sosBadge ?></td>
                <td class="px-4 py-2"><?= $berBadge ?></td>
                <td class="px-4 py-2"><?= $devBadge ?></td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <a href="admin_sid.php?action=edit&id=<?= (int)$r['id'] ?>" class="text-xs px-2 py-1 rounded bg-amber-500 text-white hover:bg-amber-600">Edit</a>
                    <form method="post" onsubmit="return confirm('Hapus data desa ini?');">
                      <input type="hidden" name="op" value="delete">
                      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                      <button type="submit" class="text-xs px-2 py-1 rounded bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="text-xs text-gray-500 mt-6">Catatan: Halaman ini untuk pengelola/admin SID. Perubahan data akan langsung mempengaruhi statistik dan daftar desa.</div>
  </div>
</body>
</html>
