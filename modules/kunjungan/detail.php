<?php
/**
 * Modul Kunjungan - Detail & Resume Medis Kunjungan
 * Dapat dicetak langsung (Printable Medical Summary)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID Kunjungan tidak valid.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}

$pageTitle = 'Resume Medis Kunjungan #' . $id;
$currentMenu = 'kunjungan';

// Ambil data kunjungan
$resK = api_request('/kunjungan', 'GET', ['id' => $id]);
if (!$resK['status'] || empty($resK['data'])) {
    set_flash('danger', 'Data kunjungan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}
$k = is_array($resK['data']) && isset($resK['data'][0]) ? $resK['data'][0] : $resK['data'];

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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="text-emerald text-decoration-none">Kunjungan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Rincian & Resume Medis Pasien</h3>
                <p class="text-muted small mb-0">Lembar catatan rekam medis kunjungan poliklinik</p>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-emerald" onclick="window.print();">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Lembar Medis
                </button>
                <?php if ($role === 'dokter' || $role === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/kunjungan/periksa.php?id=<?php echo $id; ?>" class="btn btn-emerald">
                        <i class="bi bi-pencil-square me-1"></i> Ubah Diagnosis / Resep
                    </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Printable Document Container -->
        <div class="card card-modern border-0 p-4 p-md-5 bg-white">
            <!-- Kop Surat Resmi Rumah Sakit (Muncul saat cetak dan rapi di layar) -->
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

            <!-- Judul Dokumen -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 1px;">RESUME PELAYANAN MEDIS RAWAT JALAN</h4>
                <div class="small text-muted">No. Registrasi Kunjungan: <strong>#REG-<?php echo str_pad($k['id_kunjungan'], 6, '0', STR_PAD_LEFT); ?></strong></div>
            </div>

            <!-- Tabel Informasi Identitas -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold text-emerald-dark mb-2"><i class="bi bi-person-fill me-1"></i> Identitas Pasien</h6>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td style="width: 140px;" class="text-muted">Nomor Rekam Medis</td>
                                <td class="fw-bold text-emerald-dark">: <?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Lengkap</td>
                                <td class="fw-bold">: <?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td>: <?php echo ($k['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Penjamin / Asuransi</td>
                                <td>: <?php echo htmlspecialchars($k['nama_asuransi'] ?? 'Umum (Pribadi)'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold text-emerald-dark mb-2"><i class="bi bi-hospital-fill me-1"></i> Pelayanan & Dokter</h6>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td style="width: 140px;" class="text-muted">Poliklinik Tujuan</td>
                                <td class="fw-bold">: <?php echo htmlspecialchars($k['nama_poli'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dokter Pemeriksa</td>
                                <td class="fw-bold">: <?php echo htmlspecialchars($k['nama_dokter'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Kunjungan</td>
                                <td>: <?php echo date('d F Y', strtotime($k['tanggal_kunjungan'])); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Kunjungan</td>
                                <td>: <?php echo render_status_kunjungan($k['status']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rekam Klinis -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">1. Anamnesa & Keluhan Utama Pasien</h6>
                <div class="p-3 bg-mint rounded-3 border border-mint small">
                    <?php echo nl2br(htmlspecialchars($k['keluhan'] ?? 'Tidak ada catatan keluhan.')); ?>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">2. Diagnosa Medis (Diagnosis Klinis)</h6>
                <div class="p-3 bg-light rounded-3 border">
                    <?php if (!empty($k['diagnosis'])): ?>
                        <div class="fw-bold text-success fs-6"><?php echo htmlspecialchars($k['diagnosis']); ?></div>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Pasien belum diperiksa oleh dokter.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">3. Tindakan Medis & Terapi Resep Obat</h6>
                <div class="p-3 bg-light rounded-3 border small">
                    <?php if (!empty($k['tindakan'])): ?>
                        <pre class="mb-0 font-monospace" style="white-space: pre-wrap; font-family: inherit;"><?php echo htmlspecialchars($k['tindakan']); ?></pre>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Belum ada resep atau tindakan tertulis.</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kolom Tanda Tangan Cetak (Muncul saat print & dokumen) -->
            <div class="row pt-4 mt-4 border-top">
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Petugas Administrasi Medis,</div>
                    <div class="fw-bold text-dark text-decoration-underline">( Petugas Admisi RS )</div>
                    <small class="text-muted">NIP: 19820412.2023.01</small>
                </div>
                <div class="col-6 text-center">
                    <div class="small text-muted mb-5">Dokter Pemeriksa,</div>
                    <div class="fw-bold text-dark text-decoration-underline"><?php echo htmlspecialchars($k['nama_dokter'] ?? '( Dokter Spesialis )'); ?></div>
                    <small class="text-muted">SIP / STR Dokter Terlampir</small>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
