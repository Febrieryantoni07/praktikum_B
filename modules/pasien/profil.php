<?php
/**
 * Modul Pasien - Profil Pasien Saya (Khusus Role Pasien)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('pasien');

$pasienId = get_pasien_id();
if (!$pasienId) {
    // Cari pasien berdasarkan user id
    $resPas = api_request('/pasien');
    if ($resPas['status'] && is_array($resPas['data'])) {
        foreach ($resPas['data'] as $p) {
            if (isset($p['id_pengguna']) && (int)$p['id_pengguna'] === (int)get_user_id()) {
                $pasienId = $p['id_pasien'];
                $_SESSION['user']['id_pasien'] = $pasienId;
                break;
            }
        }
    }
}

if ($pasienId) {
    header('Location: ' . BASE_URL . '/modules/pasien/edit.php?id=' . $pasienId);
    exit;
} else {
    set_flash('warning', 'Profil data pasien belum terhubung dengan akun login Anda. Hubungi bagian administrasi.');
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}
