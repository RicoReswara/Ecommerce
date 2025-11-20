<?php
/**
 * Configuration & Helper Functions
 * Fungsi-fungsi helper untuk aplikasi
 */

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect helper
 */
function redirect($page) {
    header("Location: " . BASE_URL . "/" . $page);
    exit();
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type; // success, danger, warning, info
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

/**
 * Require login (redirect to login if not logged in)
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}

/**
 * Require admin (redirect to login if not admin)
 */
function require_admin() {
    if (!is_admin()) {
        set_flash('danger', 'Akses ditolak. Halaman hanya untuk admin.');
        redirect('login.php');
    }
}

/**
 * Get current user data
 */
function get_logged_user() {
    if (!is_logged_in()) {
        return null;
    }

    global $conn;
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = " . intval($user_id);
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Format currency (Rupiah)
 */
function format_rupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}

/**
 * Get cart count for current user
 */
function get_cart_count() {
    if (!is_logged_in()) {
        return 0;
    }

    global $conn;
    $user_id = $_SESSION['user_id'];
    $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = " . intval($user_id);
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['total'] ?? 0;
    }
    return 0;
}

/**
 * Upload product image
 */
function upload_product_image($file) {
    // Tentukan base path berdasarkan lokasi script
    $is_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $base_path = $is_admin ? '../' : '';
    $target_dir = $base_path . "product_images/";
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $max_size = 2 * 1024 * 1024; // 2MB

    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Tidak ada file yang diupload.'];
    }

    // Get file info
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', $allowed_types)];
    }

    // Validate file size
    if ($file_size > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'];
    }

    // Generate unique filename
    $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;

    // Upload file
    if (move_uploaded_file($file_tmp, $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file.'];
    }
}

/**
 * Delete product image
 */
function delete_product_image($filename) {
    if (empty($filename)) {
        return false;
    }

    $file_path = "product_images/" . $filename;
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

/**
 * Get product image URL
 */
function get_product_image($filename) {
    // Tentukan apakah kita berada di admin panel atau tidak
    $is_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $base_path = $is_admin ? '../' : '';
    
    if (empty($filename)) {
        $no_image_path = $base_path . 'img/no-image.jpg';
        return BASE_URL . '/img/no-image.jpg?v=' . filemtime($no_image_path);
    }

    $file_path = $base_path . "product_images/" . $filename;
    if (file_exists($file_path)) {
        return BASE_URL . '/product_images/' . $filename;
    }

    $no_image_path = $base_path . 'img/no-image.jpg';
    return BASE_URL . '/img/no-image.jpg?v=' . filemtime($no_image_path);
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
?>
