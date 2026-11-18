<?php
/**
 * Modul Poliklinik - Daftar Poliklinik
 * Sesuai Matriks Hak Akses: Admin (CRUD), Dokter (Lihat), Pasien (Lihat)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$pageTitle = 'Kelola Poliklinik';
$currentMenu = 'poliklinik';

// Ambil data poliklinik dari Server B
$res = api_request('/poliklinik');
$listPoli = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-content">
        <!-- Breadcrumb & Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-emerald text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Poliklinik</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Data Poliklinik</h3>
                <p class="text-muted small mb-0">Daftar poli dan unit pelayanan medis di <?php echo APP_NAME; ?></p>
            </div>

            <!-- Tombol Tambah hanya untuk Admin -->
            <?php if ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/modules/poliklinik/tambah.php" class="btn btn-emerald">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Poliklinik
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$res['status']): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($res['message']); ?></div>
            </div>
        <?php endif; ?>

        <!-- Card Tabel -->
        <div class="card card-modern border-0">
            <div class="card-header card-header-clean d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-buildings-fill text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Semua Poliklinik</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listPoli); ?> Poli</span>
                </div>
                
                <!-- Pencarian Cepat -->
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari nama atau lokasi poli..." data-table-search="tablePoli">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tablePoli">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nama Poliklinik</th>
                            <th>Lokasi Gedung / Ruang</th>
                            <th>Keterangan Layanan</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listPoli)): ?>
                            <?php foreach ($listPoli as $p): ?>
                                <tr>
                                    <td class="text-muted fw-semibold">#<?php echo htmlspecialchars($p['id_poli']); ?></td>
                                    <td class="fw-bold text-emerald-dark">
                                        <i class="bi bi-hospital me-1 text-emerald"></i>
                                        <?php echo htmlspecialchars($p['nama_poli']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <i class="bi bi-geo-alt me-1 text-emerald"></i>
                                            <?php echo htmlspecialchars($p['lokasi'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo htmlspecialchars($p['keterangan'] ?? '-'); ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>/modules/poliklinik/edit.php?id=<?php echo $p['id_poli']; ?>" class="btn btn-light border text-emerald" title="Ubah">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/poliklinik/hapus.php?id=<?php echo $p['id_poli']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="<?php echo htmlspecialchars($p['nama_poli']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin') ? '5' : '4'; ?>" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Belum ada data poliklinik yang tersimpan.
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
