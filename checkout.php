<?php
require_once 'db.php';
require_once 'config.php';
require_once 'midtrans_config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];
$snap_token = '';

// Handle checkout form submission first (before any output)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = escape($_POST['address']);
    $phone = escape($_POST['phone']);
    $city = escape($_POST['city']);
    $postal_code = escape($_POST['postal_code']);
    $notes = escape($_POST['notes'] ?? '');

    // Initialize response
    $response = ['success' => false, 'message' => '', 'snap_token' => '', 'order_id' => 0];

    // Validate
    if (empty($address) || empty($phone) || empty($city)) {
        $response['message'] = 'Semua field harus diisi.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
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
                $response['message'] = 'Stok produk "' . $item['name'] . '" tidak mencukupi.';
                break;
            }
        }

        if (!$stock_ok) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        if ($stock_ok && $total > 0) {
            // Get user data
            $user_query = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
            $user_result = mysqli_query($conn, $user_query);
            $user_data = mysqli_fetch_assoc($user_result);

            // Create order (menggunakan status enum: pending, paid, shipped, completed, cancelled)
            $insert_order = "INSERT INTO orders (user_id, total, shipping_address, shipping_phone, shipping_city, shipping_postal_code, notes, status)
                            VALUES ($user_id, $total, '$address', '$phone', '$city', '$postal_code', '$notes', 'pending')";

            if (mysqli_query($conn, $insert_order)) {
                $order_id = mysqli_insert_id($conn);
                $response['order_id'] = $order_id;

                // Insert order items (don't update stock yet, wait for payment confirmation)
                foreach ($cart_items as $item) {
                    $product_id = $item['product_id'];
                    $product_name = escape($item['name']);
                    $quantity = $item['quantity'];
                    $price = $item['price'];

                    // Insert order item
                    $insert_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                                   VALUES ($order_id, $product_id, '$product_name', $quantity, $price)";
                    mysqli_query($conn, $insert_item);
                }

                // Save/update user info
                $user_info_query = "SELECT * FROM user_info WHERE user_id = $user_id LIMIT 1";
                $user_info_result = mysqli_query($conn, $user_info_query);
                $user_info = mysqli_fetch_assoc($user_info_result);

                if ($user_info) {
                    $update_info = "UPDATE user_info SET address = '$address', phone = '$phone', city = '$city', postal_code = '$postal_code' WHERE user_id = $user_id";
                    mysqli_query($conn, $update_info);
                } else {
                    $insert_info = "INSERT INTO user_info (user_id, address, phone, city, postal_code) VALUES ($user_id, '$address', '$phone', '$city', '$postal_code')";
                    mysqli_query($conn, $insert_info);
                }

                // Generate Midtrans Snap Token
                try {
                    $orderData = [
                        'id' => $order_id,
                        'customer_name' => $user_data['name'],
                        'customer_email' => $user_data['email'],
                        'phone' => $phone,
                    ];

                    $transaction = generate_midtrans_transaction($orderData, $cart_items, $total);
                    $snap_token = generate_snap_token($transaction);

                    if ($snap_token) {
                        // Save snap token to database
                        $snap_token_escaped = escape($snap_token);
                        $update_token = "UPDATE orders SET snap_token = '$snap_token_escaped' WHERE id = $order_id";
                        mysqli_query($conn, $update_token);
                        
                        // Clear cart only after snap token is generated successfully
                        $clear_cart = "DELETE FROM cart WHERE user_id = $user_id";
                        mysqli_query($conn, $clear_cart);

                        // Return success response with snap token
                        $response['success'] = true;
                        $response['snap_token'] = $snap_token;
                        $response['message'] = 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.';
                    } else {
                        $response['message'] = 'Gagal mengoneksikan ke payment gateway. Silakan coba lagi.';
                        // Delete order if snap token fails
                        $delete_order = "DELETE FROM orders WHERE id = $order_id";
                        mysqli_query($conn, $delete_order);
                    }
                } catch (Exception $e) {
                    error_log('Midtrans Error: ' . $e->getMessage());
                    $response['message'] = 'Gagal memproses pembayaran: ' . $e->getMessage();
                    // Delete order if payment processing fails
                    $delete_order = "DELETE FROM orders WHERE id = $order_id";
                    mysqli_query($conn, $delete_order);
                }
            } else {
                $response['message'] = 'Gagal membuat pesanan. Silakan coba lagi.';
            }
        } else if ($total == 0) {
            $response['message'] = 'Keranjang Anda kosong.';
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
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
?>

<!-- Load Midtrans Snap JS -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>

<div class="container py-4">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/cart.php">Keranjang</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <h2 class="fw-bold mb-4"><i class="bi bi-credit-card"></i> Checkout</h2>

    <!-- Alert Messages -->
    <div id="alert-container"></div>

    <form method="POST" action="" id="checkout-form">
        <div class="row g-4">
            <!-- Shipping Information -->
            <div class="col-lg-7">
                <!-- Informasi Pengiriman -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Penerima *</label>
                            <input type="text" class="form-control" name="recipient_name" 
                                   value="<?php echo isset($user_data) ? clean($user_data['name']) : ''; ?>" 
                                   placeholder="Nama lengkap penerima" required>
                        </div>

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
                                       placeholder="Contoh: Jakarta Selatan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Pos</label>
                                <input type="text" class="form-control" name="postal_code"
                                       value="<?php echo $user_info['postal_code'] ?? ''; ?>"
                                       placeholder="12345" pattern="[0-9]{5}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Telepon *</label>
                            <input type="tel" class="form-control" name="phone" required
                                   value="<?php echo $user_info['phone'] ?? ''; ?>"
                                   placeholder="081234567890" pattern="[0-9+]{10,}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Catatan Pengiriman (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="2"
                                      placeholder="Contoh: Barang mudah pecah, letakkan di teras, dll..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Pengiriman -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Pilihan Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_regular" value="regular" checked>
                            <label class="form-check-label" for="shipping_regular">
                                <strong>Pengiriman Reguler</strong>
                                <br>
                                <small class="text-muted">5-7 hari kerja - GRATIS</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_express" value="express">
                            <label class="form-check-label" for="shipping_express">
                                <strong>Pengiriman Express</strong>
                                <br>
                                <small class="text-muted">1-2 hari kerja - Rp 50.000</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary & Payment -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="order-items" style="max-height: 350px; overflow-y: auto;">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-1"><?php echo clean($item['name']); ?></h6>
                                        <small class="text-muted"><?php echo $item['quantity']; ?> × <?php echo format_rupiah($item['price']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong><?php echo format_rupiah($item['price'] * $item['quantity']); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="my-3">

                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <strong><?php echo format_rupiah($total); ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya Admin</span>
                                <span class="text-danger">Rp 0</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span class="text-success fw-bold" data-shipping-cost><?php echo format_rupiah(0); ?></span>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between mb-4">
                                <h6 class="mb-0 fw-bold">Total Pembayaran</h6>
                                <h5 class="mb-0 text-primary fw-bold" data-total-price><?php echo format_rupiah($total); ?></h5>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Perhatian:</strong> Pesanan akan diproses setelah pembayaran dikonfirmasi.
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-lg fw-bold" id="checkout-btn">
                                <i class="bi bi-credit-card"></i> Lanjutkan ke Pembayaran
                            </button>
                            <a href="<?php echo BASE_URL; ?>/cart.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>

                        <!-- Payment Method Info -->
                        <div class="mt-3 pt-3 border-top">
                            <p class="small text-muted mb-2"><i class="bi bi-shield-check"></i> <strong>Metode Pembayaran Aman</strong></p>
                            <p class="small text-muted mb-0">
                                Pembayaran diproses oleh <strong>Midtrans</strong> - Payment Gateway terpercaya di Indonesia.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    @media (max-width: 992px) {
        .card.sticky-top {
            position: relative !important;
            top: auto !important;
            margin-bottom: 2rem;
        }
    }

    .order-items {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.1);
    }

    .order-items::-webkit-scrollbar {
        width: 6px;
    }

    .order-items::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .order-items::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .order-items::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutForm = document.getElementById('checkout-form');
    const alertContainer = document.getElementById('alert-container');
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');

    // Base total (dari PHP)
    let baseTotal = <?php echo (int)$total; ?>;
    const shippingCosts = {
        'regular': 0,
        'express': 50000
    };

    // Handle shipping method change
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateTotalPrice();
        });
    });

    // Function to update total price
    function updateTotalPrice() {
        const selectedShipping = document.querySelector('input[name="shipping_method"]:checked').value;
        const shippingCost = shippingCosts[selectedShipping] || 0;
        const totalPrice = baseTotal + shippingCost;

        // Update ongkos kirim display
        const shippingDisplay = document.querySelector('[data-shipping-cost]');
        if (shippingDisplay) {
            shippingDisplay.textContent = formatRupiah(shippingCost);
        }

        // Update total pembayaran display
        const totalDisplay = document.querySelector('[data-total-price]');
        if (totalDisplay) {
            totalDisplay.textContent = formatRupiah(totalPrice);
        }

        // Update form value for total
        const totalInput = document.querySelector('input[name="total_with_shipping"]');
        if (totalInput) {
            totalInput.value = totalPrice;
        }
    }

    // Format Rupiah helper
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Handle checkout button click
    checkoutBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        // Validate form
        if (!checkoutForm.checkValidity()) {
            checkoutForm.reportValidity();
            return;
        }

        // Disable button and show loading
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

        try {
            // Collect form data
            const formData = new FormData(checkoutForm);

            // Send AJAX request
            const response = await fetch('<?php echo BASE_URL; ?>/checkout.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.snap_token) {
                // Show Midtrans payment modal
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        // Payment successful - redirect to orders page
                        window.location.href = '<?php echo BASE_URL; ?>/user/orders.php';
                    },
                    onPending: function(result) {
                        // Payment pending - redirect to orders page
                        console.log('Pending payment', result);
                        window.location.href = '<?php echo BASE_URL; ?>/user/orders.php';
                    },
                    onError: function(result) {
                        // Payment error
                        console.log('Payment error', result);
                        checkoutBtn.disabled = false;
                        checkoutBtn.innerHTML = '<i class="bi bi-credit-card"></i> Lanjutkan ke Pembayaran';
                        showAlert('Pembayaran gagal! Silakan coba lagi.', 'danger');
                    },
                    onClose: function() {
                        // User closed modal without completing payment
                        console.log('Customer closed the modal');
                        // Redirect to orders page
                        window.location.href = '<?php echo BASE_URL; ?>/user/orders.php';
                    }
                });
            } else {
                // Error response
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = '<i class="bi bi-credit-card"></i> Lanjutkan ke Pembayaran';
                showAlert(data.message || 'Gagal memproses pesanan.', 'danger');
            }
        } catch (error) {
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i class="bi bi-credit-card"></i> Lanjutkan ke Pembayaran';
            console.error('Error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
    });

    // Helper function to show alert
    function showAlert(message, type = 'info') {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        // Scroll to alert
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Initialize total price on page load
    updateTotalPrice();
});
</script>

<?php include 'footer.php'; ?>
