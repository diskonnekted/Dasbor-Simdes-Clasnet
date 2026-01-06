<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Peta Sebaran SID</title>
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
      <?php $activeSlug = 'peta'; include __DIR__ . '/partials/nav.php'; ?>
    </div>
  </header>
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white shadow-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Peta Sebaran SID</h1>
          <p class="text-sm mt-1 opacity-90">Peta interaktif distribusi SID, dibuka dalam iframe responsif.</p>
          <p class="text-xs mt-2 opacity-80">Statistik dikelola oleh <span class="font-semibold">Clasnet Group</span> — <a href="https://www.clasnet.co.id" target="_blank" class="underline">www.clasnet.co.id</a></p>
        </div>
        <div class="p-3 rounded-lg bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 opacity-90"><path d="M3 5h18v14H3V5zm2 2v10h14V7H5zm3 2h8v2H8V9zm0 4h5v2H8v-2z"/></svg>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 bg-white rounded-xl shadow-lg ring-1 ring-gray-100">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <div>
            <div class="text-sm text-gray-600">Peta Administrasi OpenSID</div>
            <div class="text-xs text-gray-400">Sumber: arifsusilo.com/peta-administrasi-opensid</div>
          </div>
          <a href="https://www.arifsusilo.com/peta-administrasi-opensid/" target="_blank" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg border bg-white shadow-sm hover:bg-gray-50 text-sm">
            Buka di tab baru
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M14 3h7v7h-2V6.41l-6.29 6.3-1.42-1.42L17.59 5H14V3z"/><path d="M5 5h7v2H7v10h10v-5h2v7H5V5z"/></svg>
          </a>
        </div>
        <div class="p-3">
          <div class="relative w-full h-[70vh] rounded-lg overflow-hidden ring-1 ring-gray-100">
            <iframe src="https://www.arifsusilo.com/peta-administrasi-opensid/" title="Peta Administrasi OpenSID" class="absolute inset-0 w-full h-full" loading="lazy"></iframe>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-5">
        <div class="text-sm text-gray-600">Catatan & Bantuan</div>
        <ul class="mt-3 space-y-2 text-sm text-gray-700">
          <li class="flex items-start gap-2">
            <span class="mt-1 inline-block w-2 h-2 rounded-full bg-blue-600"></span>
            Jika peta tidak tampil, situs sumber mungkin membatasi tampilan melalui iframe (X-Frame-Options).
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-1 inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            Klik "Buka di tab baru" untuk melihat peta langsung di situs sumber.
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-1 inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
            Peta ini menampilkan administrasi OpenSID tingkat desa dan kecamatan.
          </li>
        </ul>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
