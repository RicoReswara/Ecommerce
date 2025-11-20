<?php
$page_title = "Edit Profil";
require_once '../db.php';
require_once '../config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = "SELECT u.*, ui.*
               FROM users u
               LEFT JOIN user_info ui ON u.id = ui.user_id
               WHERE u.id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = escape($_POST['name']);
    $first_name = escape($_POST['first_name']);
    $last_name = escape($_POST['last_name']);
    $phone = escape($_POST['phone']);
    $address = escape($_POST['address']);
    $city = escape($_POST['city']);
    $postal_code = escape($_POST['postal_code']);

    // Update user name
    $update_user = "UPDATE users SET name = '$name' WHERE id = $user_id";
    mysqli_query($conn, $update_user);

    // Update session
    $_SESSION['user_name'] = $name;

    // Update or insert user_info
    if ($user && isset($user['first_name'])) {
        // Update existing user_info
        $update_info = "UPDATE user_info SET
                        first_name = '$first_name',
                        last_name = '$last_name',
                        phone = '$phone',
                        address = '$address',
                        city = '$city',
                        postal_code = '$postal_code'
                        WHERE user_id = $user_id";
        mysqli_query($conn, $update_info);
    } else {
        // Insert new user_info
        $insert_info = "INSERT INTO user_info (user_id, first_name, last_name, phone, address, city, postal_code)
                       VALUES ($user_id, '$first_name', '$last_name', '$phone', '$address', '$city', '$postal_code')";
        mysqli_query($conn, $insert_info);
    }

    set_flash('success', 'Profil berhasil diupdate.');
    redirect('user/index.php');
}

include '../header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/user/index.php">Profil</a></li>
            <li class="breadcrumb-item active">Edit Profil</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4"><i class="bi bi-pencil"></i> Edit Profil</h2>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <h5 class="fw-bold mb-3">Informasi Akun</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap *</label>
                            <input type="text" class="form-control" name="name" required
                                   value="<?php echo clean($user['name']); ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Depan</label>
                                <input type="text" class="form-control" name="first_name"
                                       value="<?php echo clean($user['first_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Belakang</label>
                                <input type="text" class="form-control" name="last_name"
                                       value="<?php echo clean($user['last_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" value="<?php echo clean($user['email']); ?>" disabled>
                            <small class="text-muted">Email tidak dapat diubah.</small>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Informasi Kontak</h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Telepon</label>
                            <input type="tel" class="form-control" name="phone"
                                   value="<?php echo clean($user['phone'] ?? ''); ?>"
                                   placeholder="08123456789">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea class="form-control" name="address" rows="3"
                                      placeholder="Jalan, Nomor Rumah, RT/RW"><?php echo clean($user['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kota/Kabupaten</label>
                                <input type="text" class="form-control" name="city"
                                       value="<?php echo clean($user['city'] ?? ''); ?>"
                                       placeholder="Contoh: Jakarta">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Pos</label>
                                <input type="text" class="form-control" name="postal_code"
                                       value="<?php echo clean($user['postal_code'] ?? ''); ?>"
                                       placeholder="12345">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?php echo BASE_URL; ?>/user/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
