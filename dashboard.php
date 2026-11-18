<?php
/**
 * Dashboard Utama Sistem Informasi Rumah Sakit (Server A)
 * Berdasarkan PRD Bab 3.1 & 6.2 (Berbasis RBAC: admin, dokter, pasien)
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

// Pastikan pengguna sudah login
require_login();

$user = get_user();
$role = get_user_role();
$pageTitle = 'Dashboard - ' . ucfirst($role);
$currentMenu = 'dashboard';

// Ambil data statistik dari Server B
$resStats = api_request('/laporan/statistik_dashboard.php');
$stats = ($resStats['status'] && is_array($resStats['data'])) ? $resStats['data'] : [
    'total_pasien' => 0,
    'total_dokter' => 0,
    'total_poli' => 0,
    'kunjungan_hari_ini' => 0,
    'kunjungan_menunggu' => 0,
    'kunjungan_selesai' => 0
];

// Ambil daftar kunjungan terbaru
$resKunjungan = api_request('/kunjungan');
$listKunjungan = ($resKunjungan['status'] && is_array($resKunjungan['data'])) ? $resKunjungan['data'] : [];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Layout Wrapper -->
<div class="d-flex">
    <!-- Sidebar Navigation -->
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Breadcrumb & Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-emerald text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">
                    Selamat Datang, <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                </h3>
                <p class="text-muted small mb-0">
                    Anda masuk sebagai <strong><?php echo ucfirst($role); ?></strong> di <?php echo APP_NAME; ?>.
                </p>
            </div>

            <!-- Quick Action per Role -->
            <div class="d-flex gap-2">
                <?php if ($role === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="btn btn-emerald btn-sm">
                        <i class="bi bi-calendar-plus me-1"></i> Buat Kunjungan
                    </a>
                    <a href="<?php echo BASE_URL; ?>/modules/pasien/tambah.php" class="btn btn-outline-emerald btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Tambah Pasien
                    </a>
                <?php elseif ($role === 'pasien'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="btn btn-emerald">
                        <i class="bi bi-calendar-check-fill me-1"></i> + Daftar Kunjungan Baru
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- ============================================================= -->
            <!-- DASHBOARD ROLE: ADMINISTRATOR -->
            <!-- ============================================================= -->
            <!-- Row Statistik 4 Kartu -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Total Pasien Terdaftar</div>
                                <div class="stat-number"><?php echo (int)($stats['total_pasien'] ?? 0); ?></div>
                                <small class="text-muted"><i class="bi bi-person-check text-emerald me-1"></i> Rekam Medis</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Dokter Spesialis</div>
                                <div class="stat-number"><?php echo (int)($stats['total_dokter'] ?? 0); ?></div>
                                <small class="text-muted"><i class="bi bi-check-circle text-emerald me-1"></i> Aktif Bertugas</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Poliklinik Aktif</div>
                                <div class="stat-number"><?php echo (int)($stats['total_poli'] ?? 0); ?></div>
                                <small class="text-muted"><i class="bi bi-buildings text-emerald me-1"></i> Layanan Tersedia</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-hospital-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Kunjungan Hari Ini</div>
                                <div class="stat-number"><?php echo (int)($stats['kunjungan_hari_ini'] ?? 0); ?></div>
                                <small class="text-muted">
                                    <span class="text-warning fw-semibold"><?php echo (int)($stats['kunjungan_menunggu'] ?? 0); ?> menunggu</span>
                                </small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-calendar2-day-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row Grafik & Kunjungan Terbaru -->
            <div class="row g-4 mb-4">
                <!-- Visual Chart.js -->
                <div class="col-lg-5">
                    <div class="card card-modern h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-emerald me-2"></i> Status Kunjungan</h6>
                            <span class="badge bg-mint text-emerald-dark border">Realtime</span>
                        </div>
                        <div class="position-relative" style="height: 250px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Info Arsitektur SOA Server A & Server B -->
                <div class="col-lg-7">
                    <div class="card card-modern h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3-fill text-emerald me-2"></i> Status Integrasi SOA Dua Server</h6>
                            <span class="badge bg-emerald text-white">Terhubung</span>
                        </div>
                        <p class="text-muted small">
                            Aplikasi berjalan dengan arsitektur Service-Oriented Architecture (SOA), di mana Client UI pada Server A berkomunikasi secara asinkronus melalui protokol HTTP/cURL JSON ke Server B.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered small">
                                <thead class="bg-mint">
                                    <tr>
                                        <th>Komponen</th>
                                        <th>Alamat / Lokasi</th>
                                        <th>Teknologi</th>
                                        <th>Peran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Server A (Client)</td>
                                        <td><code>http://192.168.1.13/rs_client</code></td>
                                        <td>PHP Native + Bootstrap 5</td>
                                        <td>Antarmuka UI, Session, RBAC</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Server B (Web Service)</td>
                                        <td><code><?php echo htmlspecialchars(BASE_API); ?></code></td>
                                        <td>PHP API + MariaDB</td>
                                        <td>Database, CRUD, Token JWT/Random</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-shield-check text-emerald me-1"></i> Semua query SQL dieksekusi di Server B dengan prepared statements untuk mencegah SQL Injection.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Kunjungan Pasien Terbaru -->
            <div class="card card-modern border-0">
                <div class="card-header card-header-clean d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Antrean & Kunjungan Pasien Terbaru</h5>
                        <small class="text-muted">Data kunjungan yang masuk hari ini</small>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/modules/kunjungan/index.php" class="btn btn-sm btn-outline-emerald">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Poliklinik</th>
                                <th>Dokter Pemeriksa</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listKunjungan)): ?>
                                <?php foreach (array_slice($listKunjungan, 0, 5) as $k): ?>
                                    <tr>
                                        <td class="fw-semibold text-emerald-dark"><?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></td>
                                        <td><span class="badge bg-light text-secondary border"><?php echo htmlspecialchars($k['nama_poli'] ?? '-'); ?></span></td>
                                        <td><?php echo htmlspecialchars($k['nama_dokter'] ?? '-'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($k['tanggal_kunjungan'])); ?></td>
                                        <td><?php echo render_status_kunjungan($k['status']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/detail.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-sm btn-light border py-1 px-2 text-emerald">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block text-secondary mb-1"></i>
                                        Belum ada kunjungan yang tercatat saat ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($role === 'dokter'): ?>
            <!-- ============================================================= -->
            <!-- DASHBOARD ROLE: DOKTER -->
            <!-- ============================================================= -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Pasien Menunggu Antrean</div>
                                <div class="stat-number text-warning"><?php echo (int)($stats['kunjungan_menunggu'] ?? 0); ?></div>
                                <small class="text-muted"><i class="bi bi-clock-history me-1"></i> Butuh Pemeriksaan</small>
                            </div>
                            <div class="stat-icon bg-warning-subtle text-warning">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Pasien Selesai Hari Ini</div>
                                <div class="stat-number text-success"><?php echo (int)($stats['kunjungan_selesai'] ?? 0); ?></div>
                                <small class="text-muted"><i class="bi bi-check-circle me-1"></i> Diagnosis Tersimpan</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-check-all"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Total Riwayat Pasien</div>
                                <div class="stat-number"><?php echo count($listKunjungan); ?></div>
                                <small class="text-muted"><i class="bi bi-folder-check me-1"></i> Rekam Medis</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-clipboard2-pulse-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Antrean Pasien Dokter Ini -->
            <div class="card card-modern border-0">
                <div class="card-header card-header-clean d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Daftar Antrean Pasien Anda</h5>
                        <small class="text-muted">Pilih pasien untuk memulai pemeriksaan dan mengisi diagnosis serta tindakan medis</small>
                    </div>
                    <span class="badge bg-mint text-emerald-dark border">
                        <i class="bi bi-heart-pulse me-1"></i> Poliklinik: <?php echo htmlspecialchars($user['nama_poli'] ?? 'Spesialis'); ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Tgl Kunjungan</th>
                                <th>Keluhan Pasien</th>
                                <th>Status</th>
                                <th class="text-center">Aksi Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listKunjungan)): ?>
                                <?php foreach ($listKunjungan as $k): ?>
                                    <tr>
                                        <td class="fw-semibold text-emerald-dark"><?php echo htmlspecialchars($k['no_rm'] ?? '-'); ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($k['nama_pasien'] ?? '-'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($k['tanggal_kunjungan'])); ?></td>
                                        <td class="text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($k['keluhan'] ?? '-'); ?></td>
                                        <td><?php echo render_status_kunjungan($k['status']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/periksa.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-sm btn-emerald py-1 px-3">
                                                <i class="bi bi-pencil-square me-1"></i> Periksa / Diagnosis
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-check fs-3 d-block text-secondary mb-1"></i>
                                        Tidak ada antrean pasien yang sedang menunggu saat ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($role === 'pasien'): ?>
            <!-- ============================================================= -->
            <!-- DASHBOARD ROLE: PASIEN -->
            <!-- ============================================================= -->
            <div class="row g-4 mb-4">
                <!-- Kartu Identitas Rekam Medis Pasien -->
                <div class="col-lg-5">
                    <div class="card card-modern p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; font-size: 1.8rem;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h5>
                                <span class="badge bg-mint text-emerald-dark border">Pasien Terdaftar</span>
                            </div>
                        </div>

                        <ul class="list-unstyled mb-4">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Nomor Rekam Medis (No. RM)</span>
                                <strong class="text-emerald-dark"><?php echo htmlspecialchars($user['no_rm'] ?? 'RM-' . str_pad($user['id_pasien'] ?? '1', 4, '0', STR_PAD_LEFT)); ?></strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Status Akun</span>
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted small">Hak Akses</span>
                                <span class="text-secondary small">Riwayat & Booking Mandiri</span>
                            </li>
                        </ul>

                        <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="btn btn-emerald w-100 py-2 fw-bold shadow">
                            <i class="bi bi-calendar-plus-fill me-1"></i> Daftar Kunjungan Dokter Baru
                        </a>
                    </div>
                </div>

                <!-- Info Panduan Pasien -->
                <div class="col-lg-7">
                    <div class="card card-modern p-4 h-100 bg-mint border-mint">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-info-circle-fill text-emerald fs-4"></i>
                            <h5 class="fw-bold text-emerald-dark mb-0">Petunjuk Pelayanan Pasien</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Selamat datang di portal pasien Rumah Sakit Sehat Harmoni. Melalui akun ini, Anda dapat menjadwalkan pemeriksaan ke dokter spesialis serta melihat seluruh riwayat diagnosis dan resep obat yang telah diberikan.
                        </p>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-3 shadow-sm">
                                    <div class="fw-bold small text-dark"><i class="bi bi-1-circle text-emerald me-1"></i> Daftarkan Kunjungan</div>
                                    <small class="text-muted">Pilih tanggal, poli tujuan, dan sampaikan keluhan Anda.</small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-3 shadow-sm">
                                    <div class="fw-bold small text-dark"><i class="bi bi-2-circle text-emerald me-1"></i> Hadir Tepat Waktu</div>
                                    <small class="text-muted">Datang ke poliklinik 15 menit sebelum jam pemeriksaan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Kunjungan Pasien Sendiri -->
            <div class="card card-modern border-0">
                <div class="card-header card-header-clean d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Riwayat Kunjungan Saya</h5>
                        <small class="text-muted">Semua riwayat pemeriksaan dan status janji temu Anda</small>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/modules/kunjungan/tambah.php" class="btn btn-sm btn-emerald">
                        <i class="bi bi-plus-lg me-1"></i> Buat Janji Baru
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Tgl Kunjungan</th>
                                <th>Poliklinik</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                                <th class="text-center">Hasil Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listKunjungan)): ?>
                                <?php foreach ($listKunjungan as $k): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo date('d/m/Y', strtotime($k['tanggal_kunjungan'])); ?></td>
                                        <td><span class="badge bg-mint text-emerald-dark border"><?php echo htmlspecialchars($k['nama_poli'] ?? '-'); ?></span></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($k['nama_dokter'] ?? '-'); ?></td>
                                        <td class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($k['keluhan'] ?? '-'); ?></td>
                                        <td><?php echo render_status_kunjungan($k['status']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo BASE_URL; ?>/modules/kunjungan/detail.php?id=<?php echo $k['id_kunjungan']; ?>" class="btn btn-sm btn-light border text-emerald">
                                                <i class="bi bi-file-earmark-medical me-1"></i> Rincian & Resep
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar2-x fs-3 d-block text-secondary mb-1"></i>
                                        Anda belum memiliki riwayat kunjungan. Silakan klik tombol <strong>Daftar Kunjungan Baru</strong> untuk membuat janji.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Chart.js CDN (Khusus Admin) -->
<?php if ($role === 'admin'): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statusChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Selesai', 'Diperiksa / Lainnya'],
                datasets: [{
                    data: [
                        <?php echo (int)($stats['kunjungan_menunggu'] ?? 0); ?>,
                        <?php echo (int)($stats['kunjungan_selesai'] ?? 0); ?>,
                        <?php echo max(0, (int)($stats['kunjungan_hari_ini'] ?? 0) - (int)($stats['kunjungan_menunggu'] ?? 0) - (int)($stats['kunjungan_selesai'] ?? 0)); ?>
                    ],
                    backgroundColor: [
                        '#f59e0b', // Amber/Yellow
                        '#059669', // Emerald
                        '#0284c7'  // Sky Blue
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 12 },
                            usePointStyle: true
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
