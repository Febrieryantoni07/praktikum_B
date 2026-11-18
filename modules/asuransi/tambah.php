<?php
/**
 * Modul Asuransi - Tambah Asuransi Baru (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Tambah Asuransi';
$currentMenu = 'asuransi';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_asuransi = trim($_POST['nama_asuransi'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $no_kontak = trim($_POST['no_kontak'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nama_asuransi)) {
        $errorMessage = 'Nama Asuransi wajib diisi.';
    } else {
        $payload = [
            'nama_asuransi' => $nama_asuransi,
            'jenis' => $jenis,
            'no_kontak' => $no_kontak,
            'keterangan' => $keterangan
        ];

        $res = api_request('/asuransi', 'POST', $payload);

        if ($res['status']) {
            set_flash('success', 'Asuransi "' . htmlspecialchars($nama_asuransi) . '" berhasil ditambahkan.');
            header('Location: ' . BASE_URL . '/modules/asuransi/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal menambahkan asuransi.';
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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/asuransi/index.php" class="text-emerald text-decoration-none">Asuransi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Tambah Asuransi Rekanan</h3>
                <p class="text-muted small mb-0">Daftarkan mitra asuransi kesehatan baru</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/asuransi/index.php" class="btn btn-outline-secondary">
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
                            <label for="nama_asuransi" class="form-label fw-semibold text-secondary">Nama Asuransi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_asuransi" name="nama_asuransi" placeholder="Contoh: BPJS Kesehatan / Mandiri Inhealth" required autofocus>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="jenis" class="form-label fw-semibold text-secondary">Jenis Asuransi</label>
                                <select class="form-select" id="jenis" name="jenis">
                                    <option value="Pemerintah">Pemerintah / BPJS</option>
                                    <option value="Swasta" selected>Swasta</option>
                                    <option value="Perusahaan">Korporasi / Perusahaan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="no_kontak" class="form-label fw-semibold text-secondary">No. Kontak / Call Center</label>
                                <input type="text" class="form-control" id="no_kontak" name="no_kontak" placeholder="Contoh: 1500400 atau 021-xxxx">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Catatan Layanan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Melayani rawat jalan dan rawat inap dengan rujukan faskes..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/asuransi/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Simpan Asuransi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
