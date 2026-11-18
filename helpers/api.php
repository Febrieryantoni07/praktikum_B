<?php
/**
 * Helper Pemanggilan Web Service (Server B) menggunakan cURL
 * Berdasarkan PRD Sistem Informasi Rumah Sakit Berbasis SOA
 * 
 * Aturan: Server A tidak boleh akses database langsung!
 * Semua komunikasi data harus melalui fungsi ini menuju Server B.
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Mengirim HTTP Request ke Server B (Web Service API)
 *
 * @param string $endpoint Path endpoint (misal '/auth/login.php' atau '/poliklinik')
 * @param string $method GET, POST, PUT, DELETE
 * @param array|null $data Payload data yang dikirim
 * @param string|null $token Bearer token (otomatis diambil dari session jika ada)
 * @return array Format: ['status' => bool, 'code' => int, 'message' => string, 'data' => mixed]
 */
function api_request($endpoint, $method = 'GET', $data = null, $token = null) {
    // Normalisasi endpoint dan URL agar tidak double slash
    $url = rtrim(BASE_API, '/') . '/' . ltrim($endpoint, '/');

    // Jika token tidak dikirim manual, ambil dari session jika ada
    if ($token === null && isset($_SESSION['token'])) {
        $token = $_SESSION['token'];
    }

    $ch = curl_init();
    $headers = [
        'Accept: application/json',
        'User-Agent: RS-Harmoni-Client-ServerA/1.0'
    ];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $method = strtoupper($method);

    // Konfigurasi per metode HTTP
    if ($method === 'GET') {
        if (!empty($data) && is_array($data)) {
            $queryString = http_build_query($data);
            $url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
        }
    } else {
        $headers[] = 'Content-Type: application/json';
        $jsonPayload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6); // Timeout koneksi 6 detik
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);        // Timeout respon 12 detik
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $rawResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Tangani jika terjadi error koneksi cURL (Server B mati atau jaringan putus)
    if ($curlError) {
        return [
            'status' => false,
            'code' => 0,
            'message' => 'Gagal menghubungi Server API B (' . BASE_API . '): ' . $curlError . '. Pastikan Server B menyala, Apache aktif, dan IP sesuai.',
            'data' => null
        ];
    }

    // Decode JSON dari Server B
    $decoded = json_decode($rawResponse, true);

    // Tangani HTTP 401 (Token kedaluwarsa atau tidak sah)
    if ($httpCode === 401) {
        // Hapus token session
        unset($_SESSION['token']);
        unset($_SESSION['user']);
        unset($_SESSION['role']);
        set_flash('danger', 'Sesi login Anda telah berakhir atau token tidak valid. Silakan login kembali.');
        
        return [
            'status' => false,
            'code' => 401,
            'message' => $decoded['message'] ?? 'Sesi autentikasi telah berakhir.',
            'data' => null
        ];
    }

    // Tangani HTTP 404 (Endpoint API tidak ditemukan)
    if ($httpCode === 404) {
        return [
            'status' => false,
            'code' => 404,
            'message' => 'Endpoint API tidak ditemukan (HTTP 404) pada URL: ' . $url . '. Pastikan folder "rs_api" sudah berada langsung di C:\\xampp\\htdocs\\rs_api (bukan di dalam subfolder), dan periksa IP BASE_API di config/config.php.',
            'data' => null
        ];
    }

    // Jika respon bukan JSON valid
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status' => false,
            'code' => $httpCode,
            'message' => 'Respons dari Server API tidak berformat JSON valid (HTTP ' . $httpCode . '). Cuplikan: ' . htmlspecialchars(substr(strip_tags($rawResponse), 0, 150)),
            'data' => null,
            'raw' => $rawResponse
        ];
    }

    return [
        'status' => $decoded['status'] ?? ($httpCode >= 200 && $httpCode < 300),
        'code' => $httpCode,
        'message' => $decoded['message'] ?? 'Selesai.',
        'data' => $decoded['data'] ?? null
    ];
}
