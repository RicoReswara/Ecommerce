-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.infinityfree.com
-- Generation Time: Dec 06, 2025 at 04:19 AM
-- Server version: 10.6.22-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40402503_ecommerce2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(11, 5, 15, 1, '2025-11-13 13:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_title` varchar(100) NOT NULL,
  `cat_description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_title`, `cat_description`, `image`, `created_at`) VALUES
(1, 'Laptops', 'Laptop dan notebook untuk berbagai kebutuhan', NULL, '2025-10-09 20:02:43'),
(2, 'Smartphones', 'Smartphone dan tablet terbaru', NULL, '2025-10-09 20:02:43'),
(3, 'Cameras', 'Kamera DSLR, mirrorless, dan action camera', NULL, '2025-10-09 20:02:43'),
(4, 'Accessories', 'Aksesoris elektronik dan gadget', NULL, '2025-10-09 20:02:43'),
(5, 'Fashion', 'Pakaian dan aksesoris fashion', NULL, '2025-10-09 20:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `snap_token` varchar(255) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `snap_token`, `shipping_address`, `shipping_phone`, `shipping_city`, `shipping_postal_code`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, '15750000.00', 'paid', NULL, 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345', NULL, '2025-10-09 20:02:43', '2025-10-09 20:02:43'),
(2, 3, '14500000.00', 'shipped', NULL, 'Jl. Sudirman No. 456', '08198765432', 'Bandung', '54321', NULL, '2025-10-09 20:02:43', '2025-10-09 20:02:43'),
(3, 2, '37500000.00', 'pending', NULL, 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345', '', '2025-10-09 21:03:43', '2025-10-09 21:03:43'),
(4, 2, '0.00', 'cancelled', NULL, 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345', '', '2025-10-09 21:05:35', '2025-10-10 08:43:57'),
(5, 1, '2200000.00', 'cancelled', NULL, 'sadfghj', '123456787890o', 'bvn', '12342', '', '2025-10-10 07:59:45', '2025-10-10 08:01:17'),
(6, 1, '14280000.00', 'pending', NULL, 'sadfghj', '123456787890o', 'bvn', '12342', '', '2025-11-13 00:45:12', '2025-11-13 00:45:12'),
(7, 1, '20000.00', 'pending', NULL, 'sadfghj', '123456787890o', 'bvn', '12342', '', '2025-11-13 00:55:57', '2025-11-13 00:55:57'),
(8, 6, '133000000.00', 'pending', NULL, 'ahsbsbjs', '76799794', 'shshsbs', '', '', '2025-11-19 00:49:33', '2025-11-19 00:49:33'),
(9, 7, '60000.00', 'pending', NULL, 'gujjkkm', '054634548484', 'hauah', 'ahauah', 'cah', '2025-11-29 05:50:05', '2025-11-29 05:50:05'),
(10, 1, '15020000.00', 'pending', NULL, 'sadfghj', '123456787890o', 'bvn', '12342', 'nig', '2025-12-04 15:27:34', '2025-12-04 15:27:34'),
(11, 1, '40000.00', 'pending', NULL, 'sadfghj', '123456787890', 'bvn', '12342', '', '2025-12-05 03:47:34', '2025-12-05 03:47:34'),
(12, 1, '20000.00', 'pending', NULL, 'sadfghj', '123456787890', 'bvn', '12342', '', '2025-12-05 03:48:51', '2025-12-05 03:48:51'),
(13, 1, '7500000.00', 'pending', NULL, 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 07:41:26', '2025-12-06 07:41:26'),
(14, 9, '25500000.00', 'pending', NULL, 'Jalan Gejayan', '081234567890', 'Sleman', '12345', '-', '2025-12-06 07:55:31', '2025-12-06 07:55:31'),
(15, 1, '11000000.00', 'pending', NULL, 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 07:55:53', '2025-12-06 07:55:53'),
(16, 1, '7500000.00', 'pending', NULL, 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 07:56:57', '2025-12-06 07:56:57'),
(17, 1, '11000000.00', 'pending', NULL, 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 07:59:30', '2025-12-06 07:59:30'),
(18, 9, '25500000.00', 'pending', NULL, 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345', '', '2025-12-06 08:15:31', '2025-12-06 08:15:31'),
(19, 9, '25500000.00', 'pending', NULL, 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345', '', '2025-12-06 08:28:21', '2025-12-06 08:28:21'),
(20, 9, '25500000.00', 'pending', NULL, 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345', '', '2025-12-06 08:33:15', '2025-12-06 08:33:15'),
(21, 1, '7500000.00', 'pending', '1e310f4f-6e4d-4907-96a5-785edbf871ef', 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 09:00:57', '2025-12-06 09:00:58'),
(22, 9, '25500000.00', 'pending', 'f2514cf6-d38f-4939-9f3d-fe7a3871c078', 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345', '', '2025-12-06 09:09:23', '2025-12-06 09:09:24'),
(23, 1, '12000000.00', 'pending', '5bd1edac-d564-4750-8113-2df7a1f7c1c2', 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 09:11:50', '2025-12-06 09:11:51'),
(24, 1, '7500000.00', 'pending', '95bf9480-f5c0-4733-816d-5716d01ab49a', 'Jl melati', '123456787890', 'Sleman', '53149', '', '2025-12-06 09:12:31', '2025-12-06 09:12:32'),
(25, 9, '25500000.00', 'pending', 'dcaa644f-5a99-45da-95d0-68e00747946f', 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345', '', '2025-12-06 09:14:01', '2025-12-06 09:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL COMMENT 'Harga saat pembelian'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
(1, 1, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(2, 1, 4, 'Samsung Galaxy S23 Ultra', 1, '15000000.00'),
(3, 2, 7, 'Canon EOS 90D Body', 1, '14500000.00'),
(4, 3, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(5, 3, 4, 'Samsung Galaxy S23 Ultra', 2, '15000000.00'),
(7, 6, 3, 'HP Pavilion Gaming 15', 1, '12000000.00'),
(9, 6, 15, 'Pin Turtle', 4, '20000.00'),
(10, 7, 15, 'Pin Turtle', 1, '20000.00'),
(11, 8, 9, 'Fujifilm X-T4', 7, '19000000.00'),
(12, 9, 15, 'Pin Turtle', 3, '20000.00'),
(13, 10, 1, 'Asus Vivobook 15 OLED', 2, '7500000.00'),
(14, 10, 15, 'Pin Turtle', 1, '20000.00'),
(15, 11, 15, 'Pin Turtle', 2, '20000.00'),
(16, 12, 15, 'Pin Turtle', 1, '20000.00'),
(17, 13, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(18, 14, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(19, 14, 5, 'iPhone 14 Pro Max', 1, '18000000.00'),
(20, 15, 6, 'Xiaomi 13 Pro', 1, '11000000.00'),
(21, 16, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(22, 17, 6, 'Xiaomi 13 Pro', 1, '11000000.00'),
(23, 18, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(24, 18, 5, 'iPhone 14 Pro Max', 1, '18000000.00'),
(25, 19, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(26, 19, 5, 'iPhone 14 Pro Max', 1, '18000000.00'),
(27, 20, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(28, 20, 5, 'iPhone 14 Pro Max', 1, '18000000.00'),
(29, 21, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(30, 22, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(31, 22, 5, 'iPhone 14 Pro Max', 1, '18000000.00'),
(32, 23, 3, 'HP Pavilion Gaming 15', 1, '12000000.00'),
(33, 24, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(34, 25, 1, 'Asus Vivobook 15 OLED', 1, '7500000.00'),
(35, 25, 5, 'iPhone 14 Pro Max', 1, '18000000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_cat` int(11) DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL COMMENT 'Nama file gambar di folder product_images/',
  `featured` tinyint(1) DEFAULT 0 COMMENT '1=Featured product',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_cat`, `product_brand`, `name`, `description`, `price`, `stock`, `image`, `featured`, `created_at`) VALUES
(1, 1, 'Asus', 'Asus Vivobook 15 OLED', 'Laptop slim dengan layar OLED 15.6 inch, Intel Core i5, RAM 8GB, SSD 512GB. Cocok untuk kerja dan multimedia.', '7500000.00', 7, '693354c1bdfeb_1764971713.png', 1, '2025-10-09 20:02:43'),
(2, 1, 'Lenovo', 'Lenovo ThinkPad X1 Carbon', 'Business laptop premium dengan Intel Core i7, RAM 16GB, SSD 1TB, layar 14 inch Full HD.', '18500000.00', 5, '693355940d36e_1764971924.jpg', 1, '2025-10-09 20:02:43'),
(3, 1, 'HP', 'HP Pavilion Gaming 15', 'Gaming laptop dengan RTX 3050, AMD Ryzen 5, RAM 16GB, SSD 512GB, layar 144Hz.', '12000000.00', 7, '693355f210911_1764972018.png', 1, '2025-10-09 20:02:43'),
(4, 2, 'Samsung', 'Samsung Galaxy S23 Ultra', 'Flagship smartphone dengan kamera 200MP, Snapdragon 8 Gen 2, RAM 12GB, storage 256GB.', '15000000.00', 13, '693356ab1b3bf_1764972203.png', 1, '2025-10-09 20:02:43'),
(5, 2, 'iPhone', 'iPhone 14 Pro Max', 'Premium smartphone dengan chip A16, kamera 48MP, Dynamic Island, storage 256GB.', '18000000.00', 12, '6933590b27da2_1764972811.jpg', 1, '2025-10-09 20:02:43'),
(6, 2, 'Xiaomi', 'Xiaomi 13 Pro', 'Flagship killer dengan kamera Leica, Snapdragon 8 Gen 2, RAM 12GB, storage 256GB.', '11000000.00', 20, '69335bd0d3f0b_1764973520.png', 1, '2025-10-09 20:02:43'),
(7, 3, 'Canon', 'Canon EOS 90D Body', 'DSLR camera 32.5MP dengan 4K video, Dual Pixel CMOS AF, cocok untuk profesional.', '14500000.00', 5, '69335c1ee34cd_1764973598.jpg', 1, '2025-10-09 20:02:43'),
(8, 3, 'Sony', 'Sony A7 IV Mirrorless', 'Full-frame mirrorless 33MP dengan 4K 60fps, stabilisasi 5-axis, untuk foto dan video.', '28000000.00', 0, '69335c98dcaa3_1764973720.jpg', 0, '2025-10-09 20:02:43'),
(9, 3, 'Fujifilm', 'Fujifilm X-T4', 'Mirrorless APS-C 26MP dengan IBIS, film simulation, cocok untuk street photography.', '19000000.00', 0, '69335d558f930_1764973909.png', 0, '2025-10-09 20:02:43'),
(10, 4, 'Logitech', 'Logitech G502 HERO Gaming Mouse', 'Gaming mouse dengan sensor HERO 25K, 11 tombol programmable, RGB lighting.', '750000.00', 25, '69335d98775f8_1764973976.png', 0, '2025-10-09 20:02:43'),
(11, 4, 'Razer', 'Razer BlackShark V2 Pro', 'Wireless gaming headset dengan THX Spatial Audio, battery 24 jam, mic detachable.', '2500000.00', 15, '69335dd59610e_1764974037.jpg', 0, '2025-10-09 20:02:43'),
(12, 4, 'Anker', 'Anker PowerCore 20000mAh', 'Power bank kapasitas besar dengan fast charging 18W, multi-port USB.', '450000.00', 30, '69335df7b173a_1764974071.jpg', 0, '2025-10-09 20:02:43'),
(15, 5, 'adaaja', 'Pin Turtle', 'pin', '20000.00', 11, '68e864bfe078d_1760060607.jpg', 1, '2025-10-10 08:43:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Prototype: MD5 hash (HARUS MIGRASI ke password_hash)',
  `is_admin` tinyint(1) DEFAULT 0 COMMENT '0=Customer, 1=Admin',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'Administrator', 'admin@shop.com', '0192023a7bbd73250516f069df18b500', 1, '2025-10-09 20:02:43'),
(2, 'Budi Santoso', 'budi@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 0, '2025-10-09 20:02:43'),
(3, 'Siti Nurhaliza', 'siti@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', 0, '2025-10-09 20:02:43'),
(4, 'Rico Reswara', 'rico@customer.com', '5f4dcc3b5aa765d61d8327deb882cf99', 0, '2025-10-09 21:10:23'),
(5, 'Testku', 'test@gmail.com', 'cc03e747a6afbbcbf8be7668acfebee5', 0, '2025-11-13 05:44:02'),
(6, 'abcde', 'abcde@gmail.com', '7dee600ff777ef00b0e2341ede871913', 0, '2025-11-19 00:48:03'),
(7, 'abcde', 'ahsjsj@gmail.com', '1bd00b80a4dc5c21f12eea547737f252', 0, '2025-11-29 05:48:50'),
(8, 'mirelle', 'mirelle123@gmail.com', '11442b13294f8f9049efdb1e3893a822', 0, '2025-12-05 15:55:00'),
(9, 'Nicosius Aldho Wiratama', 'nicosius@gmail.com', '25d55ad283aa400af464c76d713c07ad', 0, '2025-12-06 05:20:37'),
(10, 'jaden', 'nmnm@gmail.com', '7f3160018843f919debf03a56ac7b8ff', 0, '2025-12-06 08:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `first_name`, `last_name`, `address`, `phone`, `city`, `postal_code`) VALUES
(1, 2, 'Budi', 'Santoso', 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345'),
(2, 3, 'Siti', 'Nurhaliza', 'Jl. Sudirman No. 456', '08198765432', 'Bandung', '54321'),
(3, 2, NULL, NULL, 'Jl. Merdeka No. 123', '08123456789', 'Jakarta', '12345'),
(4, 1, NULL, NULL, 'Jl melati', '123456787890', 'Sleman', '53149'),
(5, 1, NULL, NULL, 'Jl melati', '123456787890', 'Sleman', '53149'),
(6, 1, NULL, NULL, 'Jl melati', '123456787890', 'Sleman', '53149'),
(7, 6, NULL, NULL, 'ahsbsbjs', '76799794', 'shshsbs', ''),
(8, 7, NULL, NULL, 'gujjkkm', '054634548484', 'hauah', 'ahauah'),
(9, 1, NULL, NULL, 'Jl melati', '123456787890', 'Sleman', '53149'),
(10, 9, NULL, NULL, 'Jalan Gejayan Gang Dahlia', '081234567890', 'Sleman', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_cart_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`product_cat`),
  ADD KEY `idx_products_featured` (`featured`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
