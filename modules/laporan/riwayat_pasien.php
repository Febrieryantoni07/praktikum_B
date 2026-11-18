<?php
/**
 * Laporan Riwayat Rekam Medis Satu Pasien
 * Endpoint API: /laporan/riwayat_pasien.php?id_pasien=
 * Matriks RBAC: Admin, Dokter, Pasien (riwayat sendiri)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$pageTitle = 'Riwayat Rekam Medis Pasien';
$currentMenu = ($role === 'pasien') ? 'kunjungan' : 'laporan';

$id_pasien = (int)($_GET['id_pasien'] ?? 0);

// Jika role pasien, kunci id_pasien ke id milik pasien sendiri
if ($role === 'pasien') {
    $myId = get_pasien_id();
    if (!$myId) {
        $resPas = api_request('/pasien');
        if ($resPas['status'] && is_array($resPas['data'])) {
            foreach ($resPas['data'] as $p) {
                if (isset($p['id_pengguna']) && (int)$p['id_pengguna'] === (int)get_user_id()) {
                    $myId = $p['id_pasien'];
                    $_SESSION['user']['id_pasien'] = $myId;
                    break;
                }
            }
        }
    }
    $id_pasien = (int)$myId;
}

// Ambil daftar pasien untuk dropdown (jika admin atau dokter)
$listPasien = [];
if ($role === 'admin' || $role === 'dokter') {
    $resAllPas = api_request('/pasien');
    $listPasien = ($resAllPas['status'] && is_array($resAllPas['data'])) ? $resAllPas['data'] : [];
    
    // Jika belum pilih pasien dan ada data, pilih pasien pertama sebagai default
    if ($id_pasien <= 0 && !empty($listPasien)) {
        $id_pasien = (int)$listPasien[0]['id_pasien'];
    }
}

// Ambil riwayat rekam medis pasien terpilih dari Server B
$riwayat = [];
$pasienInfo = null;

if ($id_pasien > 0) {
    $resRiwayat = api_request('/laporan/riwayat_pasien.php', 'GET', ['id_pasien' => $id_pasien]);
    if ($resRiwayat['status'] && is_array($resRiwayat['data'])) {
        $riwayat = $resRiwayat['data'];
    }

    // Ambil info pasien
    $resPasDetail = api_request('/pasien', 'GET', ['id' => $id_pasien]);
    if ($resPasDetail['status'] && !empty($resPasDetail['data'])) {
        $pasienInfo = is_array($resPasDetail['data']) && isset($resPasDetail['data'][0]) ? $resPasDetail['data'][0] : $resPasDetail['data'];
    }
}

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
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Pasien</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Riwayat Rekam Medis Pasien</h3>
                <p class="text-muted small mb-0">Catatan riwayat kunjungan medis, diagnosa, dan tindakan berobat</p>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-emerald" onclick="window.print();">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Rekam Medis
                </button>
                <a href="<?php echo BASE_URL; ?>/modules/laporan/index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Pemilih Pasien (Hanya Admin / Dokter) -->
        <?php if ($role === 'admin' || $role === 'dokter'): ?>
            <div class="card card-modern border-0 p-3 mb-4 no-print">
                <form action="" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-mint border-mint text-emerald fw-semibold">
                                <i class="bi bi-person-bounding-box me-1"></i> Pilih Pasien
                            </span>
                            <select class="form-select" name="id_pasien" onchange="this.form.submit()">
                                <?php foreach ($listPasien as $p): ?>
                                    <option value="<?php echo $p['id_pasien']; ?>" <?php echo ((int)$p['id_pasien'] === $id_pasien) ? 'selected' : ''; ?>>
                                        [<?php echo htmlspecialchars($p['no_rm']); ?>] <?php echo htmlspecialchars($p['nama_pasien']); ?> - NIK: <?php echo htmlspecialchars($p['nik'] ?? '-'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button type="submit" class="btn btn-emerald btn-sm">
                            <i class="bi bi-search me-1"></i> Muat Rekam Medis
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Lembar Resmi Riwayat Pasien -->
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
                <h4 class="fw-bold text-dark text-uppercase mb-1">LEMBAR RIWAYAT REKAM MEDIS PASIEN</h4>
                <div class="small text-muted">Dicetak pada: <?php echo date('d F Y, H:i'); ?> WIB</div>
            </div>

            <!-- Kartu Identitas Pasien -->
            <?php if ($pasienInfo): ?>
                <div class="p-3 bg-mint rounded-3 border border-mint mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">No. Rekam Medis (RM)</small>
                            <span class="fw-bold text-emerald-dark fs-5"><?php echo htmlspecialchars($pasienInfo['no_rm'] ?? '-'); ?></span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Nama Lengkap Pasien</small>
                            <strong class="text-dark fs-6"><?php echo htmlspecialchars($pasienInfo['nama_pasien'] ?? '-'); ?></strong>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">NIK & Jenis Kelamin</small>
                            <div><?php echo htmlspecialchars($pasienInfo['nik'] ?? '-'); ?> (<?php echo ($pasienInfo['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?>)</div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted d-block">Penjamin / Asuransi</small>
                            <span class="badge bg-white text-emerald-dark border fw-semibold"><?php echo htmlspecialchars($pasienInfo['nama_asuransi'] ?? 'Umum'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Riwayat Kunjungan Kronologis -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered mb-0">
                    <thead class="bg-mint text-emerald-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">No.</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th>Poliklinik & Dokter</th>
                            <th>Keluhan Awal</th>
                            <th>Diagnosa Klinis</th>
                            <th>Tindakan & Resep Obat</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayat)): ?>
                            <?php $no = 1; foreach ($riwayat as $r): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="fw-semibold"><?php echo date('d/m/Y', strtotime($r['tanggal_kunjungan'])); ?></td>
                                    <td>
                                        <div class="fw-bold text-emerald-dark"><?php echo htmlspecialchars($r['nama_poli'] ?? '-'); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($r['nama_dokter'] ?? '-'); ?></small>
                                    </td>
                                    <td class="small"><?php echo nl2br(htmlspecialchars($r['keluhan'] ?? '-')); ?></td>
                                    <td>
                                        <?php if (!empty($r['diagnosis'])): ?>
                                            <span class="fw-bold text-success"><?php echo htmlspecialchars($r['diagnosis']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Belum diperiksa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small font-monospace" style="white-space: pre-wrap; font-family: inherit;">
                                        <?php echo htmlspecialchars($r['tindakan'] ?? '-'); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo render_status_kunjungan($r['status']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada catatan riwayat kunjungan untuk pasien ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan Cetak -->
            <div class="print-footer row pt-4 mt-5 border-top">
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Dokter Penanggung Jawab Pasien (DPJP),</div>
                    <div class="fw-bold text-dark text-decoration-underline">( Dokter Pemeriksa )</div>
                    <small class="text-muted">SIP Dokter Terdaftar</small>
                </div>
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Petugas Arsip Rekam Medis,</div>
                    <div class="fw-bold text-dark text-decoration-underline"><?php echo htmlspecialchars(get_user_name()); ?></div>
                    <small class="text-muted">Instalasi Rekam Medis RS</small>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
