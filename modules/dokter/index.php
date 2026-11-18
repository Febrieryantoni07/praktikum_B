<?php
/**
 * Modul Dokter - Daftar Tenaga Medis / Dokter
 * Matriks RBAC: Admin (CRUD), Dokter (Lihat & Edit Profil Sendiri), Pasien (Lihat)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$dokterId = get_dokter_id();
$pageTitle = 'Data Dokter';
$currentMenu = 'dokter';

// Ambil data dokter dari Server B
$res = api_request('/dokter');
$listDokter = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

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
                        <li class="breadcrumb-item active" aria-current="page">Dokter</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Data Tenaga Dokter</h3>
                <p class="text-muted small mb-0">Informasi dokter spesialis dan umum di <?php echo APP_NAME; ?></p>
            </div>

            <div class="d-flex gap-2">
                <?php if ($role === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/dokter/tambah.php" class="btn btn-emerald">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Dokter Baru
                    </a>
                <?php elseif ($role === 'dokter'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/dokter/profil.php" class="btn btn-emerald">
                        <i class="bi bi-person-lines-fill me-1"></i> Edit Profil Saya
                    </a>
                <?php endif; ?>
            </div>
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
                    <i class="bi bi-person-badge-fill text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Semua Dokter</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listDokter); ?> Dokter</span>
                </div>
                
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari nama atau spesialisasi..." data-table-search="tableDokter">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tableDokter">
                    <thead>
                        <tr>
                            <th>Nama Dokter</th>
                            <th>Poliklinik</th>
                            <th>Spesialisasi</th>
                            <th>No. STR</th>
                            <th>No. Telepon / HP</th>
                            <?php if ($role === 'admin' || $role === 'dokter'): ?>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listDokter)): ?>
                            <?php foreach ($listDokter as $d): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.95rem;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($d['nama_dokter']); ?></div>
                                                <small class="text-muted">ID: #<?php echo htmlspecialchars($d['id_dokter']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-mint text-emerald-dark border">
                                            <i class="bi bi-hospital me-1"></i>
                                            <?php echo htmlspecialchars($d['nama_poli'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-secondary">
                                        <?php echo htmlspecialchars($d['spesialisasi'] ?? 'Umum'); ?>
                                    </td>
                                    <td>
                                        <code class="text-dark bg-light px-2 py-1 rounded small">
                                            <?php echo htmlspecialchars($d['no_str'] ?? '-'); ?>
                                        </code>
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone text-muted me-1 small"></i>
                                        <?php echo htmlspecialchars($d['no_hp'] ?? '-'); ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>/modules/dokter/edit.php?id=<?php echo $d['id_dokter']; ?>" class="btn btn-light border text-emerald" title="Ubah Data">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/dokter/hapus.php?id=<?php echo $d['id_dokter']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="<?php echo htmlspecialchars($d['nama_dokter']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php elseif ($role === 'dokter'): ?>
                                        <td class="text-center">
                                            <?php if ($dokterId && (int)$d['id_dokter'] === (int)$dokterId): ?>
                                                <a href="<?php echo BASE_URL; ?>/modules/dokter/profil.php" class="btn btn-sm btn-outline-emerald">
                                                    <i class="bi bi-pencil me-1"></i> Edit Profil
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin' || $role === 'dokter') ? '6' : '5'; ?>" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Belum ada data dokter terdaftar.
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
