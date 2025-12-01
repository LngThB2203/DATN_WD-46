-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th10 26, 2025 lúc 03:59 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `da`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banners`
--

CREATE TABLE `banners` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banners`
--

INSERT INTO `banners` (`id`, `title`, `image`, `link`, `start_date`, `end_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'uploads/banners/rQNZP98Rv6tG0m974gHqfYGDy6JAGGAs4oxppPtw.png', 'http://127.0.0.1:8000/', '2025-11-19', '2025-11-23', 5, '2025-11-18 23:58:28', '2025-11-18 23:58:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:1;', 1763517566),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1763517566;', 1763517566);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 3, '2025-11-13 21:45:37', '2025-11-13 21:45:37'),
(3, NULL, '2025-11-25 00:15:23', '2025-11-25 00:15:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `variant_id`, `quantity`, `price`, `added_at`, `created_at`, `updated_at`) VALUES
(11, 2, 1, 4, 1, 200000.00, NULL, '2025-11-25 02:01:14', '2025-11-25 02:01:14'),
(12, 2, 3, 1, 1, 250000.00, NULL, '2025-11-25 02:02:22', '2025-11-25 02:02:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `slug`, `description`, `image`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ignites', 'ignites', 'dávxxcv', 'uploads/categories/1760689595.jpg', NULL, 1, '2025-10-17 00:31:30', '2025-10-23 02:46:45'),
(3, 'Dior', 'dior', 'ãgcvnb', 'uploads/categories/1760687943.jpg', 1, 1, '2025-10-17 00:59:03', '2025-10-17 00:59:03'),
(4, 'Chanel', 'chanel', 'Là tín đồ với nước hoa, chắc hẳn bạn không còn quá xa lạ với thương hiệu Chanel. Đây là một trong những hãng nước hoa nổi tiếng nhất thế giới được đông đảo khách hàng ưa chuộng sử dụng. Đặc trưng của hương nước hoa này không chỉ dừng lại ở mức dễ chịu, hấp dẫn mà nó còn phản ánh được tính cách của người sử dụng. Đó cũng là lý do cho sự thịnh hành của nước hoa Chanel trong suốt thời gian qua.', 'uploads/categories/1760689710.jpg', NULL, 1, '2025-10-17 01:28:30', '2025-10-17 01:28:30'),
(5, 'Lancome', 'lancome', 'Lancome là một trong những thương hiệu nước hoa nổi tiếng của pháp được ưa chuộng trong suốt nhiều năm qua. Để phù hợp với nhiều đối tượng khách hàng, thương hiệu đã luôn nỗ lực ra mắt nhiều mùi hương đến từ nhiều loài hoa khác nhau. Hương thơm đặc trưng của dòng nước hoa này là giúp cho người dùng tôn lên nét đẹp huyền bí, hấp dẫn và quyến rũ với phái nữ. Ngoài ra Lancome sẽ dễ dàng thu hút cánh đàn ông nhờ vào nét mạnh mẽ, nam tính.', 'uploads/categories/1760689760.jpg', NULL, 1, '2025-10-17 01:29:20', '2025-10-17 01:29:20'),
(6, 'D&G', 'dg', 'Đây là dòng nước hoa xuất xứ tại Ý. Tuy chỉ mới gia nhập làng nước hoa khoảng 35 năm như D&G đã nhanh chóng trở thành một trong những nhãn hiệu nước hoa nổi tiếng, đứng cạnh sánh vai cùng những người anh lớn như Chanel, Gucci,...\r\n\r\nTìm đến G&G, bạn sẽ dễ dàng lựa chọn cho mình các mùi hương vô cùng độc đáo. Dù là nhẹ nhàng, thanh mát, tinh tế hay đến cả sự quyến rũ, cuốn hút, sang trọng,... Tất cả đều khiến khách hàng hài lòng tuyệt đối về những hương thơm này.', 'uploads/categories/1760689803.jpg', NULL, 1, '2025-10-17 01:30:03', '2025-10-23 02:46:42'),
(8, 'Time', 'time', 'dxc', 'uploads/categories/1761753476.jpg', NULL, 1, '2025-10-29 08:57:56', '2025-11-18 18:59:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','read','replied','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `min_order_value` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_10_15_090700_create_roles_table', 1),
(4, '2025_10_15_103036_create_users_table', 1),
(5, '2025_10_15_103046_create_categories_table', 1),
(6, '2025_10_15_103047_create_variants_colors_table', 1),
(7, '2025_10_15_103110_create_variants_sizes_table', 1),
(8, '2025_10_15_103133_create_products_table', 1),
(9, '2025_10_15_103134_create_product_variants_table', 1),
(10, '2025_10_15_103144_create_product_galleries_table', 1),
(11, '2025_10_15_103146_create_warehouse_table', 1),
(12, '2025_10_15_103148_create_warehouse_products_table', 1),
(13, '2025_10_15_103206_create_carts_table', 1),
(14, '2025_10_15_103208_create_cart_items_table', 1),
(15, '2025_10_15_103208_create_discounts_table', 1),
(16, '2025_10_15_103209_create_orders_table', 1),
(17, '2025_10_15_103210_create_order_details_table', 1),
(18, '2025_10_15_103211_create_payments_table', 1),
(19, '2025_10_15_103212_create_shipments_table', 1),
(20, '2025_10_15_103221_create_reviews_table', 1),
(21, '2025_10_15_103222_create_banners_table', 1),
(22, '2025_10_15_103222_create_posts_table', 1),
(23, '2025_10_15_125731_create_sessions_table', 2),
(24, '2025_10_17_074927_add_image_to_categories_table', 3),
(25, '2025_10_23_013628_add_status_to_categories_table', 4),
(26, '2025_10_26_103124_add_brand_to_products_table', 5),
(27, '2025_10_30_115218_add_min_stock_threshold_to_warehouse_products_table', 6),
(28, '2025_11_02_090610_create_password_resets_table', 7),
(29, '2025_11_05_093618_create_stock_transactions_table', 8),
(30, '2025_11_04_163110_create_contacts_table', 9),
(31, '2025_11_05_044037_add_used_count_to_discounts_table', 9),
(32, '2025_11_05_121713_create_stock_transactions_table', 10),
(33, '2025_11_05_121800_add_stock_trigger', 10),
(34, '2025_11_05_163420_create_stock_transactions_table', 11),
(35, '2025_11_11_024954_add_attributes_to_product_variants_table', 12),
(36, '2025_11_12_070643_create_variants_scents_table', 13),
(37, '2025_11_12_070647_create_variants_concentrations_table', 13),
(38, '2025_11_12_070648_update_product_variants_add_scent_concentration', 13),
(39, '2025_11_12_073405_remove_color_from_product_variants_table', 14),
(40, '2025_11_11_000001_add_checkout_fields_to_orders_table', 15),
(41, '2025_11_11_200000_add_status_to_reviews_table', 15),
(42, '2025_11_12_124828_add_variant_id_to_warehouse_products_table', 16),
(43, '2025_11_14_045116_add_status_to_reviews_table', 16),
(44, '2025_11_18_092308_create_chat_messages_table', 16),
(45, '2025_11_19_071716_create_brands_table', 17),
(46, '2025_11_19_072044_add_brand_id_to_products_table', 17),
(47, '2025_11_24_181949_add_cancellation_fields_to_orders_table', 18),
(48, '2025_11_26_033001_add_image_to_product_variants', 18);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_id` bigint UNSIGNED DEFAULT NULL,
  `payment_id` bigint UNSIGNED DEFAULT NULL,
  `order_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `shipping_province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_ward` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address_line` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_note` text COLLATE utf8mb4_unicode_ci,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `discount_id`, `payment_id`, `order_status`, `total_price`, `subtotal`, `discount_total`, `grand_total`, `payment_method`, `cancellation_reason`, `cancelled_at`, `shipping_address`, `shipping_province`, `shipping_district`, `shipping_ward`, `shipping_address_line`, `customer_note`, `shipping_cost`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, NULL, NULL, NULL, NULL, 'processing', 200000.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'adfwgershtdm ', NULL, NULL, NULL, NULL, NULL, 10000.00, '2025-11-14 03:35:36', '2025-11-13 20:37:14'),
(2, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'processing', 100000.00, 100000.00, 0.00, 130000.00, 'cod', NULL, NULL, 'sdfes, zbegxdrf, szxdcfv, zgsxhdcfvm', 'zgsxhdcfvm', 'szxdcfv', 'zbegxdrf', 'sdfes', 'czsvdx', 30000.00, '2025-11-18 19:35:10', '2025-11-18 19:35:10'),
(3, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'processing', 89899.00, 89899.00, 0.00, 119899.00, 'cod', NULL, NULL, 'sdfes, zbegxdrf, szxdcfv, àgsd', 'àgsd', 'szxdcfv', 'zbegxdrf', 'sdfes', 'ègfbgc', 30000.00, '2025-11-18 19:38:30', '2025-11-18 19:38:30'),
(4, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'processing', 191000.00, 191000.00, 0.00, 221000.00, 'cod', NULL, NULL, 'szgxrdtf, zbegxdrf, szxdcfv, zgsxhdcfvm', 'zgsxhdcfvm', 'szxdcfv', 'zbegxdrf', 'szgxrdtf', 'xsdvv', 30000.00, '2025-11-18 19:38:52', '2025-11-18 19:38:52'),
(5, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'processing', 89899.00, 89899.00, 0.00, 119899.00, 'cod', NULL, NULL, 'qédxfcg, sdfjm, sgdhfjvb, sgdhfvb', 'sgdhfvb', 'sgdhfjvb', 'sdfjm', 'qédxfcg', 'fsgdhfjg', 30000.00, '2025-11-18 21:03:40', '2025-11-18 21:03:40'),
(6, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'preparing', 191000.00, 191000.00, 0.00, 221000.00, 'cod', NULL, NULL, 'sdfes, zbegxdrf, szxdcfv, zgsxhdcfvm', 'zgsxhdcfvm', 'szxdcfv', 'zbegxdrf', 'sdfes', 'sfxdcv', 30000.00, '2025-11-18 22:00:02', '2025-11-25 00:36:18'),
(7, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'pending', 89899.00, 89899.00, 0.00, 119899.00, 'cod', NULL, NULL, 'sdfes, sdfjm, sgdhfjvb, àgsd', 'àgsd', 'sgdhfjvb', 'sdfjm', 'sdfes', 'Dfzsgdfg', 30000.00, '2025-11-25 00:26:08', '2025-11-25 00:26:08'),
(8, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'completed', 189899.00, 189899.00, 0.00, 219899.00, 'online', NULL, NULL, 'sdfes, zbegxdrf, szxdcfv, zgsxhdcfvm', 'zgsxhdcfvm', 'szxdcfv', 'zbegxdrf', 'sdfes', 'xzxc', 30000.00, '2025-11-25 00:42:38', '2025-11-25 06:14:01'),
(9, 3, 'Luong The Bao', 'lngtthb@gmail.com', '0391771654', NULL, NULL, 'canceled', 250000.00, 250000.00, 0.00, 280000.00, 'bank_transfer', NULL, NULL, 'sdfes, zbegxdrf, szxdcfv, zgsxhdcfvm', 'zgsxhdcfvm', 'szxdcfv', 'zbegxdrf', 'sdfes', 'df', 30000.00, '2025-11-25 02:02:51', '2025-11-25 02:24:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `variant_id`, `quantity`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 1, 150000.00, 150000.00, NULL, NULL),
(2, 1, 5, 2, 1, 150000.00, 150000.00, NULL, NULL),
(3, 2, 1, NULL, 1, 100000.00, 100000.00, '2025-11-18 19:35:10', '2025-11-18 19:35:10'),
(4, 3, 5, NULL, 1, 89899.00, 89899.00, '2025-11-18 19:38:30', '2025-11-18 19:38:30'),
(5, 4, 4, NULL, 1, 191000.00, 191000.00, '2025-11-18 19:38:52', '2025-11-18 19:38:52'),
(6, 5, 5, NULL, 1, 89899.00, 89899.00, '2025-11-18 21:03:40', '2025-11-18 21:03:40'),
(7, 6, 4, NULL, 1, 191000.00, 191000.00, '2025-11-18 22:00:02', '2025-11-18 22:00:02'),
(8, 7, 5, NULL, 1, 89899.00, 89899.00, '2025-11-25 00:26:08', '2025-11-25 00:26:08'),
(9, 8, 5, NULL, 1, 89899.00, 89899.00, '2025-11-25 00:42:38', '2025-11-25 00:42:38'),
(10, 8, 1, NULL, 1, 100000.00, 100000.00, '2025-11-25 00:42:38', '2025-11-25 00:42:38'),
(11, 9, 3, 1, 1, 250000.00, 250000.00, '2025-11-25 02:02:51', '2025-11-25 02:02:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `transaction_code`, `amount`, `status`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'cod', NULL, 130000.00, 'pending', NULL, '2025-11-18 19:35:10', '2025-11-18 19:35:10'),
(2, 3, 'cod', NULL, 119899.00, 'pending', NULL, '2025-11-18 19:38:30', '2025-11-18 19:38:30'),
(3, 4, 'cod', NULL, 221000.00, 'pending', NULL, '2025-11-18 19:38:52', '2025-11-18 19:38:52'),
(4, 5, 'cod', NULL, 119899.00, 'pending', NULL, '2025-11-18 21:03:40', '2025-11-18 21:03:40'),
(5, 6, 'cod', NULL, 221000.00, 'pending', NULL, '2025-11-18 22:00:02', '2025-11-18 22:00:02'),
(6, 7, 'cod', NULL, 119899.00, 'pending', NULL, '2025-11-25 00:26:08', '2025-11-25 00:26:08'),
(7, 8, 'online', NULL, 219899.00, 'pending', NULL, '2025-11-25 00:42:38', '2025-11-25 00:42:38'),
(8, 9, 'bank_transfer', NULL, 280000.00, 'pending', NULL, '2025-11-25 02:02:51', '2025-11-25 02:02:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `author_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(10,2) DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `brand_id`, `name`, `sku`, `image`, `price`, `sale_price`, `slug`, `description`, `category_id`, `brand`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Dior J’adore Parfum d’Eau', 'pro1', 'scdvsfbd.jpg', 110000.00, 100000.00, 'dior-jadore-parfum-deau', 'xzdgfcg', 3, 'sdfb', 1, '2025-10-29 16:07:03', '2025-11-18 19:02:55'),
(3, NULL, 'Valentino Donna EDP', 'pro4', NULL, 111000.00, 100000.00, 'valentino-donna-edp', 'zxbcnvefsd', 6, 'ưdvfb12d', 1, '2025-10-29 09:23:05', '2025-11-18 19:05:16'),
(4, NULL, 'Chanel Coco Mademoiselle EDP Intense', 'pro3', NULL, 211000.00, 191000.00, 'chanel-coco-mademoiselle-edp-intense', 'dsfdxfg', 4, 'sdfb', 1, '2025-10-29 09:38:26', '2025-11-18 19:05:49'),
(5, NULL, 'Yves Saint Laurent Libre Le Parfum EDP', 'pro2', NULL, 100000.00, 89899.00, 'yves-saint-laurent-libre-le-parfum-edp', 'xcv', 5, 'zx', 1, '2025-10-29 09:39:02', '2025-11-18 19:04:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_galleries`
--

CREATE TABLE `product_galleries` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_galleries`
--

INSERT INTO `product_galleries` (`id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 3, 'products/1761755854_0.jpg', 'erstdxx1', 1, '2025-10-29 09:37:34', '2025-10-29 09:37:34'),
(2, 5, 'products/1761755942_0.jpg', 'Luoadfsg', 1, '2025-10-29 09:39:02', '2025-10-29 09:39:02'),
(3, 1, 'products/1761756063_0.jpg', 'zxvfg adv', 1, '2025-10-29 09:41:03', '2025-10-29 09:41:03'),
(4, 3, 'products/1761756079_0.jpg', 'erstdxx1', 1, '2025-10-29 09:41:19', '2025-10-29 09:41:19'),
(5, 4, 'products/1761756099_0.jpg', 'qefsadsv', 1, '2025-10-29 09:41:39', '2025-10-29 09:41:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `scent_id` bigint UNSIGNED DEFAULT NULL,
  `concentration_id` bigint UNSIGNED DEFAULT NULL,
  `sku` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `price_adjustment` decimal(10,2) DEFAULT NULL,
  `gender` enum('male','female','unisex') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unisex',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size_id`, `scent_id`, `concentration_id`, `sku`, `image`, `stock`, `price_adjustment`, `gender`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 1, 1, 'pfo.1', 'variants/Um1QCEQVVn7ImZuou406WHgEIGRBVadsYlXhccZy.jpg', 10, 150000.00, 'female', '2025-11-12 06:20:55', '2025-11-25 20:47:20'),
(2, 1, 2, 2, 2, 'qád', 'variants/J2nykgrhhwspieQHWpTe1ty6CsAMxYIM3zQI00sg.jpg', 8, 150000.00, 'male', '2025-11-12 20:36:35', '2025-11-25 20:49:11'),
(3, 1, 2, 3, 2, '111', 'variants/u4EKEnARQfCwHIDbBMQHGM8bUhGUdE3H2ykhlLyj.jpg', 10, 200000.00, 'male', '2025-11-14 00:39:51', '2025-11-25 20:58:17'),
(4, 1, 3, 4, 2, 'pro1.1', 'variants/SNueFPKXsDZAvSCDdr7hDcHTwBlxKIUbwJ0408Jz.jpg', 20, 100000.00, 'male', '2025-11-18 21:08:10', '2025-11-25 20:58:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'grhntre', NULL, NULL),
(2, 'user', 'sdgfgh', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0gnOk5qqIIhTJG67B0l3oZsXY6Ey24fUexvQmCIL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEZjemlpTUd3SjFncXIzaUF3SUUxR2hhN3JuekJPSGlpYllrZ2c3MyI7czoyMjoiUEhQREVCVUdCQVJfU1RBQ0tfREFUQSI7YTowOnt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWN0L3ZhbGVudGluby1kb25uYS1lZHAiO3M6NToicm91dGUiO3M6MTI6InByb2R1Y3Quc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1764054911),
('bYsekssbvTXjqZAAHXXyauvGa4e499HOErpDGGba', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMzUwcEZUMFNkU1BiTnplbmxLQlM5UjdwUUQ0NDVlY3JYTmhIcHd1YSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9vcmRlcnMvOCI7czo1OiJyb3V0ZSI7czoxMToib3JkZXJzLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vb3JkZXJzL2xpc3QiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MjI6IlBIUERFQlVHQkFSX1NUQUNLX0RBVEEiO2E6MDp7fX0=', 1764076452),
('TIYF2tgX8cEFE90nKjkfEyWvL5J7WqrxJB7x7qud', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6IlVYSTNsOWs5dDdSYWUxOWQ1UWtIaVluZGdYSDdBR1JrYTN3QVZYMDQiO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9pbnZlbnRvcmllcy9yZWNlaXZlZC1vcmRlcnMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2NhcnQiO3M6NToicm91dGUiO3M6MTA6ImNhcnQuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjc6ImNhcnRfaWQiO2k6MztzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MTY6Imxhc3Rfb3JkZXJfZW1haWwiO3M6MTc6ImxuZ3R0aGJAZ21haWwuY29tIjtzOjE2OiJsYXN0X29yZGVyX3Bob25lIjtzOjEwOiIwMzkxNzcxNjU0IjtzOjQ6ImNhcnQiO2E6NTp7czo1OiJpdGVtcyI7YToxOntpOjA7YTo3OntzOjEwOiJwcm9kdWN0X2lkIjtpOjE7czoxMDoidmFyaWFudF9pZCI7aTo0O3M6ODoicXVhbnRpdHkiO2k6MTtzOjU6InByaWNlIjtkOjIwMDAwMDtzOjQ6Im5hbWUiO3M6Mjk6IkRpb3IgSuKAmWFkb3JlIFBhcmZ1bSBk4oCZRWF1IjtzOjEyOiJ2YXJpYW50X25hbWUiO3M6NjQ6IlNpemU6IDE1MG1sIHwgTcO5aTogRmxvcmFsIHwgTuG7k25nIMSR4buZOiBFYXUgZGUgVG9pbGV0dGUgKEVEVCkiO3M6NToiaW1hZ2UiO3M6MjU6InByb2R1Y3RzLzE3NjE3NTYwNjNfMC5qcGciO319czoxMjoic2hpcHBpbmdfZmVlIjtpOjMwMDAwO3M6MTQ6ImRpc2NvdW50X3RvdGFsIjtpOjA7czo4OiJzdWJ0b3RhbCI7aTowO3M6MTE6ImdyYW5kX3RvdGFsIjtpOjA7fXM6MjI6IlBIUERFQlVHQkFSX1NUQUNLX0RBVEEiO2E6MDp7fX0=', 1764062692),
('YvVshGRUiIFfBJGxDaG1dix2V29notClyD6yj6It', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiY21VbnRWY0U2ZE9YMWN5T2dBdGVwaFE1aVBPUzRpaW13UGJYclEyVyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL29yZGVycy9zaG93LzgiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3ZhcmlhbnRzIjtzOjU6InJvdXRlIjtzOjE0OiJ2YXJpYW50cy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1764129524);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipments`
--

CREATE TABLE `shipments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `carrier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shipments`
--

INSERT INTO `shipments` (`id`, `order_id`, `carrier`, `tracking_number`, `shipping_status`, `shipped_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'SPX', 'SPX32403569', 'preparing', NULL, NULL, '2025-11-13 20:37:16', '2025-11-13 20:37:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `type` enum('import','export') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `warehouse_id`, `product_id`, `type`, `quantity`, `note`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'import', 100, NULL, NULL, '2025-11-05 10:08:16', '2025-11-05 10:08:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `avatar`, `address`, `email`, `phone`, `password`, `role_id`, `gender`, `created_at`, `updated_at`) VALUES
(2, 'Luong The Bao', 'a.jpg', 'fvsfds xbv', 'lngthb@gmail.com', '0912777123', 'abcd12345', 1, 'male', NULL, NULL),
(3, 'Luong The Bao', NULL, 'dfsgdbfxngcvm', 'lngtthb@gmail.com', '0391771654', '$2y$12$6QRXvCHMXSw2KAYRk/H99OLE1sYKydFyI5I5jyRMvHQUJLRFCMsF6', NULL, 'male', '2025-11-05 10:50:45', '2025-11-13 20:43:59'),
(4, 'asfg', 'cadfsgv.jpg', 'avsg', 'aervsg@gmail.com', '0976477112', 'lngthb1702', 1, 'male', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variants_concentrations`
--

CREATE TABLE `variants_concentrations` (
  `id` bigint UNSIGNED NOT NULL,
  `concentration_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variants_concentrations`
--

INSERT INTO `variants_concentrations` (`id`, `concentration_name`, `created_at`, `updated_at`) VALUES
(1, 'Eau de Cologne (EDC)', NULL, NULL),
(2, 'Eau de Toilette (EDT)', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variants_scents`
--

CREATE TABLE `variants_scents` (
  `id` bigint UNSIGNED NOT NULL,
  `scent_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variants_scents`
--

INSERT INTO `variants_scents` (`id`, `scent_name`, `created_at`, `updated_at`) VALUES
(1, 'Woody', NULL, NULL),
(2, 'Citrus', NULL, NULL),
(3, 'Gourmand', NULL, NULL),
(4, 'Floral', NULL, NULL),
(5, 'Fruity', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `variants_sizes`
--

CREATE TABLE `variants_sizes` (
  `id` bigint UNSIGNED NOT NULL,
  `size_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `variants_sizes`
--

INSERT INTO `variants_sizes` (`id`, `size_name`, `created_at`, `updated_at`) VALUES
(1, '50ml', NULL, NULL),
(2, '100ml', NULL, NULL),
(3, '150ml', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warehouse`
--

CREATE TABLE `warehouse` (
  `id` bigint UNSIGNED NOT NULL,
  `warehouse_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` bigint UNSIGNED DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `warehouse`
--

INSERT INTO `warehouse` (`id`, `warehouse_name`, `address`, `manager_id`, `phone`, `created_at`, `updated_at`) VALUES
(2, 'Kho Trung Tâm Hà Nội', 'Số 1 Trần Duy Hưng, Cầu Giấy, Hà Nội', 3, '0901234567', '2025-10-30 02:35:10', '2025-11-18 21:34:00'),
(3, 'dágdbfh', 'dfsgdbfxngcvm', 2, '0391771654', '2025-10-30 03:08:49', '2025-10-30 03:08:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warehouse_products`
--

CREATE TABLE `warehouse_products` (
  `id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `min_stock_threshold` int NOT NULL DEFAULT '10',
  `last_updated` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `warehouse_products`
--

INSERT INTO `warehouse_products` (`id`, `warehouse_id`, `product_id`, `variant_id`, `quantity`, `min_stock_threshold`, `last_updated`, `created_at`, `updated_at`) VALUES
(2, 2, 1, NULL, 100, 10, NULL, '2025-10-30 05:12:06', '2025-11-05 02:55:31'),
(3, 2, 3, NULL, 200, 10, NULL, '2025-11-05 02:56:02', '2025-11-05 10:08:16'),
(4, 2, 3, 1, 16, 10, NULL, '2025-11-12 06:20:55', '2025-11-18 21:55:19');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banners_created_by_foreign` (`created_by`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_cart_id_foreign` (`cart_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`),
  ADD KEY `cart_items_variant_id_foreign` (`variant_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Chỉ mục cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_discount_id_foreign` (`discount_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_foreign` (`order_id`),
  ADD KEY `order_details_product_id_foreign` (`product_id`),
  ADD KEY `order_details_variant_id_foreign` (`variant_id`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_email_index` (`email`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`),
  ADD KEY `posts_author_id_foreign` (`author_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Chỉ mục cho bảng `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_galleries_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`),
  ADD KEY `product_variants_size_id_foreign` (`size_id`),
  ADD KEY `product_variants_scent_id_foreign` (`scent_id`),
  ADD KEY `product_variants_concentration_id_foreign` (`concentration_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipments_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transactions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_transactions_product_id_foreign` (`product_id`),
  ADD KEY `stock_transactions_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `variants_concentrations`
--
ALTER TABLE `variants_concentrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `variants_scents`
--
ALTER TABLE `variants_scents`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `variants_sizes`
--
ALTER TABLE `variants_sizes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_manager_id_foreign` (`manager_id`);

--
-- Chỉ mục cho bảng `warehouse_products`
--
ALTER TABLE `warehouse_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_products_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `warehouse_products_product_id_foreign` (`product_id`),
  ADD KEY `warehouse_products_variant_id_foreign` (`variant_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `product_galleries`
--
ALTER TABLE `product_galleries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `variants_concentrations`
--
ALTER TABLE `variants_concentrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `variants_scents`
--
ALTER TABLE `variants_scents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `variants_sizes`
--
ALTER TABLE `variants_sizes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `warehouse_products`
--
ALTER TABLE `warehouse_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cart_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_details_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD CONSTRAINT `product_galleries_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_concentration_id_foreign` FOREIGN KEY (`concentration_id`) REFERENCES `variants_concentrations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_scent_id_foreign` FOREIGN KEY (`scent_id`) REFERENCES `variants_scents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_variants_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `variants_sizes` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `stock_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `warehouse`
--
ALTER TABLE `warehouse`
  ADD CONSTRAINT `warehouse_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `warehouse_products`
--
ALTER TABLE `warehouse_products`
  ADD CONSTRAINT `warehouse_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_products_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `warehouse_products_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
