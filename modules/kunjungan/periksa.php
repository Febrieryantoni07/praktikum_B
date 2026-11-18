<?php
/**
 * Modul Kunjungan - Pemeriksaan Medis Pasien (Dokter & Admin)
 * Dokter memasukkan Diagnosa, Tindakan Medis, Resep Obat, dan memperbarui Status Kunjungan
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role(['dokter', 'admin']);

$role = get_user_role();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID Kunjungan tidak valid.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}

$pageTitle = 'Pemeriksaan Pasien';
$currentMenu = 'kunjungan';
$errorMessage = '';

// Ambil data kunjungan saat ini
$resK = api_request('/kunjungan', 'GET', ['id' => $id]);
if (!$resK['status'] || empty($resK['data'])) {
    set_flash('danger', 'Data kunjungan tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}
$k = is_array($resK['data']) && isset($resK['data'][0]) ? $resK['data'][0] : $resK['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? 'selesai');

    if (empty($diagnosis)) {
        $errorMessage = 'Diagnosa medis wajib diisi oleh dokter.';
    } else {
        $payload = [
            'id_kunjungan' => $id,
            'diagnosis'    => $diagnosis,
            'tindakan'     => $tindakan,
            'status'       => $status
        ];

        $res = api_request('/kunjungan', 'PUT', $payload);

        if ($res['status']) {
            set_flash('success', 'Pemeriksaan dan diagnosis pasien berhasil disimpan!');
            header('Location: ' . BASE_URL . '/modules/kunjungan/detail.php?id=' . $id);
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal menyimpan hasil pemeriksaan.';
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
                        <li class="breadcrumb-item active" aria-current="page">Pemeriksaan #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Pemeriksaan & Diagnosis Pasien</h3>
                <p class="text-muted small mb-0">Pengisian rekam medis digital hasil pemeriksaan klinis</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            <!-- Informasi Pasien & Keluhan -->
            <div class="col-lg-4">
                <div class="card card-modern border-0 p-4 mb-3">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-bounding-box text-emerald fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Informasi Pasien</h5>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Nomor Rekam Medis (RM):</small>
                        <span class="badge bg-mint text-emerald-dark border fw-bold fs-6">
                            <?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Nama Lengkap:</small>
                        <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></div>
                        <small class="text-muted"><?php echo ($k['jenis_kelamin'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?></small>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Jaminan / Asuransi:</small>
                        <span class="badge bg-light text-secondary border">
                            <?php echo htmlspecialchars($k['nama_asuransi'] ?? 'Umum'); ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Poliklinik & Dokter:</small>
                        <div class="fw-semibold text-emerald-dark"><?php echo htmlspecialchars($k['nama_poli'] ?? '-'); ?></div>
                        <small class="text-muted"><?php echo htmlspecialchars($k['nama_dokter'] ?? '-'); ?></small>
                    </div>

                    <div class="p-3 bg-mint rounded-3 border border-mint">
                        <div class="small fw-bold text-emerald-dark mb-1">
                            <i class="bi bi-chat-left-dots-fill me-1"></i> Keluhan Awal Pasien:
                        </div>
                        <div class="small text-dark">
                            "<?php echo nl2br(htmlspecialchars($k['keluhan'] ?? 'Tidak ada keluhan tertulis.')); ?>"
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Diagnosa & Tindakan -->
            <div class="col-lg-8">
                <div class="card card-modern border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clipboard2-pulse-fill text-emerald fs-4"></i>
                            <h5 class="fw-bold text-dark mb-0">Form Hasil Pemeriksaan Klinis</h5>
                        </div>
                        <span class="badge bg-light text-secondary border">Status: <?php echo ucfirst($k['status'] ?? 'menunggu'); ?></span>
                    </div>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?php echo htmlspecialchars($errorMessage); ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label fw-bold text-dark">
                                Diagnosa Medis (Diagnosis) <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg fw-semibold" id="diagnosis" name="diagnosis" value="<?php echo htmlspecialchars($k['diagnosis'] ?? ''); ?>" placeholder="Contoh: Dispepsia Fungsional / Hipertensi Grade 1" required autofocus>
                            <div class="form-text small">Tuliskan nama diagnosa penyakit berdasarkan anamnesa dan pemeriksaan fisik.</div>
                        </div>

                        <div class="mb-4">
                            <label for="tindakan" class="form-label fw-bold text-dark">
                                Tindakan Medis & Resep Obat (Therapy & Prescription)
                            </label>
                            <textarea class="form-control" id="tindakan" name="tindakan" rows="5" placeholder="Contoh:&#10;1. Injeksi Ranitidine 50mg&#10;2. Antasida Doen tab 3x1 ac&#10;3. Omeprazole 20mg 2x1 ac&#10;4. Edukasi pola makan teratur dan kurangi makanan pedas/asam"><?php echo htmlspecialchars($k['tindakan'] ?? ''); ?></textarea>
                            <div class="form-text small">Catatan tindakan yang telah dilakukan serta instruksi resep obat untuk pasien.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Status Akhir Pemeriksaan</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusSelesai" value="selesai" <?php echo ($k['status'] !== 'diperiksa' && $k['status'] !== 'batal') ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-semibold text-success" for="statusSelesai">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai (Pemeriksaan Tuntas)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusDiperiksa" value="diperiksa" <?php echo ($k['status'] === 'diperiksa') ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-semibold text-primary" for="statusDiperiksa">
                                        <i class="bi bi-activity me-1"></i> Sedang Diperiksa (Observasi)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusBatal" value="batal" <?php echo ($k['status'] === 'batal') ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-semibold text-danger" for="statusBatal">
                                        <i class="bi bi-x-circle-fill me-1"></i> Batalkan Kunjungan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald px-4 py-2">
                                <i class="bi bi-save2-fill me-1"></i> Simpan Rekam Medis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
