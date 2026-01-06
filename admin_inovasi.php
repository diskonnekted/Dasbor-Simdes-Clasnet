<?php
require_once __DIR__ . '/admin_auth.php';
admin_require();
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
$db = db();

// Buat tabel inovasi jika belum ada
$db->query("CREATE TABLE IF NOT EXISTS inovasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT DEFAULT NULL,
  gambar VARCHAR(255) DEFAULT NULL,
  published TINYINT(1) NOT NULL DEFAULT 1,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Helper upload file (opsional)
function handle_upload($field, $destDir = 'uploads') {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) { return null; }
  $name = basename($_FILES[$field]['name']);
  $ext = pathinfo($name, PATHINFO_EXTENSION);
  $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($name, PATHINFO_FILENAME));
  $newName = time() . '_' . $safeName . ($ext ? ('.' . $ext) : '');
  $destPath = __DIR__ . '/' . $destDir . '/' . $newName;
  if (!is_dir(__DIR__ . '/' . $destDir)) { mkdir(__DIR__ . '/' . $destDir, 0777, true); }
  if (move_uploaded_file($_FILES[$field]['tmp_name'], $destPath)) {
    return $destDir . '/' . $newName;
  }
  return null;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$errors = []; $success = '';

// CRUD handler
if ($action === 'create') {
  $judul = trim($_POST['judul'] ?? '');
  $deskripsi = trim($_POST['deskripsi'] ?? '');
  $published = isset($_POST['published']) ? (int)$_POST['published'] : 1;
  if ($judul === '') { $errors[] = 'Judul wajib diisi.'; }
  $gambarPath = handle_upload('gambar');
  if (empty($errors)) {
    $stmt = $db->prepare('INSERT INTO inovasi (judul, deskripsi, gambar, published) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('sssi', $judul, $deskripsi, $gambarPath, $published);
    if ($stmt->execute()) { $success = 'Inovasi berhasil ditambahkan.'; }
    else { $errors[] = 'Gagal menambah inovasi: ' . $stmt->error; }
    $stmt->close();
  }
} elseif ($action === 'update') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $judul = trim($_POST['judul'] ?? '');
  $deskripsi = trim($_POST['deskripsi'] ?? '');
  $published = isset($_POST['published']) ? (int)$_POST['published'] : 1;
  if ($id <= 0) { $errors[] = 'ID inovasi tidak valid.'; }
  if ($judul === '') { $errors[] = 'Judul wajib diisi.'; }
  $gambarPath = handle_upload('gambar');
  if (empty($errors)) {
    if ($gambarPath) {
      $stmt = $db->prepare('UPDATE inovasi SET judul=?, deskripsi=?, gambar=?, published=? WHERE id=?');
      $stmt->bind_param('sssii', $judul, $deskripsi, $gambarPath, $published, $id);
    } else {
      $stmt = $db->prepare('UPDATE inovasi SET judul=?, deskripsi=?, published=? WHERE id=?');
      $stmt->bind_param('ssii', $judul, $deskripsi, $published, $id);
    }
    if ($stmt->execute()) { $success = 'Inovasi berhasil diperbarui.'; }
    else { $errors[] = 'Gagal memperbarui inovasi: ' . $stmt->error; }
    $stmt->close();
  }
} elseif ($action === 'delete') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if ($id <= 0) { $errors[] = 'ID inovasi tidak valid.'; }
  else {
    $stmt = $db->prepare('DELETE FROM inovasi WHERE id=?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) { $success = 'Inovasi berhasil dihapus.'; }
    else { $errors[] = 'Gagal menghapus inovasi: ' . $stmt->error; }
    $stmt->close();
  }
} elseif ($action === 'toggle') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $newPub = isset($_POST['new_published']) ? (int)$_POST['new_published'] : 1;
  if ($id <= 0) { $errors[] = 'ID inovasi tidak valid.'; }
  else {
    $stmt = $db->prepare('UPDATE inovasi SET published=? WHERE id=?');
    $stmt->bind_param('ii', $newPub, $id);
    if ($stmt->execute()) { $success = 'Status publikasi inovasi diperbarui.'; }
    else { $errors[] = 'Gagal memperbarui status: ' . $stmt->error; }
    $stmt->close();
  }
} elseif ($action === 'seed') {
  // Seed 4 konten bawaan jika belum ada (cek berdasarkan judul atau path gambar)
  $defaults = [
    ['judul' => 'Integrasi LoRa untuk Desa', 'deskripsi' => 'Jaringan LoRa untuk konektivitas jarak jauh berdaya rendah, menghubungkan sensor di wilayah desa.', 'gambar' => 'images/an1.jpg'],
    ['judul' => 'Sensor IoT Lingkungan', 'deskripsi' => 'Pemantauan kualitas udara, banjir, dan cuaca lokal untuk respons cepat dan mitigasi risiko.', 'gambar' => 'images/an2.jpg'],
    ['judul' => 'Dashboard Kinerja Desa', 'deskripsi' => 'Visualisasi data pelayanan, statistik penduduk, dan aktivitas desa dalam satu dashboard.', 'gambar' => 'images/an3.jpg'],
    ['judul' => 'Anjungan Pelayanan Mandiri', 'deskripsi' => 'Terminal layanan warga mandiri, integrasi cetak dokumen, antrean, dan autentikasi aman.', 'gambar' => 'images/an4.jpg'],
  ];
  $inserted = 0; $skipped = 0;
  foreach ($defaults as $d) {
    $judul = $d['judul']; $gambar = $d['gambar']; $deskripsi = $d['deskripsi'];
    $stmt = $db->prepare('SELECT COUNT(*) AS c FROM inovasi WHERE judul=? OR gambar=?');
    $stmt->bind_param('ss', $judul, $gambar);
    if ($stmt->execute()) {
      $res = $stmt->get_result(); $r = $res ? $res->fetch_assoc() : ['c' => 0];
      $exists = (int)($r['c'] ?? 0) > 0;
    } else { $exists = false; }
    $stmt->close();
    if (!$exists) {
      $ins = $db->prepare('INSERT INTO inovasi (judul, deskripsi, gambar, published) VALUES (?, ?, ?, 1)');
      $ins->bind_param('sss', $judul, $deskripsi, $gambar);
      if ($ins->execute()) { $inserted++; } else { $errors[] = 'Gagal seed: ' . $ins->error; }
      $ins->close();
    } else { $skipped++; }
  }
  if (!$errors) { $success = 'Seed selesai: ' . $inserted . ' ditambahkan, ' . $skipped . ' dilewati.'; }
}

