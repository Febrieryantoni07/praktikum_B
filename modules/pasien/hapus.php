<?php
/**
 * Modul Pasien - Hapus Data Pasien (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $res = api_request('/pasien?id=' . $id, 'DELETE', ['id_pasien' => $id]);

    if ($res['status']) {
        set_flash('success', 'Data pasien berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus data pasien.');
    }
} else {
    set_flash('warning', 'ID Pasien tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/pasien/index.php');
exit;
