<?php
/**
 * Modul Pengguna - Hapus Akun Pengguna (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$myId = (int)get_user_id();

if ($id === $myId) {
    set_flash('danger', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan saat ini.');
    header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
    exit;
}

if ($id > 0) {
    $res = api_request('/pengguna?id=' . $id, 'DELETE', ['id_pengguna' => $id]);

    if ($res['status']) {
        set_flash('success', 'Akun pengguna berhasil dihapus.');
    } else {
        set_flash('danger', $res['message'] ?? 'Gagal menghapus akun pengguna.');
    }
} else {
    set_flash('warning', 'ID Pengguna tidak valid.');
}

header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
exit;
