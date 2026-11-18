<?php
/**
 * Modul Kunjungan - Hapus Kunjungan (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $res = api_request('/kunjungan?id=' . $id, 'DELETE', ['id_kunjungan' => $id]);

    if ($res['status']) {
        set_flash('success', 'Data kunjungan berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus data kunjungan.');
    }
} else {
    set_flash('warning', 'ID Kunjungan tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
exit;
