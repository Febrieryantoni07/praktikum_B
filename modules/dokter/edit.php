<?php
/**
 * Modul Dokter - Ubah Data Dokter
 * Matriks RBAC: Admin atau Dokter (profil miliknya)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$myDokterId = get_dokter_id();
$id = (int)($_GET['id'] ?? 0);

// Cek hak akses: hanya admin atau dokter bersangkutan
if ($role !== 'admin' && ($role !== 'dokter' || (int)$myDokterId !== $id)) {
    header('Location: ' . BASE_URL . '/403.php');
    exit;
}

$pageTitle = 'Ubah Data Dokter';
$currentMenu = 'dokter';
$errorMessage = '';

// Ambil data dokter saat ini
$resDoc = api_request('/dokter', 'GET', ['id' => $id]);
if (!$resDoc['status'] || empty($resDoc['data'])) {
    set_flash('danger', 'Data dokter tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/dokter/index.php');
    exit;
}
$doc = is_array($resDoc['data']) && isset($resDoc['data'][0]) ? $resDoc['data'][0] : $resDoc['data'];

// Ambil daftar poliklinik
$resPoli = api_request('/poliklinik');
$listPoli = ($resPoli['status'] && is_array($resPoli['data'])) ? $resPoli['data'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter = trim($_POST['nama_dokter'] ?? '');
    $id_poli = (int)($_POST['id_poli'] ?? ($doc['id_poli'] ?? 0));
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_str = trim($_POST['no_str'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');

    if (empty($nama_dokter) || $id_poli <= 0) {
        $errorMessage = 'Nama dokter dan poliklinik wajib diisi.';
    } else {
        $payload = [
            'id_dokter'    => $id,
            'nama_dokter'  => $nama_dokter,
            'id_poli'      => $id_poli,
            'spesialisasi' => $spesialisasi,
            'no_str'       => $no_str,
            'no_hp'        => $no_hp
        ];

        $res = api_request('/dokter', 'PUT', $payload);

        if ($res['status']) {
            set_flash('success', 'Data dokter berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/dokter/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal memperbarui data dokter.';
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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/dokter/index.php" class="text-emerald text-decoration-none">Dokter</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ubah #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Ubah Data Dokter</h3>
                <p class="text-muted small mb-0">Perbarui spesialisasi, poliklinik, atau kontak dokter</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/dokter/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-9">
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
                            <div class="col-md-7">
                                <label for="nama_dokter" class="form-label fw-semibold text-secondary">Nama Lengkap Dokter <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" value="<?php echo htmlspecialchars($doc['nama_dokter'] ?? ''); ?>" required autofocus>
                            </div>
                            <div class="col-md-5">
                                <label for="id_poli" class="form-label fw-semibold text-secondary">Poliklinik Bertugas <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_poli" name="id_poli" required <?php echo ($role === 'dokter') ? 'disabled' : ''; ?>>
                                    <?php foreach ($listPoli as $p): ?>
                                        <option value="<?php echo $p['id_poli']; ?>" <?php echo ((int)$p['id_poli'] === (int)($doc['id_poli'] ?? 0)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['nama_poli']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($role === 'dokter'): ?>
                                    <input type="hidden" name="id_poli" value="<?php echo htmlspecialchars($doc['id_poli'] ?? 0); ?>">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="spesialisasi" class="form-label fw-semibold text-secondary">Spesialisasi</label>
                                <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" value="<?php echo htmlspecialchars($doc['spesialisasi'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="no_str" class="form-label fw-semibold text-secondary">Nomor STR</label>
                                <input type="text" class="form-control" id="no_str" name="no_str" value="<?php echo htmlspecialchars($doc['no_str'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="no_hp" class="form-label fw-semibold text-secondary">No. HP / WhatsApp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($doc['no_hp'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/dokter/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Perbarui Dokter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
