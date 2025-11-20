-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ecommerce2
CREATE DATABASE IF NOT EXISTS `ecommerce2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ecommerce2`;

-- Dumping structure for table ecommerce2.cart
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_cart_user` (`user_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.cart: ~2 rows (approximately)
INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
	(1, 2, 1, 1, '2025-10-09 13:02:43'),
	(2, 2, 4, 2, '2025-10-09 13:02:43');

-- Dumping structure for table ecommerce2.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(100) NOT NULL,
  `cat_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.categories: ~5 rows (approximately)
INSERT INTO `categories` (`cat_id`, `cat_title`, `cat_description`, `created_at`) VALUES
	(1, 'Laptops', 'Laptop dan notebook untuk berbagai kebutuhan', '2025-10-09 13:02:43'),
	(2, 'Smartphones', 'Smartphone dan tablet terbaru', '2025-10-09 13:02:43'),
	(3, 'Cameras', 'Kamera DSLR, mirrorless, dan action camera', '2025-10-09 13:02:43'),
	(4, 'Accessories', 'Aksesoris elektronik dan gadget', '2025-10-09 13:02:43'),
	(5, 'Fashion', 'Pakaian dan aksesoris fashion', '2025-10-09 13:02:43');

-- Dumping structure for table ecommerce2.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `shipping_address` text,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.orders: ~2 rows (approximately)
INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `shipping_address`, `shipping_phone`, `shipping_city`, `shipping_postal_code`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 2, 15750000.00, 'paid', 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345', NULL, '2025-10-09 13:02:43', '2025-10-09 13:02:43'),
	(2, 3, 14500000.00, 'shipped', 'Jl. Sudirman No. 456', '08198765432', 'Bandung', '54321', NULL, '2025-10-09 13:02:43', '2025-10-09 13:02:43');

-- Dumping structure for table ecommerce2.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(12,2) NOT NULL COMMENT 'Harga saat pembelian',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.order_items: ~0 rows (approximately)
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
	(1, 1, 1, 'Asus Vivobook 15 OLED', 1, 7500000.00),
	(2, 1, 4, 'Samsung Galaxy S23 Ultra', 1, 15000000.00),
	(3, 2, 7, 'Canon EOS 90D Body', 1, 14500000.00);

-- Dumping structure for table ecommerce2.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_cat` int DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(12,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL COMMENT 'Nama file gambar di folder product_images/',
  `featured` tinyint(1) DEFAULT '0' COMMENT '1=Featured product',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_products_category` (`product_cat`),
  KEY `idx_products_featured` (`featured`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.products: ~3 rows (approximately)
INSERT INTO `products` (`id`, `product_cat`, `product_brand`, `name`, `description`, `price`, `stock`, `image`, `featured`, `created_at`) VALUES
	(1, 1, 'Asus', 'Asus Vivobook 15 OLED', 'Laptop slim dengan layar OLED 15.6 inch, Intel Core i5, RAM 8GB, SSD 512GB. Cocok untuk kerja dan multimedia.', 7500000.00, 10, 'laptop1.jpg', 1, '2025-10-09 13:02:43'),
	(2, 1, 'Lenovo', 'Lenovo ThinkPad X1 Carbon', 'Business laptop premium dengan Intel Core i7, RAM 16GB, SSD 1TB, layar 14 inch Full HD.', 18500000.00, 5, 'laptop2.jpg', 1, '2025-10-09 13:02:43'),
	(3, 1, 'HP', 'HP Pavilion Gaming 15', 'Gaming laptop dengan RTX 3050, AMD Ryzen 5, RAM 16GB, SSD 512GB, layar 144Hz.', 12000000.00, 8, 'laptop3.jpg', 0, '2025-10-09 13:02:43'),
	(4, 2, 'Samsung', 'Samsung Galaxy S23 Ultra', 'Flagship smartphone dengan kamera 200MP, Snapdragon 8 Gen 2, RAM 12GB, storage 256GB.', 15000000.00, 15, 'phone1.jpg', 1, '2025-10-09 13:02:43'),
	(5, 2, 'iPhone', 'iPhone 14 Pro Max', 'Premium smartphone dengan chip A16, kamera 48MP, Dynamic Island, storage 256GB.', 18000000.00, 12, 'phone2.jpg', 1, '2025-10-09 13:02:43'),
	(6, 2, 'Xiaomi', 'Xiaomi 13 Pro', 'Flagship killer dengan kamera Leica, Snapdragon 8 Gen 2, RAM 12GB, storage 256GB.', 11000000.00, 20, 'phone3.jpg', 0, '2025-10-09 13:02:43'),
	(7, 3, 'Canon', 'Canon EOS 90D Body', 'DSLR camera 32.5MP dengan 4K video, Dual Pixel CMOS AF, cocok untuk profesional.', 14500000.00, 5, 'camera1.jpg', 1, '2025-10-09 13:02:43'),
	(8, 3, 'Sony', 'Sony A7 IV Mirrorless', 'Full-frame mirrorless 33MP dengan 4K 60fps, stabilisasi 5-axis, untuk foto dan video.', 28000000.00, 3, 'camera2.jpg', 1, '2025-10-09 13:02:43'),
	(9, 3, 'Fujifilm', 'Fujifilm X-T4', 'Mirrorless APS-C 26MP dengan IBIS, film simulation, cocok untuk street photography.', 19000000.00, 7, 'camera3.jpg', 0, '2025-10-09 13:02:43'),
	(10, 4, 'Logitech', 'Logitech G502 HERO Gaming Mouse', 'Gaming mouse dengan sensor HERO 25K, 11 tombol programmable, RGB lighting.', 750000.00, 25, 'accessory1.jpg', 0, '2025-10-09 13:02:43'),
	(11, 4, 'Razer', 'Razer BlackShark V2 Pro', 'Wireless gaming headset dengan THX Spatial Audio, battery 24 jam, mic detachable.', 2500000.00, 15, 'accessory2.jpg', 0, '2025-10-09 13:02:43'),
	(12, 4, 'Anker', 'Anker PowerCore 20000mAh', 'Power bank kapasitas besar dengan fast charging 18W, multi-port USB.', 450000.00, 30, 'accessory3.jpg', 0, '2025-10-09 13:02:43'),
	(13, 5, 'Nike', 'Nike Air Max 270', 'Sepatu sneakers dengan Air cushioning, desain modern, nyaman untuk aktivitas sehari-hari.', 1500000.00, 20, 'fashion1.jpg', 0, '2025-10-09 13:02:43'),
	(14, 5, 'Adidas', 'Adidas Ultraboost 23', 'Running shoes dengan Boost cushioning, Primeknit upper, responsif dan nyaman.', 2200000.00, 18, 'fashion2.jpg', 0, '2025-10-09 13:02:43');

-- Dumping structure for table ecommerce2.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Prototype: MD5 hash (HARUS MIGRASI ke password_hash)',
  `is_admin` tinyint(1) DEFAULT '0' COMMENT '0=Customer, 1=Admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
	(1, 'Administrator', 'admin@shop.com', '0192023a7bbd73250516f069df18b500', 1, '2025-10-09 13:02:43'),
	(2, 'Budi Santoso', 'budi@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 0, '2025-10-09 13:02:43'),
	(3, 'Siti Nurhaliza', 'siti@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 0, '2025-10-09 13:02:43');

-- Dumping structure for table ecommerce2.user_info
CREATE TABLE IF NOT EXISTS `user_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ecommerce2.user_info: ~2 rows (approximately)
INSERT INTO `user_info` (`id`, `user_id`, `first_name`, `last_name`, `address`, `phone`, `city`, `postal_code`) VALUES
	(1, 2, 'Budi', 'Santoso', 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345'),
	(2, 3, 'Siti', 'Nurhaliza', 'Jl. Sudirman No. 456', '08198765432', 'Bandung', '54321');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
