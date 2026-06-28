-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 05 فبراير 2026 الساعة 12:33
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecomerce_shop`
--

-- --------------------------------------------------------

--
-- بنية الجدول `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('elegance-fashion-cache-shop_currencies', 'a:8:{i:0;O:8:\"stdClass\":9:{s:2:\"id\";i:1;s:4:\"name\";s:19:\"ريال سعودي\";s:4:\"code\";s:3:\"SAR\";s:6:\"symbol\";s:5:\"ر.س\";s:13:\"exchange_rate\";i:1;s:10:\"is_default\";b:1;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T12:05:00.000000Z\";}i:1;O:8:\"stdClass\":9:{s:2:\"id\";i:2;s:4:\"name\";s:23:\"دولار أمريكي\";s:4:\"code\";s:3:\"USD\";s:6:\"symbol\";s:1:\"$\";s:13:\"exchange_rate\";i:1;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T12:05:00.000000Z\";}i:2;O:8:\"stdClass\":9:{s:2:\"id\";i:3;s:4:\"name\";s:23:\"درهم إماراتي\";s:4:\"code\";s:3:\"AED\";s:6:\"symbol\";s:5:\"د.إ\";s:13:\"exchange_rate\";d:0.98;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}i:3;O:8:\"stdClass\":9:{s:2:\"id\";i:4;s:4:\"name\";s:21:\"دينار كويتي\";s:4:\"code\";s:3:\"KWD\";s:6:\"symbol\";s:5:\"د.ك\";s:13:\"exchange_rate\";d:0.082;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}i:4;O:8:\"stdClass\":9:{s:2:\"id\";i:5;s:4:\"name\";s:8:\"يورو\";s:4:\"code\";s:3:\"EUR\";s:6:\"symbol\";s:3:\"€\";s:13:\"exchange_rate\";d:0.25;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}i:5;O:8:\"stdClass\":9:{s:2:\"id\";i:6;s:4:\"name\";s:25:\"جنيه استرليني\";s:4:\"code\";s:3:\"GBP\";s:6:\"symbol\";s:2:\"£\";s:13:\"exchange_rate\";d:0.21;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}i:6;O:8:\"stdClass\":9:{s:2:\"id\";i:7;s:4:\"name\";s:19:\"ليرة تركية\";s:4:\"code\";s:3:\"TRY\";s:6:\"symbol\";s:2:\"TL\";s:13:\"exchange_rate\";d:8.25;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}i:7;O:8:\"stdClass\":9:{s:2:\"id\";i:8;s:4:\"name\";s:17:\"جنيه مصري\";s:4:\"code\";s:3:\"EGP\";s:6:\"symbol\";s:5:\"ج.م\";s:13:\"exchange_rate\";d:12.85;s:10:\"is_default\";b:0;s:6:\"status\";b:1;s:10:\"created_at\";s:27:\"2026-02-04T11:22:12.000000Z\";s:10:\"updated_at\";s:27:\"2026-02-04T11:22:12.000000Z\";}}', 1770291443),
('elegance-fashion-cache-shop_languages', 'a:0:{}', 1770291443);

-- --------------------------------------------------------

