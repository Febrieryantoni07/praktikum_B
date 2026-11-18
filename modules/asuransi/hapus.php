<?php
/**
 * Modul Asuransi - Hapus Asuransi (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $res = api_request('/asuransi?id=' . $id, 'DELETE', ['id_asuransi' => $id]);

    if ($res['status']) {
        set_flash('success', 'Asuransi berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus asuransi.');
    }
} else {
    set_flash('warning', 'ID Asuransi tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
exit;
