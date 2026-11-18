<?php
/**
 * Modul Kunjungan - Ubah Data Kunjungan (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID Kunjungan tidak valid.');
    header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
    exit;
}

$pageTitle = 'Ubah Kunjungan #' . $id;
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

// Ambil poliklinik dan dokter
$resPoli = api_request('/poliklinik');
$listPoli = ($resPoli['status'] && is_array($resPoli['data'])) ? $resPoli['data'] : [];

$resDokter = api_request('/dokter');
$listDokter = ($resDokter['status'] && is_array($resDokter['data'])) ? $resDokter['data'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_poli = (int)($_POST['id_poli'] ?? ($k['id_poli'] ?? 0));
    $id_dokter = (int)($_POST['id_dokter'] ?? ($k['id_dokter'] ?? 0));
    $tanggal_kunjungan = trim($_POST['tanggal_kunjungan'] ?? ($k['tanggal_kunjungan'] ?? ''));
    $keluhan = trim($_POST['keluhan'] ?? '');
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? ($k['status'] ?? 'menunggu'));

    $payload = [
        'id_kunjungan'      => $id,
        'id_poli'           => $id_poli,
        'id_dokter'         => $id_dokter,
        'tanggal_kunjungan' => $tanggal_kunjungan,
        'keluhan'           => $keluhan,
        'diagnosis'         => $diagnosis,
        'tindakan'          => $tindakan,
        'status'            => $status
    ];

    $res = api_request('/kunjungan', 'PUT', $payload);

    if ($res['status']) {
        set_flash('success', 'Data kunjungan #' . $id . ' berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/modules/kunjungan/index.php');
        exit;
    } else {
        $errorMessage = $res['message'] ?? 'Gagal memperbarui data kunjungan.';
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
                        <li class="breadcrumb-item active" aria-current="page">Ubah #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Ubah Data Kunjungan</h3>
                <p class="text-muted small mb-0">Pasien: <strong><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></strong> (<?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?>)</p>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_poli" class="form-label fw-semibold text-secondary">Poliklinik</label>
                                <select class="form-select" id="id_poli" name="id_poli" required>
                                    <?php foreach ($listPoli as $p): ?>
                                        <option value="<?php echo $p['id_poli']; ?>" <?php echo ((int)$p['id_poli'] === (int)($k['id_poli'] ?? 0)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['nama_poli']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_dokter" class="form-label fw-semibold text-secondary">Dokter</label>
                                <select class="form-select" id="id_dokter" name="id_dokter" required>
                                    <?php foreach ($listDokter as $d): ?>
                                        <option value="<?php echo $d['id_dokter']; ?>" <?php echo ((int)$d['id_dokter'] === (int)($k['id_dokter'] ?? 0)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($d['nama_dokter']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_kunjungan" class="form-label fw-semibold text-secondary">Tanggal Kunjungan</label>
                                <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" value="<?php echo htmlspecialchars($k['tanggal_kunjungan'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold text-secondary">Status Kunjungan</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="menunggu" <?php echo (($k['status'] ?? '') === 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="diperiksa" <?php echo (($k['status'] ?? '') === 'diperiksa') ? 'selected' : ''; ?>>Sedang Diperiksa</option>
                                    <option value="selesai" <?php echo (($k['status'] ?? '') === 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="batal" <?php echo (($k['status'] ?? '') === 'batal') ? 'selected' : ''; ?>>Dibatalkan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keluhan" class="form-label fw-semibold text-secondary">Keluhan Pasien</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3"><?php echo htmlspecialchars($k['keluhan'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label fw-semibold text-secondary">Diagnosa Medis</label>
                            <input type="text" class="form-control" id="diagnosis" name="diagnosis" value="<?php echo htmlspecialchars($k['diagnosis'] ?? ''); ?>">
                        </div>

                        <div class="mb-4">
                            <label for="tindakan" class="form-label fw-semibold text-secondary">Tindakan Medis / Resep Obat</label>
                            <textarea class="form-control" id="tindakan" name="tindakan" rows="3"><?php echo htmlspecialchars($k['tindakan'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Perbarui Kunjungan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
