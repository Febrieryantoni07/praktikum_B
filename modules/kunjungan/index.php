<?php
/**
 * Modul Kunjungan - Daftar Kunjungan & Antrean Pemeriksaan
 * Matriks RBAC:
 * - Admin: Semua kunjungan (CRUD)
 * - Dokter: Kunjungan pasien yang ditangani (Lihat & Isi Diagnosis/Tindakan)
 * - Pasien: Daftar kunjungan baru dan riwayat kunjungan miliknya
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$pageTitle = 'Pelayanan Kunjungan';
$currentMenu = 'kunjungan';

// Filter parameter dari GET
$statusFilter = $_GET['status'] ?? '';
$tanggalFilter = $_GET['tanggal'] ?? '';

// Ambil data kunjungan dari Server B (Server B otomatis filter berdasarkan token role)
$queryParams = [];
if (!empty($statusFilter)) $queryParams['status'] = $statusFilter;
if (!empty($tanggalFilter)) $queryParams['tanggal'] = $tanggalFilter;

$res = api_request('/kunjungan', 'GET', $queryParams);
$listKunjungan = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

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
                        <li class="breadcrumb-item active" aria-current="page">Kunjungan</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">
                    <?php 
                    if ($role === 'dokter') echo 'Antrean & Pemeriksaan Pasien';
                    elseif ($role === 'pasien') echo 'Riwayat & Janji Temu Kunjungan';
                    else echo 'Daftar Seluruh Kunjungan Medis';
                    ?>
                </h3>
                <p class="text-muted small mb-0">Manajemen antrean periksa dan pencatatan rekam medis terpadu</p>
            </div>

            <!-- Tombol Tambah Kunjungan (Admin & Pasien) -->
            <?php if ($role === 'admin' || $role === 'pasien'): ?>
                <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="btn btn-emerald">
                    <i class="bi bi-calendar-plus-fill me-1"></i> + Buat Kunjungan Baru
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$res['status']): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($res['message']); ?></div>
            </div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <div class="card card-modern border-0 p-3 mb-4">
            <form action="" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-funnel"></i> Status</span>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">-- Semua Status --</option>
                            <option value="menunggu" <?php echo ($statusFilter === 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="diperiksa" <?php echo ($statusFilter === 'diperiksa') ? 'selected' : ''; ?>>Sedang Diperiksa</option>
                            <option value="selesai" <?php echo ($statusFilter === 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                            <option value="batal" <?php echo ($statusFilter === 'batal') ? 'selected' : ''; ?>>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-event"></i> Tanggal</span>
                        <input type="date" class="form-control" name="tanggal" value="<?php echo htmlspecialchars($tanggalFilter); ?>" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <?php if (!empty($statusFilter) || !empty($tanggalFilter)): ?>
                        <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Kunjungan -->
        <div class="card card-modern border-0">
            <div class="card-header card-header-clean d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-check-fill text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Data Kunjungan</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listKunjungan); ?> Data</span>
                </div>
                
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari pasien / dokter..." data-table-search="tableKunjungan">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tableKunjungan">
                    <thead>
                        <tr>
                            <th>No. RM & Pasien</th>
                            <th>Poliklinik & Dokter</th>
                            <th>Tanggal Kunjungan</th>
                            <th>Keluhan Pasien</th>
                            <th>Diagnosa & Tindakan</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listKunjungan)): ?>
                            <?php foreach ($listKunjungan as $k): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></div>
                                        <span class="badge bg-mint text-emerald-dark border" style="font-size: 0.72rem;">
                                            <?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-emerald-dark"><?php echo htmlspecialchars($k['nama_poli'] ?? '-'); ?></div>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($k['nama_dokter'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo date('d/m/Y', strtotime($k['tanggal_kunjungan'])); ?></div>
                                        <small class="text-muted"><?php echo !empty($k['created_at']) ? date('H:i', strtotime($k['created_at'])) . ' WIB' : ''; ?></small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($k['keluhan'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($k['keluhan'] ?? '-'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($k['diagnosis'])): ?>
                                            <div class="fw-bold small text-success"><?php echo htmlspecialchars($k['diagnosis']); ?></div>
                                            <small class="text-muted text-truncate d-block" style="max-width: 160px;"><?php echo htmlspecialchars($k['tindakan'] ?? '-'); ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Belum diperiksa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo render_status_kunjungan($k['status']); ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <!-- Detail Kunjungan (Bisa diakses semua) -->
                                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/detail.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-light border text-emerald" title="Lihat Rekam / Resep">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <!-- Tombol Periksa (Khusus Dokter & Admin) -->
                                            <?php if ($role === 'dokter' || $role === 'admin'): ?>
                                                <a href="<?php echo BASE_URL; ?>/modules/kunjungan/periksa.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-light border text-primary" title="Periksa & Isi Diagnosis">
                                                    <i class="bi bi-clipboard2-pulse-fill"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Edit & Hapus (Khusus Admin) -->
                                            <?php if ($role === 'admin'): ?>
                                                <a href="<?php echo BASE_URL; ?>/modules/kunjungan/edit.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-light border text-secondary" title="Ubah Data">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/kunjungan/hapus.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="Kunjungan #<?php echo $k['id_kunjungan']; ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Tidak ada data kunjungan yang sesuai kriteria pencarian.
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
