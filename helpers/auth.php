<?php
/**
 * Helper Autentikasi dan Otorisasi RBAC (Role-Based Access Control)
 * Berdasarkan PRD Sistem Informasi Rumah Sakit Berbasis SOA
 * 
 * Tiga Role: admin, dokter, pasien
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Cek apakah pengguna saat ini sudah login
 */
function is_logged_in() {
    return isset($_SESSION['token']) && !empty($_SESSION['token']) && isset($_SESSION['role']);
}

/**
 * Dapatkan data pengguna dari session
 */
function get_user() {
    return $_SESSION['user'] ?? [];
}

/**
 * Dapatkan role pengguna saat ini (admin | dokter | pasien)
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Dapatkan ID Pengguna
 */
function get_user_id() {
    return $_SESSION['user']['id_pengguna'] ?? null;
}

/**
 * Dapatkan Nama Lengkap Pengguna
 */
function get_user_name() {
    return $_SESSION['user']['nama_lengkap'] ?? 'Pengguna';
}

/**
 * Dapatkan ID Dokter (khusus role dokter)
 */
function get_dokter_id() {
    return $_SESSION['user']['id_dokter'] ?? null;
}

/**
 * Dapatkan ID Pasien (khusus role pasien)
 */
function get_pasien_id() {
    return $_SESSION['user']['id_pasien'] ?? null;
}

/**
 * Wajibkan login sebelum mengakses halaman
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Cek apakah role saat ini sesuai dengan salah satu role yang diizinkan
 *
 * @param array|string $allowedRoles Array atau string role (misal: ['admin', 'dokter'])
 */
function has_role($allowedRoles) {
    if (!is_logged_in()) return false;
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    return in_array(get_user_role(), $allowedRoles);
}

/**
 * Wajibkan role tertentu untuk mengakses halaman. Jika tidak berhak, alihkan ke 403.
 *
 * @param array|string $allowedRoles
 */
function require_role($allowedRoles) {
    require_login();
    if (!has_role($allowedRoles)) {
        header('Location: ' . BASE_URL . '/403.php');
        exit;
    }
}

/**
 * Helper HTML untuk menampilkan badge role pengguna dengan tema hijau medis
 */
function render_role_badge($role) {
    switch (strtolower($role ?? '')) {
        case 'admin':
            return '<span class="badge bg-emerald-700 text-white shadow-sm"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>';
        case 'dokter':
            return '<span class="badge bg-teal-600 text-white shadow-sm"><i class="bi bi-heart-pulse-fill me-1"></i> Dokter</span>';
        case 'pasien':
            return '<span class="badge bg-success-subtle text-success border border-success-subtle shadow-sm"><i class="bi bi-person-fill me-1"></i> Pasien</span>';
        default:
            return '<span class="badge bg-secondary">' . htmlspecialchars($role ?? '-') . '</span>';
    }
}

/**
 * Helper HTML untuk menampilkan status kunjungan
 */
function render_status_kunjungan($status) {
    switch (strtolower($status ?? '')) {
        case 'menunggu':
            return '<span class="badge-status status-menunggu"><i class="bi bi-clock-history me-1"></i> Menunggu</span>';
        case 'diperiksa':
            return '<span class="badge-status status-diperiksa"><i class="bi bi-activity me-1"></i> Sedang Diperiksa</span>';
        case 'selesai':
            return '<span class="badge-status status-selesai"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>';
        case 'batal':
            return '<span class="badge-status status-batal"><i class="bi bi-x-circle-fill me-1"></i> Dibatalkan</span>';
        default:
            return '<span class="badge bg-secondary">' . htmlspecialchars($status ?? '-') . '</span>';
    }
}
