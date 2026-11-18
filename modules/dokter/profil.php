<?php
/**
 * Modul Dokter - Profil Dokter Saya (Khusus Role Dokter)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('dokter');

$dokterId = get_dokter_id();
if (!$dokterId) {
    // Cari dokter berdasarkan user id
    $resDoc = api_request('/dokter');
    if ($resDoc['status'] && is_array($resDoc['data'])) {
        foreach ($resDoc['data'] as $d) {
            if (isset($d['id_pengguna']) && (int)$d['id_pengguna'] === (int)get_user_id()) {
                $dokterId = $d['id_dokter'];
                $_SESSION['user']['id_dokter'] = $dokterId;
                break;
            }
        }
    }
}

if ($dokterId) {
    header('Location: ' . BASE_URL . '/modules/dokter/edit.php?id=' . $dokterId);
    exit;
} else {
    set_flash('warning', 'Profil data dokter belum terhubung dengan akun pengguna Anda. Hubungi administrator.');
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}
