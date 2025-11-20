<?php
require_once 'db.php';
require_once 'config.php';

// If already logged in, redirect
if (is_logged_in()) {
    redirect('index.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = escape($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        set_flash('danger', 'Email dan password harus diisi.');
    } else {
        // Check user credentials (using MD5 - PROTOTYPE ONLY)
        $password_md5 = md5($password);
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password_md5' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];

            set_flash('success', 'Login berhasil! Selamat datang, ' . $user['name']);

            // Redirect to admin or user page
            if ($user['is_admin'] == 1) {
                redirect('admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            set_flash('danger', 'Email atau password salah.');
        }
    }
}

$page_title = "Login";
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3">Login ke Akun Anda</h3>
                        <p class="text-muted">Masuk untuk melanjutkan belanja</p>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control form-control-lg" name="email"
                                   placeholder="nama@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control form-control-lg" name="password"
                                   placeholder="Masukkan password" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Belum punya akun?
                                <a href="<?php echo BASE_URL; ?>/register.php" class="text-decoration-none fw-bold">
                                    Daftar di sini
                                </a>
                            </p>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info mb-0">
                        <small>
                            <strong>Demo Account:</strong><br>
                            <strong>Admin:</strong> admin@shop.com / admin123<br>
                            <strong>Customer:</strong> budi@example.com / password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
