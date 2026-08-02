<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
$db = db();

echo "<h1>Database Update Tool</h1>";
echo "<div style='background:#f5f5f5; padding:20px; border:1px solid #ddd; border-radius:5px; font-family:monospace;'>";

function checkAndAddColumn($db, $table, $column, $spec) {
    echo "Checking table '<b>$table</b>' for column '<b>$column</b>'...<br>";
    $cols = [];
    if ($res = $db->query("SHOW COLUMNS FROM $table")) {
        while ($r = $res->fetch_assoc()) { $cols[] = $r['Field']; }
    }
    if (!in_array($column, $cols)) {
        echo "&nbsp;&nbsp;[+] Adding column '$column'... ";
        if ($db->query("ALTER TABLE $table ADD COLUMN $column $spec")) {
            echo "<span style='color:green'>Success</span>.<br>";
        } else {
            echo "<span style='color:red'>Failed</span>: " . $db->error . "<br>";
        }
    } else {
        echo "&nbsp;&nbsp;[OK] Column already exists.<br>";
    }
    echo "<br>";
}

// Update tabel berita
checkAndAddColumn($db, 'berita', 'tags', 'VARCHAR(255) DEFAULT NULL AFTER author');
checkAndAddColumn($db, 'berita', 'related_desa', 'VARCHAR(255) DEFAULT NULL AFTER tags');

echo "<hr>";
echo "<strong>Update Selesai.</strong><br>";
echo "Database Anda sekarang sudah sinkron.";
echo "</div>";

echo '<br><a href="kegiatan.php" style="background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Buka Halaman Kegiatan</a>';
?>