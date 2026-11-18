<?php
/**
 * Modul Pengguna - Tambah Akun Pengguna Baru (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Tambah Pengguna';
$currentMenu = 'pengguna';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $role = trim($_POST['role'] ?? 'pasien');
    $status = trim($_POST['status'] ?? 'aktif');

    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $errorMessage = 'Username, Password, dan Nama Lengkap wajib diisi.';
    } else {
        $payload = [
            'username'     => $username,
            'password'     => $password,
            'nama_lengkap' => $nama_lengkap,
            'role'         => $role,
            'status'       => $status
        ];

        $res = api_request('/pengguna', 'POST', $payload);

        if ($res['status']) {
            set_flash('success', 'Akun pengguna "' . htmlspecialchars($username) . '" berhasil dibuat.');
            header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal membuat akun pengguna.';
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
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/modules/pengguna/index.php" class="text-emerald text-decoration-none">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Tambah Akun Pengguna Baru</h3>
                <p class="text-muted small mb-0">Daftarkan akun login dengan penetapan peran RBAC</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/modules/pengguna/index.php" class="btn btn-outline-secondary">
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
                            <label for="nama_lengkap" class="form-label fw-semibold text-secondary">Nama Lengkap Pemilik Akun <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Muhammad Rian / dr. Ahmad" required autofocus>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-semibold text-secondary">Username Login <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Huruf kecil tanpa spasi" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-semibold text-secondary">Peran Akun (Role RBAC) <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="pasien">Pasien (Akses Mandiri & Riwayat)</option>
                                    <option value="dokter">Dokter (Pemeriksaan & Tindakan)</option>
                                    <option value="admin">Administrator (Akses Penuh Sistem)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold text-secondary">Status Akun</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="aktif">Aktif (Dapat Login)</option>
                                    <option value="nonaktif">Nonaktif (Dibekukan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/pengguna/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Simpan Akun Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
