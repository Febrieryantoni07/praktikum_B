<?php
/**
 * Modul Pasien - Daftar Pasien Terdaftar
 * Matriks RBAC: Admin (CRUD), Dokter (Lihat), Pasien (Diarahkan ke profil sendiri)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();

// Jika pasien membuka halaman ini, arahkan ke profil sendiri
if ($role === 'pasien') {
    header('Location: ' . BASE_URL . '/modules/pasien/profil.php');
    exit;
}

$pageTitle = 'Data Pasien';
$currentMenu = 'pasien';

// Ambil data pasien dari Server B
$res = api_request('/pasien');
$listPasien = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

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
                        <li class="breadcrumb-item active" aria-current="page">Pasien</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Data Pasien Rekam Medis</h3>
                <p class="text-muted small mb-0">Daftar rekam medis pasien terdaftar di <?php echo APP_NAME; ?></p>
            </div>

            <?php if ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/modules/pasien/tambah.php" class="btn btn-emerald">
                    <i class="bi bi-person-plus-fill me-1"></i> Registrasi Pasien Baru
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
                    <i class="bi bi-people-fill text-emerald fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark">Semua Rekam Medis Pasien</h5>
                    <span class="badge bg-mint text-emerald-dark border ms-1"><?php echo count($listPasien); ?> Pasien</span>
                </div>
                
                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari No.RM, nama, atau NIK..." data-table-search="tablePasien">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tablePasien">
                    <thead>
                        <tr>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>NIK & JK</th>
                            <th>Tgl Lahir / Usia</th>
                            <th>No. Telepon / Alamat</th>
                            <th>Jaminan / Asuransi</th>
                            <?php if ($role === 'admin'): ?>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listPasien)): ?>
                            <?php foreach ($listPasien as $p): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-mint text-emerald-dark border fw-bold">
                                            <?php echo htmlspecialchars($p['no_rm']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nama_pasien']); ?></div>
                                        <small class="text-muted">ID: #<?php echo htmlspecialchars($p['id_pasien']); ?></small>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo htmlspecialchars($p['nik'] ?? '-'); ?></div>
                                        <span class="badge bg-light text-secondary border small">
                                            <?php echo ($p['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small"><?php echo !empty($p['tanggal_lahir']) ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-'; ?></div>
                                        <?php if (!empty($p['tanggal_lahir'])): ?>
                                            <?php 
                                                $birthDate = new DateTime($p['tanggal_lahir']);
                                                $today = new DateTime();
                                                $age = $today->diff($birthDate)->y;
                                            ?>
                                            <small class="text-muted"><?php echo $age; ?> tahun</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?php echo htmlspecialchars($p['no_hp'] ?? '-'); ?></div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 180px;"><?php echo htmlspecialchars($p['alamat'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['nama_asuransi'])): ?>
                                            <span class="badge bg-mint text-emerald-dark border">
                                                <i class="bi bi-shield-check me-1"></i> <?php echo htmlspecialchars($p['nama_asuransi']); ?>
                                            </span>
                                            <?php if (!empty($p['no_kartu_asuransi'])): ?>
                                                <div class="text-muted small" style="font-size: 0.72rem;">No: <?php echo htmlspecialchars($p['no_kartu_asuransi']); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border">Umum (Pribadi)</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($role === 'admin'): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>/modules/laporan/riwayat_pasien.php?id_pasien=<?php echo $p['id_pasien']; ?>" class="btn btn-light border text-info" title="Riwayat Rekam Medis">
                                                    <i class="bi bi-clock-history"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/pasien/edit.php?id=<?php echo $p['id_pasien']; ?>" class="btn btn-light border text-emerald" title="Ubah Data">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/modules/pasien/hapus.php?id=<?php echo $p['id_pasien']; ?>" class="btn btn-light border text-danger btn-confirm-delete" data-item-name="<?php echo htmlspecialchars($p['nama_pasien']); ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin') ? '7' : '6'; ?>" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                    Belum ada data pasien tersimpan.
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
