<?php
require_once __DIR__ . '/admin_auth.php';
admin_require();
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

// Tabel foto pendukung per berita
$db->query("CREATE TABLE IF NOT EXISTS berita_foto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  berita_id INT NOT NULL,
  path VARCHAR(255) NOT NULL,
  caption VARCHAR(255) DEFAULT NULL,
  urutan INT DEFAULT 0,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (berita_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$message = '';

function sanitize_filename($name) {
  $name = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $name);
  return $name;
}

// Handle Create/Update/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'create' || $action === 'update') {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $published = isset($_POST['published']) ? 1 : 0;
    $author = trim($_POST['author'] ?? 'Clasnet Group');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $dibuat_pada = $tanggal !== '' ? date('Y-m-d H:i:s', strtotime($tanggal)) : date('Y-m-d H:i:s');

    // Upload gambar jika ada
    $gambarPath = null;
    if (!empty($_FILES['gambar']['name'])) {
      $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      $mime = mime_content_type($_FILES['gambar']['tmp_name']);
      if (!isset($allowed[$mime])) {
        $message = 'Format gambar tidak didukung. Gunakan JPG/PNG/WebP.';
      } else {
        $ext = $allowed[$mime];
        $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0777, true); }
        $base = sanitize_filename(pathinfo($_FILES['gambar']['name'], PATHINFO_FILENAME));
        $filename = time() . '_' . $base . '.' . $ext;
        $full = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $full)) {
          $gambarPath = 'uploads/' . $filename;
        } else {
          $message = 'Gagal mengunggah gambar.';
        }
      }
    }

    if ($action === 'create' && $judul !== '' && $isi !== '') {
      $stmt = $db->prepare("INSERT INTO berita (judul, isi, gambar, dibuat_pada, published, author) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param('ssssis', $judul, $isi, $gambarPath, $dibuat_pada, $published, $author);
      $stmt->execute();
      $newId = $stmt->insert_id;

      // Upload foto pendukung (galeri)
      if (!empty($_FILES['galeri']['name']) && is_array($_FILES['galeri']['name'])) {
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0777, true); }
        $order = 1;
        for ($i=0; $i<count($_FILES['galeri']['name']); $i++) {
          if (empty($_FILES['galeri']['name'][$i])) continue;
          $tmp = $_FILES['galeri']['tmp_name'][$i];
          if (!is_file($tmp)) continue;
          $mime = mime_content_type($tmp);
          if (!isset($allowed[$mime])) continue;
          $ext = $allowed[$mime];
          $base = sanitize_filename(pathinfo($_FILES['galeri']['name'][$i], PATHINFO_FILENAME));
          $filename = time() . '_' . $base . '.' . $ext;
          $full = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
          if (move_uploaded_file($tmp, $full)) {
            $path = 'uploads/' . $filename;
            $stmtG = $db->prepare('INSERT INTO berita_foto (berita_id, path, urutan) VALUES (?, ?, ?)');
            $stmtG->bind_param('isi', $newId, $path, $order);
            $stmtG->execute();
            $order++;
          }
        }
      }
      $message = 'Berita berhasil ditambahkan.';
    } elseif ($action === 'update') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id > 0 && $judul !== '' && $isi !== '') {
        if ($gambarPath) {
          $stmt = $db->prepare("UPDATE berita SET judul=?, isi=?, gambar=?, dibuat_pada=?, published=?, author=? WHERE id=?");
          $stmt->bind_param('ssssisi', $judul, $isi, $gambarPath, $dibuat_pada, $published, $author, $id);
        } else {
$stmt = $db->prepare("UPDATE berita SET judul=?, isi=?, dibuat_pada=?, published=?, author=? WHERE id=?");
$stmt->bind_param('sssisi', $judul, $isi, $dibuat_pada, $published, $author, $id);
        }
        $stmt->execute();

        // Upload foto pendukung baru (galeri)
        if (!empty($_FILES['galeri']['name']) && is_array($_FILES['galeri']['name'])) {
          $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
          $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
          if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0777, true); }
          // Cari urutan terakhir
          $order = 1;
          if ($resOrder = $db->query("SELECT COALESCE(MAX(urutan),0) AS maxu FROM berita_foto WHERE berita_id=".$id)) {
            $rowOrder = $resOrder->fetch_assoc();
            $order = (int)$rowOrder['maxu'] + 1;
          }
          for ($i=0; $i<count($_FILES['galeri']['name']); $i++) {
            if (empty($_FILES['galeri']['name'][$i])) continue;
            $tmp = $_FILES['galeri']['tmp_name'][$i];
            if (!is_file($tmp)) continue;
            $mime = mime_content_type($tmp);
            if (!isset($allowed[$mime])) continue;
            $ext = $allowed[$mime];
            $base = sanitize_filename(pathinfo($_FILES['galeri']['name'][$i], PATHINFO_FILENAME));
            $filename = time() . '_' . $base . '.' . $ext;
            $full = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($tmp, $full)) {
              $path = 'uploads/' . $filename;
              $stmtG = $db->prepare('INSERT INTO berita_foto (berita_id, path, urutan) VALUES (?, ?, ?)');
              $stmtG->bind_param('isi', $id, $path, $order);
              $stmtG->execute();
              $order++;
            }
          }
        }
        $message = 'Berita berhasil diperbarui.';
      }
    }
  } elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
      $db->query("DELETE FROM berita WHERE id=".$id);
      $message = 'Berita berhasil dihapus.';
    }
  }
}