--
-- بنية الجدول `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'عروض حصرية', NULL, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 'رجالي', NULL, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 'نسائي', NULL, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 'إكسسوارات', NULL, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT 1.000000,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `exchange_rate`, `is_default`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ريال سعودي', 'SAR', 'ر.س', 1.000000, 1, 1, '2026-02-04 09:22:12', '2026-02-04 10:05:00'),
(2, 'دولار أمريكي', 'USD', '$', 1.000000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 10:05:00'),
(3, 'درهم إماراتي', 'AED', 'د.إ', 0.980000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 'دينار كويتي', 'KWD', 'د.ك', 0.082000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 'يورو', 'EUR', '€', 0.250000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 'جنيه استرليني', 'GBP', '£', 0.210000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(7, 'ليرة تركية', 'TRY', 'TL', 8.250000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(8, 'جنيه مصري', 'EGP', 'ج.م', 12.850000, 0, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `flag` varchar(255) DEFAULT NULL,
  `direction` enum('ltr','rtl') NOT NULL DEFAULT 'rtl',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `flag`, `direction`, `is_default`, `status`, `created_at`, `updated_at`) VALUES
(1, 'العربية', 'ar', '🇸🇦', 'rtl', 1, 1, '2026-02-05 08:56:08', '2026-02-05 08:56:08'),
(2, 'English', 'en', '🇺🇸', 'ltr', 0, 1, '2026-02-05 08:56:08', '2026-02-05 08:56:08'),
(3, 'Français', 'fr', '🇫🇷', 'ltr', 0, 0, '2026-02-05 08:56:08', '2026-02-05 08:56:08');

-- --------------------------------------------------------

--
-- بنية الجدول `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_06_083637_create_categories_table', 1),
(5, '2026_01_06_083646_create_products_table', 1),
(6, '2026_01_06_083659_create_orders_table', 1),
(7, '2026_01_06_083715_create_order_items_table', 1),
(8, '2026_01_06_083722_create_cart_items_table', 1),
(9, '2026_01_10_105219_create_reviews_table', 1),
(10, '2026_01_10_124730_add_details_to_products_table', 1),
(11, '2026_01_10_124843_create_product_images_table', 1),
(12, '2026_01_10_124912_create_product_attributes_table', 1),
(13, '2026_01_18_094308_create_personal_access_tokens_table', 1),
(14, '2026_01_18_100838_add_size_to_cart_items_table', 1),
(15, '2026_01_18_102558_add_index_to_users_email', 1),
(16, '2026_01_20_094312_add_status_to_users_table', 1),
(17, '2026_01_20_102822_add_status_to_categories_table', 1),
(18, '2026_01_20_111803_add_status_to_products_table', 1),
(19, '2026_01_21_081225_create_subcategories_table', 1),
(20, '2026_01_21_081733_add_subcategory_id_to_products_table', 1),
(21, '2026_01_24_120009_add_color_to_cart_items_table', 1),
(22, '2026_01_24_121202_add_details_to_order_items_table', 1),
(23, '2026_01_24_125116_add_details_to_orders_table', 1),
(24, '2026_01_26_092300_add_offer_details_to_products', 1),
(25, '2026_01_26_094447_create_offers_table', 1),
(26, '2026_02_03_153122_add_image_to_offers_table', 1),
(27, '2026_02_04_105652_create_currencies_table', 1),
(28, '2026_02_05_102000_create_languages_table', 2);

-- --------------------------------------------------------

--
-- بنية الجدول `offers`
--

CREATE TABLE `offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount_value` int(11) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `scope` enum('all','category','subcategory','product') NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `offers`
--

