<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kontak — SID Clasnet Group</title>
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
      <?php $activeSlug = 'kontak'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Kontak Tim Pendamping Digitalisasi Desa</h1>
          <p class="text-sm mt-1 opacity-90">Clasnet Group — Layanan Sistem Informasi Desa.</p>
          <p class="text-xs mt-2 opacity-80">Alamat: Jl. Serulingmas No. 30, Banjarnegara</p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.54.57a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.21.2 2.42.57 3.54a1 1 0 01-.24 1.05l-2.2 2.2z"/></svg>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1">
          <div class="text-sm text-gray-600">Alamat</div>
          <div class="mt-1 text-lg font-semibold">Jl. Serulingmas No. 30</div>
          <div class="text-gray-700">Banjarnegara</div>
        </div>
        <div class="col-span-1">
          <div class="text-sm text-gray-600">Pendamping Digitalisasi Desa</div>
          <div class="mt-1 text-lg font-semibold">Clasnet Group</div>
          <div class="text-gray-700">Sistem Informasi Desa (SID)</div>
        </div>
        <div class="col-span-1">
          <div class="text-sm text-gray-600">Website</div>
          <a href="https://www.clasnet.co.id" target="_blank" class="mt-1 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">www.clasnet.co.id
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/></svg>
          </a>
          <a href="https://www.desaonline.cloud" target="_blank" class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">www.desaonline.cloud
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42 9.3-9.29H14V3z"/></svg>
          </a>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100 p-6 mb-8">
      <div class="flex items-center justify-between mb-4">
        <div>
          <div class="text-lg font-semibold text-gray-900">Kontak Tim</div>
          <div class="text-sm text-gray-600">Tim siap membantu melalui Telepon dan WhatsApp.</div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
        <!-- Admin -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
          <div class="p-6">
            <div class="flex items-center gap-3 mb-3">
              <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600">
                <!-- Icon: user -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
              </span>
              <div>
                <div class="text-sm font-semibold text-gray-900">Admin</div>
                <div class="text-xs text-gray-600">+62 851-1704-1846</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <a href="tel:+6285117041846" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.54.57a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.21.2 2.42.57 3.54a1 1 0 01-.24 1.05l-2.2 2.2z"/></svg>
                Telepon
              </a>
              <a href="https://wa.me/6285117041846" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100">
                <!-- Icon: whatsapp -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 3.5A9.5 9.5 0 006.6 19.9L5 21l1.1-1.6A9.5 9.5 0 1020 3.5zm-4.3 12.7c-.5.1-.8.3-1.3.2-1.2-.1-2.9-1-4.1-2.2-1.2-1.2-2.1-2.9-2.2-4.1 0-.4.1-.7.2-1.3.1-.4.7-.6 1-.7h.3c.3 0 .6.5.7.9.2.6.5 1.3.6 1.4.1.2.1.3 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.2-.1.4.2.5.9 1.4 1.7 2.1.8.8 1.6 1.4 2.1 1.7.1.1.3 0 .4-.1l.3-.3c.2-.2.3-.3.5-.4.2-.1.3-.1.5 0 .1.1.8.4 1.4.6.4.1.9.4.9.7v.3c-.1.3-.4.9-.7 1z"/></svg>
                WhatsApp
              </a>
            </div>
          </div>
        </div>

        <!-- Support -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
          <div class="p-6">
            <div class="flex items-center gap-3 mb-3">
              <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600">
                <!-- Icon: headset -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 2a8 8 0 00-8 8v5a3 3 0 003 3h2v-6H6v-2a6 6 0 1112 0v2h-3v6h2a3 3 0 003-3v-5a8 8 0 00-8-8z"/></svg>
              </span>
              <div>
                <div class="text-sm font-semibold text-gray-900">Support</div>
                <div class="text-xs text-gray-600">+62 813-9236-2332</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <a href="tel:+6281392362332" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.54.57a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.21.2 2.42.57 3.54a1 1 0 01-.24 1.05l-2.2 2.2z"/></svg>
                Telepon
              </a>
              <a href="https://wa.me/6281392362332" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 3.5A9.5 9.5 0 006.6 19.9L5 21l1.1-1.6A9.5 9.5 0 1020 3.5zm-4.3 12.7c-.5.1-.8.3-1.3.2-1.2-.1-2.9-1-4.1-2.2-1.2-1.2-2.1-2.9-2.2-4.1 0-.4.1-.7.2-1.3.1-.4.7-.6 1-.7h.3c.3 0 .6.5.7.9.2.6.5 1.3.6 1.4.1.2.1.3 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.2-.1.4.2.5.9 1.4 1.7 2.1.8.8 1.6 1.4 2.1 1.7.1.1.3 0 .4-.1l.3-.3c.2-.2.3-.3.5-.4.2-.1.3-.1.5 0 .1.1.8.4 1.4.6.4.1.9.4.9.7v.3c-.1.3-.4.9-.7 1z"/></svg>
                WhatsApp
              </a>
            </div>
          </div>
        </div>

        <!-- Nara Sumber -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-fuchsia-500"></div>
          <div class="p-6">
            <div class="flex items-center gap-3 mb-3">
              <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600">
                <!-- Icon: speaker -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M4 3h16v18H4V3zm8 2a6 6 0 100 12 6 6 0 000-12z"/></svg>
              </span>
              <div>
                <div class="text-sm font-semibold text-gray-900">Nara Sumber</div>
                <div class="text-xs text-gray-600">+62 822-2353-6812</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <a href="tel:+6282223536812" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.54.57a1 1 0 011 1v3.5a1 1 0 01-1 1C10.07 22 2 13.93 2 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.21.2 2.42.57 3.54a1 1 0 01-.24 1.05l-2.2 2.2z"/></svg>
                Telepon
              </a>
              <a href="https://wa.me/6282223536812" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 3.5A9.5 9.5 0 006.6 19.9L5 21l1.1-1.6A9.5 9.5 0 1020 3.5zm-4.3 12.7c-.5.1-.8.3-1.3.2-1.2-.1-2.9-1-4.1-2.2-1.2-1.2-2.1-2.9-2.2-4.1 0-.4.1-.7.2-1.3.1-.4.7-.6 1-.7h.3c.3 0 .6.5.7.9.2.6.5 1.3.6 1.4.1.2.1.3 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.2-.1.4.2.5.9 1.4 1.7 2.1.8.8 1.6 1.4 2.1 1.7.1.1.3 0 .4-.1l.3-.3c.2-.2.3-.3.5-.4.2-.1.3-.1.5 0 .1.1.8.4 1.4.6.4.1.9.4.9.7v.3c-.1.3-.4.9-.7 1z"/></svg>
                WhatsApp
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA WhatsApp: ditempatkan di atas Daftar Layanan SID -->
    <div class="rounded-2xl shadow-lg ring-1 ring-blue-200 bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 mb-8">
      <div class="flex items-start justify-between gap-4">
        <div class="max-w-3xl">
          <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/20">SID Gratis</span>
            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/20">Open Source</span>
            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/20">Tanpa Lisensi</span>
          </div>
          <h2 class="text-2xl font-semibold">Gabung Sekarang dan Bangun Desa Digital Anda dengan SID!</h2>
          <p class="text-sm mt-3 opacity-90">Sistem Informasi Desa (SID) adalah aplikasi <span class="font-semibold">open source</span> dan <span class="font-semibold">100% gratis</span>—siapa pun boleh menggunakannya, memodifikasi, dan menyebarkannya <span class="font-semibold">tanpa biaya lisensi</span>.</p>
          <p class="text-sm mt-2 opacity-90"><span class="font-semibold">Clasnet Group tidak menjual SID.</span> Kami percaya teknologi untuk desa harus terbuka dan merdeka.</p>
          <p class="text-sm mt-2 opacity-90">Yang kami tawarkan adalah layanan pendukung profesional agar SID Anda siap pakai dan optimal, meliputi:</p>
          <ul class="mt-3 space-y-2 text-sm">
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Hosting (infrastruktur andal dan aman)</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Instalasi & konfigurasi sistem</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Coklit data kependudukan (sinkronisasi dengan data desa aktual)</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Pelatihan bagi perangkat desa</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Pendampingan berkelanjutan</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Tema custom sesuai identitas desa</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 inline-block w-2 h-2 rounded-full border border-white/80"></span>
              <span>Pengembangan fitur tambahan (misalnya integrasi LoRa, sensor IoT, atau dashboard khusus)</span>
            </li>
          </ul>
          <p class="text-sm mt-3 opacity-90">Jangan biarkan keterbatasan teknis menghambat transparansi dan pelayanan desa Anda. Daftar sekarang dan wujudkan tata kelola desa yang akuntabel dengan SID!</p>
          <p class="text-sm mt-2 opacity-90">Hubungi Clasnet Group untuk konsultasi gratis. SID bukan produk komersial, melainkan layanan kemandirian digital untuk desa Anda.</p>
        </div>
        <div class="flex-shrink-0">
          <a href="https://wa.me/6285117041846?text=Halo%20Clasnet%2C%20saya%20ingin%20konsultasi%20SID" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white text-blue-700 hover:bg-blue-50 shadow font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-700"><path d="M20 3.5A9.5 9.5 0 006.6 19.9L5 21l1.1-1.6A9.5 9.5 0 1020 3.5zm-4.3 12.7c-.5.1-.8.3-1.3.2-1.2-.1-2.9-1-4.1-2.2-1.2-1.2-2.1-2.9-2.2-4.1 0-.4.1-.7.2-1.3.1-.4.7-.6 1-.7h.3c.3 0 .6.5.7.9.2.6.5 1.3.6 1.4.1.2.1.3 0 .5-.1.2-.2.3-.4.5l-.3.3c-.1.1-.2.2-.1.4.2.5.9 1.4 1.7 2.1.8.8 1.6 1.4 2.1 1.7.1.1.3 0 .4-.1l.3-.3c.2-.2.3-.3.5-.4.2-.1.3-.1.5 0 .1.1.8.4 1.4.6.4.1.9.4.9.7v.3c-.1.3-.4.9-.7 1z"/></svg>
            Konsultasi Gratis via WhatsApp
          </a>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg ring-1 ring-gray-100 p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <div class="text-lg font-semibold text-gray-900">Daftar Layanan SID</div>
          <div class="text-sm text-gray-600">Paket layanan disesuaikan kebutuhan desa Anda.</div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Lite -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
          <?php if (file_exists(__DIR__.'/uploads/lite.jpg')): ?>
            <img src="uploads/lite.jpg" alt="SID Lite" class="w-full h-32 md:h-36 object-cover">
          <?php endif; ?>
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600">
                  <!-- Icon: rocket -->
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M14.5 2a7.5 7.5 0 00-7.5 7.5v.379a2 2 0 01-.586 1.414l-2.793 2.793a1 1 0 001.414 1.414l2.793-2.793A2 2 0 0110.121 12h.379A7.5 7.5 0 0018 4.5V4a2 2 0 00-2-2h-1.5z"/><path d="M12.293 15.707a1 1 0 011.414-1.414l3.293 3.293a1 1 0 11-1.414 1.414l-3.293-3.293z"/></svg>
                </span>
                <div class="text-base font-semibold">SID Lite</div>
              </div>
              <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold">Rp 2.000.000</span>
            </div>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Instalasi SID</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan pengoperasian</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan entry database</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Domain</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Hosting 20GB</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Backup bulanan</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pilihan tema premium</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Standar -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
          <?php if (file_exists(__DIR__.'/uploads/standar.jpg')): ?>
            <img src="uploads/standar.jpg" alt="SID Standar" class="w-full h-32 md:h-36 object-cover">
          <?php endif; ?>
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600">
                  <!-- Icon: sparkles -->
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M11.99 2L9.19 8.63 2.56 11.43l6.63 2.8 2.8 6.63 2.8-6.63 6.63-2.8-6.63-2.8L11.99 2z"/></svg>
                </span>
                <div class="text-base font-semibold">SID Standar</div>
              </div>
              <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold">Rp 7.000.000</span>
            </div>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Semua layanan Lite</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Instalasi SID</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan pengoperasian</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan entry database</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Domain</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Hosting 50GB</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Backup bulanan</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pilihan tema premium</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan dan pendampingan pengolahan data</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- VIP -->
        <div class="group relative overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm transition transform hover:-translate-y-1 hover:shadow-xl">
          <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-fuchsia-500"></div>
          <?php if (file_exists(__DIR__.'/uploads/vip.jpg')): ?>
            <img src="uploads/vip.jpg" alt="SID VIP" class="w-full h-32 md:h-36 object-cover">
          <?php endif; ?>
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600">
                  <!-- Icon: crown -->
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M5 16l-3-9 6 4 4-6 4 6 6-4-3 9H5zm0 2h14v2H5v-2z"/></svg>
                </span>
                <div class="text-base font-semibold">SID VIP</div>
              </div>
              <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-bold">Rp 20.000.000</span>
            </div>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Semua layanan Standar</span>
              </li>
              <!-- List fitur Lite eksplisit -->
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Instalasi SID</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan pengoperasian</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pelatihan entry database</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Domain</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Hosting 1GB</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Backup bulanan</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Pilihan tema premium</span>
              </li>
              <!-- Fitur tambahan VIP -->
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Anjungan Pelayanan Mandiri</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Aplikasi Android</span>
              </li>
              <li class="flex items-start gap-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 mt-0.5"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span>Smart Office dengan IoT</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (file_exists(__DIR__.'/partials/footer.php')) { include __DIR__.'/partials/footer.php'; } ?>
</body>
</html>
