<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inovasi — Galeri Inovasi Clasnet untuk Desa</title>
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
      <?php $activeSlug = 'inovasi'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Galeri Inovasi Clasnet untuk Desa</h1>
          <p class="text-sm mt-1 opacity-90">Kumpulan inovasi untuk mendukung kemandirian digital dan layanan desa.</p>
          <p class="text-xs mt-2 opacity-80">Semua inovasi dapat diintegrasikan dengan SID dan ekosistem perangkat desa.</p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M12 2l4 4H8l4-4zm8 8H4v10h16V10zm-2 2v6H6v-6h12z"/></svg>
        </div>
      </div>
    </div>

    <?php
      require_once __DIR__ . '/config.php';
      $db = db();
      $items = [];
      // Pastikan tabel inovasi ada (hosting bisa belum membuatnya)
      try {
        $db->query(
          "CREATE TABLE IF NOT EXISTS inovasi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT DEFAULT NULL,
            gambar VARCHAR(255) DEFAULT NULL,
            published TINYINT(1) NOT NULL DEFAULT 1,
            dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            diperbarui_pada DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
      } catch (Throwable $e) {
        // Abaikan kesalahan pembuatan tabel; kita akan fallback ke konten statis
      }

      // Ambil konten inovasi yang dipublikasikan jika tabel tersedia
      try {
        if ($res = $db->query("SELECT judul, deskripsi, gambar FROM inovasi WHERE published=1 ORDER BY dibuat_pada DESC")) {
          while ($r = $res->fetch_assoc()) {
            $items[] = [
              'file' => $r['gambar'] ?: '',
              'title' => $r['judul'] ?: '',
              'desc' => $r['deskripsi'] ?: ''
            ];
          }
        }
      } catch (Throwable $e) {
        // Jika SELECT gagal (mis. tabel tidak ada), tetap gunakan fallback statis di bawah
      }
      if (empty($items)) {
        $items = [
          [
            'file' => 'images/an1.jpg',
            'title' => 'Integrasi LoRa untuk Desa',
            'desc' => 'Jaringan LoRa untuk konektivitas jarak jauh berdaya rendah, menghubungkan sensor di wilayah desa.'
          ],
          [
            'file' => 'images/an2.jpg',
            'title' => 'Sensor IoT Lingkungan',
            'desc' => 'Pemantauan kualitas udara, banjir, dan cuaca lokal untuk respons cepat dan mitigasi risiko.'
          ],
          [
            'file' => 'images/an3.jpg',
            'title' => 'Dashboard Kinerja Desa',
            'desc' => 'Visualisasi data pelayanan, statistik penduduk, dan aktivitas desa dalam satu dashboard.'
          ],
          [
            'file' => 'images/an4.jpg',
            'title' => 'Anjungan Pelayanan Mandiri',
            'desc' => 'Terminal layanan warga mandiri, integrasi cetak dokumen, antrean, dan autentikasi aman.'
          ],
        ];
      }
    ?>

    <?php if (empty($items)): ?>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6">
        <div class="text-center">
          <div class="text-lg font-semibold">Belum ada konten inovasi.</div>
          <div class="text-sm text-gray-600 mt-1">Silakan tambahkan gambar ke folder <code>images</code>.</div>
        </div>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($items as $it): $path = __DIR__ . '/' . $it['file']; $hasImg = file_exists($path); ?>
          <article class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 overflow-hidden">
            <?php if ($hasImg): ?>
              <button type="button" class="block w-full text-left" data-lightbox-src="<?= htmlspecialchars($it['file']) ?>" data-lightbox-title="<?= htmlspecialchars($it['title']) ?>">
                <div class="aspect-[16/9] bg-gray-100">
                  <img src="<?= htmlspecialchars($it['file']) ?>" alt="<?= htmlspecialchars($it['title']) ?>" class="w-full h-full object-cover" onerror="this.closest('.aspect-\\[16/9\\]').innerHTML='';">
                </div>
              </button>
            <?php else: ?>
              <div class="aspect-[16/9] bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14l4-4h12l4 4zM7 9h10v2H7V9zm0 4h7v2H7v-2z"/></svg>
              </div>
            <?php endif; ?>
            <div class="p-4">
              <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($it['title']) ?></h2>
              <p class="text-sm text-gray-700 mt-1"><?= htmlspecialchars($it['desc']) ?></p>
              <div class="mt-3">
                <a href="https://wa.me/6285117041846?text=Halo%20Clasnet%2C%20saya%20ingin%20mendalami%20inovasi%20<?= urlencode($it['title']) ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm">
                  Konsultasi Inovasi via WhatsApp
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="mt-8">
      <a href="kontak.php" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border bg-white shadow-sm hover:bg-gray-50 text-sm">
        Lihat detail layanan & kontak
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
      </a>
    </div>
  </div>

  <div id="lightbox" class="fixed inset-0 z-50 hidden">
    <div id="lightbox-backdrop" class="absolute inset-0 bg-black/70"></div>
    <div class="relative mx-auto max-w-6xl p-4 h-full flex items-center justify-center">
      <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden">
        <button type="button" id="lb-close" class="absolute top-2 right-2 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/90 border shadow text-gray-700 hover:bg-white" aria-label="Tutup">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M18.3 5.71L12 12.01 5.7 5.71 4.29 7.12 10.59 13.41 4.29 19.71 5.7 21.12 12 14.83 18.29 21.12 19.7 19.71 13.41 13.41 19.7 7.12z"/></svg>
        </button>
        <img id="lb-img" src="" alt="" class="max-h-[80vh] max-w-[90vw] object-contain bg-black">
        <div id="lb-title" class="p-3 text-sm text-gray-700"></div>
      </div>
    </div>
  </div>

  <?php if (file_exists(__DIR__.'/partials/footer.php')) { include __DIR__.'/partials/footer.php'; } ?>
  <script>
    (function(){
      var lb = document.getElementById('lightbox');
      var backdrop = document.getElementById('lightbox-backdrop');
      var img = document.getElementById('lb-img');
      var title = document.getElementById('lb-title');
      var closeBtn = document.getElementById('lb-close');
      function open(src, ttl){
        img.src = src;
        img.alt = ttl || '';
        title.textContent = ttl || '';
        lb.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      }
      function close(){
        lb.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        img.src = '';
      }
      Array.prototype.forEach.call(document.querySelectorAll('[data-lightbox-src]'), function(el){
        el.addEventListener('click', function(){
          open(el.getAttribute('data-lightbox-src'), el.getAttribute('data-lightbox-title'));
        });
      });
      backdrop.addEventListener('click', close);
      closeBtn.addEventListener('click', close);
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape'){ close(); } });
    })();
  </script>
</body>
</html>
