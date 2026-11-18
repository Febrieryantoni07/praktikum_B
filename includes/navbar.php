<?php
/**
 * Top Navbar Component
 * Menampilkan status server, nama sistem, dan informasi akun yang sedang login
 */
$currentUser = get_user();
$currentRole = get_user_role();
?>
<nav class="navbar-top d-flex align-items-center justify-content-between">
    <!-- Brand / Logo -->
    <a href="<?php echo BASE_URL; ?>/dashboard.php" class="app-brand">
        <div class="brand-icon">
            <i class="bi bi-hospital"></i>
        </div>
        <div class="brand-text d-none d-sm-block">
            <h5><?php echo APP_NAME; ?></h5>
            <span>SOA Architecture Medical System</span>
        </div>
    </a>

    <!-- Middle Info: Status Arsitektur SOA Server A -> Server B -->
    <div class="d-none d-lg-flex align-items-center gap-2 px-3 py-1 bg-mint rounded-pill border border-mint">
        <span class="badge bg-emerald text-white rounded-pill px-2 py-1" style="font-size: 0.72rem;">
            <i class="bi bi-display me-1"></i> Server A (UI)
        </span>
        <i class="bi bi-arrow-right text-emerald small"></i>
        <span class="small text-muted" style="font-size: 0.75rem;">
            <i class="bi bi-hdd-network text-emerald me-1"></i> API B: <strong><?php echo htmlspecialchars(BASE_API); ?></strong>
        </span>
    </div>

    <!-- Right: User Profile & Actions -->
    <div class="d-flex align-items-center gap-3">
        <!-- Role Badge -->
        <div class="d-none d-md-block">
            <?php echo render_role_badge($currentRole); ?>
        </div>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 border rounded-pill px-3 py-1 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700; font-size: 0.85rem;">
                    <?php echo strtoupper(substr($currentUser['nama_lengkap'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="text-start d-none d-md-block">
                    <div class="fw-bold small text-truncate" style="max-width: 130px;"><?php echo htmlspecialchars($currentUser['nama_lengkap'] ?? 'Pengguna'); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($currentUser['username'] ?? ''); ?></div>
                </div>
                <i class="bi bi-chevron-down text-muted small ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" style="min-width: 200px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($currentUser['nama_lengkap'] ?? ''); ?></div>
                    <div class="small text-muted">Role: <?php echo ucfirst($currentRole); ?></div>
                </li>
                <?php if ($currentRole === 'dokter'): ?>
                    <li><a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/modules/dokter/profil.php"><i class="bi bi-person-lines-fill me-2 text-emerald"></i> Profil Saya</a></li>
                <?php elseif ($currentRole === 'pasien'): ?>
                    <li><a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/modules/pasien/profil.php"><i class="bi bi-person-lines-fill me-2 text-emerald"></i> Profil Pasien</a></li>
                <?php endif; ?>
                <li><a class="dropdown-item py-2" href="<?php echo BASE_URL; ?>/index.php" target="_blank"><i class="bi bi-box-arrow-up-right me-2 text-secondary"></i> Buka Landing Page</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 text-danger fw-semibold" href="<?php echo BASE_URL; ?>/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
