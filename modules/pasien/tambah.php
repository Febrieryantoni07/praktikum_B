<?php
/**
 * Modul Pasien - Registrasi Pasien Baru (Admin Only)
 * Mendukung pembuatan akun login sekaligus (opsional)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Registrasi Pasien Baru';
$currentMenu = 'pasien';
$errorMessage = '';

// Ambil data asuransi untuk dropdown
$resAsu = api_request('/asuransi');
$listAsuransi = ($resAsu['status'] && is_array($resAsu['data'])) ? $resAsu['data'] : [];

// Generate default nomor RM
$defaultNoRm = 'RM-' . date('ym') . rand(100, 999);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_rm = trim($_POST['no_rm'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $nama_pasien = trim($_POST['nama_pasien'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? 'L');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $id_asuransi = !empty($_POST['id_asuransi']) ? (int)$_POST['id_asuransi'] : null;
    $no_kartu_asuransi = trim($_POST['no_kartu_asuransi'] ?? '');

    $buat_akun = isset($_POST['buat_akun']) && $_POST['buat_akun'] === '1';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nama_pasien) || empty($no_rm)) {
        $errorMessage = 'Nomor RM dan Nama Pasien wajib diisi.';
    } elseif ($buat_akun && (empty($username) || empty($password))) {
        $errorMessage = 'Jika membuat akun login pasien, username dan password wajib diisi.';
    } else {
        $payload = [
            'no_rm'             => $no_rm,
            'nik'               => $nik,
            'nama_pasien'       => $nama_pasien,
            'jenis_kelamin'     => $jenis_kelamin,
            'tanggal_lahir'     => $tanggal_lahir ?: null,
            'alamat'            => $alamat,
            'no_hp'             => $no_hp,
            'id_asuransi'       => $id_asuransi,
            'no_kartu_asuransi' => $no_kartu_asuransi,
            'buat_akun'         => $buat_akun,
            'username'          => $username,
            'password'          => $password
        ];

        $res = api_request('/pasien', 'POST', $payload);

        if ($res['status']) {
            set_flash('success', 'Pasien "' . htmlspecialchars($nama_pasien) . '" (No. RM: ' . htmlspecialchars($no_rm) . ') berhasil didaftarkan.');
            header('Location: ' . BASE_URL . '/modules/pasien/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal mendaftarkan pasien.';
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
                        <li class="breadcrumb-item active" aria-current="page">Registrasi</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Registrasi Pasien Baru</h3>
                <p class="text-muted small mb-0">Input berkas rekam medis dan buatkan akun login bila diperlukan</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="btn btn-outline-secondary">
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
                        <h6 class="fw-bold text-emerald-dark mb-3"><i class="bi bi-person-badge-fill me-1"></i> Data Identitas Pasien</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="no_rm" class="form-label fw-semibold text-secondary">No. Rekam Medis (RM) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control fw-bold text-emerald-dark" id="no_rm" name="no_rm" value="<?php echo htmlspecialchars($defaultNoRm); ?>" required>
                            </div>
                            <div class="col-md-8">
                                <label for="nama_pasien" class="form-label fw-semibold text-secondary">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" placeholder="Masukkan nama lengkap" required autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="nik" class="form-label fw-semibold text-secondary">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="16 digit NIK">
                            </div>
                            <div class="col-md-4">
                                <label for="jenis_kelamin" class="form-label fw-semibold text-secondary">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_lahir" class="form-label fw-semibold text-secondary">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label fw-semibold text-secondary">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 08123456789">
                            </div>
                            <div class="col-md-6">
                                <label for="alamat" class="form-label fw-semibold text-secondary">Alamat Tempat Tinggal</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Jl. Anggrek No. 12, Kelurahan...">
                            </div>
                        </div>

                        <h6 class="fw-bold text-emerald-dark mt-4 mb-3"><i class="bi bi-shield-check me-1"></i> Penjamin & Asuransi</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_asuransi" class="form-label fw-semibold text-secondary">Asuransi / Jaminan</label>
                                <select class="form-select" id="id_asuransi" name="id_asuransi">
                                    <option value="">-- Umum / Mandiri (Tanpa Asuransi) --</option>
                                    <?php foreach ($listAsuransi as $a): ?>
                                        <option value="<?php echo $a['id_asuransi']; ?>"><?php echo htmlspecialchars($a['nama_asuransi']); ?> (<?php echo htmlspecialchars($a['jenis'] ?? 'Swasta'); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="no_kartu_asuransi" class="form-label fw-semibold text-secondary">Nomor Kartu Asuransi</label>
                                <input type="text" class="form-control" id="no_kartu_asuransi" name="no_kartu_asuransi" placeholder="Nomor peserta BPJS / kartu asuransi">
                            </div>
                        </div>

                        <!-- Opsi Akun Login Pasien -->
                        <div class="p-3 bg-mint rounded-3 border border-mint mt-4 mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="buat_akun" name="buat_akun" value="1" onchange="document.getElementById('akunSection').style.display = this.checked ? 'block' : 'none';">
                                <label class="form-check-label fw-bold text-emerald-dark" for="buat_akun">
                                    <i class="bi bi-key-fill me-1"></i> Sekaligus Buat Akun Login untuk Pasien Ini
                                </label>
                            </div>

                            <div id="akunSection" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="username" class="form-label fw-semibold text-secondary small">Username Akun</label>
                                        <input type="text" class="form-control form-control-sm" id="username" name="username" placeholder="Contoh: pasien1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold text-secondary small">Password Akun</label>
                                        <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="Password akun pasien">
                                    </div>
                                </div>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Akun akan otomatis terhubung ke pasien ini dengan role <strong>pasien</strong>.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Simpan Data Pasien
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
