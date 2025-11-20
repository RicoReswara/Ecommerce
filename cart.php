<?php
require_once 'db.php';
require_once 'config.php';

// Require login
require_login();

$user_id = $_SESSION['user_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        // Add to cart
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        if ($product_id > 0 && $quantity > 0) {
            // Check if product exists and has stock
            $check_query = "SELECT stock FROM products WHERE id = $product_id";
            $check_result = mysqli_query($conn, $check_query);

            if ($check_result && $row = mysqli_fetch_assoc($check_result)) {
                if ($row['stock'] >= $quantity) {
                    // Check if already in cart
                    $cart_check = "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id";
                    $cart_result = mysqli_query($conn, $cart_check);

                    if (mysqli_num_rows($cart_result) > 0) {
                        // Update quantity
                        $cart_row = mysqli_fetch_assoc($cart_result);
                        $new_quantity = $cart_row['quantity'] + $quantity;

                        if ($new_quantity <= $row['stock']) {
                            $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = {$cart_row['id']}";
                            mysqli_query($conn, $update_query);
                            set_flash('success', 'Jumlah produk di keranjang diperbarui.');
                        } else {
                            set_flash('warning', 'Stok tidak mencukupi.');
                        }
                    } else {
                        // Insert new cart item
                        $insert_query = "INSERT INTO cart (user_id, product_id, quantity)
                                        VALUES ($user_id, $product_id, $quantity)";
                        mysqli_query($conn, $insert_query);
                        set_flash('success', 'Produk berhasil ditambahkan ke keranjang.');
                    }
                } else {
                    set_flash('danger', 'Stok tidak mencukupi.');
                }
            }
        }

        // Redirect jika dari halaman lain
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'cart.php') === false) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    if ($action == 'update') {
        // Update cart quantity
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);

        if ($quantity > 0) {
            // Check stock
            $check_query = "SELECT p.stock
                           FROM cart c
                           JOIN products p ON c.product_id = p.id
                           WHERE c.id = $cart_id AND c.user_id = $user_id";
            $check_result = mysqli_query($conn, $check_query);

            if ($check_result && $row = mysqli_fetch_assoc($check_result)) {
                if ($quantity <= $row['stock']) {
                    $update_query = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
                    mysqli_query($conn, $update_query);
                    set_flash('success', 'Keranjang diperbarui.');
                } else {
                    set_flash('warning', 'Stok tidak mencukupi.');
                }
            }
        }
    }

    if ($action == 'remove') {
        // Remove from cart
        $cart_id = intval($_POST['cart_id']);
        $delete_query = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        mysqli_query($conn, $delete_query);
        set_flash('success', 'Produk dihapus dari keranjang.');
    }

    redirect('cart.php');
}

$page_title = "Keranjang Belanja";
include 'header.php';

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price, p.image, p.stock
               FROM cart c
               JOIN products p ON c.product_id = p.id
               WHERE c.user_id = $user_id
               ORDER BY c.added_at DESC";
$cart_result = mysqli_query($conn, $cart_query);

// Calculate total
$total = 0;
$cart_items = [];
while ($item = mysqli_fetch_assoc($cart_result)) {
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Keranjang Belanja</h2>

    <?php if (count($cart_items) > 0): ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): ?>
                    <div class="card border-0 shadow-sm mb-3 cart-item-card">
                        <div class="card-body p-2 p-sm-3">
                            <div class="d-flex align-items-center gap-2 gap-sm-3">
                                <!-- Product Image -->
                                <a href="<?php echo BASE_URL; ?>/product_detail.php?id=<?php echo $item['product_id']; ?>">
                                    <img src="<?php echo get_product_image($item['image']); ?>"
                                         alt="<?php echo clean($item['name']); ?>"
                                         class="rounded cart-item-image">
                                </a>
                                
                                <!-- Product Info & Controls -->
                                <div class="flex-grow-1 d-flex flex-column gap-1">
                                    <!-- Product Name & Price -->
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="<?php echo BASE_URL; ?>/product_detail.php?id=<?php echo $item['product_id']; ?>"
                                               class="text-decoration-none text-dark cart-item-name">
                                                <?php echo clean($item['name']); ?>
                                            </a>
                                        </h6>
                                        <div class="cart-item-price text-muted">
                                            <?php echo format_rupiah($item['price']); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls & Subtotal in one line -->
                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <!-- Quantity Control -->
                                        <form method="POST" action="" class="cart-update-form" data-cart-id="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                            <div class="input-group input-group-sm cart-quantity-control">
                                                <button class="btn btn-outline-secondary btn-minus" type="button" 
                                                        data-quantity="<?php echo $item['quantity']; ?>"
                                                        onclick="handleQuantityChange(this, <?php echo $item['id']; ?>, <?php echo $item['quantity']; ?>)">
                                                    <i class="bi bi-<?php echo $item['quantity'] == 1 ? 'trash' : 'dash'; ?>"></i>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input"
                                                       name="quantity" value="<?php echo $item['quantity']; ?>"
                                                       min="1" max="<?php echo $item['stock']; ?>"
                                                       onchange="this.form.dispatchEvent(new Event('submit'));">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="this.previousElementSibling.stepUp(); this.form.dispatchEvent(new Event('submit'));">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <!-- Subtotal -->
                                        <div class="text-end">
                                            <div class="fw-bold text-primary cart-item-subtotal">
                                                <span class="item-subtotal"><?php echo format_rupiah($item['price'] * $item['quantity']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Hidden Delete Form -->
                        <form method="POST" action="" class="cart-remove-form d-none" data-cart-id="<?php echo $item['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm cart-summary-sticky">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (<span id="cart-item-count"><?php echo count($cart_items); ?></span> item)</span>
                            <strong id="cart-subtotal"><?php echo format_rupiah($total); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkir</span>
                            <span class="text-success fw-bold">GRATIS</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h6 class="fw-bold mb-0">Total</h6>
                            <h5 class="text-primary fw-bold mb-0" id="cart-total"><?php echo format_rupiah($total); ?></h5>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-credit-card"></i> Checkout
                            </a>
                            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-4 mb-3">Keranjang Anda Kosong</h4>
                <p class="text-muted mb-4">Yuk mulai belanja dan tambahkan produk ke keranjang!</p>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop"></i> Mulai Belanja
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
