<?php
// API Handler for Authentication (AJAX)
session_start();
require_once '../config.php';
require_once '../db.php';

// Set JSON header
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'login':
        $email = isset($_POST['email']) ? escape($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        if (empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Email dan password harus diisi'];
            break;
        }
        
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                $redirect_url = BASE_URL . '/index.php';
                if ($user['is_admin'] == 1) {
                    $redirect_url = BASE_URL . '/admin/index.php';
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang, ' . $user['name'],
                    'redirect' => $redirect_url,
                    'user' => [
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'is_admin' => $user['is_admin']
                    ]
                ];
            } else {
                $response = ['success' => false, 'message' => 'Email atau password salah'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Email atau password salah'];
        }
        break;
        
    case 'register':
        $name = isset($_POST['name']) ? escape($_POST['name']) : '';
        $email = isset($_POST['email']) ? escape($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Semua field harus diisi'];
            break;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['success' => false, 'message' => 'Format email tidak valid'];
            break;
        }
        
        if (strlen($password) < 6) {
            $response = ['success' => false, 'message' => 'Password minimal 6 karakter'];
            break;
        }
        
        if ($password !== $confirm_password) {
            $response = ['success' => false, 'message' => 'Password tidak cocok'];
            break;
        }
        
        // Check if email exists
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $response = ['success' => false, 'message' => 'Email sudah terdaftar'];
            break;
        }
        
        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (name, email, password, is_admin, created_at) 
                        VALUES ('$name', '$email', '$hashed_password', 0, NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            $user_id = mysqli_insert_id($conn);
            
            // Auto login
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['is_admin'] = 0;
            
            $response = [
                'success' => true,
                'message' => 'Registrasi berhasil! Selamat datang, ' . $name,
                'redirect' => BASE_URL . '/index.php'
            ];
        } else {
            $response = ['success' => false, 'message' => 'Terjadi kesalahan saat registrasi'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Aksi tidak valid'];
}

echo json_encode($response);
