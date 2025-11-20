<?php
/**
 * Database Connection File
 * Konfigurasi untuk koneksi ke MySQL database
 *
 * CATATAN: Sesuaikan kredensial dengan setup Laragon Anda
 * Default Laragon: host=localhost, user=root, password=empty
 */

$servername = "localhost:3306";
$username = "root";
$password = "";
$db = "ecommerce2";

// Create connection
$conn = mysqli_connect($servername, $username, $password,$db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset UTF8 untuk support karakter Indonesia
mysqli_set_charset($conn, "utf8mb4");

// Base URL untuk aplikasi (sesuaikan jika folder berbeda)
define('BASE_URL', '');

/**
 * Function helper untuk query dengan error handling sederhana
 */
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        // Untuk development, tampilkan error. Untuk production, log error ke file.
        die("Query Error: " . mysqli_error($conn));
    }
    return $result;
}

/**
 * Function untuk escape string (mencegah SQL injection sederhana)
 */
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($string));
}

/**
 * Function untuk sanitize input
 */
function clean($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}
?>
