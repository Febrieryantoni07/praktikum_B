<?php
/**
 * Skrip Generator Hash Password untuk Data Seed Server B
 * Berdasarkan PRD Bab 4.3 & Bab 11
 * 
 * Jalankan via CLI: php generate_hash.php
 * atau buka di browser: http://localhost/rs_client/database/generate_hash.php
 */

$passwords = [
    'admin'     => 'admin123',
    'dr_budi'   => 'dokter123',
    'dr_siti'   => 'dokter123',
    'dr_andi'   => 'dokter123',
    'pasien1'   => 'pasien123',
    'pasien2'   => 'pasien123'
];

echo "=== HASH GENERATOR PASSWORD BCRYPT ===\n\n";

foreach ($passwords as $user => $pass) {
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    echo "Username : " . str_pad($user, 10) . " | Password: " . str_pad($pass, 12) . " | Hash: " . $hash . "\n";
}
