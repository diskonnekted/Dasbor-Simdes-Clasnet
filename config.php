<?php
// Konfigurasi database untuk aplikasi SID
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sid');

// Kredensial Simple Auth Admin
$ADMIN_USER = 'clasnet';
$ADMIN_PASSWORD = 'Dikantor@5474';
define('APP_VERSION', '1.1.0');
header('X-CMS: Clasnet CMS');
header('X-Powered-By: Clasnet CMS');

function db() {
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($mysqli->connect_error) {
    die('DB error: ' . $mysqli->connect_error);
  }
  $mysqli->set_charset('utf8mb4');
  return $mysqli;
}
