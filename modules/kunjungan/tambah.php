<?php
/**
 * Modul Kunjungan - Buat Kunjungan Baru
 * Matriks RBAC: Admin & Pasien
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['admin', 'pasien']);

$role = get_user_role();
$myPasienId = get_pasien_id();

$pageTitle = 'Daftar Kunjungan Baru';
$currentMenu = 'kunjungan_tambah';
$errorMessage = '';

// Ambil data poliklinik
$resPoli = api_request('/poliklinik');
$listPoli = ($resPoli['status'] && is_array($resPoli['data'])) ? $resPoli['data'] : [];

// Ambil data dokter
$resDokter = api_request('/dokter');
$listDokter = ($resDokter['status'] && is_array($resDokter['data'])) ? $resDokter['data'] : [];

// Jika admin, ambil daftar pasien
$listPasien = [];
if ($role === 'admin') {
    $resPas = api_request('/pasien');
    $listPasien = ($resPas['status'] && is_array($resPas['data'])) ? $resPas['data'] : [];
} else {
    // Jika role pasien, pastikan pasienId ada
    if (!$myPasienId) {
        $resPas = api_request('/pasien');
        if ($resPas['status'] && is_array($resPas['data'])) {
            foreach ($resPas['data'] as $p) {
                if (isset($p['id_pengguna']) && (int)$p['id_pengguna'] === (int)get_user_id()) {
                    $myPasienId = $p['id_pasien'];
                    $_SESSION['user']['id_pasien'] = $myPasienId;
                    break;
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pasien = ($role === 'pasien') ? $myPasienId : (int)($_POST['id_pasien'] ?? 0);
    $id_poli = (int)($_POST['id_poli'] ?? 0);
    $id_dokter = (int)($_POST['id_dokter'] ?? 0);
    $tanggal_kunjungan = trim($_POST['tanggal_kunjungan'] ?? date('Y-m-d'));
    $keluhan = trim($_POST['keluhan'] ?? '');

    if (empty($id_pasien) || empty($id_poli) || empty($id_dokter) || empty($tanggal_kunjungan)) {
        $errorMessage = 'Silakan lengkapi pilihan pasien, poliklinik, dokter, dan tanggal kunjungan.';
    } else {
        $payload = [
            'id_pasien'         => $id_pasien,
            'id_poli'           => $id_poli,
            'id_dokter'         => $id_dokter,
            'tanggal_kunjungan' => $tanggal_kunjungan,
            'keluhan'           => $keluhan,
            'status'            => 'menunggu'
        ];

        $res = api_request('/kunjungan', 'POST', $payload);

        if ($res['status']) {
            set_flash('success', 'Pendaftaran kunjungan berhasil dibuat! Nomor antrean Anda sedang diproses.');
            header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal membuat kunjungan.';
        }
    }
}

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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="text-emerald text-decoration-none">Kunjungan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Daftar Baru</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Daftar Janji Kunjungan Dokter</h3>
                <p class="text-muted small mb-0">Pilih poliklinik tujuan dan jadwal pemeriksaan yang diinginkan</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-modern border-0 p-4">
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?php echo htmlspecialchars($errorMessage); ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <!-- Pilihan Pasien -->
                        <?php if ($role === 'admin'): ?>
                            <div class="mb-3">
                                <label for="id_pasien" class="form-label fw-semibold text-secondary">Pilih Pasien <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_pasien" name="id_pasien" required autofocus>
                                    <option value="">-- Pilih Pasien Terdaftar --</option>
                                    <?php foreach ($listPasien as $p): ?>
                                        <option value="<?php echo $p['id_pasien']; ?>">
                                            [<?php echo htmlspecialchars($p['no_rm']); ?>] <?php echo htmlspecialchars($p['nama_pasien']); ?> - NIK: <?php echo htmlspecialchars($p['nik'] ?? '-'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-mint rounded-3 border border-mint mb-3">
                                <div class="small text-muted mb-1">Pasien Pendaftar:</div>
                                <div class="fw-bold text-emerald-dark fs-5">
                                    <i class="bi bi-person-check-fill me-1"></i> <?php echo htmlspecialchars(get_user_name()); ?>
                                </div>
                                <div class="small text-muted">Nomor Rekam Medis: <strong><?php echo htmlspecialchars($_SESSION['user']['no_rm'] ?? '-'); ?></strong></div>
                            </div>
                        <?php endif; ?>

                        <!-- Poliklinik & Dokter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_poli" class="form-label fw-semibold text-secondary">Poliklinik Tujuan <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_poli" name="id_poli" required onchange="filterDokterByPoli(this.value);">
                                    <option value="">-- Pilih Poliklinik --</option>
                                    <?php foreach ($listPoli as $poli): ?>
                                        <option value="<?php echo $poli['id_poli']; ?>">
                                            <?php echo htmlspecialchars($poli['nama_poli']); ?> (<?php echo htmlspecialchars($poli['lokasi'] ?? ''); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_dokter" class="form-label fw-semibold text-secondary">Dokter Pemeriksa <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_dokter" name="id_dokter" required>
                                    <option value="">-- Pilih Dokter --</option>
                                    <?php foreach ($listDokter as $doc): ?>
                                        <option value="<?php echo $doc['id_dokter']; ?>" data-poli="<?php echo $doc['id_poli']; ?>">
                                            <?php echo htmlspecialchars($doc['nama_dokter']); ?> (<?php echo htmlspecialchars($doc['spesialisasi'] ?? 'Umum'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tanggal Kunjungan -->
                        <div class="mb-3">
                            <label for="tanggal_kunjungan" class="form-label fw-semibold text-secondary">Tanggal Rencana Kunjungan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Keluhan Awal Pasien -->
                        <div class="mb-4">
                            <label for="keluhan" class="form-label fw-semibold text-secondary">Keluhan yang Dirasakan</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="4" placeholder="Jelaskan gejala atau keluhan kesehatan yang dialami (misal: pusing berputar, batuk 3 hari, nyeri ulu hati)..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-calendar-check-fill me-1"></i> Daftarkan Kunjungan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Petunjuk Pasien -->
            <div class="col-lg-4">
                <div class="card card-modern border-0 p-4 bg-mint border-mint mb-3">
                    <h6 class="fw-bold text-emerald-dark mb-2"><i class="bi bi-info-circle-fill me-1"></i> Informasi Antrean</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="bi bi-check2 text-emerald me-1"></i> Status awal kunjungan adalah <strong>Menunggu</strong> giliran.</li>
                        <li class="mb-2"><i class="bi bi-check2 text-emerald me-1"></i> Silakan tiba di rumah sakit 15 menit sebelum poli dibuka.</li>
                        <li class="mb-0"><i class="bi bi-check2 text-emerald me-1"></i> Membawa kartu identitas (KTP) dan kartu asuransi (bila menggunakan).</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function filterDokterByPoli(poliId) {
    const dokterSelect = document.getElementById('id_dokter');
    const options = dokterSelect.querySelectorAll('option');

    let firstMatch = '';
    options.forEach(opt => {
        if (!opt.value) return; // Keep placeholder
        const docPoli = opt.getAttribute('data-poli');
        if (!poliId || docPoli === poliId) {
            opt.style.display = '';
            if (!firstMatch) firstMatch = opt.value;
        } else {
            opt.style.display = 'none';
        }
    });

    if (firstMatch) {
        dokterSelect.value = firstMatch;
    } else {
        dokterSelect.value = '';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
