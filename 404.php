<?php
/**
 * Halaman 404 - Halaman Tidak Ditemukan
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/auth.php';

$pageTitle = '404 Halaman Tidak Ditemukan';
require_once __DIR__ . '/includes/header.php';
?>
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-mint p-4">
    <div class="card card-modern p-4 p-md-5 text-center shadow-lg" style="max-width: 520px;">
        <div class="mb-4">
            <div class="rounded-circle bg-emerald text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                <i class="bi bi-question-circle-fill"></i>
            </div>
        </div>
        <h2 class="fw-bold text-dark mb-2">Halaman Tidak Ditemukan (404)</h2>
        <p class="text-muted mb-4">
            Halaman atau modul yang Anda cari tidak tersedia atau tautan telah berpindah.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-emerald">
                <i class="bi bi-house-door-fill me-1"></i> Ke Halaman Utama
            </a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
