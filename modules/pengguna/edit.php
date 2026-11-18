<?php
/**
 * Modul Pengguna - Ubah Akun Pengguna / Reset Password (Admin Only)
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/api.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_role('admin');

$pageTitle = 'Ubah Pengguna';
$currentMenu = 'pengguna';
$errorMessage = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('danger', 'ID Pengguna tidak valid.');
    header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
    exit;
}

// Ambil data pengguna saat ini
$resUser = api_request('/pengguna', 'GET', ['id' => $id]);
if (!$resUser['status'] || empty($resUser['data'])) {
    set_flash('danger', 'Data pengguna tidak ditemukan.');
    header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
    exit;
}
$user = is_array($resUser['data']) && isset($resUser['data'][0]) ? $resUser['data'][0] : $resUser['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $role = trim($_POST['role'] ?? ($user['role'] ?? 'pasien'));
    $status = trim($_POST['status'] ?? ($user['status'] ?? 'aktif'));
    $password = trim($_POST['password'] ?? '');

    if (empty($nama_lengkap)) {
        $errorMessage = 'Nama Lengkap wajib diisi.';
    } else {
        $payload = [
            'id_pengguna'  => $id,
            'nama_lengkap' => $nama_lengkap,
            'role'         => $role,
            'status'       => $status
        ];

        // Jika password diisi, sertakan untuk reset password
        if (!empty($password)) {
            $payload['password'] = $password;
        }

        $res = api_request('/pengguna', 'PUT', $payload);

        if ($res['status']) {
            set_flash('success', 'Akun pengguna berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/modules/pengguna/index.php');
            exit;
        } else {
            $errorMessage = $res['message'] ?? 'Gagal memperbarui akun pengguna.';
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
                        <li class="breadcrumb-item active" aria-current="page">Ubah #<?php echo $id; ?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Ubah Akun Pengguna</h3>
                <p class="text-muted small mb-0">Username: <strong><?php echo htmlspecialchars($user['username'] ?? '-'); ?></strong></p>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary">Username (Tetap)</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>" required autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-semibold text-secondary">Peran Akun (Role RBAC)</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="pasien" <?php echo (($user['role'] ?? '') === 'pasien') ? 'selected' : ''; ?>>Pasien</option>
                                    <option value="dokter" <?php echo (($user['role'] ?? '') === 'dokter') ? 'selected' : ''; ?>>Dokter</option>
                                    <option value="admin" <?php echo (($user['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold text-secondary">Status Akun</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="aktif" <?php echo (($user['status'] ?? '') === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="nonaktif" <?php echo (($user['status'] ?? '') === 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-3 bg-mint rounded-3 border border-mint mb-4">
                            <label for="password" class="form-label fw-semibold text-emerald-dark small mb-1">
                                <i class="bi bi-shield-lock-fill me-1"></i> Reset Password (Opsional)
                            </label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti password lama">
                            <div class="text-muted small mt-1">Hanya isi kolom ini jika Anda ingin mengubah password akun pengguna.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <a href="<?php echo BASE_URL; ?>/modules/pengguna/index.php" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-emerald">
                                <i class="bi bi-save me-1"></i> Perbarui Akun
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