// Paginasi & list
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalItems = 0;
if ($res = $db->query('SELECT COUNT(*) AS c FROM inovasi')) { $r = $res->fetch_assoc(); $totalItems = (int)$r['c']; }
$totalPages = $perPage > 0 ? max(1, (int)ceil($totalItems / $perPage)) : 1;
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;

$list = [];
$sql = sprintf('SELECT id, judul, deskripsi, gambar, published, dibuat_pada, diperbarui_pada FROM inovasi ORDER BY dibuat_pada DESC LIMIT %d OFFSET %d', $perPage, $offset);
if ($res = $db->query($sql)) { while ($r = $res->fetch_assoc()) { $list[] = $r; } }
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Inovasi — CRUD</title>
  <link rel="icon" href="clasnet.png" type="image/png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="clasnet.png" alt="Logo Clasnet Group" class="w-10 h-10 rounded object-contain">
        <div>
          <div class="font-semibold">Dasbor SID</div>
          <div class="text-xs text-gray-500">Admin Inovasi (CRUD)</div>
        </div>
      </div>
      <?php $activeSlug = 'admin_inovasi'; if (file_exists(__DIR__ . '/partials/nav.php')) { include __DIR__ . '/partials/nav.php'; } ?>
    </div>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <h1 class="text-xl font-semibold">Kelola Inovasi Desa</h1>
      <p class="text-xs opacity-80">Tambah, edit, publikasi, dan hapus konten inovasi.</p>
    </div>

    <div class="mb-4">
      <form action="" method="post" class="inline-flex items-center gap-2">
        <input type="hidden" name="action" value="seed">
        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">Seed konten bawaan</button>
        <span class="text-xs text-gray-500">Menambahkan 4 item default jika belum ada.</span>
      </form>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="bg-rose-50 text-rose-700 border border-rose-200 rounded-lg p-4 mb-4">
        <div class="font-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="list-disc list-inside text-sm">
          <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg p-3 mb-4 text-sm"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1 bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-sm text-gray-600">Tambah / Edit Inovasi</div>
        <form action="" method="post" enctype="multipart/form-data" class="mt-3 space-y-3">
          <input type="hidden" name="action" value="create" id="formAction">
          <input type="hidden" name="id" value="" id="formId">
          <div>
            <label class="text-xs text-gray-600">Judul</label>
            <input type="text" name="judul" id="formJudul" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required>
          </div>
          <div>
            <label class="text-xs text-gray-600">Deskripsi</label>
            <textarea name="deskripsi" id="formDeskripsi" rows="4" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"></textarea>
          </div>
          <div>
            <label class="text-xs text-gray-600">Gambar (opsional)</label>
            <input type="file" name="gambar" accept="image/*" class="mt-1 w-full text-sm">
          </div>
        
          <div>
            <label class="text-xs text-gray-600">Publikasi</label>
            <select name="published" id="formPublished" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
              <option value="1">Terpublikasi</option>
              <option value="0">Draft</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm">Simpan</button>
            <button type="button" id="resetForm" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Reset</button>
          </div>
        </form>
      </div>

      <div class="lg:col-span-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-600">Daftar Inovasi</div>
          <div class="text-xs text-gray-500">Total: <?= $totalItems ?> item</div>
        </div>
        <div class="mt-3 divide-y divide-gray-100">
          <?php if (empty($list)): ?>
            <div class="text-sm text-gray-600">Belum ada data.</div>
          <?php else: ?>
            <?php foreach ($list as $row): ?>
              <div class="py-3 flex items-center gap-4">
                <div class="w-28 h-16 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                  <?php if (!empty($row['gambar'])): ?>
                    <img src="<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>" class="w-full h-full object-cover" onerror="this.parentNode.innerHTML='';">
                  <?php endif; ?>
                </div>
                <div class="flex-1">
                  <div class="font-medium text-gray-900"><?= htmlspecialchars($row['judul']) ?></div>
                  <div class="text-xs text-gray-600 mt-0.5 line-clamp-2"><?= nl2br(htmlspecialchars($row['deskripsi'] ?? '')) ?></div>
                  <div class="text-xs text-gray-500 mt-1">Status: <?= $row['published'] ? 'Terpublikasi' : 'Draft' ?> • Dibuat: <?= htmlspecialchars($row['dibuat_pada']) ?></div>
                </div>
                <div class="flex items-center gap-2">
                  <button class="px-2 py-1 text-xs rounded bg-indigo-50 text-indigo-700" onclick="fillEdit(<?= (int)$row['id'] ?>, <?= json_encode($row['judul']) ?>, <?= json_encode($row['deskripsi'] ?? '') ?>, <?= (int)$row['published'] ?>)">Edit</button>
                  <form action="" method="post" onsubmit="return confirm('Hapus inovasi ini?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <button type="submit" class="px-2 py-1 text-xs rounded bg-rose-50 text-rose-700">Hapus</button>
                  </form>
                  <form action="" method="post">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <input type="hidden" name="new_published" value="<?= $row['published'] ? 0 : 1 ?>">
                    <button type="submit" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"><?= $row['published'] ? 'Set Draft' : 'Publikasikan' ?></button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <div class="mt-4 flex items-center justify-center gap-2 text-sm">
            <?php for ($p=1; $p<=$totalPages; $p++): $is = ($p===$page); ?>
              <a href="?page=<?= $p ?>" class="px-3 py-1 rounded <?= $is ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' ?>"><?= $p ?></a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    const judulEl = document.getElementById('formJudul');
    const deskEl = document.getElementById('formDeskripsi');
    const pubEl = document.getElementById('formPublished');
    const actEl = document.getElementById('formAction');
    const idEl = document.getElementById('formId');
    document.getElementById('resetForm').addEventListener('click', function(){
      judulEl.value=''; deskEl.value=''; pubEl.value='1'; actEl.value='create'; idEl.value='';
    });
    window.fillEdit = function(id, judul, deskripsi, published){
      judulEl.value = judul || '';
      deskEl.value = deskripsi || '';
      pubEl.value = String(published ?? 1);
      actEl.value = 'update';
      idEl.value = String(id);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };
  </script>
</body>
</html>