INSERT INTO `offers` (`id`, `name`, `discount_value`, `type`, `scope`, `target_id`, `starts_at`, `ends_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'خصم الافتتاح الكبير', 20, 'percentage', 'all', NULL, '2026-02-04 11:22:12', '2026-03-04 11:22:12', 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 'تصفية على الجاكيتات', 50, 'percentage', 'product', 1, '2026-01-30 11:22:12', '2026-02-19 11:22:12', 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 'عروض قسم الـ عروض حصرية', 100, 'fixed', 'category', 1, '2026-02-04 11:22:12', '2026-02-14 11:22:12', 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `phone`, `address`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'أحمد العمري', '0535540730', 'المملكة العربية السعودية، الرياض، حي الياسمين', 6200.00, 'completed', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 3, 'سارة القحطاني', '0568146577', 'المملكة العربية السعودية، الرياض، حي الياسمين', 2400.00, 'completed', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 4, 'محمد الحربي', '0521774815', 'المملكة العربية السعودية، الرياض، حي الياسمين', 8600.00, 'pending', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 5, 'نورة العتيبي', '0539591362', 'المملكة العربية السعودية، الرياض، حي الياسمين', 5180.00, 'pending', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 6, 'خالد الدوسري', '0526156908', 'المملكة العربية السعودية، الرياض، حي الياسمين', 2410.00, 'pending', '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`, `color`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 1200.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 1, 4, 2, 2500.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 2, 2, 1, 1200.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 2, 8, 1, 1200.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 3, 4, 2, 2500.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 3, 7, 2, 1800.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(7, 4, 4, 2, 2500.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(8, 4, 6, 1, 180.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(9, 5, 2, 1, 1200.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(10, 5, 5, 1, 850.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(11, 5, 6, 2, 180.00, NULL, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `old_price` decimal(10,2) DEFAULT NULL,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `offer_type` varchar(255) DEFAULT NULL,
  `offer_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `status`, `old_price`, `total_stock`, `image`, `created_at`, `updated_at`, `subcategory_id`, `offer_type`, `offer_expires_at`) VALUES
(1, 'جاكيت شتوي فاخر', 'قطعة حصرية ومميزة من جاكيت شتوي فاخر. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 450.00, 1, 900.00, 110, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 1, NULL, NULL),
(2, 'فستان مخملي ملكي', 'قطعة حصرية ومميزة من فستان مخملي ملكي. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 1200.00, 1, 2400.00, 82, 'https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 1, NULL, NULL),
(3, 'ساعة كلاسيكية مطلية', 'قطعة حصرية ومميزة من ساعة كلاسيكية مطلية. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 600.00, 1, 1200.00, 186, 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 1, NULL, NULL),
(4, 'بدلة إيطالية فاخرة', 'قطعة حصرية ومميزة من بدلة إيطالية فاخرة. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 2500.00, 1, 3000.00, 98, 'https://images.unsplash.com/photo-1594932224036-9c20533429bc?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 2, NULL, NULL),
(5, 'بليزر كحلي عصري', 'قطعة حصرية ومميزة من بليزر كحلي عصري. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 850.00, 1, 1100.00, 138, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 2, NULL, NULL),
(6, 'تيشيرت بولو كلاسيك', 'قطعة حصرية ومميزة من تيشيرت بولو كلاسيك. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 180.00, 1, 250.00, 172, 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 3, NULL, NULL),
(7, 'فستان سهرة مرصع', 'قطعة حصرية ومميزة من فستان سهرة مرصع. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 1800.00, 1, 2200.00, 151, 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 4, NULL, NULL),
(8, 'عباية مطرزة يدوياً', 'قطعة حصرية ومميزة من عباية مطرزة يدوياً. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 1200.00, 1, 1500.00, 176, 'https://images.unsplash.com/photo-1621112904887-419379ce6824?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 5, NULL, NULL),
(9, 'قفطان مغربي فاخر', 'قطعة حصرية ومميزة من قفطان مغربي فاخر. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 2800.00, 1, 3500.00, 146, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 5, NULL, NULL),
(10, 'عطر العود الكمبودي', 'قطعة حصرية ومميزة من عطر العود الكمبودي. نجمع لك بين الجودة التصميم العصري لتناسب ذوقك الرفيع.', 1500.00, 1, 2000.00, 96, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12', 6, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `size` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `product_id`, `color`, `size`, `qty`, `created_at`, `updated_at`) VALUES
(1, 1, 'أسود', 'L', 16, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 1, 'أسود', 'XL', 15, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 1, 'كحلي', 'M', 25, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 1, 'كحلي', 'L', 30, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 2, 'أسود', 'S', 18, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 2, 'أسود', 'XL', 24, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(7, 2, 'كحلي', 'L', 20, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(8, 2, 'كحلي', 'XL', 12, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(9, 3, 'أسود', 'M', 28, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(10, 3, 'أسود', 'L', 29, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(11, 3, 'أحمر', 'S', 19, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(12, 3, 'أحمر', 'M', 20, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(13, 4, 'أبيض', 'L', 17, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(14, 4, 'أبيض', 'XL', 19, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(15, 4, 'كحلي', 'S', 28, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(16, 4, 'كحلي', 'XL', 30, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(17, 5, 'أبيض', 'M', 29, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(18, 5, 'أبيض', 'L', 17, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(19, 5, 'كحلي', 'S', 21, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(20, 5, 'كحلي', 'L', 16, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(21, 6, 'أبيض', 'S', 10, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(22, 6, 'أبيض', 'M', 18, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(23, 6, 'كحلي', 'M', 28, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(24, 6, 'كحلي', 'L', 21, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(25, 7, 'أسود', 'S', 26, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(26, 7, 'أسود', 'XL', 18, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(27, 7, 'أبيض', 'M', 15, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(28, 7, 'أبيض', 'L', 10, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(29, 8, 'أسود', 'M', 23, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(30, 8, 'أسود', 'L', 12, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(31, 8, 'كحلي', 'S', 23, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(32, 8, 'كحلي', 'M', 11, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(33, 9, 'أبيض', 'S', 10, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(34, 9, 'أبيض', 'XL', 24, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(35, 9, 'أحمر', 'S', 12, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(36, 9, 'أحمر', 'M', 20, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(37, 10, 'أسود', 'M', 23, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(38, 10, 'أسود', 'L', 10, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(39, 10, 'أبيض', 'S', 18, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(40, 10, 'أبيض', 'M', 24, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 2, 'https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 3, 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 4, 'https://images.unsplash.com/photo-1594932224036-9c20533429bc?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 5, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 6, 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(7, 7, 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(8, 8, 'https://images.unsplash.com/photo-1621112904887-419379ce6824?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(9, 9, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(10, 10, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=800&auto=format&fit=crop', '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 'أفضل متجر تعاملت معه، شكراً لكم.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 6, 1, 5, 'أفضل متجر تعاملت معه، شكراً لكم.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 2, 2, 5, 'أفضل متجر تعاملت معه، شكراً لكم.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 6, 2, 4, 'جودة رائعة جداً، أنصح به!', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 5, 3, 4, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 6, 3, 4, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(7, 3, 4, 4, 'أفضل متجر تعاملت معه، شكراً لكم.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(8, 4, 4, 4, 'جودة رائعة جداً، أنصح به!', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(9, 5, 5, 5, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(10, 6, 5, 5, 'التوصيل كان سريع والمنتج مطابق للصور.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(11, 3, 6, 5, 'القماش مريح جداً والمقاس مضبوط.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(12, 4, 6, 4, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(13, 2, 7, 4, 'القماش مريح جداً والمقاس مضبوط.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(14, 4, 7, 5, 'جودة رائعة جداً، أنصح به!', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(15, 2, 8, 5, 'جودة رائعة جداً، أنصح به!', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(16, 5, 8, 4, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(17, 2, 9, 5, 'خامة ممتازة وتصميم أنيق.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(18, 6, 9, 4, 'التوصيل كان سريع والمنتج مطابق للصور.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(19, 4, 10, 5, 'أفضل متجر تعاملت معه، شكراً لكم.', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(20, 6, 10, 4, 'جودة رائعة جداً، أنصح به!', '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('aDOXStUf9GrlmMFC7Akfbgybo51M1aZYMmf7TQQq', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOFhWQWdrbnVXM2xmekJvYlRqM2RHblRoRVZsek1zZmROQlhCN3BHdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1770286408);

-- --------------------------------------------------------

--
-- بنية الجدول `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'تخفيضات الموسم', 1, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 'بدلات رسمية', 2, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 'ملابس كاجوال', 2, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 'فساتين', 3, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 'عبايات وأزياء عربية', 3, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 'عطور', 4, 1, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'مدير النظام', 'admin@elegance.com', '2026-02-04 09:22:12', '$2y$04$z.kk5HVDEZDVELtXYi1A/uAVuiv/nq0BwrfD1hn74L.OnH8hFK4h6', 'admin', 1, 'bGifBmBA7G', '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(2, 'أحمد العمري', 'ahmed@example.com', NULL, '$2y$04$gZkohTsXQg/2ddqPxqorS.dWBtGp8yBF9R0lJt/sdLWkDhF1EaaSq', 'user', 1, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(3, 'سارة القحطاني', 'sara@example.com', NULL, '$2y$04$1CdFJGIb7TJxiFW9DdsRMej1XCZBCYIPE0dQaSUGgWP0NtlENXTIe', 'user', 1, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(4, 'محمد الحربي', 'mohammed@example.com', NULL, '$2y$04$6sth4j3SviNMLtnmYwJ/6.b74g7DclmhhRmll/TCnaCk175LW5Xve', 'user', 1, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(5, 'نورة العتيبي', 'noura@example.com', NULL, '$2y$04$ZJRXl9gaTj/iw0Pih/WUx.KmN54J5by8NPzp5zEsgRLcOsR1I4UNG', 'user', 1, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12'),
(6, 'خالد الدوسري', 'khaled@example.com', NULL, '$2y$04$nZpjANB8AwGx7gzo4HFtBuLZ.c13Y0AFDc4mtKYFZaeS0ALeMO2tS', 'user', 1, NULL, '2026-02-04 09:22:12', '2026-02-04 09:22:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_user_id_foreign` (`user_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `languages_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_subcategory_id_foreign` (`subcategory_id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_attributes_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_email_index` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
