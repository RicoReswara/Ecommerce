<?php
require_once 'db.php';
require_once 'config.php';

// If already logged in, redirect
if (is_logged_in()) {
    redirect('index.php');
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = escape($_POST['name']);
    $email = escape($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Nama harus diisi.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // Check if email already exists
    if (empty($errors)) {
        $check_query = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = 'Email sudah terdaftar.';
        }
    }

    // If no errors, register user
    if (empty($errors)) {
        $password_md5 = md5($password); // PROTOTYPE ONLY - use password_hash in production

        $insert_query = "INSERT INTO users (name, email, password, is_admin)
                        VALUES ('$name', '$email', '$password_md5', 0)";

        if (mysqli_query($conn, $insert_query)) {
            set_flash('success', 'Registrasi berhasil! Silakan login.');
            redirect('login.php');
        } else {
            $errors[] = 'Gagal registrasi. Silakan coba lagi.';
        }
    }

    // Display errors
    if (!empty($errors)) {
        set_flash('danger', implode('<br>', $errors));
    }
}

$page_title = "Register";
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus text-primary" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold mt-3">Buat Akun Baru</h3>
                        <p class="text-muted">Daftar untuk mulai berbelanja</p>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg" name="name"
                                   placeholder="Nama lengkap Anda" required
                                   value="<?php echo isset($_POST['name']) ? clean($_POST['name']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control form-control-lg" name="email"
                                   placeholder="nama@email.com" required
                                   value="<?php echo isset($_POST['email']) ? clean($_POST['email']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control form-control-lg" name="password"
                                   placeholder="Minimal 6 karakter" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Konfirmasi Password</label>
                            <input type="password" class="form-control form-control-lg" name="confirm_password"
                                   placeholder="Ketik ulang password" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Daftar Sekarang
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun?
                                <a href="<?php echo BASE_URL; ?>/login.php" class="text-decoration-none fw-bold">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Catatan:</strong> Ini adalah prototype dengan keamanan dasar.
                            Untuk production, gunakan enkripsi password yang lebih aman.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
