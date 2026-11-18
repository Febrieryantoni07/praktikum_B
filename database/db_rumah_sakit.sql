-- ============================================================================
-- SKRIP SQL DATABASE: db_rumah_sakit
-- Berdasarkan PRD Sistem Informasi Rumah Sakit Berbasis SOA (Tahap 2)
-- Server Basis Data & Web Service: Server B (MariaDB/MySQL)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS db_rumah_sakit CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_rumah_sakit;

-- 1. TABEL POLIKLINIK
CREATE TABLE IF NOT EXISTS poliklinik (
  id_poli INT AUTO_INCREMENT PRIMARY KEY,
  nama_poli VARCHAR(100) NOT NULL,
  lokasi VARCHAR(100),
  keterangan TEXT
) ENGINE=InnoDB;

-- 2. TABEL ASURANSI
CREATE TABLE IF NOT EXISTS asuransi (
  id_asuransi INT AUTO_INCREMENT PRIMARY KEY,
  nama_asuransi VARCHAR(100) NOT NULL,
  jenis VARCHAR(50),
  no_kontak VARCHAR(20),
  keterangan TEXT
) ENGINE=InnoDB;

-- 3. TABEL PENGGUNA (RBAC: admin, dokter, pasien)
CREATE TABLE IF NOT EXISTS pengguna (
  id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  role ENUM('admin','dokter','pasien') NOT NULL,
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. TABEL DOKTER
CREATE TABLE IF NOT EXISTS dokter (
  id_dokter INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT NULL UNIQUE,
  id_poli INT NOT NULL,
  nama_dokter VARCHAR(100) NOT NULL,
  spesialisasi VARCHAR(100),
  no_str VARCHAR(50),
  no_hp VARCHAR(20),
  FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE SET NULL,
  FOREIGN KEY (id_poli) REFERENCES poliklinik(id_poli)
) ENGINE=InnoDB;

-- 5. TABEL PASIEN
CREATE TABLE IF NOT EXISTS pasien (
  id_pasien INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT NULL UNIQUE,
  id_asuransi INT NULL,
  no_rm VARCHAR(20) NOT NULL UNIQUE,
  nik VARCHAR(20),
  nama_pasien VARCHAR(100) NOT NULL,
  jenis_kelamin ENUM('L','P') NOT NULL,
  tanggal_lahir DATE,
  alamat TEXT,
  no_hp VARCHAR(20),
  no_kartu_asuransi VARCHAR(50),
  FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE SET NULL,
  FOREIGN KEY (id_asuransi) REFERENCES asuransi(id_asuransi) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. TABEL KUNJUNGAN
CREATE TABLE IF NOT EXISTS kunjungan (
  id_kunjungan INT AUTO_INCREMENT PRIMARY KEY,
  id_pasien INT NOT NULL,
  id_dokter INT NOT NULL,
  id_poli INT NOT NULL,
  tanggal_kunjungan DATE NOT NULL,
  keluhan TEXT,
  diagnosis TEXT,
  tindakan TEXT,
  status ENUM('menunggu','diperiksa','selesai','batal') NOT NULL DEFAULT 'menunggu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pasien) REFERENCES pasien(id_pasien) ON DELETE CASCADE,
  FOREIGN KEY (id_dokter) REFERENCES dokter(id_dokter),
  FOREIGN KEY (id_poli) REFERENCES poliklinik(id_poli)
) ENGINE=InnoDB;

-- 7. TABEL TOKEN AKSES
CREATE TABLE IF NOT EXISTS token_akses (
  id_token INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  expired_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- DATA AWAL (SEED) LENGKAP DENGAN PASSWORD HASH BCRYPT
-- ============================================================================

-- A. Data Poliklinik
INSERT INTO poliklinik (id_poli, nama_poli, lokasi, keterangan) VALUES
(1, 'Poliklinik Umum', 'Gedung A Lantai 1', 'Pelayanan pemeriksaan kesehatan dasar dan konsultasi dokter umum'),
(2, 'Poliklinik Penyakit Dalam', 'Gedung A Lantai 2', 'Penanganan penyakit organ dalam dewasa, metabolik, dan endokrin'),
(3, 'Poliklinik Gigi & Mulut', 'Gedung B Lantai 1', 'Perawatan karies gigi, penambalan, pembersihan karang, dan bedah mulut ringan'),
(4, 'Poliklinik Anak', 'Gedung B Lantai 2', 'Pemeriksaan kesehatan tumbuh kembang bayi, balita, dan imunisasi');

-- B. Data Asuransi Rekanan
INSERT INTO asuransi (id_asuransi, nama_asuransi, jenis, no_kontak, keterangan) VALUES
(1, 'BPJS Kesehatan', 'Pemerintah', '165', 'Jaminan kesehatan nasional dengan rujukan faskes tingkat 1'),
(2, 'Prudential Indonesia', 'Swasta', '1500085', 'Asuransi komersial klaim cashless dan reimbursement'),
(3, 'Mandiri Inhealth', 'Perusahaan', '021-5250900', 'Kerjasama jaminan kesehatan korporasi dan BUMN');

-- C. Data Pengguna (Password telah di-hash dengan BCRYPT password_hash())
-- Password:
-- admin   : admin123
-- dokter  : dokter123
-- pasien  : pasien123
INSERT INTO pengguna (id_pengguna, username, password, nama_lengkap, role, status) VALUES
(1, 'admin', '$2y$10$OUIfz8RaB5LYmBj0Qawc4eZ4Nby50Sb6pjyfeUEVeNY2ij9Bo7g36', 'Administrator Sistem', 'admin', 'aktif'),
(2, 'dr_budi', '$2y$10$R6O4Ui5.dd9whGtJbVokoe3ahv.Y.NYWIrS6a6Hwg/yAUD2QZzSaK', 'dr. Budi Santoso, Sp.PD', 'dokter', 'aktif'),
(3, 'dr_siti', '$2y$10$pwPrKuWB8olConfFHD5zRuT1DeocjJkKmR6dK/uCHS9MX1cAA459.', 'drg. Siti Rahayu', 'dokter', 'aktif'),
(4, 'dr_andi', '$2y$10$HwgHwkjWLgahSmSIwNoAwuR2IE.mHXcSb2R1Z/xLyaOt6WaYphi/m', 'dr. Andi Wijaya, Sp.A', 'dokter', 'aktif'),
(5, 'pasien1', '$2y$10$34wo/RwaPwNBjaZ6.WINEOsj.Bz3RBgHdOz..bnPXYdYQa1U5XfN2', 'Ahmad Fadillah', 'pasien', 'aktif'),
(6, 'pasien2', '$2y$10$3mvgGXe6F7X0BL8piHmApeYmYemsss2vt6f.1MymEIH9zK1I/FlaG', 'Nurul Hidayah', 'pasien', 'aktif');

-- D. Data Dokter
INSERT INTO dokter (id_dokter, id_pengguna, id_poli, nama_dokter, spesialisasi, no_str, no_hp) VALUES
(1, 2, 2, 'dr. Budi Santoso, Sp.PD', 'Penyakit Dalam', 'STR-3171-88912-2021', '081234567801'),
(2, 3, 3, 'drg. Siti Rahayu', 'Gigi & Mulut', 'STR-3171-77234-2022', '081234567802'),
(3, 4, 4, 'dr. Andi Wijaya, Sp.A', 'Spesialis Anak', 'STR-3171-99015-2020', '081234567803');

-- E. Data Pasien (5 Pasien: Sebagian berakun, sebagian asuransi, sebagian umum)
INSERT INTO pasien (id_pasien, id_pengguna, id_asuransi, no_rm, nik, nama_pasien, jenis_kelamin, tanggal_lahir, alamat, no_hp, no_kartu_asuransi) VALUES
(1, 5, 1, 'RM-2401001', '3201012304950001', 'Ahmad Fadillah', 'L', '1995-04-23', 'Jl. Cempaka No. 12, Harmoni', '081399887711', '000192837465'),
(2, 6, 2, 'RM-2401002', '3201015609980002', 'Nurul Hidayah', 'P', '1998-09-16', 'Jl. Melati Blok B No. 4, Harmoni', '081399887722', 'PRU-8829102'),
(3, NULL, 1, 'RM-2401003', '3201011211890003', 'Hendra Setiawan', 'L', '1989-11-12', 'Jl. Merpati No. 8, Harmoni', '081399887733', '000283746519'),
(4, NULL, 3, 'RM-2401004', '3201014502010004', 'Dewi Lestari', 'P', '2001-02-05', 'Jl. Kenanga No. 20, Harmoni', '081399887744', 'INH-9910293'),
(5, NULL, NULL, 'RM-2401005', '3201013007920005', 'Rudi Gunawan', 'L', '1992-07-30', 'Jl. Mawar No. 15, Harmoni', '081399887755', NULL);

-- F. Data Kunjungan (10 Kunjungan Berbagai Status)
INSERT INTO kunjungan (id_kunjungan, id_pasien, id_dokter, id_poli, tanggal_kunjungan, keluhan, diagnosis, tindakan, status, created_at) VALUES
(1, 1, 1, 2, '2026-11-15', 'Nyeri lambung dan mual sudah 3 hari, sering kembung sesudah makan', 'Gastritis Akut / Dispepsia', 'Pemberian Antasida 3x1 cth ac, Omeprazole 20mg 2x1 ac, edukasi diet rendah pedas/asam', 'selesai', '2026-11-15 08:30:00'),
(2, 2, 2, 3, '2026-11-16', 'Gigi geraham kanan bawah berlubang dan berdenyut hebat sejak semalam', 'Pulpitis Irreversibel Gigi 46', 'Aplikasi analgesik topikal, premedikasi Amoxicillin 500mg 3x1 dan Asam Mefenamat 500mg 3x1 prn', 'selesai', '2026-11-16 09:15:00'),
(3, 3, 1, 2, '2026-11-17', 'Kepala sering pusing berputar, leher tegang kaku, tensi tinggi saat dicek mandiri', 'Hipertensi Primer Derajat 1', 'Amlodipine 5mg 1x1 pagi, anjuran kurangi konsumsi garam dan kontrol tekanan darah rutin', 'selesai', '2026-11-17 10:00:00'),
(4, 4, 3, 4, '2026-11-17', 'Demam tinggi 38.5 C naik turun disertai batuk pilek berdahak 2 hari', 'Faringitis Akut & ISPA', 'Paracetamol drop 100mg/ml 3x sehari bila demam, Ambroxol sirup 3x1/2 cth, hidrasi cukup', 'selesai', '2026-11-17 11:20:00'),
(5, 5, 1, 2, '2026-11-18', 'Badan lemas, sering haus dan buang air kecil di malam hari', 'Observasi Diabetes Melitus Tipe 2', 'Pemeriksaan laboratorium gula darah puasa (GDP) dan HbA1c', 'diperiksa', '2026-11-18 08:10:00'),
(6, 1, 1, 2, '2026-11-18', 'Kontrol lanjutan keluhan lambung dan pengambilan obat rutin', NULL, NULL, 'menunggu', '2026-11-18 08:45:00'),
(7, 2, 2, 3, '2026-11-18', 'Pembersihan karang gigi (scaling) rutin rahang atas dan bawah', NULL, NULL, 'menunggu', '2026-11-18 09:00:00'),
(8, 3, 3, 4, '2026-11-18', 'Konsultasi imunisasi campak dan pemantauan berat badan balita', NULL, NULL, 'menunggu', '2026-11-18 09:30:00'),
(9, 4, 1, 2, '2026-11-18', 'Pusing dan lemas setelah aktivitas berat', NULL, NULL, 'menunggu', '2026-11-18 10:15:00'),
(10, 5, 2, 3, '2026-11-14', 'Pasien berhalangan hadir ke poliklinik gigi', NULL, NULL, 'batal', '2026-11-14 13:00:00');
