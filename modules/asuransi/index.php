<?php
/**
 * Modul Asuransi - Daftar Asuransi Rekanan
 * Matriks RBAC: Admin (CRUD), Dokter (Lihat), Pasien (Lihat)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$pageTitle = 'Kelola Asuransi Rekanan';
$currentMenu = 'asuransi';

// Ambil data asuransi dari Server B
$res = api_request('/asuransi');
$listAsuransi = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

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
                        <li class="breadcrumb-item active" aria-current="page">Asuransi</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Data Asuransi Rekanan</h3>
                <p class="text-muted small mb-0">Lembaga asuransi kesehatan yang bekerjasama dengan <?php echo APP_NAME; ?></p>
            </div>

            <?php if ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/modules/asuransi/tambah.php" class="btn btn-emerald">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Asuransi
                </a>
            <?php endif; ?>
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
                    <i class="bi bi-shield-check text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Daftar Asuransi</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listAsuransi); ?> Rekanan</span>
                </div>
                
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari nama asuransi..." data-table-search="tableAsuransi">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tableAsuransi">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nama Asuransi</th>
                            <th>Jenis Lembaga</th>
                            <th>No. Kontak / Hotline</th>
                            <th>Keterangan Kerjasama</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listAsuransi)): ?>
                            <?php foreach ($listAsuransi as $a): ?>
                                <tr>
                                    <td class="text-muted fw-semibold">#<?php echo htmlspecialchars($a['id_asuransi']); ?></td>
                                    <td class="fw-bold text-emerald-dark">
                                        <i class="bi bi-shield-shaded me-1 text-emerald"></i>
                                        <?php echo htmlspecialchars($a['nama_asuransi']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-mint text-emerald-dark border">
                                            <?php echo htmlspecialchars($a['jenis'] ?? 'Swasta'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone-fill me-1 text-muted small"></i>
                                        <?php echo htmlspecialchars($a['no_kontak'] ?? '-'); ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo htmlspecialchars($a['keterangan'] ?? '-'); ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>/modules/asuransi/edit.php?id=<?php echo $a['id_asuransi']; ?>" class="btn btn-light border text-emerald" title="Ubah">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/asuransi/hapus.php?id=<?php echo $a['id_asuransi']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="<?php echo htmlspecialchars($a['nama_asuransi']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin') ? '6' : '5'; ?>" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Belum ada data asuransi rekanan.
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
