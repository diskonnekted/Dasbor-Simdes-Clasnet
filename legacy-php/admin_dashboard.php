<?php
// Simple Admin Dashboard to unify admin_berita, admin_inovasi, admin_sid in one page
require_once __DIR__ . '/admin_auth.php';
admin_require();
// Usage: admin_dashboard.php?tab=sid|berita|inovasi

$tab = isset($_GET['tab']) ? strtolower(trim($_GET['tab'])) : 'sid';
$allowed = ['sid','berita','inovasi'];
if (!in_array($tab, $allowed, true)) { $tab = 'sid'; }

function tabLabel($t) {
  switch ($t) {
    case 'sid': return 'Data Desa SID';
    case 'berita': return 'Berita Desa';
    case 'inovasi': return 'Inovasi';
    default: return ucfirst($t);
  }
}

// Map iframe src
$srcMap = [
  'sid' => 'admin_sid.php',
  'berita' => 'admin_berita.php',
  'inovasi' => 'admin_inovasi.php',
];
$iframeSrc = $srcMap[$tab];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dasbor Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Dasbor Admin</h1>
          <p class="text-sm mt-1 opacity-90">Kelola Berita, Inovasi, dan Data Desa SID dalam satu halaman.</p>
          <p class="text-xs mt-2 opacity-80">Gunakan tab di bawah untuk berpindah modul.</p>
        </div>
        <div class="flex items-center gap-2">
          <a href="index.php" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white/10 hover:bg-white/20 text-white text-sm">Kembali ke Dashboard</a>
          <a href="?logout=1" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white/10 hover:bg-white/20 text-white text-sm">Keluar</a>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-4 mb-6">
      <div class="flex flex-wrap items-center gap-2">
        <?php
        foreach ($allowed as $t) {
          $isActive = ($t === $tab);
          $cls = $isActive
            ? 'px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white shadow'
            : 'px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200';
          echo '<a href="?tab='.htmlspecialchars($t).'" class="'.$cls.'">'.htmlspecialchars(tabLabel($t)).'</a>';
        }
        ?>
      </div>
    </div>

    <!-- Content: iframe -->
    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 overflow-hidden">
      <div class="px-4 py-3 border-b">
        <div class="text-sm text-gray-600">Modul aktif: <span class="font-medium text-gray-900"><?php echo htmlspecialchars(tabLabel($tab)); ?></span></div>
      </div>
      <div class="">
        <iframe src="<?php echo htmlspecialchars($iframeSrc); ?>" title="Admin Module" class="w-full" style="min-height: 1600px;"></iframe>
      </div>
    </div>

    <!-- Notes -->
    <div class="text-xs text-gray-600 mt-4">
      <p>Catatan: Setiap modul tetap berjalan mandiri di dalam iframe. Aksi seperti tambah/edit/hapus akan diproses di modul terkait.</p>
    </div>
  </div>

  <!-- Optional: dynamic height adjust via postMessage (reserved) -->
  <script>
    // Placeholder for future auto-resize if child pages post their height.
    // window.addEventListener('message', (e) => {
    //   if (typeof e.data === 'object' && e.data.type === 'setHeight') {
    //     const ifr = document.querySelector('iframe');
    //     if (ifr) ifr.style.minHeight = (e.data.height || 1600) + 'px';
    //   }
    // });
  </script>
</body>
</html>
