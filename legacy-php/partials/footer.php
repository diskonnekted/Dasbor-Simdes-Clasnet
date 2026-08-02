<div class="bg-white border-t mt-8">
  <div class="max-w-7xl mx-auto px-4 py-6 flex items-center justify-between">
    <a href="https://www.clasnet.co.id" target="_blank" class="flex items-center gap-3">
      <img src="footer.png" alt="Clasnet Group" class="w-24 h-auto object-contain">
      <div class="text-sm">
        <div class="font-semibold text-gray-900">Clasnet Group</div>
        <div class="text-gray-600">Pengembang dan pengelola Dasbor SID</div>
      </div>
    </a>
    <div class="text-xs text-gray-600 text-right">
      <div>Dasbor SID Kabupaten Banjarnegara</div>
      <div>Statistik website desa, sinkronisasi penduduk, dan peta SID</div>
      <div>Sumber data: basis data MySQL SID</div>
      <div>&copy; <?= date('Y') ?> <a href="https://www.clasnet.co.id" class="text-blue-600 hover:underline" target="_blank">clasnet.co.id</a></div>
    </div>
  </div>
</div>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('/service-worker.js');
  });
}
</script>
