# 🏥 Sistem Informasi Rumah Sakit Sehat Harmoni (Client UI - Server A)
**Praktikum Service-Oriented Architecture (SOA)**

Aplikasi antarmuka pengguna (Frontend/Client UI) berbasis **PHP Native** dengan arsitektur **Service-Oriented Architecture (SOA)** dan kontrol akses berbasis peran (**Role-Based Access Control / RBAC**). Seluruh pertukaran data dilakukan melalui protokol **HTTP/cURL JSON** menuju Web Service di Server B (`rs_api`). **Server A sama sekali tidak mengakses basis data secara langsung.**

---

## 🎨 Tema Tampilan: Hijau & Putih Medis (Green & Crisp White)
Aplikasi didesain khusus dengan tema estetika rumah sakit modern:
- **Warna Utama:** Emerald Green (`#059669`) & Deep Medical Green (`#047857`)
- **Aksen Lembut:** Mint / Sage Subtle (`#f0fdf4`, `#ecfdf5`, `#a7f3d0`)
- **Latar Belakang:** Crisp Clean White (`#ffffff`) & Soft Slate (`#f8fafc`)
- **Elemen Visual:**
  - Kartu modern dengan efek *hover lift* & bayangan lembut.
  - Tipografi profesional dengan *Plus Jakarta Sans*.
  - Indikator status berwarna (Kuning untuk *Menunggu*, Biru untuk *Diperiksa*, Hijau untuk *Selesai*, Merah untuk *Batal*).
  - Tampilan cetak (*print-friendly*) otomatis menyertakan **Kop Surat Resmi Rumah Sakit** dan kolom tanda tangan dokter/admin saat dicetak.

---

## 💻 Panduan Pemindahan ke Laptop Server A

### 1. Salin Folder
1. Salin seluruh isi folder `rs_client` ini ke Laptop Server A pada direktori web server XAMPP:
   ```
   C:\xampp\htdocs\rs_client
   ```
2. Pastikan layanan **Apache** pada XAMPP Control Panel Laptop Server A dalam keadaan **Start** (Port 80/443).

### 2. Konfigurasi Alamat IP Server B (API)
Buka file `config/config.php` pada Server A:
```php
// C:\xampp\htdocs\rs_client\config\config.php

// Sesuaikan IP Server B (Server API + Database)
define('BASE_API', 'http://192.168.1.12/rs_api');
```
> **Catatan Pengujian:**
> - Jika diuji di lab/jaringan 2 laptop: Ganti `192.168.1.12` dengan IP Server B yang sebenarnya.
> - Jika diuji dalam 1 laptop saja (lokal): Ubah menjadi `define('BASE_API', 'http://localhost/rs_api');`.

---

## 🔑 Akun Uji Coba (Demo Kredensial RBAC)

Database seed telah menyertakan akun siap pakai dengan password terenkripsi BCRYPT:

| Role | Username | Password | Hak Akses Utama |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses penuh seluruh sistem: Kelola Pengguna, Poliklinik, Dokter, Pasien, Asuransi, Kunjungan, dan Semua Laporan |
| **Dokter** | `dr_budi` | `dokter123` | Dashboard pasiennya, pemeriksaan klinis, input diagnosis & tindakan/resep, profil dokter |
| **Pasien** | `pasien1` | `pasien123` | Dashboard mandiri, daftar janji temu baru, riwayat kunjungan & rekam medis sendiri, profil biodata |

---

## 📂 Struktur Direktori `rs_client`

