<?php
$curr = basename($_SERVER['PHP_SELF'], '.php');
$activeSlug = isset($activeSlug) && $activeSlug ? $activeSlug : $curr;

$items = [
  ['href' => 'index.php',      'label' => 'Dashboard',   'slug' => 'index'],
  ['href' => 'desa.php',       'label' => 'Daftar Desa', 'slug' => 'desa'],
  ['href' => 'peta.php',       'label' => 'Peta SID',    'slug' => 'peta'],
  ['href' => 'kegiatan.php',   'label' => 'Kegiatan',    'slug' => 'kegiatan'],
  ['href' => 'inovasi.php',    'label' => 'Inovasi',     'slug' => 'inovasi'],
  ['href' => 'statistik2.php', 'label' => 'Statistik',   'slug' => 'statistik2'],
  ['href' => 'kontak.php',     'label' => 'Kontak',      'slug' => 'kontak'],
  ['href' => 'mobile.php',     'label' => 'Mobile',      'slug' => 'mobile'],
];

echo '<nav class="flex gap-4 text-sm">';
foreach ($items as $it) {
  $isActive = ($activeSlug === $it['slug']);
  $cls = $isActive ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600';
  echo '<a href="'.htmlspecialchars($it['href']).'" class="'.$cls.'">'.htmlspecialchars($it['label']).'</a>';
}
echo '</nav>';
?>
