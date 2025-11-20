<?php
// API Handler for Admin Operations (AJAX)
session_start();
require_once '../config.php';
require_once '../db.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Hanya admin yang dapat mengakses.']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'delete_product':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID produk tidak valid'];
            break;
        }
        
        // Get product image to delete
        $query = "SELECT image FROM products WHERE id = $id";
        $result = mysqli_query($conn, $query);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            delete_product_image($row['image']);
        }
        
        // Delete product
        $delete_query = "DELETE FROM products WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $response = ['success' => true, 'message' => 'Produk berhasil dihapus'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal menghapus produk'];
        }
        break;
        
    case 'delete_user':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID user tidak valid'];
            break;
        }
        
        // Prevent deleting own account
        if ($id == $_SESSION['user_id']) {
            $response = ['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'];
            break;
        }
        
        // Delete user
        $delete_query = "DELETE FROM users WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $response = ['success' => true, 'message' => 'User berhasil dihapus'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal menghapus user'];
        }
        break;
        
    case 'delete_category':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID kategori tidak valid'];
            break;
        }
        
        // Check if category has products
        $check_query = "SELECT COUNT(*) as count FROM products WHERE product_cat = $id";
        $check_result = mysqli_query($conn, $check_query);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            $response = ['success' => false, 'message' => 'Kategori masih memiliki produk. Hapus produk terlebih dahulu.'];
            break;
        }
        
        // Delete category
        $delete_query = "DELETE FROM categories WHERE cat_id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $response = ['success' => true, 'message' => 'Kategori berhasil dihapus'];
        } else {
            $response = ['success' => false, 'message' => 'Gagal menghapus kategori'];
        }
        break;
        
    case 'update_order_status':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? escape($_POST['status']) : '';
        
        if ($id <= 0 || empty($status)) {
            $response = ['success' => false, 'message' => 'Data tidak valid'];
            break;
        }
        
        $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            $response = ['success' => false, 'message' => 'Status tidak valid'];
            break;
        }
        
        // Update order status
        $update_query = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $id";
        if (mysqli_query($conn, $update_query)) {
            $response = [
                'success' => true, 
                'message' => 'Status order berhasil diupdate',
                'status' => $status
            ];
        } else {
            $response = ['success' => false, 'message' => 'Gagal mengupdate status order'];
        }
        break;
        
    case 'toggle_featured':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'ID produk tidak valid'];
            break;
        }
        
        // Get current featured status
        $query = "SELECT featured FROM products WHERE id = $id";
        $result = mysqli_query($conn, $query);
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $new_featured = $row['featured'] ? 0 : 1;
            
            // Update featured status
            $update_query = "UPDATE products SET featured = $new_featured WHERE id = $id";
            if (mysqli_query($conn, $update_query)) {
                $response = [
                    'success' => true,
                    'message' => 'Status featured berhasil diupdate',
                    'featured' => $new_featured
                ];
            } else {
                $response = ['success' => false, 'message' => 'Gagal mengupdate status'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Aksi tidak valid'];
}

echo json_encode($response);
