<?php
/**
 * Halaman Login Sistem Informasi Rumah Sakit (Server A)
 * Berdasarkan PRD Bab 3.2 & 6.2
 * Mengirim kredensial username & password ke Server B endpoint /auth/login.php
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

// Jika sudah login, langsung alihkan ke dashboard
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$errorMessage = '';
$usernameVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameVal = trim($_POST['username'] ?? '');
    $passwordVal = trim($_POST['password'] ?? '');

    if (empty($usernameVal) || empty($passwordVal)) {
        $errorMessage = 'Silakan isi username dan password lengkap.';
    } else {
        // Panggil API Login di Server B
        $payload = [
            'username' => $usernameVal,
            'password' => $passwordVal
        ];

        $response = api_request('/auth/login.php', 'POST', $payload);

        if ($response['status'] && !empty($response['data']['token'])) {
            $data = $response['data'];

            // Simpan informasi autentikasi ke dalam Session Server A
            $_SESSION['token'] = $data['token'];
            $_SESSION['role'] = $data['role']; // admin, dokter, atau pasien
            $_SESSION['user'] = [
                'id_pengguna' => $data['id_pengguna'] ?? null,
                'username'    => $data['username'] ?? $usernameVal,
                'nama_lengkap'=> $data['nama_lengkap'] ?? 'Pengguna',
                'id_dokter'   => $data['id_dokter'] ?? null,
                'id_pasien'   => $data['id_pasien'] ?? null,
                'nama_poli'   => $data['nama_poli'] ?? null,
                'no_rm'       => $data['no_rm'] ?? null
            ];

            set_flash('success', 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['user']['nama_lengkap']) . '!');
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        } else {
            $errorMessage = $response['message'] ?? 'Login gagal. Periksa kembali username dan password Anda.';
        }
    }
}

$pageTitle = 'Login Sistem - ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="min-vh-100 d-flex flex-column justify-content-center bg-mint py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7 col-xl-6">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="text-decoration-none d-inline-flex align-items-center gap-2 mb-2">
                        <div class="brand-icon mx-auto shadow-sm" style="width: 48px; height: 48px; font-size: 1.6rem;">
                            <i class="bi bi-hospital"></i>
                        </div>
                    </a>
                    <h3 class="fw-bold text-emerald-dark mb-1"><?php echo APP_NAME; ?></h3>
                    <p class="text-muted small">Portal Masuk Terpadu Pasien, Dokter, dan Administrator</p>
                </div>

                <!-- Login Card -->
                <div class="card card-modern p-4 p-md-5 border-0 shadow-lg">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <h4 class="fw-bold mb-0 text-dark">Masuk ke Sistem</h4>
                        <span class="badge bg-mint text-emerald-dark border border-mint px-2 py-1 small">
                            <i class="bi bi-shield-lock-fill me-1"></i> RBAC SOA
                        </span>
                    </div>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?php echo htmlspecialchars($errorMessage); ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary small">Username Pengguna</label>
                            <div class="input-group">
                                <span class="input-group-text bg-mint border-mint text-emerald"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control py-2" id="username" name="username" value="<?php echo htmlspecialchars($usernameVal); ?>" placeholder="Masukkan username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-secondary small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-mint border-mint text-emerald"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control py-2" id="password" name="password" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-emerald w-100 py-2 fs-6 fw-bold mb-3 shadow">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>

                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>/index.php" class="text-decoration-none small text-muted">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                            </a>
                        </div>
                    </form>

                    <!-- Info Box Bantuan Pengujian (Praktikum SOA) -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="p-3 bg-mint rounded-3 border border-mint">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-bold text-emerald-dark"><i class="bi bi-key-fill me-1"></i> Akun Pengujian (Demo Seed):</span>
                            </div>
                            <div class="small text-muted" style="font-size: 0.78rem; line-height: 1.5;">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <strong>Admin:</strong><br>
                                        <code class="text-dark bg-white px-1 rounded">admin</code><br>
                                        <small class="text-muted">admin123</small>
                                    </div>
                                    <div class="col-4">
                                        <strong>Dokter:</strong><br>
                                        <code class="text-dark bg-white px-1 rounded">dr_budi</code><br>
                                        <small class="text-muted">dokter123</small>
                                    </div>
                                    <div class="col-4">
                                        <strong>Pasien:</strong><br>
                                        <code class="text-dark bg-white px-1 rounded">pasien1</code><br>
                                        <small class="text-muted">pasien123</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-muted small mt-4">
                    Tugas Praktikum Service-Oriented Architecture &copy; <?php echo date('Y'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
