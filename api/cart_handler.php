<?php
// API Handler for Cart Operations (AJAX)
session_start();
require_once '../config.php';
require_once '../db.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'add':
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $user_id = $_SESSION['user_id'];
        
        if ($product_id <= 0) {
            $response = ['success' => false, 'message' => 'Produk tidak valid'];
            break;
        }
        
        // Check product stock
        $product_query = "SELECT name, stock, price FROM products WHERE id = $product_id";
        $product_result = mysqli_query($conn, $product_query);
        
        if (!$product_result || mysqli_num_rows($product_result) == 0) {
            $response = ['success' => false, 'message' => 'Produk tidak ditemukan'];
            break;
        }
        
        $product = mysqli_fetch_assoc($product_result);
        
        if ($product['stock'] < $quantity) {
            $response = ['success' => false, 'message' => 'Stok tidak mencukupi'];
            break;
        }
        
        // Check if product already in cart
        $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update quantity
            $cart_item = mysqli_fetch_assoc($check_result);
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            if ($new_quantity > $product['stock']) {
                $response = ['success' => false, 'message' => 'Jumlah melebihi stok yang tersedia'];
                break;
            }
            
            $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = {$cart_item['id']}";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new cart item
            $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
            mysqli_query($conn, $insert_query);
        }
        
        // Get cart count
        $count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
        $count_result = mysqli_query($conn, $count_query);
        $count = mysqli_fetch_assoc($count_result)['total'] ?? 0;
        
        $response = [
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $count,
            'product_name' => $product['name']
        ];
        break;
        
    case 'update':
        $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $user_id = $_SESSION['user_id'];
        
        if ($quantity < 1) {
            $response = ['success' => false, 'message' => 'Jumlah tidak valid'];
            break;
        }
        
        // Get cart item with product info
        $cart_query = "SELECT c.*, p.stock, p.price FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.id = $cart_id AND c.user_id = $user_id";
        $cart_result = mysqli_query($conn, $cart_query);
        
        if (!$cart_result || mysqli_num_rows($cart_result) == 0) {
            $response = ['success' => false, 'message' => 'Item tidak ditemukan'];
            break;
        }
        
        $cart_item = mysqli_fetch_assoc($cart_result);
        
        if ($quantity > $cart_item['stock']) {
            $response = ['success' => false, 'message' => 'Stok tidak mencukupi'];
            break;
        }
        
        // Update quantity
        $update_query = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id";
        mysqli_query($conn, $update_query);
        
        // Calculate new subtotal
        $subtotal = $cart_item['price'] * $quantity;
        
        // Get cart total
        $total_query = "SELECT SUM(c.quantity * p.price) as total 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = $user_id";
        $total_result = mysqli_query($conn, $total_query);
        $total = mysqli_fetch_assoc($total_result)['total'] ?? 0;
        
        $response = [
            'success' => true,
            'message' => 'Keranjang berhasil diupdate',
            'subtotal' => $subtotal,
            'total' => $total
        ];
        break;
        
    case 'remove':
        $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
        $user_id = $_SESSION['user_id'];
        
        // Delete cart item
        $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        mysqli_query($conn, $delete_query);
        
        // Get cart count
        $count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
        $count_result = mysqli_query($conn, $count_query);
        $count = mysqli_fetch_assoc($count_result)['total'] ?? 0;
        
        // Get cart total
        $total_query = "SELECT SUM(c.quantity * p.price) as total 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = $user_id";
        $total_result = mysqli_query($conn, $total_query);
        $total = mysqli_fetch_assoc($total_result)['total'] ?? 0;
        
        $response = [
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart_count' => $count,
            'total' => $total
        ];
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Aksi tidak valid'];
}

echo json_encode($response);
