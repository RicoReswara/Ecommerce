<?php
$page_title = "Beranda";
include 'header.php';

// Get featured products
$featured_query = "SELECT p.*, c.cat_title
                   FROM products p
                   LEFT JOIN categories c ON p.product_cat = c.cat_id
                   WHERE p.featured = 1
                   ORDER BY p.id DESC
                   LIMIT 8";
$featured_result = mysqli_query($conn, $featured_query);

// Get categories
$cat_query = "SELECT * FROM categories LIMIT 5";
$cat_result = mysqli_query($conn, $cat_query);
?>

<!-- Hero Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
        <?php
        $carousel_items = [
            [
                'image' => 'banner1.jpg',
                'gradient' => 'bg-gradient-primary',
                'title' => 'Selamat Datang di TechShop',
                'description' => 'Temukan produk teknologi terbaru dengan harga terbaik',
                'button_text' => 'Belanja Sekarang',
                'button_icon' => 'bi-bag',
                'button_link' => BASE_URL . '/products.php'
            ],
            [
                'image' => 'banner2.jpg',
                'gradient' => 'bg-gradient-success',
                'title' => 'Laptop & Notebook',
                'description' => 'Koleksi laptop terlengkap untuk semua kebutuhan Anda',
                'button_text' => 'Lihat Laptop',
                'button_icon' => 'bi-laptop',
                'button_link' => BASE_URL . '/products.php?cat=1'
            ],
            [
                'image' => 'banner3.jpg',
                'gradient' => 'bg-gradient-info',
                'title' => 'Smartphone Terbaru',
                'description' => 'Dapatkan smartphone flagship dengan teknologi terdepan',
                'button_text' => 'Lihat Smartphone',
                'button_icon' => 'bi-phone',
                'button_link' => BASE_URL . '/products.php?cat=2'
            ]
        ];

        foreach ($carousel_items as $index => $item):
            $is_active = $index === 0 ? 'active' : '';
            $image_path = 'img/' . $item['image'];
            $has_image = file_exists($image_path);
        ?>
        <div class="carousel-item <?php echo $is_active; ?>">
            <?php if ($has_image): ?>
                <img src="<?php echo $image_path; ?>" class="d-block w-100" alt="<?php echo $item['title']; ?>">
            <?php else: ?>
                <div class="<?php echo $item['gradient']; ?>"></div>
            <?php endif; ?>
            <div class="carousel-caption">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-md-6 text-start">
                            <h1 class="display-4 fw-bold mb-3"><?php echo $item['title']; ?></h1>
                            <p class="lead mb-4"><?php echo $item['description']; ?></p>
                            <a href="<?php echo $item['button_link']; ?>" class="btn btn-light btn-lg">
                                <i class="bi <?php echo $item['button_icon']; ?>"></i> <?php echo $item['button_text']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">Kategori Populer</h2>
        <div class="row g-3">
            <?php while ($category = mysqli_fetch_assoc($cat_result)): ?>
                <div class="col-6 col-md-4 col-lg">
                    <a href="<?php echo BASE_URL; ?>/products.php?cat=<?php echo $category['cat_id']; ?>"
                       class="text-decoration-none">
                        <div class="card category-card h-100 text-center border-0 shadow-sm">
                            <div class="card-body py-4">
                                <?php 
                                $category_image = 'img/category_' . $category['cat_id'] . '.jpg';
                                if (file_exists($category_image)): ?>
                                    <img src="<?php echo BASE_URL . '/' . $category_image; ?>" 
                                         class="category-image mb-3" 
                                         alt="<?php echo clean($category['cat_title']); ?>">
                                <?php else: 
                                    // Icon mapping berdasarkan kategori
                                    $category_icons = [
                                        'Laptops' => 'bi-laptop',
                                        'Smartphones' => 'bi-phone',
                                        'Cameras' => 'bi-camera',
                                        'Accessories' => 'bi-headphones',
                                        'Fashion' => 'bi-bag-heart'
                                    ];
                                    $icon = $category_icons[$category['cat_title']] ?? 'bi-tag-fill';
                                ?>
                                    <i class="bi <?php echo $icon; ?> text-primary fs-1 mb-3"></i>
                                <?php endif; ?>
                                <h6 class="fw-bold text-dark"><?php echo clean($category['cat_title']); ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Produk Unggulan</h2>
            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-outline-primary">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($featured_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($featured_result)): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100 border-0 shadow-sm product-clickable"
                             data-product-id="<?php echo $product['id']; ?>"
                             style="cursor: pointer;">
                            <div class="position-relative">
                                <img src="<?php echo get_product_image($product['image']); ?>"
                                     class="card-img-top product-image"
                                     alt="<?php echo clean($product['name']); ?>">
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">Featured</span>
                            </div>
                            <div class="card-body">
                                <small class="text-muted"><?php echo clean($product['cat_title'] ?? 'Uncategorized'); ?></small>
                                <h6 class="card-title mt-1 mb-2">
                                    <?php echo clean(truncate($product['name'], 50)); ?>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold fs-5"><?php echo format_rupiah($product['price']); ?></span>
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
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Belum ada produk unggulan.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                <h6 class="fw-bold">Gratis Ongkir</h6>
                <small class="text-muted">Untuk pembelian di atas Rp 500.000</small>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                <h6 class="fw-bold">Pembayaran Aman</h6>
                <small class="text-muted">Transaksi dijamin aman 100%</small>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-arrow-clockwise fs-1 text-primary mb-3"></i>
                <h6 class="fw-bold">Garansi Resmi</h6>
                <small class="text-muted">Semua produk bergaransi resmi</small>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                <h6 class="fw-bold">Customer Service</h6>
                <small class="text-muted">Siap membantu 24/7</small>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
