<?php
/**
 * Modul Dokter - Tambah Dokter Baru (Admin Only)
 * Mendukung pembuatan akun login sekaligus (opsional)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Tambah Dokter';
$currentMenu = 'dokter';
$errorMessage = '';

// Ambil daftar poliklinik untuk dropdown
$resPoli = api_request('/poliklinik');
$listPoli = ($resPoli['status'] && is_array($resPoli['data'])) ? $resPoli['data'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter = trim($_POST['nama_dokter'] ?? '');
    $id_poli = (int)($_POST['id_poli'] ?? 0);
    $spesialisasi = trim($_POST['spesialisasi'] ?? '');
    $no_str = trim($_POST['no_str'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');

    $buat_akun = isset($_POST['buat_akun']) && $_POST['buat_akun'] === '1';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nama_dokter) || $id_poli <= 0) {
        $errorMessage = 'Nama dokter dan poliklinik wajib dipilih.';
    } elseif ($buat_akun && (empty($username) || empty($password))) {
        $errorMessage = 'Jika membuat akun baru, username dan password wajib diisi.';
    } else {
        $payload = [
            'nama_dokter'  => $nama_dokter,
            'id_poli'      => $id_poli,
            'spesialisasi' => $spesialisasi,
            'no_str'       => $no_str,
            'no_hp'        => $no_hp,
            'buat_akun'    => $buat_akun,
            'username'     => $username,
            'password'     => $password
        ];

        $res = api_request('/dokter', 'POST', $payload);

        if ($res['status']) {
            set_flash('success', 'Dokter "' . htmlspecialchars($nama_dokter) . '" berhasil didaftarkan.');
            header('Location: ' . BASE_URL . '/modules/dokter/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal menambahkan dokter.';
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
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Tambah Dokter Baru</h3>
                <p class="text-muted small mb-0">Input data tenaga medis dan buatkan akun login bila diperlukan</p>
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
                        <h6 class="fw-bold text-emerald-dark mb-3"><i class="bi bi-person-lines-fill me-1"></i> Data Tenaga Medis</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label for="nama_dokter" class="form-label fw-semibold text-secondary">Nama Lengkap Dokter (beserta gelar) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" placeholder="Contoh: dr. Budi Santoso, Sp.PD" required autofocus>
                            </div>
                            <div class="col-md-5">
                                <label for="id_poli" class="form-label fw-semibold text-secondary">Poliklinik Bertugas <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_poli" name="id_poli" required>
                                    <option value="">-- Pilih Poliklinik --</option>
                                    <?php foreach ($listPoli as $p): ?>
                                        <option value="<?php echo $p['id_poli']; ?>"><?php echo htmlspecialchars($p['nama_poli']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="spesialisasi" class="form-label fw-semibold text-secondary">Spesialisasi</label>
                                <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" placeholder="Contoh: Penyakit Dalam / Gigi">
                            </div>
                            <div class="col-md-4">
                                <label for="no_str" class="form-label fw-semibold text-secondary">Nomor STR</label>
                                <input type="text" class="form-control" id="no_str" name="no_str" placeholder="Nomor Surat Tanda Registrasi">
                            </div>
                            <div class="col-md-4">
                                <label for="no_hp" class="form-label fw-semibold text-secondary">No. HP / WhatsApp</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 08123456789">
                            </div>
                        </div>

                        <!-- Opsi Akun Login -->
                        <div class="p-3 bg-mint rounded-3 border border-mint mt-4 mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="buat_akun" name="buat_akun" value="1" onchange="document.getElementById('akunSection').style.display = this.checked ? 'block' : 'none';">
                                <label class="form-check-label fw-bold text-emerald-dark" for="buat_akun">
                                    <i class="bi bi-key-fill me-1"></i> Sekaligus Buat Akun Pengguna Login untuk Dokter Ini
                                </label>
                            </div>

                            <div id="akunSection" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="username" class="form-label fw-semibold text-secondary small">Username Akun</label>
                                        <input type="text" class="form-control form-control-sm" id="username" name="username" placeholder="Contoh: dr_budi">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold text-secondary small">Password Akun</label>
                                        <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="Password akun dokter">
                                    </div>
                                </div>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Akun akan otomatis terhubung ke dokter ini dengan role <strong>dokter</strong>.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/dokter/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Simpan Dokter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
