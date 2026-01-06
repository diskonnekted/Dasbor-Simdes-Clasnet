<?php
// Simple session-based auth for admin pages
// Configurable via config.php: set $ADMIN_USER and $ADMIN_PASSWORD (plain text) if desired.

function admin_auth_boot() {
  if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }

  // Optional load from config.php
  @include_once __DIR__ . '/config.php';
  // If included inside a function, variables from config.php are local to this scope.
  // Promote them to $GLOBALS if present so we can access consistently below.
  if (isset($ADMIN_USER) && is_string($ADMIN_USER) && $ADMIN_USER !== '') {
    $GLOBALS['ADMIN_USER'] = $ADMIN_USER;
  }
  if (isset($ADMIN_PASSWORD) && is_string($ADMIN_PASSWORD) && $ADMIN_PASSWORD !== '') {
    $GLOBALS['ADMIN_PASSWORD'] = $ADMIN_PASSWORD;
  }

  // Defaults if not provided by config.php
  $defaultUser = 'admin';
  $defaultPass = 'changeme';

  if (!isset($GLOBALS['ADMIN_USER']) || !is_string($GLOBALS['ADMIN_USER']) || $GLOBALS['ADMIN_USER'] === '') {
    $GLOBALS['ADMIN_USER'] = $defaultUser;
  }
  if (!isset($GLOBALS['ADMIN_PASSWORD']) || !is_string($GLOBALS['ADMIN_PASSWORD']) || $GLOBALS['ADMIN_PASSWORD'] === '') {
    $GLOBALS['ADMIN_PASSWORD'] = $defaultPass;
  }

  // Logout handler
  if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    // Redirect back without logout param
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $url);
    exit;
  }
}

function admin_is_logged_in() {
  return !empty($_SESSION['is_admin']);
}

function admin_require() {
  admin_auth_boot();

  // Already logged in
  if (admin_is_logged_in()) { return; }

  // Handle login post
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : ($_SERVER['REQUEST_URI'] ?? 'index.php');

    $ok = hash_equals($GLOBALS['ADMIN_USER'], $u) && hash_equals($GLOBALS['ADMIN_PASSWORD'], $p);
    if ($ok) {
      $_SESSION['is_admin'] = true;
      header('Location: ' . $redirect);
      exit;
    } else {
      $error = 'Username atau password salah.';
    }
  }

  // Render login page
  $redirect = $_SERVER['REQUEST_URI'] ?? 'index.php';
  echo "<!doctype html>\n";
  echo "<html lang=\"id\">\n<head>\n<meta charset=\"utf-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<title>Login Admin</title>\n<script src=\"https://cdn.tailwindcss.com\"></script>\n</head>\n<body class=\"bg-gray-50 text-gray-900\">";
  echo '<div class="min-h-screen flex items-center justify-center px-4">';
  echo '  <div class="w-full max-w-sm bg-white rounded-xl shadow-lg ring-1 ring-gray-100 p-6">';
  echo '    <div class="text-center mb-4">';
  echo '      <img src="clasnet.png" alt="Clasnet" class="mx-auto h-10 w-auto mb-3" />';
  echo '      <div class="text-lg font-semibold">Login Admin</div>';
  echo '      <div class="text-xs text-gray-600">Masuk untuk mengelola konten</div>';
  echo '    </div>';
  if (isset($error)) {
    echo '    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-lg p-3 mb-4 text-sm">'.htmlspecialchars($error).'</div>';
  }
  echo '    <form method="post" action="">';
  echo '      <input type="hidden" name="admin_login" value="1">';
  echo '      <input type="hidden" name="redirect" value="'.htmlspecialchars($redirect).'">';
  echo '      <label class="block text-sm font-medium text-gray-700">Username</label>';
  echo '      <input type="text" name="username" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required autocomplete="username">';
  echo '      <label class="block text-sm font-medium text-gray-700 mt-4">Password</label>';
  echo '      <input type="password" name="password" class="mt-1 w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required autocomplete="current-password">';
  echo '      <button type="submit" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Masuk</button>';
  echo '    </form>';
  echo '  </div>';
  echo '</div>';
  echo '</body></html>';
  exit;
}
