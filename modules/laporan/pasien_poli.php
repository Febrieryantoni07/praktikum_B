<?php
/**
 * Laporan Jumlah Pasien per Poliklinik (Admin Only)
 * Endpoint API: /laporan/pasien_per_poli.php
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Laporan Pasien per Poliklinik';
$currentMenu = 'lap_poli';

// Panggil endpoint laporan dari Server B
$res = api_request('/laporan/pasien_per_poli.php');
$dataLaporan = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

// Hitung total kunjungan kumulatif
$totalKunjungan = 0;
foreach ($dataLaporan as $row) {
    $totalKunjungan += (int)($row['total_pasien'] ?? $row['jumlah_pasien'] ?? 0);
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-content">
        <!-- Header Non-Print -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-emerald text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/laporan/index.php" class="text-emerald text-decoration-none">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pasien per Poliklinik</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Laporan Pasien per Poliklinik</h3>
                <p class="text-muted small mb-0">Statistik distribusi pemanfaatan unit poliklinik oleh pasien</p>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-emerald" onclick="window.print();">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                </button>
                <a href="<?php echo BASE_URL; ?>/modules/laporan/index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <?php if (!$res['status']): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 no-print">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($res['message']); ?></div>
            </div>
        <?php endif; ?>

        <!-- Container Laporan Resmi -->
        <div class="card card-modern border-0 p-4 p-md-5 bg-white">
            <!-- Kop Surat Resmi -->
            <div class="print-header text-center mb-4 pb-3 border-bottom border-2 border-dark">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                    <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.6rem;">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div class="text-start">
                        <h3 class="fw-bold mb-0 text-emerald-dark"><?php echo APP_NAME; ?></h3>
                        <div class="small text-muted"><?php echo APP_TAGLINE; ?></div>
                    </div>
                </div>
                <div class="small text-muted">
                    <?php echo APP_ADDRESS; ?> | Telp: <?php echo APP_PHONE; ?>
                </div>
            </div>

            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark text-uppercase mb-1">REKAPITULASI KUNJUNGAN PASIEN PER POLIKLINIK</h4>
                <div class="small text-muted">Dicetak pada: <?php echo date('d F Y, H:i'); ?> WIB oleh <strong><?php echo htmlspecialchars(get_user_name()); ?></strong></div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered mb-0">
                    <thead class="bg-mint text-emerald-dark">
                        <tr>
                            <th class="text-center" style="width: 60px;">No.</th>
                            <th>Nama Poliklinik</th>
                            <th>Lokasi Gedung</th>
                            <th class="text-center">Jumlah Pasien</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataLaporan)): ?>
                            <?php $no = 1; foreach ($dataLaporan as $row): 
                                $jml = (int)($row['total_pasien'] ?? $row['jumlah_pasien'] ?? 0);
                                $persen = ($totalKunjungan > 0) ? round(($jml / $totalKunjungan) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_poli']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lokasi'] ?? '-'); ?></td>
                                    <td class="text-center fw-bold text-emerald-dark"><?php echo $jml; ?> Pasien</td>
                                    <td class="text-center"><?php echo $persen; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold bg-light">
                                <td colspan="3" class="text-end">TOTAL KUNJUNGAN:</td>
                                <td class="text-center text-emerald-dark fs-6"><?php echo $totalKunjungan; ?> Pasien</td>
                                <td class="text-center">100%</td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada data kunjungan yang tercatat di masing-masing poliklinik.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan Cetak -->
            <div class="print-footer row pt-4 mt-5 border-top">
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Mengetahui, Kepala Bagian Pelayanan Medis</div>
                    <div class="fw-bold text-dark text-decoration-underline">( dr. Direktur Utama, MARS )</div>
                    <small class="text-muted">NIP: 19780514.2003.01</small>
                </div>
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Petugas Pembuat Laporan,</div>
                    <div class="fw-bold text-dark text-decoration-underline"><?php echo htmlspecialchars(get_user_name()); ?></div>
                    <small class="text-muted">Staff Rekam Medis & SIMRS</small>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
