<?php
/**
 * Modul Pengguna - Kelola Akun Pengguna Sistem
 * Matriks RBAC: Admin Only (CRUD)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Kelola Pengguna';
$currentMenu = 'pengguna';
$currentUserId = get_user_id();

// Ambil data akun pengguna dari Server B
$res = api_request('/pengguna');
$listUsers = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-emerald text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Kelola Akun Pengguna</h3>
                <p class="text-muted small mb-0">Manajemen akun login, peran (RBAC), dan hak akses sistem</p>
            </div>

            <a href="<?php echo BASE_URL; ?>/modules/pengguna/tambah.php" class="btn btn-emerald">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Akun Baru
            </a>
        </div>

        <?php if (!$res['status']): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($res['message']); ?></div>
            </div>
        <?php endif; ?>

        <div class="card card-modern border-0">
            <div class="card-header card-header-clean d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-gear text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Daftar Akun Pengguna</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listUsers); ?> Akun</span>
                </div>
                
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari username atau nama..." data-table-search="tableUser">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tableUser">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role / Hak Akses</th>
                            <th>Status Akun</th>
                            <th>Terdaftar Pada</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listUsers)): ?>
                            <?php foreach ($listUsers as $u): ?>
                                <tr>
                                    <td class="text-muted fw-semibold">#<?php echo htmlspecialchars($u['id_pengguna']); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <i class="bi bi-person-circle me-1 text-emerald"></i>
                                            <?php echo htmlspecialchars($u['username']); ?>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-secondary">
                                        <?php echo htmlspecialchars($u['nama_lengkap']); ?>
                                    </td>
                                    <td>
                                        <?php echo render_role_badge($u['role']); ?>
                                    </td>
                                    <td>
                                        <?php if (($u['status'] ?? 'aktif') === 'aktif'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo !empty($u['created_at']) ? date('d/m/Y H:i', strtotime($u['created_at'])) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo BASE_URL; ?>/modules/pengguna/edit.php?id=<?php echo $u['id_pengguna']; ?>" class="btn btn-light border text-emerald" title="Ubah Akun / Reset Password">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <?php if ((int)$u['id_pengguna'] !== (int)$currentUserId): ?>
                                                <a href="<?php echo BASE_URL; ?>/modules/pengguna/hapus.php?id=<?php echo $u['id_pengguna']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="<?php echo htmlspecialchars($u['username']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-light border text-muted" disabled title="Akun Anda Saat Ini">
                                                    <i class="bi bi-shield-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Belum ada data pengguna tersimpan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
