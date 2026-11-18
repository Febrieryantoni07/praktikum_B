<?php
/**
 * Modul Poliklinik - Ubah Poliklinik (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Ubah Poliklinik';
$currentMenu = 'poliklinik';
$errorMessage = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID Poliklinik tidak valid.');
    header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
    exit;
}

// Ambil data poliklinik saat ini dari API
$resGet = api_request('/poliklinik', 'GET', ['id' => $id]);
if (!$resGet['status'] || empty($resGet['data'])) {
    set_flash('danger', 'Data poliklinik tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
    exit;
}

$poli = is_array($resGet['data']) && isset($resGet['data'][0]) ? $resGet['data'][0] : $resGet['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_poli = trim($_POST['nama_poli'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nama_poli)) {
        $errorMessage = 'Nama Poliklinik wajib diisi.';
    } else {
        $payload = [
            'id_poli' => $id,
            'nama_poli' => $nama_poli,
            'lokasi' => $lokasi,
            'keterangan' => $keterangan
        ];

        $res = api_request('/poliklinik', 'PUT', $payload);

        if ($res['status']) {
            set_flash('success', 'Poliklinik berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/poliklinik/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal memperbarui poliklinik.';
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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/poliklinik/index.php" class="text-emerald text-decoration-none">Poliklinik</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ubah #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Ubah Data Poliklinik</h3>
                <p class="text-muted small mb-0">Perbarui informasi unit pelayanan medis</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/poliklinik/index.php" class="btn btn-outline-secondary">
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
                        <div class="mb-3">
                            <label for="nama_poli" class="form-label fw-semibold text-secondary">Nama Poliklinik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_poli" name="nama_poli" value="<?php echo htmlspecialchars($poli['nama_poli'] ?? ''); ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="lokasi" class="form-label fw-semibold text-secondary">Lokasi Ruang / Gedung</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars($poli['lokasi'] ?? ''); ?>">
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Deskripsi Layanan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo htmlspecialchars($poli['keterangan'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/poliklinik/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Perbarui Poliklinik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
