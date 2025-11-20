<?php
$page_title = "Produk";
include 'header.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? escape($_GET['search']) : '';
$category_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

// Build query
$where_conditions = [];

if (!empty($search)) {
    $where_conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if ($category_id > 0) {
    $where_conditions[] = "p.product_cat = $category_id";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT p.*, c.cat_title
          FROM products p
          LEFT JOIN categories c ON p.product_cat = c.cat_id
          $where_clause
          ORDER BY p.created_at DESC";

$products_result = mysqli_query($conn, $query);

// Get current category name
$current_category = '';
if ($category_id > 0) {
    $cat_query = "SELECT cat_title FROM categories WHERE cat_id = $category_id";
    $cat_result = mysqli_query($conn, $cat_query);
    if ($cat_result && $row = mysqli_fetch_assoc($cat_result)) {
        $current_category = $row['cat_title'];
    }
}
?>

<div class="container py-4">
    <!-- Breadcrumb & Title -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">
                <?php
                if (!empty($search)) {
                    echo 'Hasil Pencarian: "' . clean($search) . '"';
                } elseif ($category_id > 0) {
                    echo clean($current_category);
                } else {
                    echo 'Semua Produk';
                }
                ?>
            </h2>
            <p class="text-muted">
                Menampilkan <?php echo mysqli_num_rows($products_result); ?> produk
            </p>
        </div>

        <div class="col-md-4 text-md-end">
            <?php if (!empty($search) || $category_id > 0): ?>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($products_result) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 border-0 shadow-sm product-clickable" 
                         data-product-id="<?php echo $product['id']; ?>"
                         style="cursor: pointer;">
                        <div class="position-relative">
                            <img src="<?php echo get_product_image($product['image']); ?>"
                                 class="card-img-top product-image"
                                 alt="<?php echo clean($product['name']); ?>">
                            <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                    Stok Terbatas
                                </span>
                            <?php elseif ($product['stock'] == 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                    Stok Habis
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <small class="text-muted">
                                <?php echo clean($product['cat_title'] ?? 'Uncategorized'); ?>
                            </small>
                            <h6 class="card-title mt-1 mb-2">
                                <?php echo clean(truncate($product['name'], 50)); ?>
                            </h6>
                            <p class="card-text small text-muted mb-2">
                                <?php echo clean(truncate($product['description'], 80)); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-primary fw-bold fs-5">
                                    <?php echo format_rupiah($product['price']); ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-box"></i> Stok: <?php echo $product['stock']; ?>
                            </small>
                        </div>

                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="d-grid gap-2">
                                <a href="<?php echo BASE_URL; ?>/product_detail.php?id=<?php echo $product['id']; ?>"
                                   class="btn btn-outline-primary btn-sm"
                                   onclick="event.stopPropagation();">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>

                                <?php if ($product['stock'] > 0 && is_logged_in()): ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>/cart.php" class="m-0"
                                          onclick="event.stopPropagation();">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    </form>
                                <?php elseif (!is_logged_in()): ?>
                                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-primary btn-sm"
                                       onclick="event.stopPropagation();">
                                        <i class="bi bi-box-arrow-in-right"></i> Login untuk Beli
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        Stok Habis
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center py-5">
                    <i class="bi bi-search fs-1"></i>
                    <h5 class="mt-3">Produk tidak ditemukan</h5>
                    <p class="text-muted">Coba kata kunci lain atau lihat semua produk</p>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
