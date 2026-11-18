<?php
/**
 * Modul Poliklinik - Hapus Poliklinik (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Kirim DELETE request ke Server B
    $res = api_request('/poliklinik?id=' . $id, 'DELETE', ['id_poli' => $id]);

    if ($res['status']) {
        set_flash('success', 'Poliklinik berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus poliklinik.');
    }
} else {
    set_flash('warning', 'ID Poliklinik tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
exit;