// Ambil data untuk daftar dan edit
$editing = null;
if (isset($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  if ($res = $db->query("SELECT * FROM berita WHERE id=".$id)) { $editing = $res->fetch_assoc(); }
}

$list = [];
if ($res = $db->query("SELECT id, judul, dibuat_pada, published, gambar FROM berita ORDER BY dibuat_pada DESC")) {
  while ($r = $res->fetch_assoc()) { $list[] = $r; }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Berita SID</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Admin Kegiatan SID</h1>
          <p class="text-sm mt-1 opacity-90">Tambah, unggah foto, edit, dan hapus berita kegiatan SID.</p>
          <p class="text-xs mt-2 opacity-80">Konten dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="flex items-center gap-2">
          <div class="p-3 rounded-lg bg-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M12 2l4 4H8l4-4zm8 8H4v10h16V10zm-2 2v6H6v-6h12z"/></svg>
          </div>
          <a href="kegiatan.php" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50 text-sm text-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Kembali ke Halaman Berita
          </a>
        </div>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-sm text-gray-600 mb-3">Daftar Berita</div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">#</th>
                <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Judul</th>
                <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Tanggal</th>
                <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Publikasi</th>
                <th class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-gray-600">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($list as $item): ?>
                <tr class="border-t odd:bg-gray-50">
                  <td class="px-4 py-2 text-gray-500"><?= $i++ ?></td>
                  <td class="px-4 py-2 font-medium text-gray-900 flex items-center gap-3">
                    <?php if (!empty($item['gambar'])): ?>
                      <img src="<?= htmlspecialchars($item['gambar']) ?>" alt="thumb" class="w-10 h-10 object-cover rounded">
                    <?php else: ?>
                      <span class="inline-block w-10 h-10 rounded bg-gray-100"></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($item['judul']) ?>
                  </td>
                  <td class="px-4 py-2 text-gray-700"><?= date('d M Y', strtotime($item['dibuat_pada'])) ?></td>
                  <td class="px-4 py-2"><?php if ($item['published']) { echo '<span class="px-2 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">Terbit</span>'; } else { echo '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">Draft</span>'; } ?></td>
                  <td class="px-4 py-2">
                    <a href="?edit=<?= $item['id'] ?>" class="px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50">Edit</a>
                    <form method="post" action="" class="inline">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $item['id'] ?>">
                      <button type="submit" class="px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-rose-50 text-rose-700" onclick="return confirm('Hapus berita ini?')">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-sm text-gray-600 mb-3"><?= $editing ? 'Edit Berita' : 'Tambah Berita' ?></div>
        <form method="post" enctype="multipart/form-data" class="space-y-3">
          <?php if ($editing): ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php else: ?><input type="hidden" name="action" value="create"><?php endif; ?>
          <div>
            <label class="text-sm text-gray-700">Judul</label>
            <input type="text" name="judul" class="mt-1 w-full border rounded-lg px-3 py-2" value="<?= htmlspecialchars($editing['judul'] ?? '') ?>" required>
          </div>
          <div>
            <label class="text-sm text-gray-700">Isi Berita</label>
            <textarea name="isi" rows="6" class="mt-1 w-full border rounded-lg px-3 py-2" required><?= htmlspecialchars($editing['isi'] ?? '') ?></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-sm text-gray-700">Tanggal</label>
              <input type="date" name="tanggal" class="mt-1 w-full border rounded-lg px-3 py-2" value="<?= isset($editing['dibuat_pada']) ? date('Y-m-d', strtotime($editing['dibuat_pada'])) : '' ?>">
            </div>
            <div class="flex items-center gap-2 mt-6">
              <input type="checkbox" id="published" name="published" <?= isset($editing['published']) && $editing['published'] ? 'checked' : '' ?>>
              <label for="published" class="text-sm text-gray-700">Terbit</label>
            </div>
          </div>
          <div>
            <label class="text-sm text-gray-700">Author</label>
            <input type="text" name="author" class="mt-1 w-full border rounded-lg px-3 py-2" value="<?= htmlspecialchars($editing['author'] ?? 'Clasnet Group') ?>">
          </div>
          <div>
            <label class="text-sm text-gray-700">Gambar (JPG/PNG/WebP)</label>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" class="mt-1 w-full border rounded-lg px-3 py-2">
            <?php if (!empty($editing['gambar'])): ?>
              <div class="mt-2"><img src="<?= htmlspecialchars($editing['gambar']) ?>" alt="preview" class="w-32 h-32 object-cover rounded"></div>
            <?php endif; ?>
          </div>
          <div>
            <label class="text-sm text-gray-700">Foto pendukung (JPG/PNG/WebP, bisa pilih banyak)</label>
            <input type="file" name="galeri[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 w-full border rounded-lg px-3 py-2">
          </div>
          <div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-6 text-xs text-gray-500">Halaman admin ini tidak ditampilkan di menu navigasi.</div>
  </div>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
