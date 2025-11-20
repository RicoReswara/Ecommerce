<?php
$page_title = "Detail Produk";
include 'header.php';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id == 0) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('products.php');
}

// Get product details
$query = "SELECT p.*, c.cat_title
          FROM products p
          LEFT JOIN categories c ON p.product_cat = c.cat_id
          WHERE p.id = $product_id
          LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    set_flash('danger', 'Produk tidak ditemukan.');
    redirect('products.php');
}

$product = mysqli_fetch_assoc($result);

// Get related products from same category
$related_query = "SELECT * FROM products
                  WHERE product_cat = {$product['product_cat']}
                  AND id != $product_id
                  LIMIT 4";
$related_result = mysqli_query($conn, $related_query);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/products.php">Produk</a></li>
            <li class="breadcrumb-item active"><?php echo clean($product['name']); ?></li>
        </ol>
    </nav>

    <!-- Product Detail -->
    <div class="row g-4 mb-5">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <img src="<?php echo get_product_image($product['image']); ?>"
                     class="card-img-top"
                     alt="<?php echo clean($product['name']); ?>"
                     style="max-height: 500px; object-fit: contain; background: transparent;">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <div class="mb-2">
                <span class="badge bg-primary"><?php echo clean($product['cat_title'] ?? 'Uncategorized'); ?></span>
                <?php if ($product['featured']): ?>
                    <span class="badge bg-danger">Featured</span>
                <?php endif; ?>
            </div>

            <h2 class="fw-bold mb-3"><?php echo clean($product['name']); ?></h2>

            <?php if (!empty($product['product_brand'])): ?>
                <p class="text-muted mb-3">
                    <i class="bi bi-tag"></i> Brand: <strong><?php echo clean($product['product_brand']); ?></strong>
                </p>
            <?php endif; ?>

            <div class="mb-4">
                <h3 class="text-primary fw-bold"><?php echo format_rupiah($product['price']); ?></h3>
            </div>

            <div class="card bg-light border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Deskripsi Produk</h6>
                    <p class="mb-0"><?php echo nl2br(clean($product['description'])); ?></p>
                </div>
            </div>

            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-auto">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-box text-primary fs-3"></i>
                                <p class="mb-0 small mt-2">Stok: <strong><?php echo $product['stock']; ?></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-shield-check text-success fs-3"></i>
                                <p class="mb-0 small mt-2">Garansi Resmi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-truck text-info fs-3"></i>
                                <p class="mb-0 small mt-2">Gratis Ongkir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <?php if ($product['stock'] > 0): ?>
                <?php if (is_logged_in()): ?>
                    <form method="POST" action="<?php echo BASE_URL; ?>/cart.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                        <div class="row g-3 mb-4">
                            <div class="col-auto">
                                <label class="form-label fw-bold">Jumlah:</label>
                                <input type="number" class="form-control form-control-lg" name="quantity"
                                       value="1" min="1" max="<?php echo $product['stock']; ?>"
                                       style="width: 100px;" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                            </button>
                            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Silakan <a href="<?php echo BASE_URL; ?>/login.php" class="alert-link">login</a>
                        untuk membeli produk ini.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i> Produk ini sedang <strong>stok habis</strong>.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (mysqli_num_rows($related_result) > 0): ?>
        <hr class="my-5">
        <h4 class="fw-bold mb-4">Produk Terkait</h4>
        <div class="row g-4">
            <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <img src="<?php echo get_product_image($related['image']); ?>"
                             class="card-img-top product-image"
                             alt="<?php echo clean($related['name']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo clean(truncate($related['name'], 40)); ?></h6>
                            <p class="text-primary fw-bold mb-2"><?php echo format_rupiah($related['price']); ?></p>
                            <a href="<?php echo BASE_URL; ?>/product_detail.php?id=<?php echo $related['id']; ?>"
                               class="btn btn-sm btn-outline-primary w-100">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
