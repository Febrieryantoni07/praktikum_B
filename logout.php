<?php
/**
 * Logout Handler (Server A)
 * Memanggil endpoint /auth/logout.php di Server B untuk mencabut token,
 * kemudian menghapus sesi lokal di Server A.
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';

if (isset($_SESSION['token'])) {
    // Beritahu Server B untuk menghapus token akses dari tabel token_akses
    api_request('/auth/logout.php', 'POST');
}

// Bersihkan session Server A
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Mulai sesi baru untuk pesan flash
session_start();
set_flash('info', 'Anda telah berhasil keluar dari sistem. Sampai jumpa kembali!');
header('Location: ' . BASE_URL . '/login.php');
exit;
