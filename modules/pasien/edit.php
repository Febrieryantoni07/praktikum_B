<?php
/**
 * Modul Pasien - Ubah Data Pasien
 * Matriks RBAC: Admin atau Pasien (data miliknya)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();

$role = get_user_role();
$myPasienId = get_pasien_id();
$id = (int)($_GET['id'] ?? 0);

// Cek hak akses
if ($role !== 'admin' && ($role !== 'pasien' || (int)$myPasienId !== $id)) {
    header('Location: ' . BASE_URL . '/403.php');
    exit;
}

$pageTitle = 'Ubah Data Pasien';
$currentMenu = ($role === 'pasien') ? 'pasien_profil' : 'pasien';
$errorMessage = '';

// Ambil data pasien saat ini
$resPas = api_request('/pasien', 'GET', ['id' => $id]);
if (!$resPas['status'] || empty($resPas['data'])) {
    set_flash('danger', 'Data pasien tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/pasien/index.php');
    exit;
}
$pas = is_array($resPas['data']) && isset($resPas['data'][0]) ? $resPas['data'][0] : $resPas['data'];

// Ambil data asuransi
$resAsu = api_request('/asuransi');
$listAsuransi = ($resAsu['status'] && is_array($resAsu['data'])) ? $resAsu['data'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pasien = trim($_POST['nama_pasien'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? ($pas['jenis_kelamin'] ?? 'L'));
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $id_asuransi = !empty($_POST['id_asuransi']) ? (int)$_POST['id_asuransi'] : null;
    $no_kartu_asuransi = trim($_POST['no_kartu_asuransi'] ?? '');

    if (empty($nama_pasien)) {
        $errorMessage = 'Nama Pasien wajib diisi.';
    } else {
        $payload = [
            'id_pasien'         => $id,
            'nik'               => $nik,
            'nama_pasien'       => $nama_pasien,
            'jenis_kelamin'     => $jenis_kelamin,
            'tanggal_lahir'     => $tanggal_lahir ?: null,
            'alamat'            => $alamat,
            'no_hp'             => $no_hp,
            'id_asuransi'       => $id_asuransi,
            'no_kartu_asuransi' => $no_kartu_asuransi
        ];

        $res = api_request('/pasien', 'PUT', $payload);

        if ($res['status']) {
            set_flash('success', 'Data pasien berhasil diperbarui.');
            if ($role === 'pasien') {
                header('Location: ' . BASE_URL . '/modules/pasien/profil.php');
            } else {
                header('Location: ' . BASE_URL . '/modules/pasien/index.php');
            }
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal memperbarui data pasien.';
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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="text-emerald text-decoration-none">Pasien</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ubah #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">
                    <?php echo ($role === 'pasien') ? 'Profil Biodata Saya' : 'Ubah Data Pasien'; ?>
                </h3>
                <p class="text-muted small mb-0">No. Rekam Medis: <strong><?php echo htmlspecialchars($pas['no_rm'] ?? '-'); ?></strong></p>
            </div>
            <?php if ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            <?php endif; ?>
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
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">No. Rekam Medis (RM)</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($pas['no_rm'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-8">
                                <label for="nama_pasien" class="form-label fw-semibold text-secondary">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="<?php echo htmlspecialchars($pas['nama_pasien'] ?? ''); ?>" required autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="nik" class="form-label fw-semibold text-secondary">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" value="<?php echo htmlspecialchars($pas['nik'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_kelamin" class="form-label fw-semibold text-secondary">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" <?php echo ($role === 'pasien') ? 'disabled' : ''; ?>>
                                    <option value="L" <?php echo (($pas['jenis_kelamin'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo (($pas['jenis_kelamin'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                                <?php if ($role === 'pasien'): ?>
                                    <input type="hidden" name="jenis_kelamin" value="<?php echo htmlspecialchars($pas['jenis_kelamin'] ?? 'L'); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_lahir" class="form-label fw-semibold text-secondary">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($pas['tanggal_lahir'] ?? ''); ?>" <?php echo ($role === 'pasien') ? 'readonly' : ''; ?>>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label fw-semibold text-secondary">No. HP / WhatsApp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($pas['no_hp'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="alamat" class="form-label fw-semibold text-secondary">Alamat Tempat Tinggal</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo htmlspecialchars($pas['alamat'] ?? ''); ?>">
                            </div>
                        </div>

                        <h6 class="fw-bold text-emerald-dark mt-4 mb-3"><i class="bi bi-shield-check me-1"></i> Asuransi & Penjamin</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="id_asuransi" class="form-label fw-semibold text-secondary">Asuransi</label>
                                <select class="form-select" id="id_asuransi" name="id_asuransi">
                                    <option value="">-- Umum / Pribadi (Tanpa Asuransi) --</option>
                                    <?php foreach ($listAsuransi as $a): ?>
                                        <option value="<?php echo $a['id_asuransi']; ?>" <?php echo ((int)$a['id_asuransi'] === (int)($pas['id_asuransi'] ?? 0)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['nama_asuransi']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="no_kartu_asuransi" class="form-label fw-semibold text-secondary">Nomor Kartu Asuransi</label>
                                <input type="text" class="form-control" id="no_kartu_asuransi" name="no_kartu_asuransi" value="<?php echo htmlspecialchars($pas['no_kartu_asuransi'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <?php if ($role === 'admin'): ?>
                                <a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="btn btn-light border">Batal</a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Perbarui Biodata Pasien
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