```
rs_client/
├── 403.php                     # Halaman pembatasan akses RBAC (Akses Ditolak)
├── 404.php                     # Halaman tidak ditemukan
├── index.php                   # Landing Page publik Rumah Sakit (profil, poli, dokter, asuransi)
├── login.php                   # Portal masuk pengguna terpadu (kirim kredensial ke Server B)
├── logout.php                  # Keluar dari sistem (revoke token di Server B & hapus session)
├── dashboard.php               # Dashboard dinamis sesuai Role (Admin/Dokter/Pasien)
│
├── config/
│   └── config.php              # Konfigurasi BASE_API, nama RS, helper flash message
│
├── helpers/
│   ├── api.php                 # Helper cURL terpusat (GET, POST, PUT, DELETE, Bearer token)
│   └── auth.php                # Helper RBAC: is_logged_in, require_role, render badge status
│
├── includes/
│   ├── header.php              # Header HTML, CSS, font Google, SweetAlert2
│   ├── navbar.php              # Navigasi atas, info status arsitektur Server A -> B, avatar profil
│   ├── sidebar.php             # Navigasi menu berbasis RBAC (hanya tampilkan menu yang berhak)
│   └── footer.php              # Penutup layout, script JS, toast notifikasi
│
├── assets/
│   ├── css/
│   │   └── style.css           # Tema Hijau-Putih modern, layout card, badge, print layout
│   └── js/
│       └── main.js             # Filter pencarian tabel instan & konfirmasi hapus data
│
├── modules/
│   ├── poliklinik/             # CRUD Poliklinik (Admin: CRUD; Dokter/Pasien: Lihat)
│   ├── dokter/                 # CRUD Dokter & Profil Dokter (Admin: CRUD; Dokter: Profil)
│   ├── pasien/                 # CRUD Pasien & Rekam Medis (Admin: CRUD; Pasien: Profil)
│   ├── asuransi/               # CRUD Rekanan Asuransi (Admin: CRUD; Dokter/Pasien: Lihat)
│   ├── pengguna/               # CRUD Pengguna & Reset Password (Admin Only)
│   ├── kunjungan/              # Pelayanan Kunjungan (Tambah, Periksa/Diagnosa, Detail/Resep)
│   └── laporan/                # Pusat Laporan Resmi & Rekapitulasi (Siap Cetak / Print)
│       ├── index.php           # Pusat Laporan
│       ├── pasien_poli.php     # Laporan pasien per poliklinik
│       ├── pasien_asuransi.php # Laporan pasien berdasarkan penjamin/asuransi
│       ├── kunjungan_periode.php # Laporan kunjungan berdasarkan filter tanggal
│       ├── kunjungan_dokter.php  # Laporan beban penanganan per dokter
│       └── riwayat_pasien.php    # Lembar riwayat rekam medis satu pasien
│
└── database/
    ├── db_rumah_sakit.sql      # Skrip DDL + Data Seed lengkap untuk Server B
    └── generate_hash.php       # Skrip pembantu pembuat hash password BCRYPT
```

---

## 🛡️ Matriks Hak Akses (RBAC) yang Diterapkan

| Fitur / Modul | Administrator | Dokter | Pasien |
|---|:---:|:---:|:---:|
| **Landing Page** | ✅ Publik | ✅ Publik | ✅ Publik |
| **Dashboard** | Statistik RS & Grafik Antrean | Antrean & Pasien Dokter Ini | Ringkasan Janji & Rekam Medis |
| **Kelola Pengguna** | ✅ CRUD Lengkap | ❌ Dibatasi (403) | ❌ Dibatasi (403) |
| **Data Poliklinik** | ✅ CRUD Lengkap | 👁️ Lihat Saja | 👁️ Lihat Saja |
| **Data Dokter** | ✅ CRUD Lengkap | ✏️ Ubah Profil Sendiri | 👁️ Lihat Tim Dokter |
| **Data Pasien** | ✅ CRUD Lengkap | 👁️ Lihat Pasien | ✏️ Ubah Profil Sendiri |
| **Data Asuransi** | ✅ CRUD Lengkap | 👁️ Lihat Rekanan | 👁️ Lihat Rekanan |
| **Kunjungan: Daftar Baru** | ✅ Bisa Buat | ❌ | ✅ Bisa Daftar Sendiri |
| **Kunjungan: Periksa & Diagnosa**| ✅ Bisa Akses | ✅ Isi Diagnosa & Resep | ❌ Dibatasi |
| **Kunjungan: Detail & Resep** | ✅ Lihat & Cetak | ✅ Lihat & Cetak | ✅ Lihat Rekam Medisnya |
| **Laporan Eksekutif** | ✅ Semua Laporan | 👁️ Laporan Periode | ❌ Dibatasi |

---

## 🖨️ Fitur Cetak Laporan (Print Preview)
Semua halaman laporan di modul `modules/laporan/` dan resume medis di `modules/kunjungan/detail.php` memiliki tombol **Cetak Laporan / Cetak Lembar Medis**.
Saat dicetak (`Ctrl+P` atau klik tombol Cetak):
1. Navigasi atas, sidebar, tombol, dan form filter otomatis disembunyikan (`display: none;`).
2. Muncul **Kop Surat Resmi Rumah Sakit Sehat Harmoni** lengkap dengan alamat dan nomor telepon.
3. Muncul kolom **Tanda Tangan dan Nama Terang** pejabat medis/pemeriksa yang sah.
