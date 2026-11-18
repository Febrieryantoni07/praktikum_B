<?php
/**
 * Landing Page Rumah Sakit Sehat Harmoni
 * Berdasarkan PRD Bab 6.2 - Dapat diakses publik tanpa login
 * Mengambil daftar Poliklinik dan Dokter secara dinamis dari API Server B
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/api.php';
require_once __DIR__ . '/helpers/auth.php';

$pageTitle = 'Beranda - Rumah Sakit Sehat Harmoni';

// Ambil data poliklinik dari Server B
$resPoli = api_request('/poliklinik');
$listPoli = ($resPoli['status'] && is_array($resPoli['data'])) ? $resPoli['data'] : [];

// Ambil data dokter dari Server B
$resDokter = api_request('/dokter');
$listDokter = ($resDokter['status'] && is_array($resDokter['data'])) ? $resDokter['data'] : [];

// Ambil data asuransi dari Server B
$resAsuransi = api_request('/asuransi');
$listAsuransi = ($resAsuransi['status'] && is_array($resAsuransi['data'])) ? $resAsuransi['data'] : [];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Public Navigation Bar -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-3 border-bottom border-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/index.php">
            <div class="brand-icon">
                <i class="bi bi-hospital"></i>
            </div>
            <div class="brand-text">
                <h5 class="mb-0 text-emerald-dark fw-bold"><?php echo APP_NAME; ?></h5>
                <span class="text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">HOSPITAL INFORMATION SYSTEM</span>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#poliklinik">Poliklinik</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#dokter">Dokter Spesialis</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#alur">Alur Layanan</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#asuransi">Asuransi</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3" href="#kontak">Kontak</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (is_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-emerald d-flex align-items-center gap-2">
                        <i class="bi bi-speedometer2"></i> Masuk Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-emerald d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login Sistem
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="beranda" class="hero-section text-center text-lg-start">
    <div class="container py-4">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <div class="hero-pill">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    Pelayanan Medis Terpadu Berbasis Digital SOA
                </div>
                <h1 class="display-4 fw-extrabold text-white mb-3" style="line-height: 1.2;">
                    Kesehatan Anda & Keluarga Adalah <span style="color: #6ee7b7;">Prioritas Utama</span> Kami
                </h1>
                <p class="lead text-white-50 mb-4" style="font-size: 1.15rem;">
                    Rumah Sakit Sehat Harmoni menyajikan layanan kesehatan terintegrasi berbasis Service-Oriented Architecture (SOA), menghubungkan pasien, dokter, dan poliklinik secara transparan, akurat, dan cepat.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-light btn-lg text-emerald-dark fw-bold px-4 py-3 shadow">
                        <i class="bi bi-calendar-check-fill me-2 text-emerald"></i> Daftar Kunjungan Pasien
                    </a>
                    <a href="#poliklinik" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="bi bi-buildings me-2"></i> Jelajahi Poliklinik
                    </a>
                </div>

                <!-- Info Badges -->
                <div class="row mt-5 pt-3 g-3 text-start">
                    <div class="col-4">
                        <div class="border-start border-2 border-emerald-light ps-3">
                            <h3 class="fw-bold text-white mb-0">24/7</h3>
                            <small class="text-white-50">Layanan IGD & Siaga</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-start border-2 border-emerald-light ps-3">
                            <h3 class="fw-bold text-white mb-0">100%</h3>
                            <small class="text-white-50">Rekam Medis Digital</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-start border-2 border-emerald-light ps-3">
                            <h3 class="fw-bold text-white mb-0">BPJS</h3>
                            <small class="text-white-50">& Asuransi Mandiri</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Illustration Card -->
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-4 shadow-lg text-dark">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-emerald-dark">Portal Pasien Online</h5>
                            <small class="text-muted">Mudah, Cepat, Tanpa Antre Fisik</small>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-mint rounded-3 border border-mint">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small fw-bold text-emerald-dark"><i class="bi bi-check2-circle me-1"></i> Status Jaringan SOA</span>
                            <span class="badge bg-emerald text-white">Online</span>
                        </div>
                        <div class="small text-muted" style="font-size: 0.78rem;">
                            Server A (UI: Client) terhubung ke Server B (Database & Web Service)
                        </div>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="bi bi-check-circle-fill text-emerald"></i> Booking janji temu dokter umum & spesialis
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="bi bi-check-circle-fill text-emerald"></i> Pantau riwayat diagnosa dan tindakan resep
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2 text-muted small">
                            <i class="bi bi-check-circle-fill text-emerald"></i> Fasilitas klaim asuransi kesehatan resmi
                        </li>
                    </ul>

                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-emerald w-100 py-2 fw-bold">
                        <i class="bi bi-door-open-fill me-1"></i> Masuk Akun Anda
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Layanan Unggulan -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="badge bg-mint text-emerald-dark border border-mint px-3 py-2 rounded-pill fw-semibold mb-2">KEUNGGULAN KAMI</span>
            <h2 class="fw-bold text-dark">Pelayanan Kesehatan Modern & Terpercaya</h2>
            <p class="text-muted">Komitmen kami memberikan standar perawatan medis terbaik dengan kemudahan teknologi.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Poliklinik Lengkap</h5>
                    <p class="text-muted small mb-0">Menyediakan berbagai poliklinik spesialis dengan dokter ahli yang berpengalaman di bidangnya.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Dokter Berkompeten</h5>
                    <p class="text-muted small mb-0">Dokter berizin resmi (STR) siap memberikan diagnosa tepat dan pelayanan hangat untuk pasien.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="bi bi-file-medical"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Rekam Medis SOA</h5>
                    <p class="text-muted small mb-0">Data medis tersimpan aman di server pusat dengan arsitektur SOA yang reliabel dan transparan.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Klaim Asuransi Mudah</h5>
                    <p class="text-muted small mb-0">Mendukung BPJS Kesehatan serta asuransi swasta ternama dengan alur administrasi yang cepat.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Daftar Poliklinik (Data Dinamis dari API) -->
<section id="poliklinik" class="py-5 bg-mint">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="badge bg-emerald text-white px-3 py-2 rounded-pill fw-semibold mb-2">LAYANAN POLIKLINIK</span>
                <h2 class="fw-bold text-dark mb-0">Daftar Poliklinik Rumah Sakit</h2>
                <p class="text-muted mb-0">Informasi poliklinik aktif yang siap melayani kebutuhan medis Anda.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-white text-emerald border px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-hdd-network me-1"></i> Data Realtime dari Server B
                </span>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($listPoli)): ?>
                <?php foreach ($listPoli as $poli): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-modern h-100 p-4 border-0">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.4rem;">
                                    <i class="bi bi-heart-pulse"></i>
                                </div>
                                <span class="badge bg-light text-secondary border">Poli #<?php echo htmlspecialchars($poli['id_poli']); ?></span>
                            </div>
                            <h5 class="fw-bold text-emerald-dark mb-1"><?php echo htmlspecialchars($poli['nama_poli']); ?></h5>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-geo-alt text-emerald me-1"></i> <?php echo htmlspecialchars($poli['lokasi'] ?? 'Gedung Utama'); ?>
                            </div>
                            <p class="text-muted small mb-3 flex-grow-1">
                                <?php echo htmlspecialchars($poli['keterangan'] ?? 'Melayani pemeriksaan klinis dan pengobatan rawat jalan.'); ?>
                            </p>
                            <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-sm btn-outline-emerald w-100">
                                <i class="bi bi-calendar-plus me-1"></i> Buat Janji Poli
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback jika Server B belum menyala -->
                <div class="col-12">
                    <div class="card card-modern p-4 text-center border-dashed">
                        <i class="bi bi-info-circle text-emerald fs-1 mb-2"></i>
                        <h5 class="fw-bold">Belum Ada Data Poliklinik atau Server B Sedang Offline</h5>
                        <p class="text-muted small mb-0">
                            Pastikan Web Service di Server B (<strong><?php echo BASE_API; ?></strong>) sudah aktif dan database `db_rumah_sakit` sudah di-seed.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section Tim Dokter (Data Dinamis dari API) -->
<section id="dokter" class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="badge bg-mint text-emerald-dark border border-mint px-3 py-2 rounded-pill fw-semibold mb-2">TENAGA MEDIS</span>
            <h2 class="fw-bold text-dark">Tim Dokter Spesialis Kami</h2>
            <p class="text-muted">Dokter profesional yang berdedikasi memberikan perawatan medis terbaik.</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($listDokter)): ?>
                <?php foreach (array_slice($listDokter, 0, 4) as $doc): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-modern h-100 p-4 text-center border-0">
                            <div class="mx-auto mb-3">
                                <div class="rounded-circle bg-mint text-emerald d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2.2rem; border: 3px solid var(--mint-border);">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($doc['nama_dokter']); ?></h6>
                            <div class="badge bg-mint text-emerald-dark border mb-2">
                                <?php echo htmlspecialchars($doc['spesialisasi'] ?? 'Dokter Umum'); ?>
                            </div>
                            <div class="small text-muted mb-2">
                                <i class="bi bi-hospital me-1"></i> Poli: <?php echo htmlspecialchars($doc['nama_poli'] ?? '-'); ?>
                            </div>
                            <div class="small text-muted" style="font-size: 0.78rem;">
                                No. STR: <?php echo htmlspecialchars($doc['no_str'] ?? '-'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-3">
                    <p class="small mb-0">Daftar dokter dapat dimuat otomatis setelah Server B aktif.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section Alur Pendaftaran -->
<section id="alur" class="py-5 bg-mint">
    <div class="container py-4">
        <div class="text-center max-w-xl mx-auto mb-5">
            <span class="badge bg-emerald text-white px-3 py-2 rounded-pill fw-semibold mb-2">PANDUAN PASIEN</span>
            <h2 class="fw-bold text-dark">4 Langkah Mudah Berobat</h2>
            <p class="text-muted">Proses pendaftaran cepat, transparan, dan teratur dengan sistem berbasis SOA.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-modern p-4 text-center h-100">
                    <div class="rounded-circle bg-emerald text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        1
                    </div>
                    <h6 class="fw-bold mb-2">Login / Registrasi</h6>
                    <p class="text-muted small mb-0">Masuk ke akun Anda atau daftarkan diri untuk memperoleh nomor Rekam Medis (No. RM).</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern p-4 text-center h-100">
                    <div class="rounded-circle bg-emerald text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        2
                    </div>
                    <h6 class="fw-bold mb-2">Pilih Poli & Dokter</h6>
                    <p class="text-muted small mb-0">Pilih poliklinik tujuan dan dokter yang bertugas sesuai jadwal yang tersedia.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern p-4 text-center h-100">
                    <div class="rounded-circle bg-emerald text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        3
                    </div>
                    <h6 class="fw-bold mb-2">Tuliskan Keluhan</h6>
                    <p class="text-muted small mb-0">Sampaikan keluhan kesehatan awal Anda untuk memudahkan persiapan dokter pemeriksa.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-modern p-4 text-center h-100">
                    <div class="rounded-circle bg-emerald text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        4
                    </div>
                    <h6 class="fw-bold mb-2">Pemeriksaan & Resep</h6>
                    <p class="text-muted small mb-0">Datang sesuai jadwal, jalani pemeriksaan, dan pantau hasil diagnosa serta resep digital.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Asuransi Rekanan -->
<section id="asuransi" class="py-5 bg-white">
    <div class="container py-4 text-center">
        <h5 class="fw-bold text-muted text-uppercase mb-4" style="letter-spacing: 0.1em; font-size: 0.88rem;">
            Bekerjasama Dengan Berbagai Asuransi Terkemuka
        </h5>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
            <?php if (!empty($listAsuransi)): ?>
                <?php foreach ($listAsuransi as $asu): ?>
                    <div class="px-4 py-3 bg-mint rounded-4 border border-mint shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-emerald fs-4"></i>
                        <span class="fw-bold text-emerald-dark"><?php echo htmlspecialchars($asu['nama_asuransi']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-4 py-3 bg-mint rounded-4 border border-mint shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-emerald fs-4"></i>
                    <span class="fw-bold text-emerald-dark">BPJS Kesehatan</span>
                </div>
                <div class="px-4 py-3 bg-mint rounded-4 border border-mint shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-emerald fs-4"></i>
                    <span class="fw-bold text-emerald-dark">Prudential</span>
                </div>
                <div class="px-4 py-3 bg-mint rounded-4 border border-mint shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-emerald fs-4"></i>
                    <span class="fw-bold text-emerald-dark">Mandiri Inhealth</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section Kontak & Panggilan Darurat -->
<section id="kontak" class="py-5 bg-emerald-dark text-white">
    <div class="container py-3">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-danger text-white px-3 py-1 rounded-pill fw-bold">IGD 24 JAM</span>
                    <h3 class="fw-bold mb-0 text-white">Butuh Pertolongan Medis Darurat?</h3>
                </div>
                <p class="text-white-50 mb-0">Tim dokter dan armada ambulans siaga 24 jam untuk melayani keadaan darurat Anda.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="tel:119" class="btn btn-light btn-lg text-danger fw-bold px-4 py-3 rounded-pill shadow">
                    <i class="bi bi-telephone-fill me-2"></i> Panggil IGD: 119
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Public Footer -->
<footer class="bg-white py-5 border-top">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-emerald-dark fw-bold"><?php echo APP_NAME; ?></h5>
                        <small class="text-muted"><?php echo APP_TAGLINE; ?></small>
                    </div>
                </div>
                <p class="text-muted small mb-3">
                    Solusi terdepan pelayanan rumah sakit modern berbasis arsitektur Service-Oriented Architecture (SOA) untuk efisiensi, akurasi data medis, dan kenyamanan pasien.
                </p>
                <div class="small text-muted">
                    <i class="bi bi-geo-alt-fill text-emerald me-1"></i> <?php echo APP_ADDRESS; ?><br>
                    <i class="bi bi-telephone-fill text-emerald me-1"></i> <?php echo APP_PHONE; ?>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <h6 class="fw-bold text-dark mb-3">Tautan Cepat</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="#beranda" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="mb-2"><a href="#poliklinik" class="text-decoration-none text-muted">Poliklinik Spesialis</a></li>
                    <li class="mb-2"><a href="#dokter" class="text-decoration-none text-muted">Jadwal Dokter</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>/login.php" class="text-decoration-none text-muted">Portal Login</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-4">
                <h6 class="fw-bold text-dark mb-3">Informasi Praktikum SOA</h6>
                <div class="p-3 bg-mint rounded-3 border border-mint small">
                    <div class="fw-semibold text-emerald-dark mb-1">Infrastruktur Server:</div>
                    <div class="text-muted"><strong>Server A (Client UI):</strong> PHP Native</div>
                    <div class="text-muted"><strong>Server B (Web Service):</strong> PHP + MariaDB</div>
                    <div class="text-muted"><strong>Komunikasi:</strong> JSON via HTTP/cURL</div>
                </div>
            </div>
        </div>

        <div class="border-top mt-4 pt-3 text-center small text-muted">
            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tugas Praktikum Service-Oriented Architecture (SOA).
        </div>
    </div>
</footer>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
