<?php
/**
 * Sidebar Navigation Berbasis Role (RBAC)
 * Sesuai Matriks Hak Akses di PRD Bab 3.1
 */
$currentRole = get_user_role();
$currentMenu = $currentMenu ?? 'dashboard';
?>
<aside class="sidebar-wrapper">
    <!-- Menu Utama -->
    <div class="sidebar-heading">Menu Utama</div>
    <ul class="nav flex-column mb-3">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link-custom <?php echo ($currentMenu === 'dashboard') ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
    </ul>

    <!-- Pelayanan Medis / Kunjungan -->
    <div class="sidebar-heading">Pelayanan & Kunjungan</div>
    <ul class="nav flex-column mb-3">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="nav-link-custom <?php echo ($currentMenu === 'kunjungan') ? 'active' : ''; ?>">
                <i class="bi bi-calendar2-check-fill"></i>
                <span>
                    <?php 
                    if ($currentRole === 'dokter') echo 'Pemeriksaan Pasien';
                    elseif ($currentRole === 'pasien') echo 'Kunjungan Saya';
                    else echo 'Kelola Kunjungan';
                    ?>
                </span>
            </a>
        </li>
        <?php if ($currentRole === 'pasien' || $currentRole === 'admin'): ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="nav-link-custom <?php echo ($currentMenu === 'kunjungan_tambah') ? 'active' : ''; ?>">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Daftar Kunjungan Baru</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Data Master & Informasi -->
    <div class="sidebar-heading">
        <?php echo ($currentRole === 'admin') ? 'Master Data' : 'Informasi & Layanan'; ?>
    </div>
    <ul class="nav flex-column mb-3">
        <!-- Poliklinik (Admin: CRUD, Dokter/Pasien: Lihat) -->
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/poliklinik/index.php" class="nav-link-custom <?php echo ($currentMenu === 'poliklinik') ? 'active' : ''; ?>">
                <i class="bi bi-buildings-fill"></i>
                <span>Poliklinik</span>
            </a>
        </li>

        <!-- Dokter (Admin: CRUD, Dokter: Profil Sendiri, Pasien: Daftar Dokter) -->
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/dokter/index.php" class="nav-link-custom <?php echo ($currentMenu === 'dokter') ? 'active' : ''; ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>
                    <?php echo ($currentRole === 'pasien') ? 'Jadwal & Dokter' : 'Data Dokter'; ?>
                </span>
            </a>
        </li>

        <!-- Pasien (Admin: CRUD, Dokter: Pasien Ditangani, Pasien: Profil Saya) -->
        <?php if ($currentRole === 'admin' || $currentRole === 'dokter'): ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/pasien/index.php" class="nav-link-custom <?php echo ($currentMenu === 'pasien') ? 'active' : ''; ?>">
                <i class="bi bi-people-fill"></i>
                <span>
                    <?php echo ($currentRole === 'dokter') ? 'Pasien Ditangani' : 'Data Pasien'; ?>
                </span>
            </a>
        </li>
        <?php elseif ($currentRole === 'pasien'): ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/pasien/profil.php" class="nav-link-custom <?php echo ($currentMenu === 'pasien_profil') ? 'active' : ''; ?>">
                <i class="bi bi-person-bounding-box"></i>
                <span>Profil Biodata Saya</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Asuransi (Admin: CRUD, Dokter/Pasien: Lihat) -->
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/asuransi/index.php" class="nav-link-custom <?php echo ($currentMenu === 'asuransi') ? 'active' : ''; ?>">
                <i class="bi bi-shield-check"></i>
                <span>Asuransi Rekanan</span>
            </a>
        </li>

        <!-- Kelola Pengguna (Admin Only) -->
        <?php if ($currentRole === 'admin'): ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/pengguna/index.php" class="nav-link-custom <?php echo ($currentMenu === 'pengguna') ? 'active' : ''; ?>">
                <i class="bi bi-person-gear"></i>
                <span>Kelola Akun Pengguna</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Laporan (Admin & Dokter) -->
    <?php if ($currentRole === 'admin' || $currentRole === 'dokter'): ?>
    <div class="sidebar-heading">Laporan & Statistik</div>
    <ul class="nav flex-column mb-3">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/laporan/index.php" class="nav-link-custom <?php echo ($currentMenu === 'laporan') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Pusat Laporan</span>
            </a>
        </li>
        <?php if ($currentRole === 'admin'): ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/laporan/pasien_poli.php" class="nav-link-custom <?php echo ($currentMenu === 'lap_poli') ? 'active' : ''; ?>">
                <i class="bi bi-bar-chart-steps"></i>
                <span>Pasien per Poliklinik</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/laporan/kunjungan_dokter.php" class="nav-link-custom <?php echo ($currentMenu === 'lap_dokter') ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <span>Kunjungan per Dokter</span>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/modules/laporan/kunjungan_periode.php" class="nav-link-custom <?php echo ($currentMenu === 'lap_periode') ? 'active' : ''; ?>">
                <i class="bi bi-calendar-range"></i>
                <span>Kunjungan per Periode</span>
            </a>
        </li>
    </ul>
    <?php endif; ?>

    <!-- Bagian Bawah: Informasi Sistem -->
    <div class="mt-auto pt-3 border-top">
        <div class="p-2 bg-mint rounded-3 border border-mint-subtle">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-emerald"></i>
                <div class="small fw-semibold text-emerald-dark">Info SOA Sistem</div>
            </div>
            <div class="text-muted" style="font-size: 0.72rem; margin-top: 3px;">
                Client PHP Native terhubung via HTTP/JSON ke Server B.
            </div>
        </div>
        <div class="mt-2 text-center text-muted" style="font-size: 0.7rem;">
            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>
        </div>
    </div>
</aside>
