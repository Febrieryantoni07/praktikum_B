<?php
/**
 * Modul Dokter - Hapus Dokter (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $res = api_request('/dokter?id=' . $id, 'DELETE', ['id_dokter' => $id]);

    if ($res['status']) {
        set_flash('success', 'Data dokter berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus dokter.');
    }
} else {
    set_flash('warning', 'ID Dokter tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/dokter/index.php');
exit;
