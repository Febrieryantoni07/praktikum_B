<?php
/**
 * Konfigurasi Utama Sistem Informasi Rumah Sakit (Client UI - Server A)
 * Berdasarkan PRD Sistem Informasi Rumah Sakit Berbasis SOA
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// 1. ALAMAT WEB SERVICE / API (SERVER B)
// ============================================================================
// Sesuaikan IP Server B sesuai jaringan yang digunakan.
// Contoh di lab/jaringan: 'http://192.168.1.12/rs_api'
// Jika diuji dalam 1 laptop (localhost): 'http://localhost/rs_api'
define('BASE_API', 'http://192.168.1.12/rs_api');

// ============================================================================
// 2. DETEKSI OTOMATIS BASE URL CLIENT (SERVER A)
// ============================================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Cari posisi folder rs_client dalam URL
$pos = strpos($scriptDir, '/rs_client');
if ($pos !== false) {
    $clientPath = substr($scriptDir, 0, $pos + strlen('/rs_client'));
} else {
    $clientPath = $scriptDir;
}
$clientPath = rtrim($clientPath, '/');

define('BASE_URL', $protocol . $host . $clientPath);

// ============================================================================
// 3. INFORMASI RUMAH SAKIT
// ============================================================================
define('APP_NAME', 'RS Sehat Harmoni');
define('APP_TAGLINE', 'Sistem Informasi Rumah Sakit Terpadu Berbasis SOA');
define('APP_VERSION', '1.0.0');
define('APP_ADDRESS', 'Jl. Kesehatan No. 45, Harmoni Medical Center');
define('APP_PHONE', '(021) 555-0199 / IGD: 119');

// ============================================================================
// 4. HELPER PESAN KILAT (FLASH MESSAGE)
// ============================================================================
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
