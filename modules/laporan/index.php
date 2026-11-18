<?php
/**
 * Modul Laporan - Pusat Laporan & Analitik (Admin & Dokter)
 * Berdasarkan PRD Bab 3.1 & 5.3
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'dokter']);

$role = get_user_role();
$pageTitle = 'Pusat Laporan Rumah Sakit';
$currentMenu = 'laporan';

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
                        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Pusat Laporan & Rekapitulasi Pelayanan</h3>
                <p class="text-muted small mb-0">Laporan statistik kunjungan, rekam medis, dan utilisasi fasilitas medis</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- 1. Kunjungan Berdasarkan Periode Tanggal (Admin & Dokter) -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-modern h-100 p-4 border-0">
                    <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi bi-calendar-range-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Kunjungan per Periode</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Rekapitulasi seluruh kunjungan pasien berdasarkan rentang tanggal tertentu dengan rincian diagnosis dan tindakan.
                    </p>
                    <a href="<?php echo BASE_URL; ?>/modules/laporan/kunjungan_periode.php" class="btn btn-emerald w-100">
                        <i class="bi bi-file-earmark-text me-1"></i> Buka Laporan
                    </a>
                </div>
            </div>

            <?php if ($role === 'admin'): ?>
                <!-- 2. Pasien per Poliklinik (Admin Only) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-modern h-100 p-4 border-0">
                        <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            <i class="bi bi-bar-chart-steps"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Pasien per Poliklinik</h5>
                        <p class="text-muted small mb-4 flex-grow-1">
                            Distribusi jumlah pasien yang berkunjung ke masing-masing unit poliklinik spesialis dan umum.
                        </p>
                        <a href="<?php echo BASE_URL; ?>/modules/laporan/pasien_poli.php" class="btn btn-emerald w-100">
                            <i class="bi bi-file-earmark-text me-1"></i> Buka Laporan
                        </a>
                    </div>
                </div>

                <!-- 3. Kunjungan per Dokter (Admin Only) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-modern h-100 p-4 border-0">
                        <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Kunjungan per Dokter</h5>
                        <p class="text-muted small mb-4 flex-grow-1">
                            Statistik beban kerja dan jumlah penanganan pasien oleh masing-masing dokter spesialis.
                        </p>
                        <a href="<?php echo BASE_URL; ?>/modules/laporan/kunjungan_dokter.php" class="btn btn-emerald w-100">
                            <i class="bi bi-file-earmark-text me-1"></i> Buka Laporan
                        </a>
                    </div>
                </div>

                <!-- 4. Pasien & Asuransi (Admin Only) -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-modern h-100 p-4 border-0">
                        <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Pasien Berdasarkan Asuransi</h5>
                        <p class="text-muted small mb-4 flex-grow-1">
                            Daftar pasien berdasarkan jenis penjamin (BPJS Kesehatan, Asuransi Swasta, atau Pasien Umum).
                        </p>
                        <a href="<?php echo BASE_URL; ?>/modules/laporan/pasien_asuransi.php" class="btn btn-emerald w-100">
                            <i class="bi bi-file-earmark-text me-1"></i> Buka Laporan
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 5. Riwayat Rekam Medis Pasien (Admin & Dokter) -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-modern h-100 p-4 border-0">
                    <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi bi-folder-check"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Riwayat Rekam Medis Pasien</h5>
                    <p class="text-muted small mb-4 flex-grow-1">
                        Pencarian riwayat kunjungan lengkap, diagnosa masa lalu, dan tindakan obat seorang pasien berdasarkan No. RM.
                    </p>
                    <a href="<?php echo BASE_URL; ?>/modules/laporan/riwayat_pasien.php" class="btn btn-emerald w-100">
                        <i class="bi bi-search me-1"></i> Cari Riwayat Pasien
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
