<?php
/**
 * Laporan Kunjungan Berdasarkan Rentang Periode Tanggal (Admin & Dokter)
 * Endpoint API: /laporan/kunjungan_periode.php?dari=&sampai=
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'dokter']);

$role = get_user_role();
$pageTitle = 'Laporan Kunjungan per Periode';
$currentMenu = 'lap_periode';

// Default tanggal: awal bulan ini s/d hari ini
$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Panggil endpoint laporan dari Server B
$res = api_request('/laporan/kunjungan_periode.php', 'GET', [
    'dari' => $dari,
    'sampai' => $sampai
]);
$dataLaporan = ($res['status'] && is_array($res['data'])) ? $res['data'] : [];

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-emerald text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/laporan/index.php" class="text-emerald text-decoration-none">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kunjungan Periode</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Laporan Kunjungan Pasien per Periode</h3>
                <p class="text-muted small mb-0">Rekapitulasi kunjungan medis dalam rentang tanggal tertentu</p>
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

        <!-- Filter Tanggal Non-Print -->
        <div class="card card-modern border-0 p-3 mb-4 no-print">
            <form action="" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">Dari Tanggal</span>
                        <input type="date" class="form-control" name="dari" value="<?php echo htmlspecialchars($dari); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted">Sampai Tanggal</span>
                        <input type="date" class="form-control" name="sampai" value="<?php echo htmlspecialchars($sampai); ?>" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-emerald">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="<?php echo BASE_URL; ?>/modules/laporan/kunjungan_periode.php" class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <?php if (!$res['status']): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 no-print">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($res['message']); ?></div>
            </div>
        <?php endif; ?>

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
                <h4 class="fw-bold text-dark text-uppercase mb-1">LAPORAN KUNJUNGAN PASIEN PER PERIODE</h4>
                <div class="small text-muted">
                    Periode: <strong><?php echo date('d/m/Y', strtotime($dari)); ?></strong> s/d <strong><?php echo date('d/m/Y', strtotime($sampai)); ?></strong>
                    &bull; Dicetak: <?php echo date('d/m/Y H:i'); ?> WIB
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered mb-0">
                    <thead class="bg-mint text-emerald-dark">
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Tanggal</th>
                            <th>No. RM & Nama Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Diagnosa Medis</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataLaporan)): ?>
                            <?php $no = 1; foreach ($dataLaporan as $row): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_kunjungan'])); ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_pasien'] ?? '-'); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['no_rm'] ?? '-'); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['nama_poli'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_dokter'] ?? '-'); ?></td>
                                    <td class="small">
                                        <?php if (!empty($row['diagnosis'])): ?>
                                            <span class="text-success fw-semibold"><?php echo htmlspecialchars($row['diagnosis']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Belum ada diagnosa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo render_status_kunjungan($row['status']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada kunjungan yang tercatat pada rentang tanggal <?php echo date('d/m/Y', strtotime($dari)); ?> s/d <?php echo date('d/m/Y', strtotime($sampai)); ?>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="print-footer row pt-4 mt-5 border-top">
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Mengetahui, Direktur Pelayanan Medik</div>
                    <div class="fw-bold text-dark text-decoration-underline">( dr. Spesialis Utama )</div>
                </div>
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Petugas Pembuat Laporan,</div>
                    <div class="fw-bold text-dark text-decoration-underline"><?php echo htmlspecialchars(get_user_name()); ?></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
