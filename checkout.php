<?php
require_once 'db.php';
require_once 'config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Handle checkout form submission first (before any output)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = escape($_POST['address']);
    $phone = escape($_POST['phone']);
    $city = escape($_POST['city']);
    $postal_code = escape($_POST['postal_code']);
    $notes = escape($_POST['notes'] ?? '');

    // Validate
    if (empty($address) || empty($phone) || empty($city)) {
        set_flash('danger', 'Semua field harus diisi.');
    } else {
        // Get cart items for validation
        $cart_query = "SELECT c.*, p.name, p.price, p.stock
                       FROM cart c
                       JOIN products p ON c.product_id = p.id
                       WHERE c.user_id = $user_id";
        $cart_result = mysqli_query($conn, $cart_query);
        
        $total = 0;
        $cart_items = [];
        while ($item = mysqli_fetch_assoc($cart_result)) {
            $cart_items[] = $item;
            $total += $item['price'] * $item['quantity'];
        }

        // Check stock availability
        $stock_ok = true;
        foreach ($cart_items as $item) {
            if ($item['stock'] < $item['quantity']) {
                $stock_ok = false;
                set_flash('danger', 'Stok produk "' . $item['name'] . '" tidak mencukupi.');
                break;
            }
        }

        if ($stock_ok) {
            // Create order
            $insert_order = "INSERT INTO orders (user_id, total, shipping_address, shipping_phone, shipping_city, shipping_postal_code, notes, status)
                            VALUES ($user_id, $total, '$address', '$phone', '$city', '$postal_code', '$notes', 'pending')";

            if (mysqli_query($conn, $insert_order)) {
                $order_id = mysqli_insert_id($conn);

                // Insert order items and update stock
                foreach ($cart_items as $item) {
                    $product_id = $item['product_id'];
                    $product_name = escape($item['name']);
                    $quantity = $item['quantity'];
                    $price = $item['price'];

                    // Insert order item
                    $insert_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                                   VALUES ($order_id, $product_id, '$product_name', $quantity, $price)";
                    mysqli_query($conn, $insert_item);

                    // Update stock
                    $update_stock = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
                    mysqli_query($conn, $update_stock);
                }

                // Clear cart
                $clear_cart = "DELETE FROM cart WHERE user_id = $user_id";
                mysqli_query($conn, $clear_cart);

                // Save/update user info
                if ($user_info) {
                    $update_info = "UPDATE user_info SET address = '$address', phone = '$phone', city = '$city', postal_code = '$postal_code' WHERE user_id = $user_id";
                    mysqli_query($conn, $update_info);
                } else {
                    $insert_info = "INSERT INTO user_info (user_id, address, phone, city, postal_code) VALUES ($user_id, '$address', '$phone', '$city', '$postal_code')";
                    mysqli_query($conn, $insert_info);
                }

                set_flash('success', 'Pesanan berhasil dibuat!');
                redirect('order_success.php?order_id=' . $order_id);
            } else {
                set_flash('danger', 'Gagal membuat pesanan. Silakan coba lagi.');
            }
        }
    }
}

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price, p.stock
               FROM cart c
               JOIN products p ON c.product_id = p.id
               WHERE c.user_id = $user_id";
$cart_result = mysqli_query($conn, $cart_query);

// Check if cart is empty
if (mysqli_num_rows($cart_result) == 0) {
    set_flash('warning', 'Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.');
    redirect('cart.php');
}

// Calculate total
$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

$page_title = "Checkout";
include 'header.php';

// Get user info
$user_info_query = "SELECT * FROM user_info WHERE user_id = $user_id LIMIT 1";
$user_info_result = mysqli_query($conn, $user_info_query);
$user_info = mysqli_fetch_assoc($user_info_result);

// Form submission is handled at the top of the file
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-credit-card"></i> Checkout</h2>

    <form method="POST" action="">
        <div class="row g-4">
            <!-- Shipping Information -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap *</label>
                            <textarea class="form-control" name="address" rows="3" required
                                      placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan"><?php echo $user_info['address'] ?? ''; ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kota/Kabupaten *</label>
                                <input type="text" class="form-control" name="city" required
                                       value="<?php echo $user_info['city'] ?? ''; ?>"
                                       placeholder="Contoh: Jakarta">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Pos</label>
                                <input type="text" class="form-control" name="postal_code"
                                       value="<?php echo $user_info['postal_code'] ?? ''; ?>"
                                       placeholder="12345">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Telepon *</label>
                            <input type="tel" class="form-control" name="phone" required
                                   value="<?php echo $user_info['phone'] ?? ''; ?>"
                                   placeholder="081234567890">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="2"
                                      placeholder="Catatan untuk penjual atau kurir..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm cart-summary-sticky">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="order-items" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo clean($item['name']); ?></h6>
                                        <small class="text-muted"><?php echo $item['quantity']; ?>x <?php echo format_rupiah($item['price']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong><?php echo format_rupiah($item['price'] * $item['quantity']); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong><?php echo format_rupiah($total); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span class="text-success fw-bold">GRATIS</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                            <h4 class="text-primary fw-bold mb-0"><?php echo format_rupiah($total); ?></h4>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle"></i> Pesanan akan diproses setelah pembayaran dikonfirmasi.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Buat Pesanan
                            </button>
                            <a href="<?php echo BASE_URL; ?>/cart.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
