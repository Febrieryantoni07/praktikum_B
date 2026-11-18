<?php
/**
 * Halaman 403 - Akses Ditolak (RBAC Restriction)
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/auth.php';

$pageTitle = '403 Akses Ditolak';
require_once __DIR__ . '/includes/header.php';
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-mint p-4">
    <div class="card card-modern p-4 p-md-5 text-center shadow-lg" style="max-width: 520px;">
        <div class="mb-4">
            <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                <i class="bi bi-shield-slash-fill"></i>
            </div>
        </div>
        <h2 class="fw-bold text-dark mb-2">Akses Ditolak (403)</h2>
        <p class="text-muted mb-4">
            Mohon maaf, peran akun Anda (<strong><?php echo htmlspecialchars(strtoupper(get_user_role() ?? 'TAMU')); ?></strong>) tidak memiliki hak akses untuk membuka halaman ini sesuai matriks keamanan RBAC.
        </p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-emerald">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-1"></i> Ganti Akun
            </a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
