-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2026 at 03:06 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2b`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouts`
--

CREATE TABLE `abouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `about` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`about`)),
  `mission` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mission`)),
  `vision` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vision`)),
  `goals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`goals`)),
  `about_en` text DEFAULT NULL,
  `about_ar` text DEFAULT NULL,
  `mission_en` text DEFAULT NULL,
  `mission_ar` text DEFAULT NULL,
  `vision_en` text DEFAULT NULL,
  `vision_ar` text DEFAULT NULL,
  `goals_en` text DEFAULT NULL,
  `goals_ar` text DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `abouts`
--

INSERT INTO `abouts` (`id`, `about`, `mission`, `vision`, `goals`, `about_en`, `about_ar`, `mission_en`, `mission_ar`, `vision_en`, `vision_ar`, `goals_en`, `goals_ar`, `image`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\":\"<ul class=\\\"list-unstyled who_we_are\\\">\\r\\n<li class=\\\"d-flex gap-10\\\">تُعد شركة تو بي من الشركات الرائدة في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات في مصر وتشمل منتجاتنا أجهزة الكمبيوتر المحمولة، والهواتف المحمولة، والأجهزة اللوحية، وأجهزة الألعاب، والطابعات، والملحقات، وكاميرات المراقبة، والأجهزة المنزلية.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">تأسست شركتنا عام ٢٠٠٠، وتعمل في هذا المجال منذ أكثر من ٢٠ عامًا في مصر، و١٠ سنوات في أسواق المملكة العربية السعودية.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">تُعد تو بي من الشركات الرائدة في مصر في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">تُصنف تو بي كأول شركة تجزئة في مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف المحمول، وخدمة مركز الاتصال.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">يضم فريق عمل تو بي أكثر من ٦٠٠ موظف، يبذلون قصارى جهدهم لتلبية متطلبات العملاء واحتياجاتهم.</li>\\r\\n</ul>\",\"en\":\"<ul class=\\\"list-unstyled who_we_are\\\">\\r\\n<li class=\\\"d-flex gap-10\\\">2B is one of the leading IT &amp; consumer-electronics retailers in Egypt. Our portfolio covers laptops, smartphones, tablets, gaming consoles, printers, accessories, surveillance cameras and home appliances.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">Founded in 2000, we have been operating for more than 20 years in Egypt and for 10 years across the Saudi Arabian markets.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">2B is recognised as one of the top retailers in Egypt for information technology and electronics.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">2B is classified as the first IT retailer in Egypt. We currently operate 60 stores across the country, alongside our e-commerce website, mobile application and call-centre service.</li>\\r\\n<li class=\\\"d-flex gap-10\\\">Our 2B team includes more than 600 employees, all dedicated to meeting customer requirements and needs.</li>\\r\\n</ul>\"}', '{\"ar\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"200\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\\r\\n</div>\\r\\n</div>\",\"en\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"200\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">A leader in Egypt\'s IT sector. We currently run 60 stores across the country, plus our e-commerce site and mobile app.</p>\\r\\n</div>\\r\\n</div>\"}', '{\"ar\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"400\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\\r\\n</div>\\r\\n</div>\",\"en\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"400\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">To remain a benchmark IT retailer in Egypt — operating 60+ stores, an e-commerce platform, and a mobile app.</p>\\r\\n</div>\\r\\n</div>\"}', '{\"ar\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"400\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\\r\\n</div>\\r\\n</div>\",\"en\":\"<div class=\\\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\\\" data-aos=\\\"fade-left\\\" data-aos-duration=\\\"400\\\">\\r\\n<div class=\\\"flex-grow-1\\\">\\r\\n<p class=\\\"text-neutral-500\\\">Grow Egypt\'s IT retail leadership across 60+ branches, our e-commerce site and our mobile app.</p>\\r\\n</div>\\r\\n</div>\"}', '<ul class=\"list-unstyled who_we_are\">\r\n<li class=\"d-flex gap-10\">2B is one of the leading IT &amp; consumer-electronics retailers in Egypt. Our portfolio covers laptops, smartphones, tablets, gaming consoles, printers, accessories, surveillance cameras and home appliances.</li>\r\n<li class=\"d-flex gap-10\">Founded in 2000, we have been operating for more than 20 years in Egypt and for 10 years across the Saudi Arabian markets.</li>\r\n<li class=\"d-flex gap-10\">2B is recognised as one of the top retailers in Egypt for information technology and electronics.</li>\r\n<li class=\"d-flex gap-10\">2B is classified as the first IT retailer in Egypt. We currently operate 60 stores across the country, alongside our e-commerce website, mobile application and call-centre service.</li>\r\n<li class=\"d-flex gap-10\">Our 2B team includes more than 600 employees, all dedicated to meeting customer requirements and needs.</li>\r\n</ul>', '<ul class=\"list-unstyled who_we_are\">\r\n<li class=\"d-flex gap-10\">تُعد شركة تو بي من الشركات الرائدة في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات في مصر وتشمل منتجاتنا أجهزة الكمبيوتر المحمولة، والهواتف المحمولة، والأجهزة اللوحية، وأجهزة الألعاب، والطابعات، والملحقات، وكاميرات المراقبة، والأجهزة المنزلية.</li>\r\n<li class=\"d-flex gap-10\">تأسست شركتنا عام ٢٠٠٠، وتعمل في هذا المجال منذ أكثر من ٢٠ عامًا في مصر، و١٠ سنوات في أسواق المملكة العربية السعودية.</li>\r\n<li class=\"d-flex gap-10\">تُعد تو بي من الشركات الرائدة في مصر في مجال تجارة التجزئة في مجال تكنولوجيا المعلومات والإلكترونيات.</li>\r\n<li class=\"d-flex gap-10\">تُصنف تو بي كأول شركة تجزئة في مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف المحمول، وخدمة مركز الاتصال.</li>\r\n<li class=\"d-flex gap-10\">يضم فريق عمل تو بي أكثر من ٦٠٠ موظف، يبذلون قصارى جهدهم لتلبية متطلبات العملاء واحتياجاتهم.</li>\r\n</ul>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"200\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">A leader in Egypt\'s IT sector. We currently run 60 stores across the country, plus our e-commerce site and mobile app.</p>\r\n</div>\r\n</div>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"200\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">To remain a benchmark IT retailer in Egypt — operating 60+ stores, an e-commerce platform, and a mobile app.</p>\r\n</div>\r\n</div>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">Grow Egypt\'s IT retail leadership across 60+ branches, our e-commerce site and our mobile app.</p>\r\n</div>\r\n</div>', '<div class=\"flex-align align-items-start gap-28 mb-32 aos-init aos-animate\" data-aos=\"fade-left\" data-aos-duration=\"400\">\r\n<div class=\"flex-grow-1\">\r\n<p class=\"text-neutral-500\">مجال تكنولوجيا المعلومات في مصر. ندير حاليًا ٦٠ متجرًا في جميع أنحاء مصر، بالإضافة إلى موقعنا الإلكتروني للتجارة الإلكترونية، وتطبيق الهاتف</p>\r\n</div>\r\n</div>', 'About/mUeo9KML5KhLGK0lxnrQe7db34e9dda22fd6cb6fcc47c9fbee9e.png', '2025-08-07 10:27:30', '2025-08-12 09:11:42');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `image`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'محمد سعيد', 'dev.mohamedsaid@gmail.com', NULL, 'active', NULL, NULL, '$2y$10$uX7nJ210OYEwlLncfC6DMeBw5YZlywjTR9FwwUWVqrTdN7pqNpHRK', NULL, '2025-07-06 15:53:02', '2025-07-08 17:22:23'),
(3, 'Ezz Aly', 'ezzaly74@gmail.com', NULL, 'active', NULL, NULL, '$2y$10$OJqbQiJsGWO5WSfW6dpzJeqOWFtbteK4mIYXaf9M0KwmPk9PyE.gW', NULL, '2026-05-23 20:02:55', '2026-05-31 11:02:04');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `ip` varchar(191) DEFAULT NULL,
  `device_type` varchar(191) DEFAULT NULL,
  `logged_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `email`, `ip`, `device_type`, `logged_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-08 23:08:33', '2025-07-08 17:08:33', '2025-07-08 17:08:33'),
(2, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-29 15:12:09', '2025-07-29 09:12:09', '2025-07-29 09:12:09'),
(3, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-30 13:41:59', '2025-07-30 07:41:59', '2025-07-30 07:41:59'),
(4, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-07-30 17:16:18', '2025-07-30 11:16:18', '2025-07-30 11:16:18'),
(5, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-06 12:35:58', '2025-08-06 06:35:58', '2025-08-06 06:35:58'),
(6, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-07 14:42:32', '2025-08-07 08:42:32', '2025-08-07 08:42:32'),
(7, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-07 15:04:35', '2025-08-07 09:04:35', '2025-08-07 09:04:35'),
(8, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-12 00:36:13', '2025-08-11 18:36:13', '2025-08-11 18:36:13'),
(9, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-12 13:29:29', '2025-08-12 07:29:29', '2025-08-12 07:29:29'),
(10, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-15 00:39:01', '2025-08-14 18:39:01', '2025-08-14 18:39:01'),
(11, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-16 14:42:21', '2025-08-16 08:42:21', '2025-08-16 08:42:21'),
(12, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-17 14:02:58', '2025-08-17 08:02:58', '2025-08-17 08:02:58'),
(13, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 14:55:59', '2025-08-20 08:55:59', '2025-08-20 08:55:59'),
(14, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 15:34:18', '2025-08-20 09:34:18', '2025-08-20 09:34:18'),
(15, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 18:33:23', '2025-08-20 12:33:23', '2025-08-20 12:33:23'),
(16, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-20 18:36:06', '2025-08-20 12:36:06', '2025-08-20 12:36:06'),
(17, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-23 15:58:13', '2025-08-23 09:58:13', '2025-08-23 09:58:13'),
(18, 1, 'dev.mohamedsaid@gmail.com', '::1', 'Desktop', '2025-08-23 23:13:24', '2025-08-23 17:13:24', '2025-08-23 17:13:24'),
(19, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-25 15:38:26', '2025-08-25 09:38:26', '2025-08-25 09:38:26'),
(20, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-08-25 21:03:10', '2025-08-25 15:03:10', '2025-08-25 15:03:10'),
(21, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 14:23:14', '2025-09-01 08:23:14', '2025-09-01 08:23:14'),
(22, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 16:21:25', '2025-09-01 10:21:25', '2025-09-01 10:21:25'),
(23, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 21:46:00', '2025-09-01 15:46:00', '2025-09-01 15:46:00'),
(24, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-01 22:33:05', '2025-09-01 16:33:05', '2025-09-01 16:33:05'),
(25, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-02 15:35:30', '2025-09-02 09:35:30', '2025-09-02 09:35:30'),
(26, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-07 20:55:09', '2025-09-07 14:55:09', '2025-09-07 14:55:09'),
(27, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-08 23:40:11', '2025-09-08 17:40:11', '2025-09-08 17:40:11'),
(28, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-09 17:03:41', '2025-09-09 11:03:41', '2025-09-09 11:03:41'),
(29, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-10 13:54:50', '2025-09-10 07:54:50', '2025-09-10 07:54:50'),
(30, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-10 13:55:32', '2025-09-10 07:55:32', '2025-09-10 07:55:32'),
(31, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-13 15:20:13', '2025-09-13 09:20:13', '2025-09-13 09:20:13'),
(32, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-13 15:41:33', '2025-09-13 09:41:33', '2025-09-13 09:41:33'),
(33, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-18 15:11:58', '2025-09-18 09:11:58', '2025-09-18 09:11:58'),
(34, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-09-20 12:52:45', '2025-09-20 06:52:45', '2025-09-20 06:52:45'),
(35, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-05 16:54:32', '2025-10-05 10:54:32', '2025-10-05 10:54:32'),
(36, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-07 13:34:53', '2025-10-07 07:34:53', '2025-10-07 07:34:53'),
(37, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-07 15:24:33', '2025-10-07 09:24:33', '2025-10-07 09:24:33'),
(38, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-08 13:26:06', '2025-10-08 07:26:06', '2025-10-08 07:26:06'),
(39, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-10-26 14:30:52', '2025-10-26 08:30:52', '2025-10-26 08:30:52'),
(40, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-04 13:04:16', '2025-11-04 09:04:16', '2025-11-04 09:04:16'),
(41, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-05 12:55:18', '2025-11-05 08:55:18', '2025-11-05 08:55:18'),
(42, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-10 19:55:50', '2025-11-10 15:55:50', '2025-11-10 15:55:50'),
(43, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-11 00:19:57', '2025-11-10 20:19:57', '2025-11-10 20:19:57'),
(44, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-11-13 20:48:35', '2025-11-13 16:48:35', '2025-11-13 16:48:35'),
(45, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-11 19:40:21', '2025-12-11 15:40:21', '2025-12-11 15:40:21'),
(46, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 14:37:40', '2025-12-22 10:37:40', '2025-12-22 10:37:40'),
(47, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 14:51:58', '2025-12-22 10:51:58', '2025-12-22 10:51:58'),
(48, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-22 16:46:16', '2025-12-22 12:46:16', '2025-12-22 12:46:16'),
(49, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2025-12-28 15:06:43', '2025-12-28 11:06:43', '2025-12-28 11:06:43'),
(50, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-01-08 16:14:38', '2026-01-08 12:14:38', '2026-01-08 12:14:38'),
(51, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-01-31 14:18:24', '2026-01-31 10:18:24', '2026-01-31 10:18:24'),
(52, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:13:44', '2026-02-04 13:13:44', '2026-02-04 13:13:44'),
(53, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:25:40', '2026-02-04 13:25:40', '2026-02-04 13:25:40'),
(54, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 17:47:32', '2026-02-04 13:47:32', '2026-02-04 13:47:32'),
(55, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-04 20:58:11', '2026-02-04 16:58:11', '2026-02-04 16:58:11'),
(56, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-05 13:01:22', '2026-02-05 09:01:22', '2026-02-05 09:01:22'),
(57, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-05 20:28:01', '2026-02-05 16:28:01', '2026-02-05 16:28:01'),
(58, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-10 20:45:32', '2026-02-10 16:45:32', '2026-02-10 16:45:32'),
(59, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-11 12:40:46', '2026-02-11 08:40:46', '2026-02-11 08:40:46'),
(60, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-18 16:24:03', '2026-02-18 12:24:03', '2026-02-18 12:24:03'),
(61, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-18 17:34:13', '2026-02-18 13:34:13', '2026-02-18 13:34:13'),
(62, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-19 12:54:31', '2026-02-19 08:54:31', '2026-02-19 08:54:31'),
(63, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-22 14:07:19', '2026-02-22 10:07:19', '2026-02-22 10:07:19'),
(64, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-02-26 13:07:17', '2026-02-26 09:07:17', '2026-02-26 09:07:17'),
(65, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-03-02 12:46:31', '2026-03-02 08:46:31', '2026-03-02 08:46:31'),
(66, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-03-05 00:27:12', '2026-03-04 20:27:12', '2026-03-04 20:27:12'),
(67, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-04-01 13:44:14', '2026-04-01 09:44:14', '2026-04-01 09:44:14'),
(68, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-04-27 15:54:30', '2026-04-27 09:54:30', '2026-04-27 09:54:30'),
(69, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-05-09 11:03:03', '2026-05-09 05:03:03', '2026-05-09 05:03:03'),
(70, 1, 'dev.mohamedsaid@gmail.com', '127.0.0.1', 'Desktop', '2026-05-23 21:22:52', '2026-05-23 18:22:52', '2026-05-23 18:22:52');

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(191) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admin_messages`
--

INSERT INTO `admin_messages` (`id`, `admin_id`, `subject`, `body`, `created_at`, `updated_at`) VALUES
(1, 1, 'test', 'testtttt', '2026-05-23 18:29:07', '2026-05-23 18:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `admin_message_recipients`
--

CREATE TABLE `admin_message_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `admin_message_recipients`
--

INSERT INTO `admin_message_recipients` (`id`, `admin_message_id`, `user_id`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1761, NULL, '2026-05-23 18:29:07', '2026-05-23 18:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('news','blogs','event') NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `title_ar` varchar(191) NOT NULL,
  `title_en` varchar(191) DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `description_ar` text NOT NULL,
  `slug` varchar(191) NOT NULL,
  `date_publish` date DEFAULT NULL,
  `image` varchar(191) NOT NULL,
  `is_home` tinyint(1) DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `type`, `title`, `description`, `title_ar`, `title_en`, `description_en`, `description_ar`, `slug`, `date_publish`, `image`, `is_home`, `active`, `created_at`, `updated_at`) VALUES
(1, 'blogs', '{\"ar\":\"المقالة الأولي\",\"en\":\"First Article\"}', '{\"ar\":\"<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>\",\"en\":\"<p>The first article. The first article. The first article.</p>\"}', 'المقالة الأولي', 'First Article', '<p>The first article. The first article. The first article.</p>', '<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>', 'المقالة-الأولي', '2025-08-01', 'Article/GwLyLODpP5G7yC3G0yCX6a7f2cb00a634a200b8ed48827c822e6.png', 1, 1, '2025-08-07 10:40:20', '2025-08-07 10:41:44'),
(2, 'blogs', '{\"ar\":\"المقالة الثانية\",\"en\":\"Second Article\"}', '{\"ar\":\"<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>\",\"en\":\"<p>The second article. The second article. The second article.</p>\"}', 'المقالة الثانية', 'Second Article', '<p>The second article. The second article. The second article.</p>', '<p>المقالة الأولي المقالة الأولي المقالة الأولي</p>', 'المقالة-الثانية', '2025-08-01', 'Article/GwLyLODpP5G7yC3G0yCX6a7f2cb00a634a200b8ed48827c822e6.png', 1, 1, '2025-08-07 10:40:20', '2025-08-07 10:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_machine_code` varchar(191) DEFAULT NULL,
  `user_department` varchar(191) DEFAULT NULL,
  `course_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_category_name` varchar(191) DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `course_name` varchar(191) DEFAULT NULL,
  `course_hours` bigint(20) DEFAULT 0,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_hours` double DEFAULT 0,
  `is_manual` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `user_id`, `user_machine_code`, `user_department`, `course_category_id`, `course_category_name`, `course_id`, `course_name`, `course_hours`, `section_id`, `session_id`, `attendance_hours`, `is_manual`, `created_at`, `updated_at`) VALUES
(13, 1801, '1000', 'الحسابات', 3, 'قسم التسويق', 6, 'الكورس الأول', 20, 20, NULL, 6.67, 0, '2025-12-31 21:00:01', '2026-02-04 21:00:01'),
(14, 1801, '1000', 'الحسابات', 3, 'قسم التسويق', 6, 'الكورس الأول', 20, 20, NULL, 6.67, 0, '2026-02-11 21:00:23', '2026-02-04 21:00:23'),
(15, 1801, '1000', 'الحسابات', 3, 'قسم التسويق', 6, 'الكورس الأول', 20, 20, NULL, 6.67, 0, '2026-02-14 21:00:30', '2026-02-04 21:00:30'),
(16, 1801, '1000', 'الحسابات', 4, 'قسم البرمجة', 7, 'الكورس الثاني', 20, 23, NULL, 20, 0, '2026-02-04 21:00:39', '2026-02-04 21:00:39'),
(17, 1802, '2531', 'ادارة الموارد البشرية', 3, 'قسم التسويق', 6, 'الكورس الأول', 20, 21, NULL, 10, 0, '2026-02-22 16:39:25', '2026-02-05 16:39:25'),
(20, 1802, '2531', 'ادارة الموارد البشرية', 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 0, '2026-02-09 09:41:55', '2026-02-09 09:41:55'),
(21, 1802, '2531', 'ادارة الموارد البشرية', 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 0, '2026-02-09 09:42:31', '2026-02-09 09:42:31'),
(26, 1801, '1000', 'الحسابات', 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 0, '2026-02-10 07:10:29', '2026-02-10 07:10:29'),
(28, 1802, '2531', 'ادارة الموارد البشرية', 3, 'قسم التسويق', 6, 'الكورس الأول', 20, 21, NULL, 10, 0, '2026-02-24 10:15:58', '2026-02-19 10:15:58'),
(29, 1801, '1000', NULL, 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 0, '2026-02-19 10:16:54', '2026-02-19 10:16:54'),
(36, 1761, '1610', 'ادارة الموارد البشرية', 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 1, '2026-02-22 12:08:15', '2026-02-22 12:08:15'),
(37, 1761, '1610', 'ادارة الموارد البشرية', 5, 'Academic Courses', 8, 'كورس الإدارة', 100, 26, NULL, 50, 1, '2026-02-22 12:08:17', '2026-02-22 12:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_code` varchar(191) DEFAULT NULL,
  `log` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `user_id`, `attendance_id`, `employee_code`, `log`, `created_at`, `updated_at`) VALUES
(1, 1, 35, '1610', ' تم حذف سيشن للموظف 1761 والدورة التدريبية 8', '2026-02-22 12:07:40', '2026-02-22 12:07:40'),
(2, 1, 33, '1610', ' تم حذف سيشن للموظف 1761 والدورة التدريبية 8', '2026-02-22 12:08:05', '2026-02-22 12:08:05');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(191) NOT NULL DEFAULT 'admin',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(191) DEFAULT NULL,
  `actor_role` varchar(30) DEFAULT NULL,
  `action` varchar(191) NOT NULL,
  `model_type` varchar(191) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_type`, `user_id`, `user_name`, `actor_role`, `action`, `model_type`, `model_id`, `description`, `ip_address`, `created_at`, `updated_at`) VALUES
(18, 'admin', 2, 'Ezz Aly', 'admin', 'created', 'App\\Models\\PublicNotification', 6, 'ezz', '127.0.0.1', '2026-05-23 19:47:19', NULL),
(19, 'admin', 2, 'Ezz Aly', 'admin', 'created', 'App\\Models\\PublicNotification', 7, 'asdasasdasdasd', '127.0.0.1', '2026-05-23 19:48:16', NULL),
(20, 'admin', 1, 'محمد سعيد', 'admin', 'activated', 'App\\Models\\Course', 8, 'كورس الإدارة', '127.0.0.1', '2026-05-23 19:57:42', NULL),
(21, 'admin', 1, 'محمد سعيد', 'admin', 'created', 'App\\Models\\Admin', 3, 'Ezz Aly', '127.0.0.1', '2026-05-23 20:02:55', NULL),
(22, 'admin', 3, 'Ezz Aly', 'admin', 'created', 'App\\Models\\CourseAssignment', 9, 'Safety Assessment', '127.0.0.1', '2026-05-23 20:38:44', NULL),
(23, 'admin', 3, 'Ezz Aly', 'admin', 'created', 'App\\Models\\Instructor', 3, 'aadasdasd', '127.0.0.1', '2026-05-23 20:44:26', NULL),
(24, 'admin', 3, 'Ezz Aly', 'admin', 'created', 'App\\Models\\PublicNotification', 8, 'ك', '127.0.0.1', '2026-05-24 09:42:17', NULL),
(25, 'admin', 3, 'Ezz Aly', 'admin', 'created', 'App\\Models\\Category', 6, 'Safety', '127.0.0.1', '2026-05-24 09:49:35', NULL),
(26, 'user', 25, 'عز الدين على عبد الله السيد', 'learner', 'logged_in', 'App\\Models\\User', 25, 'Logged in session', '127.0.0.1', '2026-05-24 10:11:49', NULL),
(27, 'user', 25, 'عز الدين على عبد الله السيد', 'learner', 'created', 'App\\Models\\CourseRating', 1, 'CourseRating #1', '127.0.0.1', '2026-05-24 10:12:19', NULL),
(28, 'user', 25, 'عز الدين على عبد الله السيد', 'learner', 'updated', 'App\\Models\\CourseRating', 1, 'CourseRating #1', '127.0.0.1', '2026-05-24 10:13:47', NULL),
(29, 'admin', 3, 'Ezz Aly', 'admin', 'created', 'App\\Models\\Course', 9, 'Safety Essentials', '127.0.0.1', '2026-05-24 11:48:32', NULL),
(30, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Course', 9, 'Safety Essentials', '127.0.0.1', '2026-05-24 11:50:09', NULL),
(31, 'admin', 3, 'Ezz Aly', 'admin', 'activated', 'App\\Models\\Course', 9, 'Safety Essentials', '127.0.0.1', '2026-05-24 11:55:00', NULL),
(32, 'admin', 3, 'Ezz Aly', 'admin', 'activated', 'App\\Models\\Course', 9, 'Safety Essentials', '127.0.0.1', '2026-05-24 12:19:41', NULL),
(33, 'admin', 3, 'Ezz Aly', 'admin', 'activated', 'App\\Models\\Course', 9, 'اساسيات السلامة', '127.0.0.1', '2026-05-24 12:45:50', NULL),
(34, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\CourseSession', 14, 'Cohort A', '127.0.0.1', '2026-05-24 13:32:46', NULL),
(35, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\CourseSession', 15, 'Cohort B', '127.0.0.1', '2026-05-24 13:33:04', NULL),
(36, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\CourseSession', 16, 'Cohort C', '127.0.0.1', '2026-05-24 13:33:25', NULL),
(37, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 282, 'Course Attendance', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(38, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 283, 'Passcode Reset (seconds)', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(39, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 257, 'About — Description', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(40, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 258, 'About — Our Values', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(41, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 259, 'About — Our Mission', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(42, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Setting', 260, 'About — Our Vision', '127.0.0.1', '2026-05-31 10:40:53', NULL),
(43, 'admin', 1, 'محمد سعيد', 'admin', 'created', 'App\\Models\\Instructor', 4, 'عز علي', '127.0.0.1', '2026-05-31 10:55:04', NULL),
(44, 'admin', 1, 'محمد سعيد', 'admin', 'updated', 'App\\Models\\Course', 9, 'اساسيات السلامة', '127.0.0.1', '2026-05-31 10:55:42', NULL),
(45, 'admin', 1, 'محمد سعيد', 'admin', 'updated', 'App\\Models\\Course', 9, 'اساسيات السلامة', '127.0.0.1', '2026-05-31 10:56:50', NULL),
(46, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\CourseSession', 5, 'session 1', '127.0.0.1', '2026-05-31 11:02:56', NULL),
(47, 'admin', 3, 'Ezz Aly', 'admin', 'updated', 'App\\Models\\Course', 8, 'Management Course', '127.0.0.1', '2026-05-31 11:43:49', NULL),
(48, 'admin', 1, 'محمد سعيد', 'admin', 'updated', 'App\\Models\\Course', 10, 'Effective Communication', '127.0.0.1', '2026-05-31 12:14:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `logo` varchar(191) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `logo`, `active`, `created_at`, `updated_at`) VALUES
(3, '{\"ar\":\"قسم التسويق\",\"en\":\"Marketing Department\"}', 'Category/uGWbdRAVXoaYxLuE28MHfc400d6e78ac1cbbe0d7d69e680e758e.png', 1, '2025-09-10 08:07:09', '2026-05-23 19:43:51'),
(4, '{\"ar\":\"قسم البرمجة\",\"en\":\"Programming Department\"}', 'Category/uGWbdRAVXoaYxLuE28MHfc400d6e78ac1cbbe0d7d69e680e758e.png', 1, '2025-09-10 08:07:09', '2025-09-10 08:07:09'),
(5, '{\"ar\":\"الدورات الأكاديمية\",\"en\":\"Academic Courses\"}', 'Category/QFlcSLKJW2zJFujgEZWv0ece728009845ea10beba3c282c229e1.png', 1, '2026-02-09 09:05:05', '2026-02-09 09:05:05'),
(6, '{\"ar\":\"السلامة\",\"en\":\"Safety\"}', 'Category/wM3S44owsUanvbl1Xxwnfe8284c71b2108ec8021d718b7891a3b.jpg', 1, '2026-05-24 09:49:35', '2026-05-24 09:49:35');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_templates`
--

CREATE TABLE `certificate_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `description_ar` varchar(500) DEFAULT NULL,
  `auto_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`auto_fields`)),
  `file_path` varchar(500) DEFAULT NULL,
  `original_filename` varchar(191) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `certificate_templates`
--

INSERT INTO `certificate_templates` (`id`, `name`, `name_ar`, `description`, `description_ar`, `auto_fields`, `file_path`, `original_filename`, `mime_type`, `file_size`, `uploaded_by`, `is_active`, `created_at`, `updated_at`) VALUES
(16, 'NAS Standard Certificate', 'شهادة NAS القياسية', 'Default template — Auto-fills learner name, course, date, instructor', 'القالب الافتراضي — يملأ تلقائيًا اسم المتدرب والدورة والتاريخ والمدرب', '[\"Learner full name\",\"Course name\",\"Completion date\",\"Instructor name\",\"Certificate ID\"]', NULL, NULL, NULL, NULL, NULL, 0, '2026-05-24 08:39:41', '2026-05-24 08:40:18'),
(17, 'NAS Standard Certificate', 'شهادة NAS القياسية', 'Default template — Auto-fills learner name, course, date, instructor', 'القالب الافتراضي — يملأ تلقائيًا اسم المتدرب والدورة والتاريخ والمدرب', '[\"Learner full name\",\"Course name\",\"Completion date\",\"Instructor name\",\"Certificate ID\"]', 'certificate-templates/ZgjzbDaUqYnHFZLCBfmP2740e76a2bd80df9be57270bfa4ae862.png', 'iso45001.png', 'image/png', 1565331, 3, 0, '2026-05-24 08:40:18', '2026-05-24 08:41:15'),
(18, 'NAS Standard Certificate', 'شهادة NAS القياسية', 'Default template — Auto-fills learner name, course, date, instructor', 'القالب الافتراضي — يملأ تلقائيًا اسم المتدرب والدورة والتاريخ والمدرب', '[\"Learner full name\",\"Course name\",\"Completion date\",\"Instructor name\",\"Certificate ID\"]', 'certificate-templates/Z4TLLaqjjqN4YWhqBn9G3ffc2ad4aca66abaf760490fa5ff5389.jpg', 'a1JKpvYV2OmBZr1xe4hjcadf2ebfbe3bb67a4089e222013566fd.jpg', 'image/jpeg', 656749, 3, 1, '2026-05-24 08:41:15', '2026-05-24 09:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `message` varchar(191) NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `mobile`, `message`, `is_seen`, `created_at`, `updated_at`) VALUES
(2, 'محمد سعيد', 'dev@dev.com', '01015454545', 'عندي مشكلة في الاداره', 1, '2025-09-10 08:14:58', '2025-09-10 08:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `title_for_certificate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title_for_certificate`)),
  `notification_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_text`)),
  `course_type` varchar(191) NOT NULL DEFAULT 'online',
  `image` varchar(191) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `intro_video` varchar(191) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(191) DEFAULT 'EGP',
  `hours` int(11) DEFAULT NULL,
  `max_learners` int(10) UNSIGNED DEFAULT NULL,
  `language` varchar(191) DEFAULT NULL,
  `level` varchar(191) DEFAULT NULL,
  `certificate` tinyint(1) DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `for_public` tinyint(1) DEFAULT 0,
  `allow_attendances` tinyint(1) DEFAULT 1,
  `is_evaluate` tinyint(1) DEFAULT 0,
  `outside_materials` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `title_for_certificate`, `notification_text`, `course_type`, `image`, `category_id`, `intro_video`, `price`, `currency`, `hours`, `max_learners`, `language`, `level`, `certificate`, `active`, `for_public`, `allow_attendances`, `is_evaluate`, `outside_materials`, `created_at`, `updated_at`) VALUES
(6, '{\"ar\":\"الكورس الأول\",\"en\":\"First Course\"}', '{\"ar\":\"<p>الكورس الأول الكورس الأول</p>\",\"en\":\"<p>The first course. The first course.</p>\"}', '{\"ar\":\"الكورس الأول\",\"en\":\"First Course\"}', NULL, 'offline', 'Course/HJC3c2hNzmPRXwR9LXZO6b1e3358994bd26f67d8682a8bc4686c.jpg', 3, NULL, NULL, NULL, 20, NULL, 'عربي', 'medium', 1, 1, 0, 0, 1, 0, '2026-02-02 17:58:10', '2026-05-23 19:43:51'),
(7, '{\"ar\":\"الكورس الثاني\",\"en\":\"Second Course\"}', '{\"ar\":\"<p>الكورس الثاني</p>\",\"en\":\"<p>The second course.</p>\"}', '{\"ar\":\"الكورس الثاني\",\"en\":\"Second Course\"}', NULL, 'offline', 'Course/8vsq4bin3oYzhTli1cCHf236d07bd8780eb36ad22de451466f17.png', 4, NULL, NULL, NULL, 20, NULL, 'عربي', 'medium', 1, 1, 0, 1, 1, 0, '2026-02-03 10:10:58', '2026-02-03 10:10:58'),
(8, '{\"ar\":\"Management Course\",\"en\":\"Management Course\"}', '{\"ar\":\"<p>Management course.</p>\",\"en\":\"<p>Management course.</p>\"}', '{\"ar\":\"كورس الإدارة\",\"en\":\"Management Course\"}', NULL, 'offline', 'Course/rFoQaTiiv6uP0JRMHedcaeecd9a4ae32ff5da3fda25e4caf29e8.jpg', 5, NULL, NULL, NULL, 100, 30, 'عربي', 'intermediate', 1, 0, 0, 0, 0, 0, '2026-02-09 09:09:21', '2026-05-31 11:43:49'),
(9, '{\"en\":\"اساسيات السلامة\",\"ar\":\"اساسيات السلامة\"}', '{\"en\":\"test\",\"ar\":\"test\"}', NULL, NULL, 'online', 'Course/mNq5epVOq7SLdqB4GWxR21459b7c5d54cd4b8d0deab92216999c.jpg', 6, NULL, '0.00', 'EGP', 1, 30, NULL, 'intermediate', 1, 0, 0, 0, 0, 0, '2026-05-24 11:48:32', '2026-05-31 10:56:50'),
(10, '{\"en\":\"Effective Communication\",\"ar\":\"Effective Communication\"}', '{\"en\":\"Open soft-skills course anyone can join\",\"ar\":\"Open soft-skills course anyone can join\"}', '{\"ar\":\"الكورس الثاني\",\"en\":\"Second Course\"}', NULL, 'offline', 'Course/8vsq4bin3oYzhTli1cCHf236d07bd8780eb36ad22de451466f17.png', 4, NULL, NULL, NULL, 20, 30, 'عربي', 'beginner', 1, 0, 1, 0, 0, 0, '2026-05-31 12:07:16', '2026-05-31 12:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `courses_instructors`
--

CREATE TABLE `courses_instructors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `instructor_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `courses_instructors`
--

INSERT INTO `courses_instructors` (`id`, `course_id`, `instructor_id`, `created_at`, `updated_at`) VALUES
(7, 6, 1, NULL, NULL),
(8, 7, 2, NULL, NULL),
(9, 8, 1, NULL, NULL),
(11, 9, 4, NULL, NULL),
(12, 6, 4, NULL, NULL),
(13, 10, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_assignments`
--

CREATE TABLE `course_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `file` varchar(191) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `instructions_en` text DEFAULT NULL,
  `instructions_ar` text DEFAULT NULL,
  `cohort_scope` enum('all','specific') NOT NULL DEFAULT 'all',
  `pass_score` int(10) UNSIGNED DEFAULT NULL,
  `total_score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('draft','active') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_assignments`
--

INSERT INTO `course_assignments` (`id`, `course_id`, `title`, `title_ar`, `file`, `due_date`, `instructions_en`, `instructions_ar`, `cohort_scope`, `pass_score`, `total_score`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(6, 6, 'فايل اساين 1', NULL, 'CourseAssignment/z8aJm0bWpvgYUmEBcBMd926c7d6bf00d850de492cf7d62bdf3de.pdf', NULL, NULL, NULL, 'all', NULL, 0, 'draft', NULL, '2026-05-09 05:04:26', '2026-05-09 05:04:26'),
(7, 6, 'فايل اساين 2', NULL, 'CourseAssignment/G5xRaF68H1ysvAh89RMp258f325c17e9367df866c4ecafe75393.pdf', NULL, NULL, NULL, 'all', NULL, 0, 'draft', NULL, '2026-05-09 05:04:26', '2026-05-09 05:04:26'),
(8, 7, 'ezz', NULL, 'CourseAssignment/505sucVSaVnO0L0Z8ppWf40ec2e3aecbe220afc28b3dc975677b.pdf', '2026-05-07', NULL, NULL, 'all', NULL, 0, 'draft', NULL, '2026-05-23 16:19:27', '2026-05-23 16:19:27'),
(9, 8, 'Safety Assessment', 'تقييم السلامة', NULL, '2026-05-25', 'test', 'تست', 'all', 5, 10, 'active', 3, '2026-05-23 20:38:44', '2026-05-23 20:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `course_assignment_cohorts`
--

CREATE TABLE `course_assignment_cohorts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_assignment_id` bigint(20) UNSIGNED NOT NULL,
  `course_session_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `course_assignment_questions`
--

CREATE TABLE `course_assignment_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_assignment_id` bigint(20) UNSIGNED NOT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` enum('mcq','yes_no','open','reorder') NOT NULL,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `question_en` text NOT NULL,
  `question_ar` text DEFAULT NULL,
  `options_en` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options_en`)),
  `options_ar` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options_ar`)),
  `correct_answer_en` text DEFAULT NULL,
  `correct_answer_ar` text DEFAULT NULL,
  `explanation_en` text DEFAULT NULL,
  `explanation_ar` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_assignment_questions`
--

INSERT INTO `course_assignment_questions` (`id`, `course_assignment_id`, `position`, `type`, `score`, `question_en`, `question_ar`, `options_en`, `options_ar`, `correct_answer_en`, `correct_answer_ar`, `explanation_en`, `explanation_ar`, `created_at`, `updated_at`) VALUES
(1, 9, 0, 'mcq', 10, 'what is your name ?', 'ما اسمك ؟ظ', '[\"test 1\",\"test 2\"]', '[\"\\u062a\\u0633\\u062a 1\",\"\\u062a\\u0633\\u062a 2\"]', 'test 1', 'تست 1', 'testtttttttt', 'تستتتتتتتتتت', '2026-05-23 20:38:44', '2026-05-23 20:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `course_exams`
--

CREATE TABLE `course_exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `title_ar` varchar(191) DEFAULT NULL,
  `instructions_en` text DEFAULT NULL,
  `instructions_ar` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `cohort_scope` varchar(16) NOT NULL DEFAULT 'all',
  `pass_score` int(11) DEFAULT NULL,
  `total_score` int(11) NOT NULL DEFAULT 0,
  `status` varchar(16) NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `degree` bigint(20) NOT NULL,
  `duration` bigint(20) NOT NULL DEFAULT 60,
  `is_final` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_exams`
--

INSERT INTO `course_exams` (`id`, `course_id`, `section_id`, `title`, `title_ar`, `instructions_en`, `instructions_ar`, `due_date`, `cohort_scope`, `pass_score`, `total_score`, `status`, `created_by`, `degree`, `duration`, `is_final`, `created_at`, `updated_at`) VALUES
(10, 7, 25, '{\"ar\":\"اختبار نهائي علي الكورس الثاني\",\"en\":\"Final exam for the Second Course\"}', NULL, NULL, NULL, NULL, 'all', NULL, 0, 'draft', NULL, 100, 60, 1, '2026-02-03 10:14:15', '2026-02-03 10:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `course_exam_cohorts`
--

CREATE TABLE `course_exam_cohorts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_exam_id` bigint(20) UNSIGNED NOT NULL,
  `course_session_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `course_exam_questions`
--

CREATE TABLE `course_exam_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_exam_id` bigint(20) UNSIGNED NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `type` varchar(16) NOT NULL DEFAULT 'mcq',
  `score` int(11) NOT NULL DEFAULT 0,
  `question_en` text DEFAULT NULL,
  `question_ar` text DEFAULT NULL,
  `options_en` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options_en`)),
  `options_ar` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options_ar`)),
  `correct_answer_en` text DEFAULT NULL,
  `correct_answer_ar` text DEFAULT NULL,
  `explanation_en` text DEFAULT NULL,
  `explanation_ar` text DEFAULT NULL,
  `question` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`question`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_exam_questions`
--

INSERT INTO `course_exam_questions` (`id`, `course_exam_id`, `position`, `type`, `score`, `question_en`, `question_ar`, `options_en`, `options_ar`, `correct_answer_en`, `correct_answer_ar`, `explanation_en`, `explanation_ar`, `question`, `created_at`, `updated_at`) VALUES
(42, 10, 0, 'mcq', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"ar\":\"السؤال الأول : ما معنى الإدارة ؟\",\"en\":\"Question 1: What does Management mean?\"}', '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(43, 10, 0, 'mcq', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"ar\":\"السؤال الأول : ما معنى التسويق؟\",\"en\":\"Question 1: What does Marketing mean?\"}', '2026-02-03 10:14:15', '2026-02-03 10:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `course_exam_question_answers`
--

CREATE TABLE `course_exam_question_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answer`)),
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_exam_question_answers`
--

INSERT INTO `course_exam_question_answers` (`id`, `question_id`, `answer`, `is_correct`, `created_at`, `updated_at`) VALUES
(140, 42, '{\"ar\":\"المشروعات\",\"en\":\"Projects\"}', 1, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(141, 42, '{\"ar\":\"البحوث\",\"en\":\"Research\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(142, 42, '{\"ar\":\"التسويق\",\"en\":\"Marketing\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(143, 42, '{\"ar\":\"PMP\",\"en\":\"PMP\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(144, 43, '{\"ar\":\"المبيعات\",\"en\":\"Sales\"}', 1, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(145, 43, '{\"ar\":\"الإعلانات\",\"en\":\"Advertising\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(146, 43, '{\"ar\":\"الإدارة\",\"en\":\"Management\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15'),
(147, 43, '{\"ar\":\"البرمجة\",\"en\":\"Programming\"}', 0, '2026-02-03 10:14:15', '2026-02-03 10:14:15');

-- --------------------------------------------------------

--
-- Table structure for table `course_lectures`
--

CREATE TABLE `course_lectures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `type` varchar(191) NOT NULL DEFAULT 'url',
  `content_type` varchar(16) NOT NULL DEFAULT 'video',
  `learner_scope` varchar(16) NOT NULL DEFAULT 'all',
  `session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `duration_minutes` int(10) UNSIGNED DEFAULT NULL,
  `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`instructions`)),
  `require_completion` tinyint(1) NOT NULL DEFAULT 0,
  `video` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `course_lecture_questions`
--

CREATE TABLE `course_lecture_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `lecture_id` bigint(20) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `answered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `course_qualification_skills`
--

CREATE TABLE `course_qualification_skills` (
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `qualification_skill_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_qualification_skills`
--

INSERT INTO `course_qualification_skills` (`course_id`, `qualification_skill_id`) VALUES
(7, 3),
(8, 3),
(9, 2),
(9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_ratings`
--

CREATE TABLE `course_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 1,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_ratings`
--

INSERT INTO `course_ratings` (`id`, `user_id`, `course_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 25, 8, 5, NULL, '2026-05-24 10:12:19', '2026-05-31 11:41:59'),
(7, 25, 9, 4, NULL, '2026-05-31 11:46:07', '2026-05-31 11:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `course_resources`
--

CREATE TABLE `course_resources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `link` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `course_sections`
--

CREATE TABLE `course_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `enrolment_closes_at` date DEFAULT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(32) DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_sections`
--

INSERT INTO `course_sections` (`id`, `course_id`, `name`, `start_date`, `end_date`, `enrolment_closes_at`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(20, 6, '{\"ar\":\"المجموعة الأولي\",\"en\":\"First Group\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-02 17:58:41', '2026-02-02 17:58:41'),
(21, 6, '{\"ar\":\"المجموعة الثانية\",\"en\":\"Second Group\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-02 17:58:41', '2026-02-02 17:58:41'),
(22, 6, '{\"ar\":\"المجموعة الثالثة\",\"en\":\"Third Group\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-02 17:58:41', '2026-02-02 17:58:41'),
(23, 7, '{\"ar\":\"المجموعة الأولي\",\"en\":\"First Group\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-03 10:11:21', '2026-02-03 10:11:21'),
(24, 7, '{\"ar\":\"المجموعة الثانية\",\"en\":\"Second Group\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-03 10:11:21', '2026-02-03 10:11:21'),
(25, 7, '{\"ar\":\"اختبار نهائي\",\"en\":\"Final Exam\"}', NULL, NULL, NULL, NULL, 'scheduled', '2026-02-03 10:13:31', '2026-02-03 10:13:31'),
(26, 8, '{\"ar\":\"المجموعة الأولي\",\"en\":\"Cohort A\"}', '2026-05-22', '2026-05-29', NULL, NULL, 'active', '2026-02-09 09:10:24', '2026-05-31 11:43:49'),
(27, 8, '{\"ar\":\"المجموعة الثانية\",\"en\":\"Cohort B\"}', '2026-05-24', '2026-05-31', NULL, NULL, 'active', '2026-02-09 09:10:24', '2026-05-24 13:55:24'),
(29, 8, '{\"ar\":\"المجموعة C\",\"en\":\"Cohort C\"}', '2026-05-31', '2026-10-31', NULL, 20, 'scheduled', '2026-05-24 14:07:24', '2026-05-24 14:07:24'),
(30, 9, '{\"en\":\"Cohort 1\",\"ar\":\"الدفعة 1\"}', '2026-04-30', '2026-09-29', NULL, NULL, 'scheduled', '2026-05-31 10:56:50', '2026-05-31 10:56:50'),
(31, 7, '{\"en\":\"Open Intake\",\"ar\":\"???????? ????????????\"}', '2026-06-30', '2026-09-28', '2026-06-20', 25, 'scheduled', '2026-05-31 11:40:19', '2026-05-31 11:40:19'),
(32, 9, '{\"en\":\"Open Intake\",\"ar\":\"???????? ????????????\"}', '2026-06-30', '2026-09-28', '2026-06-20', 25, 'scheduled', '2026-05-31 11:40:19', '2026-05-31 11:40:19'),
(33, 10, '{\"en\":\"Cohort A\",\"ar\":\"???????????? ??\"}', '2026-07-04', '2026-08-04', '2026-06-28', 30, 'scheduled', '2026-05-31 12:07:16', '2026-05-31 12:14:16'),
(34, 7, '{\"en\":\"Cohort D\",\"ar\":\"???????????? ??\"}', '2026-07-10', '2026-08-10', '2026-07-01', 25, 'scheduled', '2026-05-31 12:22:47', '2026-05-31 12:22:47');

-- --------------------------------------------------------

--
-- Table structure for table `course_sessions`
--

CREATE TABLE `course_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `passcode` varchar(10) DEFAULT NULL,
  `passcode_issued_at` timestamp NULL DEFAULT NULL,
  `passcode_expires_at` timestamp NULL DEFAULT NULL,
  `attendance_window_minutes` int(10) UNSIGNED DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course_sessions`
--

INSERT INTO `course_sessions` (`id`, `course_id`, `section_id`, `title`, `time_from`, `time_to`, `location`, `passcode`, `passcode_issued_at`, `passcode_expires_at`, `attendance_window_minutes`, `session_date`, `created_at`, `updated_at`) VALUES
(5, 6, 20, 'session 1', NULL, NULL, 'السراج مول الدور السابع', '27281', '2026-05-31 11:02:56', '2026-05-31 11:32:56', 30, '2026-05-31', '2026-02-02 18:02:24', '2026-05-31 11:02:56'),
(6, 6, 20, 'session 2', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-12', '2026-02-02 18:02:24', '2026-02-02 18:02:24'),
(7, 6, 20, 'session 3', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-15', '2026-02-02 18:02:24', '2026-02-02 18:02:24'),
(8, 6, 21, 'session 1', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-22', '2026-02-02 18:03:34', '2026-02-02 18:03:34'),
(9, 6, 21, 'session 2', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-24', '2026-02-02 18:03:34', '2026-02-02 18:03:34'),
(10, 6, 22, 'session 1', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-03-01', '2026-02-02 18:04:06', '2026-02-02 18:04:06'),
(11, 6, 22, 'session 2', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-03-04', '2026-02-02 18:04:06', '2026-02-02 18:04:06'),
(12, 7, 23, 'session 1', '14:00:00', '18:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-10', '2026-02-03 10:12:43', '2026-02-03 10:12:43'),
(13, 7, 24, 'session 1', '14:00:00', '19:00:00', 'السراج مول الدور السابع', NULL, NULL, NULL, NULL, '2026-02-15', '2026-02-03 10:13:02', '2026-02-03 10:13:02'),
(14, 8, 26, 'Cohort A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-09', '2026-02-09 09:11:37', '2026-05-24 13:32:46'),
(15, 8, 26, 'Cohort B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19', '2026-02-09 09:11:37', '2026-05-24 13:33:04'),
(16, 8, 27, 'Cohort C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-24', '2026-02-09 09:12:44', '2026-05-24 13:33:25');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evaluation_category_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `evaluation_category_id`, `type`, `title`, `is_required`, `created_at`, `updated_at`) VALUES
(3, 2, 'five', '{\"ar\":\"هل المحاضر على دراية بالمادة التعليمة التي يقوم بشرحها ؟\",\"en\":\"Is the instructor knowledgeable about the material being taught?\"}', 1, '2026-01-31 10:22:51', '2026-01-31 10:22:51'),
(4, 2, 'five', '{\"ar\":\"هل المحاضرشجع الحضور على الاشتراك وتبادل الاراء ؟\",\"en\":\"Did the instructor encourage attendees to participate and exchange ideas?\"}', 1, '2026-01-31 10:23:04', '2026-01-31 10:23:04'),
(5, 2, 'ten', '{\"ar\":\"المدرب دعم المادة العلمية بتدريبات وأنشطة متنوعة وهادفة ووثيقة الصلة بموضوع التدريب ؟\",\"en\":\"Did the trainer support the material with varied, purposeful and relevant activities?\"}', 1, '2026-01-31 10:23:17', '2026-01-31 10:23:17'),
(6, 2, 'text', '{\"ar\":\"قام المحاضر بتغطية كافة الاهداف المرجوة من التدريب ؟\",\"en\":\"Did the instructor cover all of the intended training objectives?\"}', 1, '2026-01-31 10:27:58', '2026-01-31 10:27:58'),
(7, 3, 'five', '{\"ar\":\"هل الماده التعليمية معدة بشكل جيد ؟\",\"en\":\"Was the training material well prepared?\"}', 1, '2026-02-03 06:54:37', '2026-02-03 06:54:37'),
(8, 3, 'ten', '{\"ar\":\"هل كان مضمون التدريب منظم وسهل المتابعه؟\",\"en\":\"Was the training content organised and easy to follow?\"}', 1, '2026-02-03 06:54:55', '2026-02-03 06:54:55'),
(9, 3, 'text', '{\"ar\":\"اذكر/ اذكري النقاط الايجابية في التدريب وفي المحاضر ؟\",\"en\":\"List the positive points about the training and the instructor.\"}', 1, '2026-02-03 06:55:12', '2026-02-03 06:55:12'),
(10, 4, 'ten', '{\"ar\":\"هل ترشح حضور زملاء اخرين لنفس الكورس مع المحاضر ؟\",\"en\":\"Would you recommend other colleagues attend the same course with this instructor?\"}', 1, '2026-02-09 09:27:46', '2026-02-09 09:27:46');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_categories`
--

CREATE TABLE `evaluation_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `evaluation_categories`
--

INSERT INTO `evaluation_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, '{\"ar\":\"تقييم  المحاضر\",\"en\":\"Instructor Evaluation\"}', '2026-01-31 10:19:53', '2026-01-31 10:19:53'),
(3, '{\"ar\":\"تقييم الكورس\",\"en\":\"Course Evaluation\"}', '2026-02-03 06:54:03', '2026-02-03 06:54:03'),
(4, '{\"ar\":\"NPS\",\"en\":\"NPS\"}', '2026-02-09 09:27:00', '2026-02-09 09:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `duration` bigint(20) NOT NULL DEFAULT 60,
  `full_mark` bigint(20) DEFAULT 100,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `uuid`, `title`, `duration`, `full_mark`, `active`, `created_at`, `updated_at`) VALUES
(7, 'c23239c5-bf7e-4c41-8878-086fe4907241', '{\"ar\":\"الأختبار الأول\",\"en\":\"First Exam\"}', 60, 100, 1, '2026-02-10 17:44:45', '2026-02-10 17:44:45'),
(8, '9214cafd-5fb8-4eab-9347-f23ceb21101c', '{\"ar\":\"اختبار تجريبي\",\"en\":\"Test Exam\"}', 100, 100, 1, '2026-04-27 11:38:40', '2026-04-27 11:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `form_questions`
--

CREATE TABLE `form_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) DEFAULT 'radio',
  `question` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`question`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `form_questions`
--

INSERT INTO `form_questions` (`id`, `form_id`, `type`, `question`, `created_at`, `updated_at`) VALUES
(10, 7, 'radio', '{\"ar\":\"يسب\",\"en\":\"Sample radio question 1\"}', '2026-02-10 17:46:54', '2026-02-10 17:46:54'),
(11, 7, 'yes_no', '{\"ar\":\"dsfsd\",\"en\":\"Sample yes/no question 1\"}', '2026-02-10 17:47:17', '2026-02-10 17:47:17'),
(13, 7, 'text', '{\"ar\":\"تيكست\",\"en\":\"Free-text answer\"}', '2026-02-10 19:10:55', '2026-02-10 19:10:55'),
(14, 7, 'radio', '{\"ar\":\"aa\",\"en\":\"Sample radio question 2\"}', '2026-04-01 09:48:04', '2026-04-01 09:48:04'),
(15, 7, 'radio', '{\"ar\":\"يسب\",\"en\":\"Sample radio question 3\"}', '2026-04-27 09:56:12', '2026-04-27 09:56:12'),
(16, 7, 'radio', '{\"ar\":\"Labore excepteur nem\",\"en\":\"Labore excepteur nem\"}', '2026-04-27 10:02:24', '2026-04-27 10:02:24'),
(17, 7, 'yes_no', '{\"ar\":\"Possimus architecto\",\"en\":\"Possimus architecto\"}', '2026-04-27 10:02:40', '2026-04-27 10:02:40'),
(18, 7, 'radio', '{\"ar\":\"Explicabo Vitae cil\",\"en\":\"Explicabo Vitae cil\"}', '2026-04-27 10:03:46', '2026-04-27 10:03:46'),
(19, 7, 'yes_no', '{\"ar\":\"Quo atque ut neque s\",\"en\":\"Quo atque ut neque s\"}', '2026-04-27 10:04:17', '2026-04-27 10:04:17'),
(20, 8, 'yes_no', '{\"ar\":\"نعم\",\"en\":\"Yes / No question\"}', '2026-04-27 11:39:00', '2026-04-27 11:39:00'),
(21, 8, 'radio', '{\"ar\":\"3\",\"en\":\"Choose option\"}', '2026-04-27 11:39:20', '2026-04-27 11:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `form_question_answers`
--

CREATE TABLE `form_question_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answer`)),
  `is_true` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `form_question_answers`
--

INSERT INTO `form_question_answers` (`id`, `form_question_id`, `answer`, `is_true`, `created_at`, `updated_at`) VALUES
(33, 10, '{\"ar\":\"يسب\",\"en\":\"Option A\"}', 1, '2026-02-10 17:46:54', '2026-02-10 17:46:54'),
(34, 10, '{\"ar\":\"يب\",\"en\":\"Option B\"}', 0, '2026-02-10 17:46:54', '2026-02-10 17:46:54'),
(35, 10, '{\"ar\":\"يسب\",\"en\":\"Option C\"}', 0, '2026-02-10 17:46:54', '2026-02-10 17:46:54'),
(36, 10, '{\"ar\":\"يب\",\"en\":\"Option D\"}', 0, '2026-02-10 17:46:54', '2026-02-10 17:46:54'),
(37, 11, '{\"ar\":\"نعم\",\"en\":\"Yes\"}', 1, '2026-02-10 17:47:17', '2026-02-10 17:47:17'),
(38, 11, '{\"ar\":\"لا\",\"en\":\"No\"}', 0, '2026-02-10 17:47:17', '2026-02-10 17:47:17'),
(39, 14, '{\"ar\":\"a\",\"en\":\"a\"}', 0, '2026-04-01 09:48:04', '2026-04-01 09:48:04'),
(40, 14, '{\"ar\":\"b\",\"en\":\"b\"}', 1, '2026-04-01 09:48:04', '2026-04-01 09:48:04'),
(41, 14, '{\"ar\":\"c\",\"en\":\"c\"}', 0, '2026-04-01 09:48:04', '2026-04-01 09:48:04'),
(42, 14, '{\"ar\":\"d\",\"en\":\"d\"}', 0, '2026-04-01 09:48:04', '2026-04-01 09:48:04'),
(43, 15, '{\"ar\":\"aa\",\"en\":\"aa\"}', 0, '2026-04-27 09:56:12', '2026-04-27 09:56:12'),
(44, 15, '{\"ar\":\"vb\",\"en\":\"vb\"}', 1, '2026-04-27 09:56:12', '2026-04-27 09:56:12'),
(45, 15, '{\"ar\":\"as\",\"en\":\"as\"}', 0, '2026-04-27 09:56:12', '2026-04-27 09:56:12'),
(46, 15, '{\"ar\":\"-\",\"en\":\"-\"}', 0, '2026-04-27 09:56:12', '2026-04-27 09:56:12'),
(47, 16, '{\"ar\":\"Consequatur eius qua\",\"en\":\"Consequatur eius qua\"}', 1, '2026-04-27 10:02:24', '2026-04-27 10:02:24'),
(48, 16, '{\"ar\":\"Sunt deserunt quaer\",\"en\":\"Sunt deserunt quaer\"}', 0, '2026-04-27 10:02:24', '2026-04-27 10:02:24'),
(49, 16, '{\"ar\":\"Proident fugiat rep\",\"en\":\"Proident fugiat rep\"}', 0, '2026-04-27 10:02:24', '2026-04-27 10:02:24'),
(50, 16, '{\"ar\":\"-\",\"en\":\"-\"}', 0, '2026-04-27 10:02:24', '2026-04-27 10:02:24'),
(51, 17, '{\"ar\":\"نعم\",\"en\":\"Yes\"}', 0, '2026-04-27 10:02:40', '2026-04-27 10:02:40'),
(52, 17, '{\"ar\":\"لا\",\"en\":\"No\"}', 1, '2026-04-27 10:02:40', '2026-04-27 10:02:40'),
(53, 18, '{\"ar\":\"Proident numquam in\",\"en\":\"Proident numquam in\"}', 0, '2026-04-27 10:03:46', '2026-04-27 10:03:46'),
(54, 18, '{\"ar\":\"Magni enim deserunt\",\"en\":\"Magni enim deserunt\"}', 0, '2026-04-27 10:03:46', '2026-04-27 10:03:46'),
(55, 18, '{\"ar\":\"Enim ut fugit dolor\",\"en\":\"Enim ut fugit dolor\"}', 1, '2026-04-27 10:03:46', '2026-04-27 10:03:46'),
(56, 18, '{\"ar\":\"Debitis non minus bl\",\"en\":\"Debitis non minus bl\"}', 0, '2026-04-27 10:03:46', '2026-04-27 10:03:46'),
(57, 19, '{\"ar\":\"نعم\",\"en\":\"Yes\"}', 1, '2026-04-27 10:04:17', '2026-04-27 10:04:17'),
(58, 19, '{\"ar\":\"لا\",\"en\":\"No\"}', 0, '2026-04-27 10:04:17', '2026-04-27 10:04:17'),
(59, 20, '{\"ar\":\"نعم\",\"en\":\"Yes\"}', 1, '2026-04-27 11:39:00', '2026-04-27 11:39:00'),
(60, 20, '{\"ar\":\"لا\",\"en\":\"No\"}', 0, '2026-04-27 11:39:00', '2026-04-27 11:39:00'),
(61, 21, '{\"ar\":\"1\",\"en\":\"1\"}', 0, '2026-04-27 11:39:20', '2026-04-27 11:39:20'),
(62, 21, '{\"ar\":\"2\",\"en\":\"2\"}', 0, '2026-04-27 11:39:20', '2026-04-27 11:39:20'),
(63, 21, '{\"ar\":\"3\",\"en\":\"3\"}', 1, '2026-04-27 11:39:20', '2026-04-27 11:39:20'),
(64, 21, '{\"ar\":\"4\",\"en\":\"4\"}', 0, '2026-04-27 11:39:20', '2026-04-27 11:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `bio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bio`)),
  `status` varchar(20) DEFAULT 'active',
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `name`, `bio`, `status`, `last_active_at`, `email`, `image`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\":\"محمد سعيد\",\"en\":\"Mohamed Said\"}', '{\"ar\":\"<p>محمد سعيد محمد سعيد محمد سعيد</p>\",\"en\":\"<p>Mohamed Said. Mohamed Said. Mohamed Said.</p>\"}', 'active', NULL, 'mohamedsaid11129@gmail.com', 'Instructor/Ayf5J8zawG5ZlRTYNOH9f4671ef1bea584118ee4623d6c577a98.png', '2025-08-06 09:08:29', '2025-08-06 09:08:50'),
(2, '{\"ar\":\"كريم حسن\",\"en\":\"Karim Hassan\"}', '{\"ar\":\"<p>كريم حسن كريم حسن كريم حسن</p>\",\"en\":\"<p>Karim Hassan. Karim Hassan. Karim Hassan.</p>\"}', 'deactivated', NULL, 'k@hassan.com', 'Instructor/J1MivWmo89BmmCaODRJR630a1e36f1a380d43a985996c79c8044.png', '2025-08-06 09:09:31', '2025-08-06 09:09:31'),
(3, '{\"en\":\"aadasdasd\",\"ar\":\"asdadsadasd\"}', '{\"en\":\"\"}', 'active', NULL, 'asdsadad@awdasd.ff', '', '2026-05-23 20:44:26', '2026-05-23 20:44:26'),
(4, '{\"en\":\"ezz aly\",\"ar\":\"عز علي\"}', '{\"ar\":\"\"}', 'active', NULL, 'ezzaly74@gmail.com', 'instructors/YMleDzN2xQO3kh1xBd9Tda78bf1b3563df84c90e37dab3a2fad0.jpg', '2026-05-31 10:55:04', '2026-05-31 10:55:04');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"790c0075-8666-466d-ae7c-53d62e1eb88c\",\"displayName\":\"App\\\\Jobs\\\\RunSyncEmployees\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RunSyncEmployees\",\"command\":\"O:25:\\\"App\\\\Jobs\\\\RunSyncEmployees\\\":0:{}\"}}', 0, NULL, 1779618291, 1779618291);

-- --------------------------------------------------------

--
-- Table structure for table `job_titles`
--

CREATE TABLE `job_titles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `job_titles`
--

INSERT INTO `job_titles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(35, 'رئيس قطاع  للموارد البشرية', '2026-05-24 10:40:58', '2026-05-24 10:40:58'),
(36, 'قائد فريق نظم و معلومات', '2026-05-24 10:40:58', '2026-05-24 10:40:58'),
(37, 'مطور برامج الكترونية', '2026-05-24 10:40:58', '2026-05-24 10:40:58'),
(38, 'مطور اول  تطبيقات الالكترونية', '2026-05-24 10:40:58', '2026-05-24 10:40:58'),
(39, 'اخصائي انظمة برمجية', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(40, 'مهندس الواجهه الاماميه', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(41, 'مدير مبرمجى مواقع الكترونية', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(42, 'مهندس نظم امعلومات', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(43, 'الرئيس التنفيذي', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(44, 'مهندس جودة برمجيات', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(45, 'رئيس قطاع القاهرة', '2026-05-24 10:41:00', '2026-05-24 10:41:00'),
(46, 'مدير اول التحول الرقمي', '2026-05-24 10:41:02', '2026-05-24 10:41:02'),
(47, 'محاسب اول  رواتب', '2026-05-24 10:41:02', '2026-05-24 10:41:02'),
(48, 'مصمم  UI\\UX', '2026-05-24 10:41:02', '2026-05-24 10:41:02'),
(49, 'عالم بيانات', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(50, 'مدير اول عام حسابات', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(51, 'مشرف خزينة', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(52, 'رئيس القطاع المالى', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(53, 'مدير حسابات البنوك', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(54, 'مندوب خزينة', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(55, 'مدير حسابات الائتمان', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(56, 'مشرف حسابات عام', '2026-05-24 10:41:04', '2026-05-24 10:41:04'),
(57, 'محاسب اول بنوك', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(58, 'محاسب اول فروع', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(59, 'محاسب ضرائب', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(60, 'محاسب ائتمان', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(61, 'محاسب تقسيط', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(62, 'محاسب فروع', '2026-05-24 10:41:06', '2026-05-24 10:41:06'),
(63, 'محاسب بنوك', '2026-05-24 10:41:07', '2026-05-24 10:41:07'),
(64, 'محاسب خزينة', '2026-05-24 10:41:07', '2026-05-24 10:41:07'),
(65, 'محاسب اول ضرائب', '2026-05-24 10:41:08', '2026-05-24 10:41:08'),
(66, 'مدير حسابات  الضرائب', '2026-05-24 10:41:08', '2026-05-24 10:41:08'),
(67, 'مشرف حسابات فروع', '2026-05-24 10:41:08', '2026-05-24 10:41:08'),
(68, 'محاسب عام', '2026-05-24 10:41:08', '2026-05-24 10:41:08'),
(69, 'رئيس قطاع  منتجات الاكسسوارت', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(70, 'رئيس قطاع المشتريات', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(71, 'أخصائي اول مشتريات', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(72, 'مديرمنتجات الاجهزة A', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(73, 'مدير المشتريات', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(74, 'رئيس قطاع  منتجات الاجهزة', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(75, 'مدير اول منتجات الاكسسوارات', '2026-05-24 10:41:10', '2026-05-24 10:41:10'),
(76, 'مدير اول منتجات الاجهزة', '2026-05-24 10:41:11', '2026-05-24 10:41:11'),
(77, 'مدير منتجات الاكسسوارات B', '2026-05-24 10:41:11', '2026-05-24 10:41:11'),
(78, 'قائد فريق مشتريات', '2026-05-24 10:41:11', '2026-05-24 10:41:11'),
(79, 'مدير منتج اكسسوار', '2026-05-24 10:41:11', '2026-05-24 10:41:11'),
(80, 'أخصائي مشتريات', '2026-05-24 10:41:11', '2026-05-24 10:41:11'),
(81, 'اخصائى اول التركيبات', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(82, 'مشرف البنية التحتيه لنظم المعلومات', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(83, 'اخصائى اول دعم نظم معلومات', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(84, 'مهندس شبكات', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(85, 'اخصائى دعم نظم معلومات', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(86, 'استشارى برامج', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(87, 'اخصائى مخزن', '2026-05-24 10:41:12', '2026-05-24 10:41:12'),
(88, 'رئيس قطاع الاستيراد', '2026-05-24 10:41:14', '2026-05-24 10:41:14'),
(89, 'رئيس قطاع العمليات', '2026-05-24 10:41:14', '2026-05-24 10:41:14'),
(90, 'مدير منتجات الاجهزة B', '2026-05-24 10:41:14', '2026-05-24 10:41:14'),
(91, 'مدير موقع الكترونى', '2026-05-24 10:41:14', '2026-05-24 10:41:14'),
(92, 'مدير الفرع', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(93, 'اخصائى اول تسويق الكترونى', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(94, 'اخصائي تسويق الكتروني', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(95, 'اخصائي تسويق الكتروني A', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(96, 'قائد فريق فنى جرافيك', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(97, 'اخصائي شراكات', '2026-05-24 10:41:15', '2026-05-24 10:41:15'),
(98, 'مصمم فنى جرافيك', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(99, 'مدير أول التسويق', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(100, 'اخصائى اول تسويق', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(101, 'مصمم اول فنى جرافيك', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(102, 'اخصائي تسويق', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(103, 'مدير قسم التقسيط', '2026-05-24 10:41:16', '2026-05-24 10:41:16'),
(104, 'اخصائي اول تقسيط', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(105, 'اخصائي تقسيط', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(106, 'مراجع داخلي', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(107, 'مدير حساب كبار العملاء', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(108, 'محامى أول', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(109, 'قائد فريق مبيعات شركات', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(110, 'مساعد الرئيس التنفيذى', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(111, 'مدير أول مشروعات', '2026-05-24 10:41:17', '2026-05-24 10:41:17'),
(112, 'عامل بوفيه و نظافة', '2026-05-24 10:41:19', '2026-05-24 10:41:19'),
(113, 'رئيس قسم مبيعات الشركات', '2026-05-24 10:41:19', '2026-05-24 10:41:19'),
(114, 'رئيس قطاع الشئون الادارية و القانونية', '2026-05-24 10:41:19', '2026-05-24 10:41:19'),
(115, 'محامي', '2026-05-24 10:41:19', '2026-05-24 10:41:19'),
(116, 'مشرف تشطيبات', '2026-05-24 10:41:20', '2026-05-24 10:41:20'),
(117, 'مدير حساب', '2026-05-24 10:41:20', '2026-05-24 10:41:20'),
(118, 'اخصائى  انظمة برمجية', '2026-05-24 10:41:20', '2026-05-24 10:41:20'),
(119, 'المدير العام', '2026-05-24 10:41:20', '2026-05-24 10:41:20'),
(120, 'اخصائي مبيعات داخلية', '2026-05-24 10:41:20', '2026-05-24 10:41:20'),
(121, 'متدرب', '2026-05-24 10:41:21', '2026-05-24 10:41:21'),
(122, 'مدير التسويق', '2026-05-24 10:41:22', '2026-05-24 10:41:22'),
(123, 'أخصائي مبيعات شركات', '2026-05-24 10:41:22', '2026-05-24 10:41:22'),
(124, 'مهندس تطبيقات', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(125, 'مساعد رئيس العمليات', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(126, 'مهندس تحكم مخزون', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(127, 'مشرف محتوى موقع إلكترونى', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(128, 'اخصائي متابعة طلبات موقع الكترونى', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(129, 'اخصائى الشكوى', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(130, 'مشرف خدمة عملاء', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(131, 'أخصائى محتوى موقع إلكترونى', '2026-05-24 10:41:24', '2026-05-24 10:41:24'),
(132, 'اخصائى مواقع تواصل اجتماعى', '2026-05-24 10:41:25', '2026-05-24 10:41:25'),
(133, 'مصمم اول UI/UX', '2026-05-24 10:41:25', '2026-05-24 10:41:25'),
(134, 'أخصائى مبيعات اون لاين', '2026-05-24 10:41:25', '2026-05-24 10:41:25'),
(135, 'رئيس قطاع مبيعات الجملة', '2026-05-24 10:41:26', '2026-05-24 10:41:26'),
(136, 'مشرف مبيعات الجملة', '2026-05-24 10:41:26', '2026-05-24 10:41:26'),
(137, 'مندوب تسويق جملة', '2026-05-24 10:41:27', '2026-05-24 10:41:27'),
(138, 'مدير منطقه الجملة', '2026-05-24 10:41:27', '2026-05-24 10:41:27'),
(139, 'اخصائى اول مبيعات الجملة', '2026-05-24 10:41:27', '2026-05-24 10:41:27'),
(140, 'اخصائى اول كبار العملاء', '2026-05-24 10:41:27', '2026-05-24 10:41:27'),
(141, 'اخصائى مبيعات الجملة B', '2026-05-24 10:41:27', '2026-05-24 10:41:27'),
(142, 'أخصائى خدمة عملاء', '2026-05-24 10:41:32', '2026-05-24 10:41:32'),
(143, 'اخصائي  اول متابعة طلبات موقع الكترونى', '2026-05-24 10:41:32', '2026-05-24 10:41:32'),
(144, 'مسئول اول متابعة طلبات موقع الكترونى', '2026-05-24 10:41:33', '2026-05-24 10:41:33'),
(145, 'تلي سليز', '2026-05-24 10:41:33', '2026-05-24 10:41:33'),
(146, 'اخصائى اول دعم فنى', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(147, 'اخصائي ادخال بيانات', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(148, 'مندوب مراسلات', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(149, 'منسق أول RMA', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(150, 'مدير  عام خدمة ما بعد البيع', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(151, 'مشرف قسم الدعم الفني', '2026-05-24 10:41:34', '2026-05-24 10:41:34'),
(152, 'قائد فريق دعم فنى', '2026-05-24 10:41:35', '2026-05-24 10:41:35'),
(153, 'قائد فريق RMA', '2026-05-24 10:41:35', '2026-05-24 10:41:35'),
(154, 'اخصائي دعم فنى', '2026-05-24 10:41:35', '2026-05-24 10:41:35'),
(155, 'اخصائي خدمة عملاء (ما بعد البيع)', '2026-05-24 10:41:36', '2026-05-24 10:41:36'),
(156, 'مسئول اول محتوى موقع الكترونى ( كذا)', '2026-05-24 10:41:37', '2026-05-24 10:41:37'),
(157, 'منسق  RMA', '2026-05-24 10:41:40', '2026-05-24 10:41:40'),
(158, 'منسق منتجات اكسسوارات', '2026-05-24 10:41:43', '2026-05-24 10:41:43'),
(159, 'اخصائي مبيعات داخليه', '2026-05-24 10:41:43', '2026-05-24 10:41:43'),
(160, 'نائب مدير فرع', '2026-05-24 10:41:43', '2026-05-24 10:41:43'),
(161, 'مدير فرع', '2026-05-24 10:41:46', '2026-05-24 10:41:46'),
(162, 'اخصائى اول مبيعات داخلية', '2026-05-24 10:41:46', '2026-05-24 10:41:46'),
(163, 'مدير اول فرع', '2026-05-24 10:41:50', '2026-05-24 10:41:50'),
(164, 'مدير الصيانه', '2026-05-24 10:41:53', '2026-05-24 10:41:53'),
(165, 'مساعد مدير منتجات الاكسسوارات', '2026-05-24 10:41:53', '2026-05-24 10:41:53'),
(166, 'مدير منطقة فروع', '2026-05-24 10:42:08', '2026-05-24 10:42:08'),
(167, 'مدير تطوير اعمال', '2026-05-24 10:42:08', '2026-05-24 10:42:08'),
(168, 'مدير مبيعات البيع بالفروع', '2026-05-24 10:42:12', '2026-05-24 10:42:12'),
(169, 'اخصائى مبيعات داخلية ( كذا )', '2026-05-24 10:42:12', '2026-05-24 10:42:12'),
(170, 'عامل مخزن', '2026-05-24 10:42:24', '2026-05-24 10:42:24'),
(171, 'امين مخزن', '2026-05-24 10:42:25', '2026-05-24 10:42:25'),
(172, 'مساعد مدير مخزن', '2026-05-24 10:42:25', '2026-05-24 10:42:25'),
(173, 'غفير مخزن', '2026-05-24 10:42:25', '2026-05-24 10:42:25'),
(174, 'مسئول تسليم', '2026-05-24 10:42:28', '2026-05-24 10:42:28'),
(175, 'سائق 3PL', '2026-05-24 10:42:29', '2026-05-24 10:42:29'),
(176, 'رئيس قطاع لوجيستي', '2026-05-24 10:42:30', '2026-05-24 10:42:30'),
(177, 'اخصائى تحكم مخزون', '2026-05-24 10:42:31', '2026-05-24 10:42:31'),
(178, 'قائد فريق مخزن', '2026-05-24 10:42:31', '2026-05-24 10:42:31'),
(179, 'اخصائى اول تحكم مخزون', '2026-05-24 10:42:32', '2026-05-24 10:42:32'),
(180, 'اخصائى اول مخزن', '2026-05-24 10:42:32', '2026-05-24 10:42:32'),
(181, 'مدير مخزن', '2026-05-24 10:42:33', '2026-05-24 10:42:33'),
(182, 'مدير حركة', '2026-05-24 10:42:33', '2026-05-24 10:42:33'),
(183, 'اخصائى اول ادخال بيانات', '2026-05-24 10:42:35', '2026-05-24 10:42:35'),
(184, 'مشرف ترخيص', '2026-05-24 10:42:35', '2026-05-24 10:42:35'),
(185, 'محاسب', '2026-05-24 10:42:35', '2026-05-24 10:42:35'),
(186, 'مدرب', '2026-05-24 10:42:37', '2026-05-24 10:42:37'),
(187, 'اخصائى مبيعات الخلية', '2026-05-24 10:42:38', '2026-05-24 10:42:38'),
(188, 'تدريب  مالك منتج', '2026-05-24 10:42:41', '2026-05-24 10:42:41'),
(189, 'مدير التسويق الالكترونى', '2026-05-24 10:42:50', '2026-05-24 10:42:50'),
(190, 'اخصائى ادخال بيانات', '2026-05-24 10:42:59', '2026-05-24 10:42:59'),
(191, 'منسق مبيعات داخلية', '2026-05-24 10:43:05', '2026-05-24 10:43:05'),
(192, 'كول سنتر', '2026-05-24 10:43:08', '2026-05-24 10:43:08'),
(193, 'اخصائى اول انظمه برمجيه', '2026-05-24 10:43:09', '2026-05-24 10:43:09'),
(194, 'اخصائي تصميم منتجات', '2026-05-24 10:43:13', '2026-05-24 10:43:13'),
(195, 'منسق تسويق', '2026-05-24 10:43:14', '2026-05-24 10:43:14'),
(196, 'اخصائى مبيعات انظمة برمجيه', '2026-05-24 10:43:15', '2026-05-24 10:43:15'),
(197, 'سائق', '2026-05-24 10:43:15', '2026-05-24 10:43:15'),
(198, 'مدير عام استراد', '2026-05-24 10:43:16', '2026-05-24 10:43:16'),
(199, 'تقنى صيانة', '2026-05-24 10:43:21', '2026-05-24 10:43:21');

-- --------------------------------------------------------

--
-- Table structure for table `job_title_qualification_skill`
--

CREATE TABLE `job_title_qualification_skill` (
  `job_title_id` bigint(20) UNSIGNED NOT NULL,
  `qualification_skill_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `job_title_qualification_skill`
--

INSERT INTO `job_title_qualification_skill` (`job_title_id`, `qualification_skill_id`, `created_at`, `updated_at`) VALUES
(37, 2, '2026-05-24 11:37:03', '2026-05-24 11:37:03'),
(37, 3, '2026-05-24 11:37:03', '2026-05-24 11:37:03'),
(142, 2, '2026-05-24 10:49:26', '2026-05-24 10:49:26'),
(142, 3, '2026-05-24 10:49:26', '2026-05-24 10:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `lms_resources`
--

CREATE TABLE `lms_resources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `type` enum('article','link','file') NOT NULL,
  `content` longtext DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `file_path` varchar(191) DEFAULT NULL,
  `file_name` varchar(191) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `qualification_skill_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `lms_resources`
--

INSERT INTO `lms_resources` (`id`, `title`, `type`, `content`, `url`, `file_path`, `file_name`, `file_size`, `qualification_skill_id`, `created_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'ezz', 'link', NULL, 'https://2b.com.eg', NULL, NULL, NULL, NULL, 1, '2026-05-23 16:01:01', '2026-05-23 16:01:01'),
(2, 'ezz 2', 'file', NULL, NULL, 'lms-resources/CnadFsVxVwHlz0jrYZdLsyeUbnoD8TUyi2TCMQpM.pdf', 'Lab7_POM_Final.pdf', 755960, 1, 1, '2026-05-23 16:01:54', '2026-05-23 18:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `mainlogs`
--

CREATE TABLE `mainlogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_email` varchar(191) DEFAULT NULL,
  `url` varchar(191) DEFAULT NULL,
  `method` varchar(191) DEFAULT NULL,
  `headers` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_12_10_115026_create_admins_table', 1),
(7, '2024_12_11_214603_create_abouts_table', 1),
(8, '2024_12_12_142841_create_articles_table', 1),
(9, '2024_12_14_125236_create_testimonials_table', 1),
(10, '2024_12_15_122229_create_settings_table', 1),
(11, '2024_12_17_122259_create_contacts_table', 1),
(12, '2025_04_15_125500_create_permission_tables', 1),
(13, '2025_04_15_132756_add_column_to_permissions_table', 1),
(14, '2025_04_15_160011_create_mainlogs_table', 1),
(15, '2025_07_08_225839_create_admin_login_logs_table', 1),
(16, '2025_07_29_151841_create_categories_table', 1),
(17, '2025_07_30_134253_create_courses_table', 1),
(18, '2025_07_30_172854_create_course_sections_table', 1),
(19, '2025_08_06_124631_create_course_resources_table', 1),
(20, '2025_08_06_144433_create_instructors_table', 1),
(21, '2025_08_06_152253_create_courses_instructors_table', 1),
(22, '2025_08_06_171439_create_course_lectures_table', 1),
(23, '2025_08_06_203250_create_course_exams_table', 1),
(24, '2025_08_06_203346_create_course_exam_questions_table', 1),
(25, '2025_08_06_203355_create_course_exam_question_answers_table', 1),
(26, '2025_08_16_155543_create_users_courses_table', 1),
(27, '2025_08_18_131125_create_course_ratings_table', 1),
(28, '2025_08_18_163500_create_course_lecture_questions_table', 1),
(29, '2025_08_18_210751_create_user_exams_table', 1),
(30, '2025_08_18_210801_create_user_exam_answers_table', 1),
(31, '2025_08_25_143546_create_user_lecture_progress_table', 1),
(32, '2025_09_02_154239_create_course_assignments_table', 1),
(33, '2025_09_02_160746_create_user_course_assignments_table', 1),
(34, '2025_10_05_164409_add_for_public_to_courses_table', 1),
(35, '2025_10_05_173133_add_course_type_to_courses_table', 1),
(36, '2025_10_05_175030_create_course_sessions_table', 1),
(37, '2025_10_05_214835_add_group_id_to_users_courses_table', 1),
(38, '2025_11_03_152833_add_material_to_courses_table', 1),
(39, '2025_11_10_191035_create_forms_table', 1),
(40, '2025_11_10_191252_create_form_questions_table', 1),
(41, '2025_11_10_191307_create_form_question_answers_table', 1),
(42, '2025_11_10_191417_create_user_forms_table', 1),
(43, '2025_11_10_193109_create_user_form_answers_table', 1),
(44, '2025_11_11_125950_add_start_at_to_user_forms_table', 1),
(45, '2025_12_22_161659_create_public_notifications_table', 1),
(46, '2025_12_28_150943_create_jobs_table', 1),
(47, '2026_01_08_172750_add_notification_text_to_courses_table', 1),
(48, '2026_01_11_172349_create_public_notification_users_table', 1),
(49, '2026_01_11_172428_add_for_public_to_public_notifications_table', 1),
(50, '2026_01_31_125833_create_evaluation_categories_table', 1),
(51, '2026_01_31_125853_create_evaluations_table', 1),
(52, '2026_02_02_214753_add_is_evaluate_to_courses_table', 1),
(53, '2026_02_03_124502_create_user_course_evaluations_table', 1),
(54, '2026_02_04_113808_create_attendances_table', 1),
(55, '2026_02_10_204603_add_some_cols_to_forms_table', 1),
(56, '2026_02_22_150510_create_attendance_logs_table', 1),
(57, '2026_02_22_155525_add_type_in_attendances_table', 1),
(58, '2026_04_01_135626_add_allow_attendances_to_courses_table', 1),
(59, '2026_05_09_110057_add_feedback_to_user_course_assignments_table', 1),
(60, '2026_05_14_000001_localize_core_lms_content_tables', 1),
(61, '2026_05_14_000002_localize_course_structure_tables', 1),
(62, '2026_05_14_000003_localize_assessment_tables', 1),
(63, '2026_05_14_000004_localize_cms_tables', 1),
(64, '2026_05_14_000005_add_duration_to_course_exams_table', 1),
(65, '2026_05_19_140000_add_job_title_to_users_table', 1),
(66, '2026_05_19_150000_create_qualification_skills_table', 1),
(67, '2026_05_19_150100_create_course_qualification_skills_table', 1),
(68, '2026_05_19_150200_seed_qualification_skills_permissions', 1),
(69, '2026_05_20_100000_create_job_titles_table', 1),
(70, '2026_05_20_100100_create_job_title_qualification_skill_table', 1),
(71, '2026_05_20_110000_create_admin_messages_table', 1),
(72, '2026_05_20_110100_create_admin_message_recipients_table', 1),
(73, '2026_05_20_120000_add_learner_type_to_users_table', 1),
(74, '2026_05_20_120000_create_lms_resources_table', 1),
(75, '2026_05_20_130000_create_audit_logs_table', 1),
(76, '2026_05_20_130000_drop_translation_backup_columns', 1),
(77, '2026_05_20_131000_make_courses_image_nullable', 1),
(78, '2026_05_21_100000_add_performance_indexes', 1),
(79, '2026_05_21_120000_extend_course_lectures_for_modules', 1),
(80, '2026_05_21_130000_add_file_name_to_course_lectures', 1),
(81, '2026_05_21_140000_add_max_learners_to_courses', 1),
(82, '2026_05_23_000001_add_due_date_to_course_assignments_table', 2),
(83, '2026_05_23_100000_extend_course_assignments_for_rich_questions', 3),
(84, '2026_05_23_100100_create_course_assignment_questions_table', 3),
(85, '2026_05_23_100200_create_course_assignment_cohorts_table', 3),
(86, '2026_05_23_100300_extend_user_course_assignments_for_rich_scoring', 3),
(87, '2026_05_23_100400_create_user_course_assignment_answers_table', 3),
(88, '2026_05_23_120000_extend_course_exams_for_rich_quizzes', 4),
(89, '2026_05_23_120100_extend_course_exam_questions_for_rich_types', 4),
(90, '2026_05_23_120200_create_course_exam_cohorts_table', 4),
(91, '2026_05_23_120300_extend_user_exams_for_rich_scoring', 4),
(92, '2026_05_23_120400_extend_user_exam_answers_for_rich_grading', 4),
(93, '2026_05_23_180000_create_report_export_logs_table', 5),
(94, '2026_05_23_210000_add_admin_user_columns_to_users_table', 6),
(95, '2026_05_23_220000_add_admin_status_columns_to_instructors_and_admins', 7),
(96, '2026_05_23_230000_add_actor_role_to_audit_logs_table', 8),
(97, '2026_05_23_240000_add_admin_columns_to_roles_table', 9),
(98, '2026_05_23_240100_seed_admin_view_permissions_and_system_roles', 9),
(99, '2026_05_23_250000_add_view_controllers_permission', 10),
(100, '2026_05_24_100000_create_certificate_templates_table', 11),
(101, '2026_05_24_110000_add_auto_fields_to_certificate_templates', 12),
(102, '2026_05_24_120000_add_session_id_to_attendances_table', 13),
(103, '2026_05_24_140000_add_cohort_metadata_to_course_sections_table', 14),
(104, '2026_05_25_120000_add_image_to_users_and_admins_and_seed_learner_role', 15),
(105, '2026_05_25_120100_drop_job_title_from_user_tables', 15),
(106, '2026_05_25_140000_add_job_title_id_to_users_table', 15),
(107, '2026_05_25_160000_add_mobile_attendance_passcode_and_enrolment_close', 15),
(108, '2026_05_31_000000_make_course_ratings_comment_nullable', 16);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Admin', 1),
(3, 'App\\Models\\Admin', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_name` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `table_name`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'users', 'users-index', 'admin', '2026-05-23 15:14:58', '2025-07-06 15:53:02'),
(2, 'users', 'users-show', 'admin', '2026-05-23 15:14:58', '2025-07-06 15:53:02'),
(3, 'abouts', 'abouts-edit', 'admin', '2026-05-23 15:14:58', '2025-07-06 15:53:02'),
(4, 'blogs', 'blogs-index', 'admin', '2026-05-23 15:14:58', '2025-07-06 15:53:02'),
(5, 'blogs', 'blogs-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(6, 'blogs', 'blogs-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(7, 'blogs', 'blogs-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(8, 'facts', 'facts-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(9, 'facts', 'facts-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(10, 'facts', 'facts-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(11, 'facts', 'facts-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(12, 'partners', 'partners-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(13, 'partners', 'partners-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(14, 'partners', 'partners-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(15, 'partners', 'partners-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(16, 'testimonials', 'testimonials-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(17, 'testimonials', 'testimonials-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(18, 'testimonials', 'testimonials-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(19, 'testimonials', 'testimonials-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(20, 'careers', 'careers-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(21, 'careers', 'careers-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(22, 'careers', 'careers-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(23, 'careers', 'careers-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(24, 'contact_form', 'contact_form-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(25, 'contact_form', 'contact_form-show', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(26, 'contact_form', 'contact_form-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(27, 'settings', 'settings-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(28, 'seo', 'seo-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(29, 'admins', 'admins-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(30, 'admins', 'admins-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(31, 'admins', 'admins-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(32, 'admins', 'admins-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(33, 'roles', 'roles-index', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(34, 'roles', 'roles-create', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(35, 'roles', 'roles-edit', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(36, 'roles', 'roles-delete', 'admin', '2025-07-06 15:53:02', '2025-07-06 15:53:02'),
(37, 'categories', 'categories-index', 'admin', '2025-07-29 10:35:37', '2025-07-29 10:35:37'),
(38, 'categories', 'categories-create', 'admin', '2025-07-29 10:35:37', '2025-07-29 10:35:37'),
(39, 'categories', 'categories-edit', 'admin', '2025-07-29 10:35:37', '2025-07-29 10:35:37'),
(40, 'categories', 'categories-delete', 'admin', '2025-07-29 10:35:37', '2025-07-29 10:35:37'),
(41, 'courses', 'courses-index', 'admin', '2025-07-30 09:02:29', '2025-07-30 09:02:29'),
(42, 'courses', 'courses-create', 'admin', '2025-07-30 09:02:29', '2025-07-30 09:02:29'),
(43, 'courses', 'courses-edit', 'admin', '2025-07-30 09:02:29', '2025-07-30 09:02:29'),
(44, 'courses', 'courses-delete', 'admin', '2025-07-30 09:02:29', '2025-07-30 09:02:29'),
(45, 'courses-sections', 'courses-sections-index', 'admin', '2025-07-30 12:03:36', '2025-07-30 12:03:36'),
(46, 'courses-resources', 'courses-resources-index', 'admin', '2025-08-06 07:58:39', '2025-08-06 07:58:39'),
(47, 'instructors', 'instructors-index', 'admin', '2025-08-06 09:07:36', '2025-08-06 09:07:36'),
(48, 'instructors', 'instructors-create', 'admin', '2025-08-06 09:07:36', '2025-08-06 09:07:36'),
(49, 'instructors', 'instructors-edit', 'admin', '2025-08-06 09:07:36', '2025-08-06 09:07:36'),
(50, 'instructors', 'instructors-delete', 'admin', '2025-08-06 09:07:36', '2025-08-06 09:07:36'),
(51, 'courses-lectures', 'courses-lectures-index', 'admin', '2025-08-06 11:43:12', '2025-08-06 11:43:12'),
(52, 'courses-lectures', 'courses-lectures-create', 'admin', '2025-08-06 11:43:12', '2025-08-06 11:43:12'),
(53, 'courses-lectures', 'courses-lectures-edit', 'admin', '2025-08-06 11:43:12', '2025-08-06 11:43:12'),
(54, 'courses-lectures', 'courses-lectures-delete', 'admin', '2025-08-06 11:43:12', '2025-08-06 11:43:12'),
(55, 'courses-exams', 'courses-exams-index', 'admin', '2025-08-06 14:46:51', '2025-08-06 14:46:51'),
(56, 'courses-exams', 'courses-exams-create', 'admin', '2025-08-06 14:46:51', '2025-08-06 14:46:51'),
(57, 'courses-exams', 'courses-exams-edit', 'admin', '2025-08-06 14:46:51', '2025-08-06 14:46:51'),
(58, 'courses-exams', 'courses-exams-delete', 'admin', '2025-08-06 14:46:51', '2025-08-06 14:46:51'),
(59, 'users-courses', 'users-courses-index', 'admin', '2025-08-16 10:30:11', '2025-08-16 10:30:11'),
(60, 'users-courses', 'users-courses-create', 'admin', '2025-08-16 10:30:11', '2025-08-16 10:30:11'),
(61, 'users-courses', 'users-courses-edit', 'admin', '2025-08-16 10:30:11', '2025-08-16 10:30:11'),
(62, 'users-courses', 'users-courses-delete', 'admin', '2025-08-16 10:30:11', '2025-08-16 10:30:11'),
(63, 'users-courses-progress', 'users-courses-progress-index', 'admin', '2025-09-01 08:41:15', '2025-09-01 08:41:15'),
(64, 'users-courses-rating', 'users-courses-rating-index', 'admin', '2025-09-01 11:23:02', '2025-09-01 11:23:02'),
(65, 'users-courses-rating', 'users-courses-rating-delete', 'admin', '2025-09-01 11:23:02', '2025-09-01 11:23:02'),
(66, 'users-lectures-questions', 'users-lectures-questions-index', 'admin', '2025-09-01 11:23:02', '2025-09-01 11:23:02'),
(67, 'users-lectures-questions', 'users-lectures-questions-edit', 'admin', '2025-09-01 11:23:02', '2025-09-01 11:23:02'),
(68, 'users-lectures-questions', 'users-lectures-questions-delete', 'admin', '2025-09-01 11:23:02', '2025-09-01 11:23:02'),
(69, 'courses-assignments', 'courses-assignments-index', 'admin', '2025-09-02 09:42:28', '2025-09-02 09:42:28'),
(70, 'users-courses-assignments', 'users-courses-assignments-index', 'admin', '2025-09-02 13:26:18', '2025-09-02 13:26:18'),
(71, 'users-courses-assignments', 'users-courses-assignments-delete', 'admin', '2025-09-02 13:26:18', '2025-09-02 13:26:18'),
(72, 'courses-sessions', 'courses-sessions-index', 'admin', '2025-10-05 11:48:50', '2025-10-05 11:48:50'),
(73, 'courses-sessions', 'courses-sessions-create', 'admin', '2025-10-05 11:48:50', '2025-10-05 11:48:50'),
(74, 'courses-sessions', 'courses-sessions-edit', 'admin', '2025-10-05 11:48:50', '2025-10-05 11:48:50'),
(75, 'courses-sessions', 'courses-sessions-delete', 'admin', '2025-10-05 11:48:50', '2025-10-05 11:48:50'),
(76, 'users-courses-offline', 'users-courses-offline-index', 'admin', '2025-10-05 15:45:44', '2025-10-05 15:45:44'),
(77, 'users-courses-offline', 'users-courses-offline-create', 'admin', '2025-10-05 15:45:44', '2025-10-05 15:45:44'),
(78, 'users-courses-offline', 'users-courses-offline-edit', 'admin', '2025-10-05 15:45:44', '2025-10-05 15:45:44'),
(79, 'users-courses-offline', 'users-courses-offline-delete', 'admin', '2025-10-05 15:45:44', '2025-10-05 15:45:44'),
(80, 'videos', 'videos-index', 'admin', '2025-10-26 08:42:23', '2025-10-26 08:42:23'),
(81, 'videos', 'videos-create', 'admin', '2025-10-26 08:42:23', '2025-10-26 08:42:23'),
(82, 'forms', 'forms-index', 'admin', '2025-11-10 15:38:27', '2025-11-10 15:38:27'),
(83, 'forms', 'forms-create', 'admin', '2025-11-10 15:38:27', '2025-11-10 15:38:27'),
(84, 'forms', 'forms-edit', 'admin', '2025-11-10 15:38:27', '2025-11-10 15:38:27'),
(85, 'forms', 'forms-delete', 'admin', '2025-11-10 15:38:27', '2025-11-10 15:38:27'),
(86, 'users-certificates', 'users-certificates-index', 'admin', '2025-11-13 17:01:58', '2025-11-13 17:01:58'),
(87, 'public_notifications', 'public_notifications-index', 'admin', '2025-12-22 12:46:00', '2025-12-22 12:46:00'),
(88, 'public_notifications', 'public_notifications-create', 'admin', '2025-12-22 12:46:00', '2025-12-22 12:46:00'),
(89, 'evaluation-categories', 'evaluation-categories-index', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(90, 'evaluation-categories', 'evaluation-categories-create', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(91, 'evaluation-categories', 'evaluation-categories-edit', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(92, 'evaluation-categories', 'evaluation-categories-delete', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(93, 'evaluations', 'evaluations-index', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(94, 'evaluations', 'evaluations-create', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(95, 'evaluations', 'evaluations-edit', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(96, 'evaluations', 'evaluations-delete', 'admin', '2026-01-31 09:42:02', '2026-01-31 09:42:02'),
(97, 'evaluations-reports', 'evaluations-reports-index', 'admin', '2026-02-03 11:54:54', '2026-02-03 11:54:54'),
(98, 'attendances', 'attendances-index', 'admin', '2026-02-04 08:11:51', '2026-02-04 08:11:51'),
(99, 'qualification-skills', 'qualification-skills-index', 'admin', '2026-05-19 11:00:00', '2026-05-19 11:00:00'),
(100, 'qualification-skills', 'qualification-skills-create', 'admin', '2026-05-19 11:00:00', '2026-05-19 11:00:00'),
(101, 'qualification-skills', 'qualification-skills-edit', 'admin', '2026-05-19 11:00:00', '2026-05-19 11:00:00'),
(102, 'qualification-skills', 'qualification-skills-delete', 'admin', '2026-05-19 11:00:00', '2026-05-19 11:00:00'),
(103, 'Main', 'view-dashboard', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(104, 'Main', 'view-inbox', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(105, 'Learning Operation', 'view-courses', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(106, 'Learning Operation', 'view-assignments', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(107, 'Learning Operation', 'view-quizzes', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(108, 'Learning Operation', 'view-ratings', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(109, 'Learning Operation', 'view-resources', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(110, 'Manage Competency', 'view-job-titles', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(111, 'Manage Competency', 'view-qualifications', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(112, 'Manage Competency', 'view-certificates', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(113, 'Manage Competency', 'view-categories', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(114, 'Manage Competency', 'view-reports', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(115, 'System', 'view-users', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(116, 'System', 'view-platform-config', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(117, 'System', 'view-audit-log', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(118, 'System', 'view-roles', 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(119, 'System', 'view-controllers', 'admin', '2026-05-23 20:15:26', '2026-05-23 20:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Admin', 1, 'admin-api-token', '340c1730cca0cb180fc481eb1fbcd43c2f7ba82350764590be99b222bcae753e', '[\"role:admin\"]', '2026-05-23 18:34:19', '2026-06-22 15:59:36', '2026-05-23 15:59:36', '2026-05-23 18:34:19'),
(2, 'App\\Models\\Admin', 2, 'admin-api-token', '6d6ac5e55e000e9fafefe861276c8dc4bc44c04a728891f747e770fb725b4050', '[\"role:admin\"]', '2026-05-23 19:07:57', '2026-06-22 18:31:28', '2026-05-23 18:31:28', '2026-05-23 19:07:57'),
(3, 'App\\Models\\Admin', 2, 'admin-api-token', '105851c2f124bdde58583712ddea3f51ec3b8a34f9b8fdf7f0cbcd3118045ea6', '[\"role:admin\"]', '2026-05-23 19:51:47', '2026-06-22 19:12:14', '2026-05-23 19:12:14', '2026-05-23 19:51:47'),
(4, 'App\\Models\\Admin', 1, 'admin-api-token', 'c432e4c42e6e7ba10fb36fae4fe6d14bc2bae609cd2f12ab88b2a16b5468c861', '[\"role:admin\"]', '2026-05-23 20:03:08', '2026-06-22 19:52:43', '2026-05-23 19:52:43', '2026-05-23 20:03:08'),
(5, 'App\\Models\\Admin', 3, 'admin-api-token', '2d967bd9e8d3df4629f0d0a8b408d9030e77be2a5ba9267af964b3381c3b68a8', '[\"role:admin\"]', '2026-05-23 20:04:44', '2026-06-22 20:03:19', '2026-05-23 20:03:19', '2026-05-23 20:04:44'),
(6, 'App\\Models\\Admin', 1, 'admin-api-token', '08596a6bcc7d78abcced9b3d4121e465d01a27c21c835ecfaa2c06ce43b88e34', '[\"role:admin\"]', '2026-05-23 20:08:44', '2026-06-22 20:04:51', '2026-05-23 20:04:51', '2026-05-23 20:08:44'),
(7, 'App\\Models\\Admin', 3, 'admin-api-token', '9d10dfa5a82699a9778760e3e03d1e4d6ed8a9b883233bde196f8d3e3547b464', '[\"role:admin\"]', '2026-05-23 20:11:38', '2026-06-22 20:08:51', '2026-05-23 20:08:51', '2026-05-23 20:11:38'),
(8, 'App\\Models\\Admin', 1, 'admin-api-token', '70f6665a988f9b972365b83dbdfe354517cebe9b3d40a6c7619f989ce1b0cfa6', '[\"role:admin\"]', '2026-05-23 20:15:56', '2026-06-22 20:11:44', '2026-05-23 20:11:44', '2026-05-23 20:15:56'),
(10, 'App\\Models\\Admin', 3, 'admin-api-token', '15740747baae2897a73571fbd0e7a00528dd83eda295c2891a92e0adc95a0c14', '[\"role:admin\"]', '2026-05-23 20:16:31', '2026-06-22 20:16:02', '2026-05-23 20:16:02', '2026-05-23 20:16:31'),
(11, 'App\\Models\\Admin', 1, 'admin-api-token', '3a3bf036c2a260d8ac81f7780729750160a715716b2aaff903b3fb216ff93843', '[\"role:admin\"]', '2026-05-23 20:20:34', '2026-06-22 20:16:37', '2026-05-23 20:16:37', '2026-05-23 20:20:34'),
(12, 'App\\Models\\Admin', 3, 'admin-api-token', '51cecd956a467bfa88d43662eb50c0008af0b5baad79e1341ba5ad710c1187ad', '[\"role:admin\"]', '2026-05-23 20:25:09', '2026-06-22 20:20:44', '2026-05-23 20:20:44', '2026-05-23 20:25:09'),
(14, 'App\\Models\\Admin', 1, 'admin-api-token', '7070b26ca9ed5cd5d6dd687aa0f94a1c83d9cc95e0913663fc2193a8f2f3957e', '[\"role:admin\"]', '2026-05-23 20:26:35', '2026-06-22 20:25:14', '2026-05-23 20:25:14', '2026-05-23 20:26:35'),
(15, 'App\\Models\\Admin', 3, 'admin-api-token', 'bdfefff297a72291013b1b95a439e46a2e25a01ef0dfc9f827448df1d45e4818', '[\"role:admin\"]', '2026-05-23 20:27:44', '2026-06-22 20:26:41', '2026-05-23 20:26:41', '2026-05-23 20:27:44'),
(16, 'App\\Models\\Admin', 1, 'admin-api-token', 'fbd1b8509ab8c9d22105185bd6a17795ae3be6a490aaea0c32d9018c772f8f34', '[\"role:admin\"]', '2026-05-23 20:27:53', '2026-06-22 20:27:48', '2026-05-23 20:27:48', '2026-05-23 20:27:53'),
(17, 'App\\Models\\Admin', 3, 'admin-api-token', '2e5fdd5fbfa448631ebf2177dd53c7ac256037843f742589f3f128369312e0d1', '[\"role:admin\"]', '2026-05-31 10:44:01', '2026-06-22 20:27:57', '2026-05-23 20:27:57', '2026-05-31 10:44:01'),
(18, 'App\\Models\\Admin', 1, 'admin-api-token', 'cb760ff2d9e3bf416e65f27af432cb58fa64c5e4fc1730c08acce64afc441109', '[\"role:admin\"]', '2026-05-24 09:36:43', '2026-06-23 09:35:42', '2026-05-24 09:35:42', '2026-05-24 09:36:43'),
(19, 'App\\Models\\User', 25, 'user-api-token', 'ab89b69a619d54d69204412b77350af2d7447983daef913754c8b68f0d89f380', '[\"role:user\"]', NULL, '2026-06-23 10:08:42', '2026-05-24 10:08:42', '2026-05-24 10:08:42'),
(20, 'App\\Models\\User', 25, 'user-api-token', '1e49eb130d36f19a1ee7421bb0713907fd2bc01417c9f293c38035409ede5189', '[\"role:user\"]', NULL, '2026-06-23 10:11:32', '2026-05-24 10:11:32', '2026-05-24 10:11:32'),
(21, 'App\\Models\\Admin', 1, 'admin-api-token', 'e5e325cf2d86a92662a95df6ed0a158978334b63371957cb6c9ff5794724efd1', '[\"role:admin\"]', '2026-05-31 10:52:04', '2026-06-30 10:44:07', '2026-05-31 10:44:07', '2026-05-31 10:52:04'),
(22, 'App\\Models\\Admin', 3, 'admin-api-token', '9e65a28e9971303f58a5db2262f1d2de9e068e27f30b544fe37c17d4e33e2146', '[\"role:admin\"]', '2026-05-31 10:54:22', '2026-06-30 10:52:11', '2026-05-31 10:52:11', '2026-05-31 10:54:22'),
(23, 'App\\Models\\Admin', 1, 'admin-api-token', 'a30009b57b2a11f0d45786d88a8730179c0679e0f4908028eb9150b2d5207fc5', '[\"role:admin\"]', '2026-05-31 10:57:02', '2026-06-30 10:54:28', '2026-05-31 10:54:28', '2026-05-31 10:57:02'),
(24, 'App\\Models\\Admin', 3, 'admin-api-token', '49066d9aa2cbe354d55d8d24779e082e1732b72b3afb498d9dbd8a6e1f015b8c', '[\"role:admin\"]', '2026-05-31 10:59:37', '2026-06-30 10:57:09', '2026-05-31 10:57:09', '2026-05-31 10:59:37'),
(25, 'App\\Models\\Admin', 1, 'admin-api-token', 'a509d813d59473eb096aa1e48c7025a6f3500f1fd9361e2220e8cd0e85ee376c', '[\"role:admin\"]', '2026-05-31 10:59:56', '2026-06-30 10:59:45', '2026-05-31 10:59:45', '2026-05-31 10:59:56'),
(26, 'App\\Models\\Admin', 3, 'admin-api-token', '500ce0cc7d59c8a441b7877d484a73087c63fb7672ec5f31f749dce960e013e5', '[\"role:admin\"]', '2026-05-31 11:57:53', '2026-06-30 11:02:52', '2026-05-31 11:02:52', '2026-05-31 11:57:53'),
(27, 'App\\Models\\Admin', 1, 'admin-api-token', '00d998de8e574d42e6a3363ff14daf8d7d17b7b6a7a3bf9a871138455066241b', '[\"role:admin\"]', '2026-05-31 12:25:50', '2026-06-30 11:57:54', '2026-05-31 11:57:54', '2026-05-31 12:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `public_notifications`
--

CREATE TABLE `public_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`body`)),
  `for_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `public_notifications`
--

INSERT INTO `public_notifications` (`id`, `title`, `body`, `for_public`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\":\"Tempora numquam libe\",\"en\":\"Tempora numquam libe\"}', '{\"ar\":\"Reprehenderit alias\",\"en\":\"Reprehenderit alias\"}', 0, '2025-12-22 12:49:18', '2025-12-22 12:49:18'),
(2, '{\"ar\":\"sad\",\"en\":\"sad\"}', '{\"ar\":\"sad\",\"en\":\"sad\"}', 0, '2026-01-11 13:47:50', '2026-01-11 13:47:50'),
(3, '{\"ar\":\"تيست\",\"en\":\"Test\"}', '{\"ar\":\"تيست  من الاشعارات العامه\",\"en\":\"Test from the public notifications\"}', 0, '2026-01-11 13:54:25', '2026-01-11 13:54:25'),
(4, '{\"ar\":\"test\",\"en\":\"test\"}', '{\"ar\":\"test\",\"en\":\"test\"}', 0, '2026-02-18 12:34:32', '2026-02-18 12:34:32'),
(5, '{\"ar\":\"test\",\"en\":\"test\"}', '{\"ar\":\"test\",\"en\":\"test\"}', 0, '2026-02-18 12:34:56', '2026-02-18 12:34:56'),
(6, '{\"ar\":\"ezz\",\"en\":\"ezz\"}', '{\"ar\":\"ezzzzzzzzzzzzzzzzzzz\",\"en\":\"ezzzzzzzzzzzzzzzzzzz\"}', 1, '2026-05-23 19:47:19', '2026-05-23 19:47:19'),
(7, '{\"ar\":\"asdasasdasdasd\",\"en\":\"asdasasdasdasd\"}', '{\"ar\":\"adsadasdasdas\",\"en\":\"adsadasdasdas\"}', 0, '2026-05-23 19:48:16', '2026-05-23 19:48:16'),
(8, '{\"ar\":\"ك\",\"en\":\"ك\"}', '{\"ar\":\"ك\",\"en\":\"ك\"}', 0, '2026-05-24 09:42:17', '2026-05-24 09:42:17');

-- --------------------------------------------------------

--
-- Table structure for table `public_notification_users`
--

CREATE TABLE `public_notification_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_notification_id` bigint(20) UNSIGNED NOT NULL,
  `user_code` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `public_notification_users`
--

INSERT INTO `public_notification_users` (`id`, `public_notification_id`, `user_code`, `created_at`, `updated_at`) VALUES
(1, 2, '2297', '2026-01-11 13:47:51', '2026-01-11 13:47:51'),
(2, 2, '23C2', '2026-01-11 13:47:51', '2026-01-11 13:47:51'),
(3, 3, '2531', '2026-01-11 13:54:25', '2026-01-11 13:54:25'),
(4, 8, '1610', '2026-05-24 09:42:17', '2026-05-24 09:42:17');

-- --------------------------------------------------------

--
-- Table structure for table `qualification_skills`
--

CREATE TABLE `qualification_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `qualification_skills`
--

INSERT INTO `qualification_skills` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '{\"en\":\"test\",\"ar\":\"تست\"}', '2026-05-23 16:01:17', '2026-05-23 16:01:17'),
(2, '{\"en\":\"word\",\"ar\":\"وورد\"}', '2026-05-23 19:30:31', '2026-05-23 19:30:31'),
(3, '{\"en\":\"excel\",\"ar\":\"اكسل\"}', '2026-05-23 19:37:22', '2026-05-23 19:37:22'),
(4, '{\"en\":\"powerpoint\",\"ar\":\"باوربوينت\"}', '2026-05-23 19:43:10', '2026-05-23 19:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `report_export_logs`
--

CREATE TABLE `report_export_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_type` varchar(64) NOT NULL,
  `format` varchar(8) NOT NULL,
  `exported_by_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `report_export_logs`
--

INSERT INTO `report_export_logs` (`id`, `report_type`, `format`, `exported_by_admin_id`, `exported_at`, `created_at`, `updated_at`) VALUES
(1, 'certificate-status', 'xlsx', 1, '2026-05-23 18:27:13', '2026-05-23 18:27:13', '2026-05-23 18:27:13'),
(2, 'compliance-by-job-title', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03'),
(3, 'individual-compliance', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03'),
(4, 'attendance', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03'),
(5, 'completion', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03'),
(6, 'scores', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03'),
(7, 'certificate-status', 'csv', 1, '2026-05-23 18:28:03', '2026-05-23 18:28:03', '2026-05-23 18:28:03');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `name_en` varchar(191) DEFAULT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `description_en` varchar(500) DEFAULT NULL,
  `description_ar` varchar(500) DEFAULT NULL,
  `color` varchar(20) DEFAULT 'teal',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `name_en`, `name_ar`, `description_en`, `description_ar`, `color`, `is_system`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superAdmin', 'Super Admin', 'مدير عام', 'Full access to all platform features and settings.', 'وصول كامل إلى جميع ميزات المنصة وإعداداتها.', 'teal', 1, 'admin', '2025-07-06 15:53:02', '2026-05-23 19:51:56'),
(2, 'admin', 'Admin', 'مدير', 'Manages courses, users, and compliance. Cannot access Roles or Audit Log.', 'يدير الدورات والمستخدمين والامتثال. لا يمكنه الوصول إلى الأدوار أو سجل التدقيق.', 'blue', 1, 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(3, 'instructor', 'Instructor', 'مُدرّب', 'Delivers courses, grades assignments, manages cohorts.', 'يقدم الدورات ويقيّم الواجبات ويدير المجموعات.', 'green', 1, 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(4, 'reports-viewer', 'Reports Viewer', 'مشاهد التقارير', 'Read-only access to reports and compliance data.', 'وصول للقراءة فقط إلى التقارير وبيانات الامتثال.', 'orange', 1, 'admin', '2026-05-23 19:51:56', '2026-05-23 19:51:56'),
(6, 'ezz', 'ezz', 'عزوز', 'ezz', 'عزوز', 'blue', 0, 'admin', '2026-05-23 20:05:50', '2026-05-23 20:05:50'),
(7, 'learner', 'Learner', 'متدرّب', 'A learner enrolled in courses across the academy.', 'متدرّب مسجّل في دورات الأكاديمية.', 'teal', 1, 'admin', '2026-05-31 09:30:51', '2026-05-31 09:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(103, 2),
(103, 3),
(103, 4),
(103, 6),
(104, 1),
(104, 2),
(104, 3),
(104, 6),
(105, 1),
(105, 2),
(105, 3),
(105, 6),
(106, 1),
(106, 2),
(106, 3),
(106, 6),
(107, 1),
(107, 2),
(107, 3),
(107, 6),
(108, 1),
(108, 2),
(108, 3),
(108, 6),
(109, 1),
(109, 2),
(109, 3),
(109, 6),
(110, 1),
(110, 2),
(110, 6),
(111, 1),
(111, 2),
(111, 4),
(111, 6),
(112, 1),
(112, 2),
(112, 4),
(112, 6),
(113, 1),
(113, 2),
(113, 6),
(114, 1),
(114, 2),
(114, 4),
(114, 6),
(115, 1),
(115, 2),
(115, 6),
(116, 1),
(116, 2),
(116, 6),
(117, 1),
(117, 6),
(118, 1),
(118, 6),
(119, 1),
(119, 2);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'text',
  `label` varchar(191) NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `module` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `type`, `label`, `key`, `value`, `module`, `created_at`, `updated_at`) VALUES
(23, 'file', 'لوجو الهيدر', 'header_logo', 'Setting/QruQxuS5SfEsRsMkHW0Q97a620276acda9aaef889ccdeb423e4d.png', 'home', '2025-08-11 18:34:53', '2026-05-23 19:43:51'),
(24, 'file', 'صورة البانر', 'banner_background', 'Setting/oERVJ0gZg283yMxtDNzUa2d6f727c5eaa7c64c63386a44a8f00b.jpg', 'home', '2025-09-07 14:55:02', '2025-09-07 14:59:12'),
(25, 'textarea', 'وصف البانر', 'banner_description', '<h5 class=\"text-main-600 mb-0\">ارتقِ بمستوى تعلمك</h5>\r\n<h1 class=\"display2 mb-24 wow bounceInLeft\">تعلم,&nbsp;<span class=\"text-main-two-600 wow bounceInRight\" data-wow-duration=\"2s\" data-wow-delay=\".5s\">تنمو,</span>&nbsp;<span class=\"text-main-three-600 wow bounceInLeft\" data-wow-duration=\"1s\" data-wow-delay=\".5s\">حقق</span>&nbsp;وانجح</h1>\r\n<p>أهلاً بكم في توبي حيث لا حدود للتعلم. سواءً كنت طالبًا، أو محترفًا، أو متعلمًا مدى الحياة..</p>', 'home', '2025-08-11 18:34:53', '2026-02-19 10:29:16'),
(26, 'file', 'الشهادة', 'certificate', 'certificate-templates/Z4TLLaqjjqN4YWhqBn9G3ffc2ad4aca66abaf760490fa5ff5389.jpg', 'home', '2025-08-11 18:34:53', '2026-05-24 08:41:15'),
(27, 'textarea', 'محتوي الشهادة', 'certificate_content', '<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"200\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">تعلم من خبراء الصناعة</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"400\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">تعلم في أي وقت وفي أي مكان</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item border-bottom border-neutral-50 border-dashed border-0 mb-28 pb-28 aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"600\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">مصادر مجانية</h5>\r\n</div>\r\n<p class=\"text-neutral-700 text-line-2\">&nbsp;</p>\r\n</div>\r\n<div class=\"certificate-two-item animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"800\">\r\n<div class=\"flex-align gap-20 mb-12\">\r\n<h5 class=\"mb-0\">التعلم القائم على المهارات</h5>\r\n</div>\r\n</div>', 'home', '2025-08-11 18:34:53', '2026-02-19 10:29:16'),
(28, 'file', 'لوجو الفوتر', 'footer_logo', 'Setting/Jt7f3bXyBe8T2ZZZwF1Me04620b79ad80f7e798498de925e54ab.png', 'home', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(29, 'textarea', 'لماذا نحن', 'why_us', '<div class=\"mb-40\">\r\n<div class=\"flex-align d-inline-flex gap-8 mb-16 wow bounceInDown\">\r\n<h5 class=\"text-main-600 mb-0\">لماذا نحن</h5>\r\n</div>\r\n<h2 class=\"mb-24 wow bounceIn\">أكثر من 16 عامًا في التعلم عن بعد لتنمية المهارات</h2>\r\n<p class=\"text-neutral-500 text-line-2 wow bounceInUp\">نحن شغوفون بتغيير حياة الناس من خلال التعليم. تأسست مؤسستنا برؤية تهدف إلى جعل التعلم في متناول الجميع، ونؤمن بقدرة المعرفة على فتح الفرص ورسم ملامح المستقبل..</p>\r\n</div>\r\n<div class=\"grid-cols-2\">\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"600\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon1.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">تدريب ممتاز</h6>\r\n<p class=\"text-neutral-500 text-line-2\">من خلال دوراتنا التدريبية المنسقة والمحتوى التفاعلي</p>\r\n</div>\r\n</div>\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"800\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon2.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">عروض الدورات التدريبية</h6>\r\n<p class=\"text-neutral-500 text-line-2\">مسارات التعلم الشخصية، نحن نمكن المتعلمين من اكتساب</p>\r\n</div>\r\n</div>\r\n<div class=\"flex-align align-items-start gap-20 animation-item aos-init aos-animate\" data-aos=\"fade-up\" data-aos-duration=\"1000\"><span class=\"flex-shrink-0 w-60 h-60 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md\"><img class=\"animate__swing\" src=\"assets/images/icons/choose-us-icon3.png\" alt=\"\" /></span>\r\n<div class=\"flex-grow-1\">\r\n<h6 class=\"text-neutral-800 text-xl fw-medium mb-8\">التعلم المبتكر</h6>\r\n<p class=\"text-neutral-500 text-line-2\">اندماج في التعلم المبتكر</p>\r\n</div>\r\n</div>\r\n</div>', 'about', '2025-08-11 18:34:53', '2026-02-19 10:29:16'),
(30, 'text', 'العنوان الأول', 'address1', 'السراج مول - مدينة مصر - القاهرة - مصر', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(31, 'url', 'رابط الخريطة للعنوان الأول', 'address_map1', 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(32, 'text', 'العنوان الثاني', 'address2', 'السراج مول - مدينة مصر - القاهرة - مصر', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(33, 'url', 'رابط الخريطة للعنوان الثاني', 'address_map2', 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(34, 'text', 'العنوان الثالث', 'address3', 'السراج مول - مدينة مصر - القاهرة - مصر', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(35, 'url', 'رابط الخريطة للعنوان الثالث', 'address_map3', 'https://www.google.com/maps/place/2B+Egypt+-+Head+Office/@30.0506816,31.3494222,15.75z', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(36, 'number', 'رقم الهاتف 1', 'phone1', '01111111111111', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(37, 'number', 'رقم الهاتف 2', 'phone2', '0252554645', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(38, 'number', 'رقم الواتساب', 'whatsapp', '015256148555', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(39, 'text', 'البريد الإلكتروني 1', 'email1', '2b@info.com', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(40, 'text', 'البريد الإلكتروني 2', 'email2', '2b@sales.com', 'contact', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(41, 'url', 'رابط الفيسبوك', 'facebook', 'https://www.facebook.com/', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(42, 'url', 'رابط تويتر', 'twitter', 'https://x.com/?lang=en&mx=2', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(43, 'url', 'رابط اليوتيوب', 'youtube', 'https://www.youtube.com/', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(44, 'url', 'رابط الأنستغرام', 'instagram', 'https://www.instagram.com/', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(45, 'url', 'رابط لينكدان', 'linkedin', 'https://www.linkedin.com/', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(46, 'url', 'رابط سناب شات', 'snapchat', 'https://www.linkedin.com/', 'social', '2025-08-11 18:34:53', '2025-08-12 07:33:23'),
(249, 'number', 'عدد الساعات في السنة', 'yearly_hours', '60', 'settings', '2026-02-19 10:28:58', '2026-02-19 10:29:17'),
(250, 'text', 'Platform Name', 'platform_name', 'NAS LMS', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(251, 'text', 'Default Language', 'default_language', 'en', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(252, 'number', 'Default Cohort Size', 'default_cohort_size', '30', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(253, 'boolean', 'Course Ratings', 'course_ratings_enabled', '1', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(254, 'number', 'Abnormal Rating Threshold', 'abnormal_rating_threshold', '30', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(255, 'text', 'Certificate Awarded Based On', 'certificate_award_basis', 'attendance', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(256, 'number', 'Min Passing Score (%)', 'min_passing_score', '30', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(257, 'textarea', 'About — Description', 'about_description', NULL, 'platform', '2026-05-23 15:59:12', '2026-05-31 10:40:53'),
(258, 'textarea', 'About — Our Values', 'about_values', NULL, 'platform', '2026-05-23 15:59:12', '2026-05-31 10:40:53'),
(259, 'textarea', 'About — Our Mission', 'about_mission', NULL, 'platform', '2026-05-23 15:59:12', '2026-05-31 10:40:53'),
(260, 'textarea', 'About — Our Vision', 'about_vision', NULL, 'platform', '2026-05-23 15:59:12', '2026-05-31 10:40:53'),
(261, 'file', 'About — Image', 'about_image', '', 'platform', '2026-05-23 15:59:12', '2026-05-23 15:59:12'),
(262, 'number', 'Mobile · Academy — courses per page', 'academy_per_page', '15', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(263, 'number', 'Mobile · Academy — search minimum characters', 'academy_search_min_chars', '2', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(264, 'number', 'Mobile · Academy — qualification overflow threshold (show \"+N\")', 'academy_qualification_overflow_threshold', '1', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(265, 'number', 'Mobile · Academy — deadline soft warning days (orange)', 'academy_deadline_warning_days', '7', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(266, 'number', 'Mobile · Academy — deadline critical warning days (red)', 'academy_deadline_critical_days', '2', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(267, 'number', 'Mobile · Academy — default enrolment-close offset before cohort start (days)', 'academy_default_close_offset_days', '5', 'mobile_academy', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(268, 'number', 'Mobile · My Learning — active courses preview count', 'my_learning_active_preview_count', '3', 'mobile_my_learning', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(269, 'number', 'Mobile · My Learning — qualifications preview count', 'my_learning_qualifications_preview_count', '4', 'mobile_my_learning', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(270, 'number', 'Mobile · My Learning — certificates preview count', 'my_learning_certificates_preview_count', '2', 'mobile_my_learning', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(271, 'number', 'Mobile · My Learning — active courses per page (paginated view)', 'my_learning_active_per_page', '15', 'mobile_my_learning', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(272, 'number', 'Mobile · My Learning — certificates per page (paginated view)', 'my_learning_certificates_per_page', '15', 'mobile_my_learning', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(273, 'number', 'Mobile · Attendance — passcode length (digits)', 'attendance_passcode_length', '5', 'mobile_attendance', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(274, 'number', 'Mobile · Attendance — default validity window (minutes)', 'attendance_window_minutes', '30', 'mobile_attendance', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(275, 'number', 'Mobile · Attendance — session \"starts soon\" buffer (minutes before time_from)', 'attendance_session_open_buffer_minutes', '15', 'mobile_attendance', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(276, 'number', 'Mobile · Attendance — session \"still open\" buffer (minutes after time_to)', 'attendance_session_grace_minutes', '15', 'mobile_attendance', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(277, 'number', 'Mobile · Rating — minimum value', 'rating_min_value', '1', 'mobile_rating', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(278, 'number', 'Mobile · Rating — maximum value', 'rating_max_value', '5', 'mobile_rating', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(279, 'number', 'Mobile · Rating — comment required when rating ≤ this value', 'rating_comment_required_at_or_below', '3', 'mobile_rating', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(280, 'number', 'Mobile · Rating — comment max length (chars)', 'rating_comment_max_length', '2000', 'mobile_rating', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(281, 'string', 'Mobile · Security — shared bearer token (HR integration & mobile clients)', 'mobile_shared_bearer_token', '0ecCah0hLg9Ju8921KBViCgYlBEGdSKBZZl4xcTGfCUFh9WVSag2gKuz3zva', 'mobile_security', '2026-05-31 09:57:30', '2026-05-31 09:57:30'),
(282, 'boolean', 'Course Attendance', 'course_attendance_enabled', '0', 'platform', '2026-05-31 10:38:17', '2026-05-31 10:40:53'),
(283, 'number', 'Passcode Reset (seconds)', 'passcode_reset_seconds', '60', 'platform', '2026-05-31 10:38:17', '2026-05-31 10:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `name_en` varchar(191) DEFAULT NULL,
  `name_ar` varchar(191) NOT NULL,
  `description_en` text DEFAULT NULL,
  `description_ar` text NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `description`, `name_en`, `name_ar`, `description_en`, `description_ar`, `image`, `active`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\":\"محمد سعيد\",\"en\":\"Mohamed Said\"}', '{\"ar\":\"<p>\\\"لقد التحقتُ بالعديد من الدورات، وقد فاقت كلٌّ منها توقعاتي. اكتسبتُ مهاراتٍ قيّمة ساعدتني على التقدم في مسيرتي المهنية. أنصح بها بشدة.!\\\"</p>\",\"en\":\"<p>\\\"I have enrolled in many courses, and each of them exceeded my expectations. I picked up valuable skills that helped me advance in my career. Highly recommended!\\\"</p>\"}', 'Mohamed Said', 'محمد سعيد', '<p>\"I have enrolled in many courses, and each of them exceeded my expectations. I picked up valuable skills that helped me advance in my career. Highly recommended!\"</p>', '<p>\"لقد التحقتُ بالعديد من الدورات، وقد فاقت كلٌّ منها توقعاتي. اكتسبتُ مهاراتٍ قيّمة ساعدتني على التقدم في مسيرتي المهنية. أنصح بها بشدة.!\"</p>', 'Testimonial/fQPxwESGYtrnYl0BAMmJa38cc020bb9d6059f76d2a175c2453c3.png', 1, '2025-08-07 10:48:08', '2025-08-07 10:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_id` bigint(20) NOT NULL,
  `name` varchar(191) NOT NULL,
  `name_en` varchar(191) DEFAULT NULL,
  `name_ar` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `machine_code` varchar(191) DEFAULT NULL,
  `department_name` varchar(191) DEFAULT NULL,
  `job_title_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learner_type` varchar(20) DEFAULT 'online',
  `status` varchar(20) DEFAULT 'active',
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `system_id`, `name`, `name_en`, `name_ar`, `image`, `email`, `phone`, `machine_code`, `department_name`, `job_title_id`, `learner_type`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 6, 'محمود رضا طه محمد فولي', NULL, NULL, NULL, 'mahmoud.taha@2b.com.eg', '01063372430', '2393', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(4, 7, 'هدى محمد عاطف عبد الغفار حسن السيد', NULL, NULL, NULL, 'Careers@2b.com.eg', '01200284706', '2412', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(5, 8, 'هانيا محمد اسامه جريده', NULL, NULL, NULL, 'Hanya@2b.com.eg', '1006060511', '23C2', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(6, 9, 'محمد حماده محمد احمد', NULL, NULL, NULL, 'M.hamada@2b.com.eg', '01065242773', '2344', 'اداره البرمجه', 38, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(7, 10, 'محمد عصام محمد محمد', NULL, NULL, NULL, 'Mohammad.essam@2b.com.eg', '01025429399', '2396', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(8, 11, 'ربا مدحت منوفي محمد', NULL, NULL, NULL, 'Hr2@2b.com.eg', '01207689352', '2483', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(9, 12, 'عبده عادل عبدالفتاح عبد الرحمن', NULL, NULL, NULL, 'hr.services@2b.com.eg', '01005823707', '24L5', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(10, 13, 'احمد صلاح توكل السيد محمد المصري', NULL, NULL, NULL, 'ِAhmed.salah@2b.com.eg', '01022008737', '2392', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:34', '2026-05-31 12:01:18'),
(11, 14, 'رضوى محسن السيد ابراهيم عيد', NULL, NULL, NULL, 'Radwa.Mohsen@2b-cs.com', '01090632926', '20G1', 'ادارة توبي السيستم', 39, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(12, 15, 'مروه ابراهيم ابراهيم شنب', NULL, NULL, NULL, 'Marwa.Shanab@2b.com.eg', '0100428469', '2486', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(13, 16, 'احمد محمد نجدي مصيلحي عبده', NULL, NULL, NULL, 'Ahmed.Nagdy@2b.com.eg', '01090065807', '2316', NULL, 40, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(14, 17, 'هويدا عبد النبي سيد علي', NULL, NULL, NULL, 'Houida.Ali@2b.com.eg', '01282574532', '21I4', 'اداره البرمجه', 41, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(15, 18, 'محمد مجدى عبدالرازق ابراهيم', NULL, NULL, NULL, 'Mohamed.Abdelrazek@2b.com.eg', '01033210213', '24I6', NULL, 42, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(16, 19, 'احمد ايهاب مصطفي محمد عبد الغني', NULL, NULL, NULL, 'Ahmed.Ehab@2b.com.eg', '01027452399', '2391', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(17, 20, 'ايثار عبدالرحمن اسماعيل محمد', NULL, NULL, NULL, 'Ethar.Abdelrahman@2b.com.eg', '01023244553', '2425', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(18, 21, 'يوزر تست للموبايل', NULL, NULL, NULL, 'Test@2b.com', '01110017727', '1234', 'ادارة الشئون القانونية و الادارية', 43, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(19, 22, 'مصطفى محى الدين مصطفى احمد', NULL, NULL, NULL, 'Mostafa.Mohy@2b.com.eg', '010239945330', '2424', 'اداره البرمجه', 44, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(20, 23, 'محمد المرشدي الحسيني', NULL, NULL, NULL, 'Mourshedy@2b-cs.com', '01009996174', '0508', 'ادارة توبي السيستم', 45, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:35', '2026-05-31 12:01:20'),
(21, 24, 'عبد الرحمن محمد سيد ابو العلا', NULL, NULL, NULL, 'Abdelrahaman.Mohamed@2b.com.eg', '01551551092', '19J1', 'ادارة التحول الرقمي', 39, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(22, 25, 'محسن السيد محمد عامر', NULL, NULL, NULL, 'Mohsen@2b.com.eg', '01207656160', '2093', 'ادارة التحول الرقمي', 46, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(23, 26, 'اسلام احمد برعي علي ابو سمك', NULL, NULL, NULL, 'Eslam.Broaai@2b.com.eg', '01004829412', '21N8', 'ادارة التحول الرقمي', 36, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(24, 27, 'عمر خالد سعد حسن ابراهيم', NULL, NULL, NULL, 'omar.saead@2b.com.eg', '01151524793', '2395', NULL, 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(25, 28, 'عز الدين على عبد الله السيد', NULL, NULL, NULL, 'EzzEldain.ali@2b.com.eg', '01149576594', '2394', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(26, 29, 'ريهام رضا السيد عبد الرازق', NULL, NULL, NULL, 'r.reda@2b.com.eg', '01113316877', '1749', 'اداره الحسابات', 47, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(27, 30, 'ناردين رمزى كامل برتله', NULL, NULL, NULL, 'Nardine@2bcart.com', '01223076392', '2280', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(28, 31, 'ايمان محسن ابراهيم الذهبى', NULL, NULL, NULL, 'Eman.mohsen@2b.com.eg', '01145605648', '2473', NULL, 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(29, 32, 'ندى اشرف محمد محمد بكرى', NULL, NULL, NULL, 'Nada.Ashraf@2b.com.eg', '01006870492', '2474', 'اداره البرمجه', 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(30, 33, 'محمد أسامة جريدة', NULL, NULL, NULL, 'Grida@2b.com.eg', '01009996160', '0001', 'الاداره العليا', 43, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:36', '2026-05-31 12:01:21'),
(31, 34, 'افنان اشرف ابراهيم محمود', NULL, NULL, NULL, 'Afnan.ashraf@2b.com.eg', '01015133433', '24L2', 'ادارة التحول الرقمي', 49, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(32, 35, 'دعاء عصام عبد الحميد', NULL, NULL, NULL, 'Doaa.essam@2b.com.eg', '01155884428', '24L3', 'ادارة التحول الرقمي', 49, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(33, 36, 'يسرا محمد محمد البطل', NULL, NULL, NULL, 'Yousra.mohamed@2b.com.eg', '01066698491', '24L4', 'ادارة التحول الرقمي', 49, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(34, 886, 'حسين محروس حسين عبد الحافظ', NULL, NULL, NULL, '0639', '01009996163', '0639', 'اداره الحسابات', 50, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(35, 1017, 'محمد عبد الحميد انور', NULL, NULL, NULL, '0749', '01099922492', '0749', 'اداره الحسابات', 51, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(36, 1018, 'شريف احمد محمود علام', NULL, NULL, NULL, '1217', '01009996176', '1217', 'اداره الحسابات', 52, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(37, 1019, 'ولاء محمد عبد العزيز عبد الفتاح', NULL, NULL, NULL, '1310', '01098886174', '1310', 'اداره الحسابات', 53, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(38, 1020, 'مجدي ابو العلا اسماعيل سعيد', NULL, NULL, NULL, '1486', '01024251666', '1486', 'اداره الحسابات', 54, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(39, 1021, 'محمد اشرف علي محمد يمن', NULL, NULL, NULL, '1670', '01013310310-01021214141', '1670', 'اداره الحسابات', 55, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(40, 1022, 'محمد دافع محمد الجندي', NULL, NULL, NULL, '1714', '01010469786', '1714', 'اداره الحسابات', 56, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:22'),
(41, 1023, 'هدير نبيل احمد عثمان', NULL, NULL, NULL, '1748', '01123922329', '1748', 'اداره الحسابات', 57, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(42, 1024, 'اسلام احمد محمد احمد شاكر', NULL, NULL, NULL, '1873', '01153317175', '1873', 'اداره الحسابات', 58, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(43, 1025, 'محمد سعد الدين امين موسي', NULL, NULL, NULL, '18B5', '01010694531', '18B5', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(44, 1026, 'رامي عبد المقصود السيد عبد المقصود', NULL, NULL, NULL, '18C8', '01094822262', '18C8', 'اداره الحسابات', 60, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(45, 1027, 'محمد كمال طه شرف الدين', NULL, NULL, NULL, '18G7', '01119876097', '18G7', 'اداره الحسابات', 61, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(46, 1028, 'كريم محمد السيد احمد', NULL, NULL, NULL, '1902', '01149584000', '1902', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(47, 1029, 'عمرو اشرف صبحي صالح السروي', NULL, NULL, NULL, '1973', '01211125031', '1973', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(48, 1030, 'سارة محمد عوض الله خليل', NULL, NULL, NULL, '1974', '01066844061', '1974', 'اداره الحسابات', 57, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(49, 1031, 'فيبي مجدي حبيب', NULL, NULL, NULL, '1976', '01275642499', '1976', 'اداره الحسابات', 57, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(50, 1032, 'ابراهيم سمير بدوي محمد ابو ضلع', NULL, NULL, NULL, '2003', '01068316316', '2003', 'اداره الحسابات', 60, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:37', '2026-05-31 12:01:23'),
(51, 1033, 'عمرو علي شحاتة علي سليم', NULL, NULL, NULL, '20H6', '01033775936', '20H6', 'اداره الحسابات', 60, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(52, 1034, 'محمد نصاري عبد الشافي داود', NULL, NULL, NULL, '20I1', '01141958531', '20I1', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(53, 1035, 'السيد السيد احمد حامد ابو العزم', NULL, NULL, NULL, '2124', '01027898228', '2124', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(54, 1036, 'محمود رضا عبد الرحمن يوسف', NULL, NULL, NULL, '2146', '01095975829', '2146', 'ادارة الاستراد ', 61, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(55, 1037, 'محمد منير ابراهيم حزين', NULL, NULL, NULL, '2173', '01110149333', '2173', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(56, 1038, 'كريم علي محمد علي', NULL, NULL, NULL, '2192', '01012594249', '2192', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(57, 1039, 'هادي جمال محمد عبد المنعم حسن', NULL, NULL, NULL, 'inventory3@2b.com.eg', '01140512465', '21C3', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(58, 1040, 'مارينا جورج ايلي نسيم', NULL, NULL, NULL, 'Banking4@2b.com.eg', '01208661023', '21H9', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(59, 1041, 'مصطفي مجدي مصطفي محمد السعيد', NULL, NULL, NULL, '2202', '01154680032', '2202', 'اداره الحسابات', 64, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(60, 1042, 'محمد مصيلحي محمد مصيلحي السيد', NULL, NULL, NULL, '2207', '01110056793', '2207', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:38', '2026-05-31 12:01:25'),
(61, 1043, 'يوسف سامح بنيامين فرج', NULL, NULL, NULL, '2209', '01289149692', '2209', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(62, 1044, 'يارا حمدي محمد الدسوقي', NULL, NULL, NULL, '2230', '01289350413', '2230', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(63, 1045, 'اسلام محمود محمد محمود', NULL, NULL, NULL, '2277', '01115883732', '2277', 'اداره الحسابات', 65, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(64, 1046, 'احمد رفعت علي احمد', NULL, NULL, NULL, '2288', '01287626786\\01005522546', '2288', 'اداره الحسابات', 66, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(65, 1047, 'احمد عبد الفتاح عبد اللطيف حسن يحيي', NULL, NULL, NULL, '22C1', '01212110030', '22C1', 'اداره الحسابات', 67, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(66, 1048, 'احمد دافع محمد عبد الفتاح الجندي', NULL, NULL, NULL, '2330', '01029153823', '2330', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(67, 1049, 'مصطفى نبيل احمد عثمان', NULL, NULL, NULL, 'tax3@2b.com.eg', '01127677758', '2360', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(68, 1050, 'محمد ماهر محمد ابو الغيط زاهر', NULL, NULL, NULL, '2398', '01020858612', '2398', 'ادارة التحول الرقمي', 68, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(69, 1051, 'احمد مجدى محمود', NULL, NULL, NULL, '24A1', '01152653995', '24A1', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(70, 1052, 'منار احمد حسن', NULL, NULL, NULL, '24F1', '01097496040', '24F1', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:39', '2026-05-31 12:01:26'),
(71, 1053, 'نور الدين باسم مجدى', NULL, NULL, NULL, '24F9', '01098110097', '24F9', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(72, 1054, 'احمد عادل محمد فرج سيد', NULL, NULL, NULL, '24I1', '1122255825', '24I1', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(73, 1109, 'دعاء هاشم حسين', NULL, NULL, NULL, 'Doaa.Hashim@2b.com.eg', '01556073916', '24M6', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(74, 1604, 'محمد زكريا مصطفي أحمد', NULL, NULL, NULL, '0202', '01009996162', '0202', 'اداره المنتجات اكسسورات', 69, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(75, 1951, 'عبد الحكيم السيد محمد ابو العزم', NULL, NULL, NULL, '0502', '01002190173', '0502', 'اداره المشتريات', 70, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(76, 1952, 'حسن عبد العال حسن عبد العال', NULL, NULL, NULL, '0637', '01009996720', '0637', 'اداره المشتريات', 71, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(77, 1953, 'احمد على محمد خليل', NULL, NULL, NULL, '', '01006060521', '0850', 'اداره المنتجات اكسسورات', 72, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(78, 1954, 'ابراهيم شعبان حماد الشوربجي', NULL, NULL, NULL, '1205', '01099313931', '1205', 'اداره المشتريات', 73, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(79, 1955, 'شريف محمد اسماعيل محمد', NULL, NULL, NULL, 'SherifM@2b.com.eg', '01000944344', '1225', 'اداره المنتجات اكسسورات', 74, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(80, 1956, 'أنس عبد المنعم عبد الفتاح عفيفي', NULL, NULL, NULL, '1469', '01009600008', '1469', 'اداره المنتجات اكسسورات', 75, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:40', '2026-05-31 12:01:27'),
(81, 1957, 'محمد السعيد عبد السلام زيدان', NULL, NULL, NULL, '1559', '01015909020', '1559', 'اداره المنتجات الاجهزة', 76, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(82, 1958, 'اسلام محمد احمد عبد الله', NULL, NULL, NULL, '1573', '01062609451', '1573', 'اداره المنتجات اكسسورات', 77, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(83, 1959, 'محمد فتحي عبد الصادق احمد', NULL, NULL, NULL, '1741', '01020401977', '1741', 'اداره المشتريات', 78, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(84, 1960, 'احمد ثابت علي عبيد', NULL, NULL, NULL, '1878', '01000033236', '1878', 'اداره المشتريات', 78, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(85, 1961, 'مصطفي بلال محمد فهمي عبد العاطي', NULL, NULL, NULL, '19E4', '01123097971', '19E4', 'اداره المشتريات', 71, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(86, 1962, 'اسلام عادل عبد المنعم عبد الغني', NULL, NULL, NULL, '19J0', '01061471488', '19J0', 'اداره المنتجات اكسسورات', 75, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(87, 1963, 'عمرو عماد محمد حافظ', NULL, NULL, NULL, '2041', '01124335880', '2041', 'اداره المنتجات اكسسورات', 75, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(88, 1964, 'محمود رأفت فتحي محمد', NULL, NULL, NULL, '2073', '01149206582', '2073', 'اداره المنتجات اكسسورات', 79, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(89, 1965, 'امير محمد الشافعي عبد الباقي سليمان', NULL, NULL, NULL, '20E7', '01225532112', '20E7', 'اداره المشتريات', 73, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(90, 1966, 'مي علاء الدين محمد حسين احمد', NULL, NULL, NULL, '2190', '01146694483', '2190', 'اداره المشتريات', 80, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:41', '2026-05-31 12:01:28'),
(91, 1967, 'احمد عبد الوهاب ابراهيم عبد الوهاب احمد', NULL, NULL, NULL, '2318', '01111149443', '2318', 'اداره المنتجات الاجهزة', 76, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(92, 1968, 'خالد عبد المنطلب لبيب عبد المنطلب', NULL, NULL, NULL, '24K2', '01120040153', '24K2', 'اداره المنتجات الاجهزة', 76, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(93, 1969, 'احمد ثابت عبد المجيد علي', NULL, NULL, NULL, '1720', '1155348369', '1720', 'ادارة الموقع الالكترونى', 81, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(94, 1970, 'احمد محمد العوضي البلتاجي فرج', NULL, NULL, NULL, '1825', '01097778249', '1825', 'ادارة الموقع الالكترونى', 82, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(95, 1971, 'احمد مصطفي عبد الرؤوف عبد العزيز الجزار', NULL, NULL, NULL, '1928', '01008147103', '1928', 'ادارة التحول الرقمي', 83, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(96, 1972, 'ولاء محمد حامد عايد', NULL, NULL, NULL, '2036', '01098931359', '2036', NULL, 84, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(97, 1973, 'تريزا شنوده شاكر صالح', NULL, NULL, NULL, '2188', '01278756231', '2188', NULL, 80, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(98, 1974, 'محمد السيد عبد اللطيف ابو الحديد', NULL, NULL, NULL, '22A7@2b.com', '01129789641', '22A7', 'ادارة التحول الرقمي', 85, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(99, 1975, 'هيثم محمد نجدى مصلحى عبده', NULL, NULL, NULL, 'P226', '01002839486', 'P226', 'اداره البرمجه', 86, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(100, 1976, 'عبد الرحمن احمد صلاح عبد الشافى', NULL, NULL, NULL, 'ahmed @gmail.com', '01023313811', '2252', 'اداره المشتريات', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:42', '2026-05-31 12:01:30'),
(101, 1977, 'محمد احمد رشاد مسلم', NULL, NULL, NULL, 'rashad@2b.com.eg', '01002600898', '1732', 'الاداره العليا', 88, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(102, 1978, 'نهي عادل السيد مرغني', NULL, NULL, NULL, 'noha@2b.com.eg', '01009996161', '0002', 'الاداره العليا', 89, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(103, 1979, 'احمد عبد المنعم احمد عبده الشربيني', NULL, NULL, NULL, 'abdotolba193@gmail.com', '01112012221', '1607', 'اداره المنتجات الاجهزة', 90, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(105, 1981, 'محمد محمود عبد النبى عبد الفتاح مراد', NULL, NULL, NULL, 'morad@2b.com.eg', '01003591727', '2351', 'ادارة التحول الرقمي', 83, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(106, 1982, 'محمد ياسر حمدي العدوي', NULL, NULL, NULL, '11242@2b.com', '01029957430', '11242', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(107, 1983, 'غاده عبدالله خليل احمد', NULL, NULL, NULL, 'Training@2b.com.eg', '01104644367', '2371', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(108, 1984, 'محمد عدلى سعيد محمد', NULL, NULL, NULL, 'adly@2bcart.com', '01000400933', '1468', 'ادارة الموقع الالكترونى', 91, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(109, 1985, 'شادي رضا عثمان حامد', NULL, NULL, NULL, 'shady.reda@2b.com.eg', '01064229666', '1657', 'اداره المنتجات الاجهزة', 90, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(110, 1986, 'تيست', NULL, NULL, NULL, NULL, '09878766775', '111111', NULL, 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:31'),
(111, 1987, 'هدير سميح احمد رمزي', NULL, NULL, NULL, 'HADER@FMAIL.COM', '01111828287', '2537', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(112, 1988, 'عادل امام', NULL, NULL, NULL, 'Haytham.Abdo@2b.com.eg', '0017724', '2490', 'ادارة التحول الرقمي', 41, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(113, 2002, 'يوزر تيست', NULL, NULL, NULL, 'test42423423@2b.com', '9999999999', '4242', 'اداره الحسابات', 92, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(114, 2004, 'يوزر تيست', NULL, NULL, NULL, '104280', '1000000000', '104280', 'اداره الحسابات', 92, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(115, 2419, 'سمر محمد فتحي خالد', NULL, NULL, NULL, '1716', '01099614737', '1716', 'إدارة التسويق', 93, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(116, 2420, 'سالي عونى على جمال الدين انس', NULL, NULL, NULL, '1849', '01099662988', '1849', 'إدارة التسويق', 94, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(117, 2421, 'امنية عصام حنفى محمد', NULL, NULL, NULL, '1850', '01008880419', '1850', 'إدارة التسويق', 95, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(118, 2422, 'احمد هشام احمد', NULL, NULL, NULL, '18A1', '01008025011', '18A1', 'إدارة التسويق', 96, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(119, 2423, 'شيماء عاصم عبده غيث', NULL, NULL, NULL, '21C5', '01004570136', '21C5', 'إدارة التسويق', 93, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(120, 2424, 'نهال احمد محمد مجاهد', NULL, NULL, NULL, '21K8', '01112633614', '21K8', 'ادارة المبيعات الداخلية', 97, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:43', '2026-05-31 12:01:32'),
(121, 2425, 'سهيلة احمد عبد المطلب عبد البر', NULL, NULL, NULL, '2265', '01096293209', '2265', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(122, 2426, 'طارق سيد عبد العزيز على', NULL, NULL, NULL, '23H8', '01110511134', '23H8', 'إدارة التسويق', 99, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(123, 2427, 'منه الله امجد على حسين', NULL, NULL, NULL, '2447', '01125866697', '2447', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(124, 2428, 'منه عادل محمد سعد الدين محمد', NULL, NULL, NULL, '2468', '01019240905', '2468', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(125, 2429, 'هدير محمد فاروق مهدى', NULL, NULL, NULL, '2488', '01118478880', '2488', 'إدارة التسويق', 100, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(126, 2430, 'مينا ماجد لبيب', NULL, NULL, NULL, '24F7', '01094467004', '24F7', 'إدارة التسويق', 101, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(127, 2431, 'مؤمن محمد سعد عبدالرحمن', NULL, NULL, NULL, '24H8', '01016820518', '24H8', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(128, 2432, 'عز الدين محمود صابر محمود', NULL, NULL, NULL, '25N4', '01114306234', '25N4', 'إدارة التسويق', 100, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(129, 2433, 'ندى اسماعيل', NULL, NULL, NULL, 'P802', '01006060512', 'P802', 'ادارة الاستراد ', 102, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(130, 2434, 'محمود عبد العزيز احمد عثمان محمد', NULL, NULL, NULL, '1543', '1066661521', '1543', 'ادارة المبيعات الداخلية', 103, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:44', '2026-05-31 12:01:34'),
(131, 2435, 'احمد عيد حسين سالم', NULL, NULL, NULL, '18G6', '01111147523', '18G6', 'ادارة المبيعات الداخلية', 104, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(132, 2436, 'سمير صالح مصطفي عبد العزيز', NULL, NULL, NULL, '2186', '01111354514', '2186', 'ادارة المبيعات الداخلية', 105, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(133, 2437, 'كريم طارق رشاد رجب', NULL, NULL, NULL, '22A2', '01127303012', '22A2', 'ادارة المبيعات الداخلية', 105, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(134, 2438, 'محمد صبري ابو القمصان الكومي', NULL, NULL, NULL, '0745', '01099922490', '0745', 'ادارة التحول الرقمي', 106, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(135, 2439, 'عمرو سامي السيد عبد العاطي', NULL, NULL, NULL, '1655', '01002183325', '1655', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(136, 2440, 'سامي رضا السيد السيد عباس', NULL, NULL, NULL, '1665', '01270604000', '1665', 'ادارة الشئون القانونية و الادارية', 108, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(137, 2441, 'شريف محمد سعيد ابراهيم', NULL, NULL, NULL, '1672', '01144361289', '1672', 'ادارة مبيعات الشركات', 109, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(138, 2442, 'منال ماهر عبد المسيح', NULL, NULL, NULL, '1770', '01091066939', '1770', 'ادارة الموارد البشرية', 110, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(139, 2443, 'محمد حسام عبد العزيز رضوان', NULL, NULL, NULL, '1813', '01004100544', '1813', 'ادارة توبي السيستم', 111, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(140, 2444, 'احمد عبد الصمد صميدة', NULL, NULL, NULL, '1824', '01127261111', '1824', 'ادارة مبيعات الشركات', 109, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:45', '2026-05-31 12:01:35'),
(141, 2445, 'هشام عيد حسنين علي', NULL, NULL, NULL, '18D2', '01140644849', '18D2', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(142, 2446, 'احمد ناجي عبد الحفيظ خضر', NULL, NULL, NULL, '1913', '01069141077', '1913', 'ادارة مبيعات الشركات', 113, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(143, 2447, 'وليد احمد ابراهيم حسين', NULL, NULL, NULL, '1914', '01143853522', '1914', 'ادارة الشئون القانونية و الادارية', 114, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(144, 2448, 'جون بطرس سمير عازر بغدادي', NULL, NULL, NULL, '19D5', '01229326093', '19D5', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(145, 2449, 'ايه محمد علي ابراهيم', NULL, NULL, NULL, '19D7', '1017111366', '19D7', 'ادارة مبيعات الشركات', 109, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(146, 2450, 'محمود سعيد صديق علي', NULL, NULL, NULL, '19D8', '01064094967', '19D8', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(147, 2451, 'فؤاد احمد احمد محمد', NULL, NULL, NULL, '2018', '01287489604', '2018', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(148, 2452, 'مجدي مسعد ميخائيل عبد الملاك', NULL, NULL, NULL, '2020', '01278473237', '2020', 'ادارة الشئون القانونية و الادارية', 115, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(149, 2453, 'اسماء رمضان علي محمد شلبي', NULL, NULL, NULL, '2095', '01147767915', '2095', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(150, 2454, 'محمود عاطف محمود رمضان', NULL, NULL, NULL, '20E6', '01092908188', '20E6', 'ادارة الشئون القانونية و الادارية', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:46', '2026-05-31 12:01:36'),
(151, 2455, 'الصافي عبد العاطي ضيف الله سعيد', NULL, NULL, NULL, '20F4', '01111974659', '20F4', 'الاداره العليا', 116, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(152, 2456, 'عبد الرحمن حسن حسن رمضان الدالي', NULL, NULL, NULL, '20H1', '01027105153', '20H1', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(153, 2457, 'بولا ايمن عبد المسيح الديب', NULL, NULL, NULL, '2122', '01289999753', '2122', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(154, 2458, 'امجد محمد شندي', NULL, NULL, NULL, 'amgad@2b-cs.com', '01001933177', '2123', 'ادارة توبي السيستم', 119, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(155, 2459, 'منير نعيم شحاته بخيت', NULL, NULL, NULL, '2150', '01204487775', '2150', 'ادارة الشئون القانونية و الادارية', 108, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(156, 2460, 'علاء هشام انور صياح', NULL, NULL, NULL, '21B1', '01159595239', '21B1', 'ادارة مبيعات الشركات', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(157, 2461, 'انجي فاروق وديع حنا', NULL, NULL, NULL, '21J0', '01016116479', '21J0', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(158, 2462, 'اشرف احمد علي احمد عبده', NULL, NULL, NULL, '21N4', '01206607791', '21N4', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(159, 2463, 'كيرلس عصام مكرم يوسف', NULL, NULL, NULL, '2235', '01208263502', '2235', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(160, 2464, 'انس محمد غانم محمد محمد غانم', NULL, NULL, NULL, '2253', '01009640601', '2253', 'ادارة مبيعات الشركات', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:47', '2026-05-31 12:01:38'),
(161, 2465, 'خلود ناصر فؤاد محمود', NULL, NULL, NULL, '2311', '01012079033', '2311', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(162, 2466, 'هبه الله صابر عباس سيد', NULL, NULL, NULL, '2312', '01147193016', '2312', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(163, 2467, 'هشام طلعت محمد على حسام', NULL, NULL, NULL, '2357', '01021566755', '2357', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(164, 2468, 'ياسمين عماد حمدي عبده الليثي', NULL, NULL, NULL, '', '01069465644', '2366', 'ادارة توبي السيستم', 102, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(165, 2469, 'بدر الدين جابر على ابراهيم الجوهرى', NULL, NULL, NULL, '2384', '01100148480', '2384', 'ادارة مبيعات الشركات', 121, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(166, 2470, 'سلمى سعيد ابراهيم محمد', NULL, NULL, NULL, '23G2', '01062557721', '23G2', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(167, 2471, 'اميره محروس احمد سليم', NULL, NULL, NULL, '2404', '01151984041', '2404', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(168, 2472, 'محمد عصام محمد الاسرج', NULL, NULL, NULL, '2411', '01143756394', '2411', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(169, 2473, 'احمد رجائي قرني محمد', NULL, NULL, NULL, '2461', '01028292475', '2461', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(170, 2474, 'احمد عمر محمد محمود', NULL, NULL, NULL, '2462', '01154331879', '2462', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:39'),
(171, 2475, 'مصطفي محمد السعيد عبد الحي', NULL, NULL, NULL, '2463', '01005167760', '2463', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(172, 2476, 'عبد الرحمن احمد على احمد', NULL, NULL, NULL, '2464', '01151191686', '2464', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(173, 2477, 'مصطفى ناصر فاروق عبد السلام', NULL, NULL, NULL, '2466', '01281890854', '2466', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(174, 2478, 'محمود عطيه محمود عثمان', NULL, NULL, NULL, '2467', '01126336648', '2467', 'ادارة توبي السيستم', 118, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(175, 2479, 'نورا عادل محمد جلهوم', NULL, NULL, NULL, '24B9', '1101415555', '24B9', 'ادارة توبي السيستم', 122, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(176, 2480, 'امانى عبد الفتاح عبداللاهى محمد', NULL, NULL, NULL, '24D9', '01000882442', '24D9', 'ادارة مبيعات الشركات', 121, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(177, 2481, 'اسماء محمد محمود احمد', NULL, NULL, NULL, '24K6', '01028886417', '24K6', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(178, 2482, 'احلام محمد محمد سيف', NULL, NULL, NULL, '24L6', '01102453293', '24L6', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(179, 2483, 'مصطفي خالد عبد الفتاح امين', NULL, NULL, NULL, '2507', '01099344855', '2507', 'الاداره العليا', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(180, 2484, 'احمد ابراهيم عبد الرزاق', NULL, NULL, NULL, '2510', '01064781803', '2510', 'ادارة توبي السيستم', 86, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:48', '2026-05-31 12:01:41'),
(181, 2485, 'محمود عبد الروءوف', NULL, NULL, NULL, '2526', '01015019648', '2526', 'ادارة توبي السيستم', 124, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(182, 2486, 'لبني حفظي فهيم', NULL, NULL, NULL, 'A003', '01273367892', 'A003', 'الاداره العليا', 125, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(183, 2487, 'حسن محمد احمد محمد سرحان', NULL, NULL, NULL, 'P214', '01122200763', 'P214', 'ادارة الاستراد ', 126, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(184, 3502, 'جهاد عصام عونى عباس', NULL, NULL, NULL, '1677', '01008035293', '1677', 'ادارة الموقع الالكترونى', 127, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(185, 3503, 'مصطفي هاني سيد محمد', NULL, NULL, NULL, '1756', '01014757657', '1756', 'ادارة اللوجيستك', 128, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(186, 3504, 'دعاء حسين صابر سليمان', NULL, NULL, NULL, '18G8', '01009640826', '18G8', 'ادارة الموقع الالكترونى', 129, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(187, 3505, 'اميرة محمود فاروق محمود مصطفى', NULL, NULL, NULL, '18H0', '01006002123', '18H0', 'ادارة الموقع الالكترونى', 129, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:42'),
(188, 3506, 'حسام محمد محمد عزت حسين', NULL, NULL, NULL, '1904', '01033033091', '1904', 'ادارة الموقع الالكترونى', 127, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:43'),
(189, 3507, 'محمود محمد عبدة مرسي', NULL, NULL, NULL, '1905', '01090948318', '1905', 'ادارة الموقع الالكترونى', 130, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:43'),
(190, 3508, 'ولاء محمد محمود احمد', NULL, NULL, NULL, '1931', '01061724056', '1931', 'ادارة الموقع الالكترونى', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:49', '2026-05-31 12:01:43'),
(191, 3509, 'امنية عبد الحكيم سيد', NULL, NULL, NULL, '19A7', '01033553423', '19A7', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(192, 3510, 'اسلام فوزى محمد اسماعيل', NULL, NULL, NULL, '19D4', '01066700949', '19D4', 'ادارة الموقع الالكترونى', 91, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(193, 3511, 'محمود ماهر محمد محمد', NULL, NULL, NULL, '19G5', '01127833831', '19G5', 'ادارة الموقع الالكترونى', 133, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(194, 3512, 'شيماء عبد الحميد عبد الحميد محمد', NULL, NULL, NULL, '2040', '01019999610', '2040', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(195, 3513, 'مصطفي احمد محمد محمد', NULL, NULL, NULL, '20G6', '01010881430', '20G6', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(196, 3514, 'دينا حلمي العوضي ابراهيم', NULL, NULL, NULL, '20H4', '01097401805', '20H4', 'ادارة الموقع الالكترونى', 128, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(197, 3515, 'هاجر وحيد محمد السيد', NULL, NULL, NULL, '', '01100087582', '20J0', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(198, 3516, 'محمد امين مشرف متولي', NULL, NULL, NULL, '21A4', '01060193826', '21A4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(199, 3517, 'دينا ممدوح ابو شامة احمد', NULL, NULL, NULL, '21A9', '01011406891', '21A9', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(200, 3518, 'محمد ابراهيم عبده جاد', NULL, NULL, NULL, '21F8', '01061853364', '21F8', 'ادارة الموقع الالكترونى', 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:50', '2026-05-31 12:01:45'),
(201, 3519, 'احمد محمد بهيج محروس', NULL, NULL, NULL, '21G0', '01006333810', '21G0', 'ادارة الموقع الالكترونى', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(202, 3520, 'اميرة سالم محمد ابو عريضة', NULL, NULL, NULL, '21G1', '01066019216', '21G1', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(203, 3521, 'احمد حمدي حلمي حسن', NULL, NULL, NULL, '21G4', '01015939317', '21G4', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(204, 3522, 'اميرة اشرف يونس السيد', NULL, NULL, NULL, '21G5', '01279844005', '21G5', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(205, 3523, 'سيد احمد محمد مرجان ابراهيم', NULL, NULL, NULL, '2217', '01145733085', '2217', 'ادارة الموقع الالكترونى', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(206, 3524, 'احمد خالد عبد الرحيم ابراهيم', NULL, NULL, NULL, '2246', '01145540024', '2246', 'ادارة الموقع الالكترونى', 128, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(207, 3573, 'عبدالله احمد محمد عبدالمنعم', NULL, NULL, NULL, 'abdalla.ahmed@2b.com.eg', '01096796098', 'P232', 'ادارة التحول الرقمي', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(208, 3577, 'معتز سامى ابراهيم الجوهرى ', NULL, NULL, NULL, 'm.elgohary@2b.com.eg', '01001818388', '2317', 'ادارة المبيعات الداخلية', 89, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(209, 3770, 'تامر صادق صديق محمد', NULL, NULL, NULL, '0403@2b.com', '01009996165', '0403', 'ادارة مبيعات الجملة', 135, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(210, 3771, 'حسام سيد محمد عبد الحافظ', NULL, NULL, NULL, '0605', '01009996093', '0605', 'ادارة مبيعات الجملة', 136, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:47'),
(211, 3772, 'محمد موافى محمد احمد الصعيدي', NULL, NULL, NULL, '0626', '01098877284', '0626', 'ادارة اللوجيستك', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(212, 3773, 'ضياء عبد الحليم امين عبد الحليم', NULL, NULL, NULL, '1028', '01009996172', '1028', 'ادارة مبيعات الجملة', 138, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(213, 3774, 'ياسر سعيد امين سيد', NULL, NULL, NULL, '1206', '01000081187', '1206', 'ادارة مبيعات الجملة', 139, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(214, 3775, 'أمال الحسيني زكريا ابو الدهب', NULL, NULL, NULL, '1315', '01009996164', '1315', 'ادارة مبيعات الجملة', 138, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(215, 3776, 'اسلام ابراهيم طه ابراهيم', NULL, NULL, NULL, '1518@2begypt.com', '01010500285', '1518', 'ادارة مبيعات الجملة', 138, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(216, 3777, 'احمد محمود احمد عبد اللطيف', NULL, NULL, NULL, '1636', '01024251525', '1636', 'ادارة مبيعات الجملة', 140, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(217, 3778, 'حسين صلاح سعد الله حسين', NULL, NULL, NULL, '1743', '01110250810', '1743', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(218, 3779, 'اسلام اسامه جمال الدين البهائي', NULL, NULL, NULL, '1812', '01007922100', '1812', 'ادارة مبيعات الجملة', 136, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(219, 3780, 'عبد الرحمن احمد عبد العزيز عبد النبي', NULL, NULL, NULL, '1816', '01012340561', '1816', 'ادارة مبيعات الجملة', 139, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(220, 3781, 'مصطفي احمد عبد الفتاح سيد احمد دياب', NULL, NULL, NULL, '1827', '01066674463', '1827', 'ادارة مبيعات الجملة', 139, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:51', '2026-05-31 12:01:49'),
(221, 3782, 'محمد عبد الفتاح احمد ابراهيم', NULL, NULL, NULL, '1831', '01112509587', '1831', 'ادارة مبيعات الجملة', 139, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(222, 3783, 'مؤمن محمد سالم عبد الله', NULL, NULL, NULL, '1875', '01144411066', '1875', 'ادارة مبيعات الجملة', 140, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(223, 3784, 'محمود احمد احمد يوسف', NULL, NULL, NULL, '1883', '01226103615', '1883', 'ادارة مبيعات الجملة', 136, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(224, 3785, 'علي محمد عبده علي بدوي', NULL, NULL, NULL, '18G2', '01121322344', '18G2', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(225, 3786, 'محمد مصطفي عبد النعيم شعبان', NULL, NULL, NULL, '1903', '01145583370', '1903', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(226, 3787, 'وليد محمد اسماعيل الدسوقي', NULL, NULL, NULL, '1917', '01006139893', '1917', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(227, 3788, 'ماريو محسب ابو العزايم', NULL, NULL, NULL, '1992', '01061209003', '1992', 'ادارة مبيعات الجملة', 140, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(228, 3789, 'ابراهيم حمدي احمد عثمان', NULL, NULL, NULL, '19B4', '01204505174', '19B4', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(229, 3790, 'علي محمد عبد المنعم عبد الرحمن', NULL, NULL, NULL, NULL, '01153455747', '19C2', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(230, 3791, 'حازم محمد سيد عبد القادر', NULL, NULL, NULL, '19C3', '01154135341', '19C3', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:52', '2026-05-31 12:01:51'),
(231, 3792, 'يحيي محمد عبد الحميد حسين', NULL, NULL, NULL, '20C9', '01114171316', '20C9', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(232, 3793, 'اسامه منسي حسين منسي', NULL, NULL, NULL, '2174', '01006346439', '2174', 'ادارة مبيعات الجملة', 140, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(233, 3794, 'مصطفي منصور مصطفي احمد', NULL, NULL, NULL, '21B2', '01128665586', '21B2', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(234, 3795, 'عبد الله احمد ابراهيم عبد الله', NULL, NULL, NULL, '21F4', '01140024005', '21F4', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(235, 3796, 'محمد هاني شوقي محمد', NULL, NULL, NULL, '21H2', '01127630145', '21H2', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(236, 3797, 'تامر انور ناشد ساويرس', NULL, NULL, NULL, '21L1', '01275335586', '21L1', 'ادارة مبيعات الجملة', 140, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(237, 3798, 'يوسف محمد يوسف دويدار', NULL, NULL, NULL, '2210', '01004098925', '2210', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(238, 3799, 'حسام عادل السيد حافظ', NULL, NULL, NULL, '2259', '01144432589', '2259', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(239, 3800, 'محمد سعيد حسن محمد النمر', NULL, NULL, NULL, '2306', '01224121481', '2306', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(240, 3801, 'بدوى عبد الله بدوى عبد الله', NULL, NULL, NULL, '2470', '01126438947', '2470', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:53'),
(241, 3802, 'مينا طارق فهمى حنا الله', NULL, NULL, NULL, '2481', '01279618290', '2481', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(242, 3803, 'محمد سامح سيد احمد على', NULL, NULL, NULL, '24E0', '01006936891', '24E0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(243, 3804, 'محمد ايمن على على بكار', NULL, NULL, NULL, '24E4', '1067449264', '24E4', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54');
INSERT INTO `users` (`id`, `system_id`, `name`, `name_en`, `name_ar`, `image`, `email`, `phone`, `machine_code`, `department_name`, `job_title_id`, `learner_type`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(244, 3805, 'محمود ابراهيم احمد حواش', NULL, NULL, NULL, '24F6', '01151781733', '24F6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(245, 3806, 'عمر عبد الله جعفر الطيار', NULL, NULL, NULL, '24J7', '01019773974', '24J7', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(246, 3807, 'احمد محمد احمد طايع', NULL, NULL, NULL, '2502', '01129211469', '2502', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(247, 3808, 'بيتر ايمن سخرون نوار', NULL, NULL, NULL, '2521', '01206491665', '2521', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(248, 3809, 'احمد محمود فهمي علي', NULL, NULL, NULL, '2524', '01156076987', '2524', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(249, 3810, 'جورج سامى عريان زكى', NULL, NULL, NULL, '2538', '01284105720', '2538', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(250, 3811, 'محمد محسن حسن', NULL, NULL, NULL, '2541', '01101120867', '2541', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:53', '2026-05-31 12:01:54'),
(251, 4172, 'علياء احمد فاروق فهمي محمد', NULL, NULL, NULL, '2373', '01091219044', '2373', 'ادارة الموقع الالكترونى', 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(252, 4173, 'دينا رجب حسن محمد', NULL, NULL, NULL, '2383', '1119165834', '2383', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(253, 4174, 'مريم فوزى محمد ابو خشبه', NULL, NULL, NULL, '23B3', '01143644962', '23B3', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(254, 4175, 'عبد الرحمن جمعه عبيد السيد عطوه', NULL, NULL, NULL, '23C4', '01557867444', '23C4', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(255, 4176, 'رحمه محمود احمد حسين', NULL, NULL, NULL, '23G5', '01007663201', '23G5', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(256, 4177, 'محمد ايهاب سعد عبد العظيم', NULL, NULL, NULL, '23H5', '01144812786', '23H5', 'ادارة الموقع الالكترونى', 143, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(257, 4178, 'مصطفي محسن احمد محمود', NULL, NULL, NULL, '2401', '01124487613', '2401', 'ادارة الموقع الالكترونى', 128, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(258, 4179, 'روماني فرج فايق فرح مرقص', NULL, NULL, NULL, '2416', '01270716352', '2416', 'ادارة الموقع الالكترونى', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(259, 4180, 'غريب عدلي جمعة', NULL, NULL, NULL, '2480', '01033413644', '2480', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(260, 4181, 'اسراء ايمن ياسن محمد', NULL, NULL, NULL, '2485', '01008366397', '2485', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:54', '2026-05-31 12:01:56'),
(261, 4182, 'ايمن احمد اسامه', NULL, NULL, NULL, '2487', '01002768508', '2487', 'ادارة الموقع الالكترونى', 91, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(262, 4183, 'عبد الرحمن احمد محمد ابو السعود', NULL, NULL, NULL, '24C7', '01066006417', '24C7', 'ادارة توبي السيستم', 144, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(263, 4184, 'شيماء سعيد حامد', NULL, NULL, NULL, '24G5', '01110791526', '24G5', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(264, 4185, 'غاده احمد محمد محمد', NULL, NULL, NULL, '24H3', '01118343267', '24H3', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(265, 4186, 'يوسف مجدى محمد', NULL, NULL, NULL, '24J4', '01033420166', '24J4', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(266, 4187, 'احمد اكرم توفيق احمد شحات', NULL, NULL, NULL, '24J8', '01094777630', '24J8', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(267, 4188, 'اسلام محمود حنفي محمد حنفي', NULL, NULL, NULL, '24L1', '0115165578', '24L1', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(268, 4189, 'هدير احمد ابو النجا', NULL, NULL, NULL, '2512', '01067187347', '2512', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(269, 4190, 'سيف احمد عبد الكامل', NULL, NULL, NULL, '2516', '01024399533', '2516', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(270, 4191, 'عمرو عبدا العال زكي قابيل', NULL, NULL, NULL, '2525', '01152455888', '2525', 'ادارة الموقع الالكترونى', 145, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:55', '2026-05-31 12:01:57'),
(271, 4192, 'كريم محمد علي حسين', NULL, NULL, NULL, '2530', '01124583605', '2530', 'ادارة الموقع الالكترونى', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(272, 4329, 'كريم جمال على محمد', NULL, NULL, NULL, '406', '01140008168', '406', 'اداره المشتريات', 81, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(273, 4330, 'ربيع امين هاشم عبد اللطيف', NULL, NULL, NULL, '1505', '01144948399', '1505', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(274, 4331, 'محمود احمد محمود مغربى', NULL, NULL, NULL, '1527', '01006060519', '1527', 'اداره المشتريات', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(275, 4332, 'محمود عبد المقصود عبد المجيد رزق', NULL, NULL, NULL, '1629', '01110253320', '1629', 'اداره المشتريات', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(276, 4333, 'محمد حسين محمد السيد', NULL, NULL, NULL, '2068', '01211206041', '2068', 'اداره المشتريات', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(277, 4334, 'محمد سمير عبد العظيم عبد الحميد', NULL, NULL, NULL, '20B3', '01154466700', '20B3', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(278, 4335, 'ماهيتاب مصليحي محمد مصليحي', NULL, NULL, NULL, '20E5', '01121702273', '20E5', 'اداره المشتريات', 149, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(279, 4336, 'سعيد سمير موسي', NULL, NULL, NULL, '2110', '01003314443', '2110', 'اداره المشتريات', 150, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(280, 4337, 'احمد محمد عبد الستار عبد العال', NULL, NULL, NULL, '2117', '01019310089', '2117', 'اداره المشتريات', 151, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:56', '2026-05-31 12:01:59'),
(281, 4338, 'معتز احمد مصطفي احمد', NULL, NULL, NULL, '2139', '01119556008', '2139', 'اداره المشتريات', 152, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(282, 4339, 'محمد عادل محمد كامل عبد الرحمن', NULL, NULL, NULL, '2140', '01149996795', '2140', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(283, 4340, 'عماد مصطفي محمد مصطفي', NULL, NULL, NULL, '2141', '01285707216', '2141', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(284, 4341, 'منه الله عادل سعيد محمد', NULL, NULL, NULL, '2154', '01099098119', '2154', 'اداره المشتريات', 153, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(285, 4342, 'احمد محمد نادي عبد السميع جاد', NULL, NULL, NULL, '2179', '01148586722', '2179', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(286, 4343, 'محمد السيد مهدي محمد', NULL, NULL, NULL, '21F0', '01224879621', '21F0', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(287, 4344, 'مصطفي سعيد عبد الفتاح عبد المنعم', NULL, NULL, NULL, '21K4', '01067151525', '21K4', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(288, 4345, 'انطونيوس حسام صبحي فهيم حنا', NULL, NULL, NULL, '21K9', '01282164280', '21K9', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(289, 4346, 'خالد محمد وحيد فريد عبد المجيد', NULL, NULL, NULL, '21L0', '01280535954', '21L0', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(290, 4347, 'محمد كمال عبد المعين امام', NULL, NULL, NULL, '', '01095359502', '21L2', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:00'),
(291, 4348, 'محمد اسماعيل عبد الرحيم جمعة', NULL, NULL, NULL, '21N7', '01274656537', '21N7', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:01'),
(292, 4349, 'هشام رمضان طه عبد الرحيم', NULL, NULL, NULL, '2208', '01065122594', '2208', 'اداره المشتريات', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(293, 4350, 'محمد سيد حسين احمد', NULL, NULL, NULL, '2215', '01030425979', '2215', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(294, 4351, 'محمد اشرف ابراهيم عشماوى', NULL, NULL, NULL, '2219', '01146372137', '2219', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(295, 4352, 'احمد عبد الوهاب عبد الرحمن كامل', NULL, NULL, NULL, '2226', '01098748727', '2226', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(296, 4353, 'محمد قدري محمد حنفي بيومي', NULL, NULL, NULL, '2249', '01026155990', '2249', 'اداره المشتريات', 152, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(297, 4354, 'محمد خالد سمير موسي سيد', NULL, NULL, NULL, '2257', '01117208894', '2257', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(298, 4355, 'سهيلة عابد ابراهيم سعد ابراهيم', NULL, NULL, NULL, '2313', '01112757602', '2313', 'اداره المشتريات', 155, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(299, 4356, 'محسن نبيل عبد المحسن السيد', NULL, NULL, NULL, '2332', '01009443465', '2332', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(300, 4357, 'بيتر محسن لويس خليل', NULL, NULL, NULL, '2372', '01228693613', '2372', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:57', '2026-05-31 12:02:02'),
(301, 4358, 'اسلام محمد صالح عبد الله', NULL, NULL, NULL, '2376', '01099206823', '2376', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(302, 4359, 'جون ابراهيم صبحي عوض', NULL, NULL, NULL, '23D7', '01271213676', '23D7', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(303, 4360, 'مصطفى احمد احمد محمد', NULL, NULL, NULL, '2400', '01278586906', '2400', 'اداره المشتريات', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(304, 4361, 'محمود طارق محمد لطفي', NULL, NULL, NULL, '2405', '01151415444', '2405', 'اداره المشتريات', 146, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(305, 4362, 'حسين على محمود على عيسى', NULL, NULL, NULL, '2419', '01012630552', '2419', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(306, 4363, 'محمد عبد الرحمن محمد محروس', NULL, NULL, NULL, '2432', '01142424669', '2432', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(307, 4414, 'علي جمعه علي جمعه', NULL, NULL, NULL, '18c5', '01097860687', '18C5', 'ادارة الموقع الالكترونى', 156, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(308, 4415, 'محمود عاطف محمود محمد', NULL, NULL, NULL, NULL, '01015746246', '2540', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(309, 4464, 'عبد الله حمدى يسن عبد العزيز', NULL, NULL, NULL, '2532', '01095114160', '2532', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(310, 4465, 'احمد محمد كمال طه', NULL, NULL, NULL, '2529', '01020217730', '2529', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:58', '2026-05-31 12:02:03'),
(311, 4472, 'احمد اشرف علي محمد يمن', NULL, NULL, NULL, '2444', '01141787046', '2444', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(312, 4473, 'محمد عبد الحميد محمد خميس', NULL, NULL, NULL, '2451', '01061524379', '2451', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(313, 4474, 'محمد عصام محمد عبد العزيز', NULL, NULL, NULL, '24B0', '1021069877', '24B0', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(314, 4475, 'سيف اسامه فتحى ابراهيم', NULL, NULL, NULL, '24B7', '1224628410', '24B7', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(315, 4476, 'محمد عاطف زكى احمد', NULL, NULL, NULL, '24C2', '01009085227', '24C2', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(316, 4477, 'سيف نائل اسماعيل', NULL, NULL, NULL, '24F3', '01100349246', '24F3', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(317, 4478, 'زياد هاني يوسف', NULL, NULL, NULL, '24F8', '01022442515', '24F8', 'اداره المشتريات', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(318, 4479, 'احمد الهم على عبد الهادي', NULL, NULL, NULL, '24G1', '01060478626', '24G1', 'اداره المشتريات', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(319, 4480, 'حسام يحيي محمد يحيي', NULL, NULL, NULL, '24H2', '01', '24H2', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(320, 4481, 'طارق محمود محمد الفاتح', NULL, NULL, NULL, '24I2', '1155439507', '24I2', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:29:59', '2026-05-31 12:02:04'),
(321, 4482, 'اسلام عاطف عبد الحافظ', NULL, NULL, NULL, '24I8', '1557708263', '24I8', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(322, 4483, 'محمد احمد السيد محمد', NULL, NULL, NULL, '24J2', '01128941935', '24J2', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(323, 4484, 'مريم عزت عبد الرحمن عبد العزيز عاشور', NULL, NULL, NULL, '2500', '01022711048', '2500', 'اداره المشتريات', 157, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(324, 4485, 'تقي فوزى محجوب سليمان', NULL, NULL, NULL, '2519', '01002099706', '2519', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(325, 4486, 'حنان احمد ابراهيم السيد', NULL, NULL, NULL, '2520', '0110340941', '2520', 'اداره المشتريات', 157, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(326, 4487, 'محمود احمد حسنى احمد', NULL, NULL, NULL, NULL, '01033803247', '25M7', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(327, 4488, 'كيرلس رفعت عدلى جاد الرب', NULL, NULL, NULL, '25M8', '01212355737', '25M8', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(328, 4489, 'احمد مدحت السيد السيد النطار', NULL, NULL, NULL, '25N1', '01093417474', '25N1', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(329, 4490, 'كيرلس فاخر صبحي بشاى', NULL, NULL, NULL, '25N3', '01277449742', '25N3', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(330, 4491, 'محمد رمضان عيد غالى', NULL, NULL, NULL, '', '01010337526', '2527', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:06'),
(331, 4492, 'احمد عبد العزيز عبد الفتاح', NULL, NULL, NULL, '2528', '01224789887', '2528', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(332, 4493, 'عبد الرحمن محمد فهيم محمد نور الدين', NULL, NULL, NULL, '', '01111925943', '2533', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(333, 4494, 'محمد سامي عبد الوهاب عبد الهادي', NULL, NULL, NULL, NULL, '01152981608', '2542', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(334, 4495, 'يوسف محمد جمعه محمد ', NULL, NULL, NULL, '2548', '01149634792', '2548', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(335, 4496, 'مروان شريف  عبد الوهاب بسطاوى', NULL, NULL, NULL, '2554', '01020107316', '2554', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(336, 4497, 'محمد وليد سيد عبد الله', NULL, NULL, NULL, '2553', '01155398039', '2553', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(337, 4498, 'احمد ناصر محمود', NULL, NULL, NULL, 'p222', '01097940043', 'p222', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(338, 4499, 'احمد محمد جمعه محمد', NULL, NULL, NULL, '2550', '010 04784633', '2550', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(339, 4500, 'مصطفي طه محمد امين ', NULL, NULL, NULL, '2551', '01050759360', '2551', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(340, 4501, 'مصطفى محمد فهمى حسن', NULL, NULL, NULL, '2363', '01064599425', '2363', 'ادارة الموقع الالكترونى', 145, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:00', '2026-05-31 12:02:08'),
(341, 4502, 'كريم خالد عبد الرسول التلاوي', NULL, NULL, NULL, 'karim @2b .vom', '01207245632', '2563', 'اداره البرمجه', 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(342, 4503, 'هاجر عصام عبد القادر', NULL, NULL, NULL, 'hager@2b.com.eg', '01157099901', '2564', 'اداره المشتريات', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(343, 4504, 'نانسي هانى يوسف رجب', NULL, NULL, NULL, '', '01090315447', '2565', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(344, 4505, 'احمد ابراهيم احمد فتحى', NULL, NULL, NULL, 'AHMED @2B.COM', '01099992901', '2566', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(345, 4506, 'بدر نصر الدين البدرى محمد بخيت ', NULL, NULL, NULL, 'badr.com@2b', '01554042351', '2567', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(346, 4507, 'شادى هشام صابر ', NULL, NULL, NULL, 'shadt@2b.com', '01111701631', '2568', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(347, 4508, 'اسر صبحي قرنى محمد سليم', NULL, NULL, NULL, 'ASAR', '01091501552', '2558', 'ادارة التحول الرقمي', 85, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(349, 4510, 'احمد محمد طارق احمد احمد', NULL, NULL, NULL, 'ahmed.@2b.com', '01118918800', '2559', 'ادارة المبيعات الداخلية', 89, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(350, 4511, 'محمد فاروق خلاف احمد ', NULL, NULL, NULL, 'farouk@2b.ocm', '01114985028', '2570', 'اداره المشتريات', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:01', '2026-05-31 12:02:10'),
(351, 4512, 'احمد طارق ابراهيم حسن', NULL, NULL, NULL, 'ahmed tarke@2b', '01127037216', '2571', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(352, 4513, 'اسلام احمد كامل سيد ', NULL, NULL, NULL, 'islma@gmaill', '01124523422', '2580', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(353, 4514, 'اسماء فتحى محمد ذكى ', NULL, NULL, NULL, 'asmaa@2b', '01014083870', '2581', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(354, 4515, 'بسنت صابر عبد الحميد حسن', NULL, NULL, NULL, 'bassant@2b.com', '01030400265', '2582', 'ادارة المبيعات الداخلية', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(355, 4516, 'زياد اسامه فكري', NULL, NULL, NULL, 'zaid@2b.com', '01145445423', '2583', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(356, 4517, 'محمود ممدوح محمود ابو العز ', NULL, NULL, NULL, 'Mahmoud @2b', '28909170102693', '2590', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(357, 4518, 'عبد الرحمن محمد محمد عبد الرحمن', NULL, NULL, NULL, 'abdeelrhman@2b', '01016893874', '2591', 'اداره المنتجات اكسسورات', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(358, 4521, 'محمد اشرف السيد حسن', NULL, NULL, NULL, 'ashraf42@2b.com', '01000000000', '4284', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(359, 4522, 'محمد حمدى عبد المرضى عبد الحليم', NULL, NULL, NULL, 'hamdy @2b', '01149686203', '2592', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(360, 4523, 'محمد احمد سيد عثمان', NULL, NULL, NULL, '2569', '01000814955', '2569', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:11'),
(361, 4524, 'كريم محمد فوزي حسنين', NULL, NULL, NULL, '1947', '01112199572', '1947', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(362, 4525, 'احمد عبد اللطيف محمد زكى', NULL, NULL, NULL, '2508', '01011930950', '2508', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(363, 4526, 'عمر احمد هنى عصمت وصفى', NULL, NULL, NULL, '2509', '01200052002', '2509', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(364, 4527, 'مى وليد محمد محمد علي', NULL, NULL, NULL, '2503', '01009515272', '2503', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(365, 4528, 'نادر خالد محمود عبد العال', NULL, NULL, NULL, '2504', '01122068321', '2504', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(366, 4529, 'معاذ محمود جمال الدين عمر رشدان', NULL, NULL, NULL, '2505', '01114249780', '2505', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(367, 4530, 'علي محمد محمود الباز عرابي', NULL, NULL, NULL, '2506', '0106012967', '2506', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(368, 4531, 'مايكل مهاود بدروس مهاود', NULL, NULL, NULL, '2513', '1203907281', '2513', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(369, 4532, 'محمد عزت محمد عبد المنعم', NULL, NULL, NULL, '2514', '01157547383', '2514', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(370, 4533, 'انس مصطفي شفيق', NULL, NULL, NULL, '2515', '01007357607', '2515', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:02', '2026-05-31 12:02:13'),
(371, 9924, 'كريم اشرف محمد عبد الحميد', NULL, NULL, NULL, 'karim@2b', '01550078407', '2595', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(372, 9925, 'عمرو ماجد محمد توفيق', NULL, NULL, NULL, '1613', '01007271927', '1613', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(373, 9926, 'احمد حمدي عيد علي', NULL, NULL, NULL, '1321', '01147170071', '1321', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(374, 9927, 'عبد الرحمن  محمود سليمان', NULL, NULL, NULL, '24E3', '1062822922', '24E3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(375, 9928, 'زياد طارق احمد مصطفى', NULL, NULL, NULL, '2327', '1011945383', '2327', 'اداره المنتجات اكسسورات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(376, 9929, 'مصطفى عاطف زكريا محمد على', NULL, NULL, NULL, '2244', '01145319316', '2244', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(377, 9930, 'مصطفى عبد الوهاب محمد الشحات', NULL, NULL, NULL, '21E3', '01129822989', '21E3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(378, 9931, 'محمد سيد البدوى سعد احمد', NULL, NULL, NULL, '', '01008088170', '19F7', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(379, 9932, 'محمود رضا محمد عبد المطلب الشناوى', NULL, NULL, NULL, '2129', '01208406472', '2129', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(380, 9933, 'محمد مصطفى عبد الحميد هويدى', NULL, NULL, NULL, '2130', '01024222888', '2130', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:03', '2026-05-31 12:02:14'),
(381, 9934, 'محمد محمد شريف عبد الفتاح غريب', NULL, NULL, NULL, '2004', '01097000801', '2004', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(382, 9935, 'سامح محمود راشد السيد شحم', NULL, NULL, NULL, '2034', '01018279798', '2034', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(383, 9936, 'احمد عز الدين احمد عبدالله', NULL, NULL, NULL, '2075', '01118182063', '2075', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(384, 9937, 'اسلام سعد محمد محمود', NULL, NULL, NULL, '20G7', '01066757208', '20G7', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(385, 9938, 'محمد عاطف عبد الحفيظ', NULL, NULL, NULL, '20A4', '01156688938', '20A4', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(386, 9939, 'علاء الدين محمد رمضان احمد', NULL, NULL, NULL, '20A7', '01008807088', '20A7', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(387, 9940, 'أحمد مجدي عبدالباسط عطيه', NULL, NULL, NULL, '2056', '01111146163', '2056', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(388, 9941, 'ندير هشام جلال عيد حسنين', NULL, NULL, NULL, '2084', '01028466344', '2084', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(389, 9942, 'احمد محمد هاشم منصور محمد رمضان', NULL, NULL, NULL, '1959', '01210621032', '1959', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(390, 9943, 'احمد عبد الرحمن محمود علي', NULL, NULL, NULL, '19B2', '01007455058', '19B2', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:04', '2026-05-31 12:02:16'),
(391, 10272, 'اسماء اسامه عبد القادر', NULL, NULL, NULL, ' asma@2b.com', '01550014973', '2596', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(392, 10355, 'هانى رشاد عبد الله المرحومى', NULL, NULL, NULL, '19B8', '01152457415', '19B8', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(393, 10356, 'ياسر محمد يسري عبد الحميد', NULL, NULL, NULL, '', '01062710627', '19K1', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(394, 10357, 'محمود عبد الرحيم محمد محمود', NULL, NULL, NULL, '19E3', '01064913829', '19E3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(395, 10358, 'عبد الرحمن سعد محمود محمد', NULL, NULL, NULL, '19E6', '01114323195', '19E6', 'اداره المشتريات', 71, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(396, 10359, 'محمد حارس فرغلي عبد الرحيم', NULL, NULL, NULL, '19G7', '01020673737', '19G7', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(397, 10360, 'اسلام هشام زكريا سليمان', NULL, NULL, NULL, '19I0', '01027006542', '19I0', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(398, 10361, 'احمد محمد محمود رمضان', NULL, NULL, NULL, '19B6', '01009605089', '19B6', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(399, 10362, 'بسام طارق طه عبد العزيز', NULL, NULL, NULL, '19C5', '01142269997', '19C5', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(400, 10363, 'احمد جمال الحسينى السلام', NULL, NULL, NULL, '19A3', '01111707500', '19A3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:18'),
(401, 10364, 'محمد اسامه محمد فتحي حافظ', NULL, NULL, NULL, '1982', '01145752955', '1982', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(402, 10365, 'مكسموس مقارعجيب جبرة مقار', NULL, NULL, NULL, '1966', '01222482236', '1966', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(403, 10366, 'سيد زكي توفيق زكى', NULL, NULL, NULL, '1927', '01020543454', '1927', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(404, 10367, 'احمد محمد محمد يوسف', NULL, NULL, NULL, '1887', '01110784455', '1887', 'ادارة مبيعات الشركات', 107, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(405, 10368, 'اسلام محمود محمد', NULL, NULL, NULL, '1830', '01223592809', '1830', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(406, 10369, 'محمد نبوي سالم احمد', NULL, NULL, NULL, '1306', '01148806501', '1306', 'ادارة المبيعات الداخلية', 163, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(407, 10370, 'احمد ماهر سعد محمد ابراهيم', NULL, NULL, NULL, '18c2', '01278672992', '18c2', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(408, 10371, 'باسم احمد عطيه عبد المنعم ابو العزم', NULL, NULL, NULL, '18G4', '01025108726', '18G4', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(409, 10372, 'هيثم إمام عبد اللطيف عبد الرحمن', NULL, NULL, NULL, '18C6', '01115550085', '18C6', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(410, 10373, 'احمد عبد اللطيف مسعد', NULL, NULL, NULL, '1877', '01222342392', '1877', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:05', '2026-05-31 12:02:19'),
(411, 10374, 'سامح احمد السيد احمد', NULL, NULL, NULL, '1744', '01143707700', '1744', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(412, 10375, 'اسلام محمد عبد الفتاح عبد الله', NULL, NULL, NULL, '1751', '01100165394', '1751', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(413, 10376, 'اسلام السعيد محمد نصر', NULL, NULL, NULL, '1701', '01120069300', '1701', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(414, 10377, 'محمد عبده محمد الزهري', NULL, NULL, NULL, '1723', '01068387771', '1723', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(415, 10378, 'محمد يوسف مرسي المرسي', NULL, NULL, NULL, '1783', '01147721227', '1783', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(416, 10379, 'وليد عزت السيد طلبه', NULL, NULL, NULL, '', '1064662326', '1776', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(417, 10380, 'محمد محمود هاشم حسين ابوشرف', NULL, NULL, NULL, '1627', '01114456563-01281011575', '1627', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(418, 10381, 'محمد يحيى  عبد العزيز عطا السيد', NULL, NULL, NULL, '1635', '01129080807', '1635', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(419, 10382, 'اسلام سعيد عثمان خليل', NULL, NULL, NULL, '1648', '01065068218', '1648', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(420, 10383, 'احمد عبد البارى احمد عبد الفتاح', NULL, NULL, NULL, '1634', '01153117889', '1634', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:06', '2026-05-31 12:02:20'),
(421, 10384, 'محمود محمد خليل حسنين', NULL, NULL, NULL, '1552', '01001397738', '1552', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(422, 10385, 'محمد كمال محمد محمد', NULL, NULL, NULL, '1546', '01066674451', '1546', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(423, 10386, 'مجدى صبحى احمد عبد العال', NULL, NULL, NULL, '1421', '01024251529', '1421', 'ادارة المبيعات الداخلية', 164, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(424, 10387, 'شريف محمد السيد موسى', NULL, NULL, NULL, '1498', '01140880818', '1498', 'اداره المنتجات اكسسورات', 165, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(425, 10388, 'ياسر جوده سيلمان عفيفى', NULL, NULL, NULL, '1481', '01006200106', '1481', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(426, 10389, 'احمد سعد محمد على', NULL, NULL, NULL, '1560', '01000529774', '1560', 'اداره المنتجات اكسسورات', 165, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(427, 10390, 'امجد محمد سيد محمد زهران', NULL, NULL, NULL, '1219', '01009996197', '1219', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(428, 10391, 'مصطفى ممدوح محمد محمود', NULL, NULL, NULL, '1255', '01015001013', '1255', 'ادارة المبيعات الداخلية', 163, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(429, 10392, 'محمد صادق صديق محمد', NULL, NULL, NULL, '1121', '01098886175', '1121', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(430, 10393, 'كارم محمود ابراهيم رمضان', NULL, NULL, NULL, '702', '01006060514', '702', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:07', '2026-05-31 12:02:22'),
(431, 10394, 'عادل محروس عطية شعراوي', NULL, NULL, NULL, '0722', '01009996196', '0722', 'ادارة المبيعات الداخلية', 161, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(432, 10395, 'اسلام حمد احمد عامر', NULL, NULL, NULL, '0638', '01006060518', '0638', 'ادارة المبيعات الداخلية', 163, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(433, 10396, 'وسام عادل حنفي محمود', NULL, NULL, NULL, 'wessam @2b.com', '01151606482', '2597', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(434, 10397, 'محمد هشام محمد عبد اللطيف', NULL, NULL, NULL, 'MOHAMED @2B.COM', '01006784914', '2598', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(435, 10830, 'احمد علاء حفنى محمد نصر ', NULL, NULL, NULL, 'ahmed1 @2b', '01112475512', '2599', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(436, 11263, 'بدر احمد محمد عبد الباقي ', NULL, NULL, NULL, 'badr@2b.com', '01017538631', '25A0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(437, 11264, 'عبد العزيز احمد عبد العزيز مدنى محمد', NULL, NULL, NULL, '2501', '01102083814', '2501', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(438, 11265, 'عبد العزيز مدحت شريف عبد الوهاب شوشه', NULL, NULL, NULL, '2544', '01119217544', '2544', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(439, 11266, 'شريف سعيد محمد عبدالمتعال', NULL, NULL, NULL, '2440', '01112461510', '2440', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(440, 11267, 'علاء طارق احمد محد قمر', NULL, NULL, NULL, '2572', '01501209602', '2572', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:23'),
(441, 11268, 'محمد سعيد محمود عبد اللطيف', NULL, NULL, NULL, '2573', '01114854144', '2573', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(442, 11269, 'عبد الرحمن حسن عمر ابراهيم محمد', NULL, NULL, NULL, '2575', '01099142941', '2575', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(443, 11270, 'محمد صابر احمد حافظ', NULL, NULL, NULL, '2576', '01129667674', '2576', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(444, 11271, 'عمر سيد احمد طه  عبد السلام', NULL, NULL, NULL, '2577', '01000725407', '2577', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(445, 11272, 'عمار بهاء الدين كمال عبد اللطيف تمام', NULL, NULL, NULL, NULL, '01111020563', '2578', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(446, 11273, 'عمرو اشرف علي محمد ناصر', NULL, NULL, NULL, '2562', '01157180164', '2562', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(447, 11274, 'عمر عاطف  محمداحمد عبد الرحمن', NULL, NULL, NULL, '25N0', '01024523399', '25N0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(448, 11275, 'اسلام محمد عبد الرسول محمد سيد', NULL, NULL, NULL, '', '01098495916', '2251', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(449, 11276, 'يوسف محمد  يوسف على', NULL, NULL, NULL, '1907', '01010190120', '1907', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(450, 11277, 'مهند عماد على عبدالعزيز', NULL, NULL, NULL, '2478', '01142635537', '2478', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:08', '2026-05-31 12:02:24'),
(451, 11278, 'محمد احمد محمد احمد محمد', NULL, NULL, NULL, '2479', '01006358517', '2479', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(452, 11279, 'ابراهيم علاء رزق احمد', NULL, NULL, NULL, '2484', '01068503615', '2484', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(453, 11280, 'احمد وائل عبدالمطلب سعيد', NULL, NULL, NULL, '2492', '1062544018', '2492', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(454, 11281, 'حسين محمد حسين محمد', NULL, NULL, NULL, '2493', '0000', '2493', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(455, 11282, 'وسيم صلاح السيد محمود', NULL, NULL, NULL, '2475', '01011845923', '2475', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(456, 11283, 'احمد مصطفى عبد الغفار الجندى', NULL, NULL, NULL, '2498', '01112006926', '2498', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(457, 11284, 'محمد محمد سالم مصطفى', NULL, NULL, NULL, '2407', '01026764723', '2407', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(458, 11285, 'محمد قاسم السيد سالم', NULL, NULL, NULL, '2420', '01112681553', '2420', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(459, 11286, 'نورالدين احمد احمد محمد حسن', NULL, NULL, NULL, '2421', '01110585203', '2421', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(460, 11287, 'مصطفى همام محمد انور عطيه', NULL, NULL, NULL, '2423', '01159142178', '2423', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:09', '2026-05-31 12:02:25'),
(461, 11288, 'احمد مصطفى رمضان وهبه', NULL, NULL, NULL, '2452', '01101752470', '2452', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(462, 11289, 'احمد خالد السعيد ابراهيم ابوالعينين', NULL, NULL, NULL, '2454', '01115092519', '2454', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(463, 11290, 'سيف الدين عابد ابراهيم سعد', NULL, NULL, NULL, '2455', '01025521607', '2455', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(464, 11291, 'محمود عادل عبدالعظيم دسوقى', NULL, NULL, NULL, '2457', '01118925469', '2457', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(465, 11292, 'بدر الدين محمد شكرى ابراهيم', NULL, NULL, NULL, '2439', '01113548868', '2439', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(466, 11293, 'احمد حسن ابو القاسم', NULL, NULL, NULL, '20D7', '01021605093', '20D7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(467, 11294, 'كريم صلاح الدين السيد', NULL, NULL, NULL, '24h1', '01114962933', '24h1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(468, 11295, 'محمد صبري فراج محمد', NULL, NULL, NULL, '24F4', '01103903367', '24F4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(469, 11296, 'علاء محمد احمد السيد', NULL, NULL, NULL, '24F5', '01000471138', '24F5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(470, 11297, 'احمد رافت زكى معوض', NULL, NULL, NULL, '24I5', '1011729223', '24I5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:10', '2026-05-31 12:02:27'),
(471, 11298, 'اسلام محمد سعيد', NULL, NULL, NULL, '24K5', '1148862192', '24K5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(472, 11299, 'فارس عبد المنعم فارس احمد', NULL, NULL, NULL, NULL, '01013811506', '24M0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(473, 11300, 'سيف محمد عرفه احمد', NULL, NULL, NULL, '24M4', '01156443493', '24M4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(474, 11301, 'عبد الرحمن خالد عبد الرحمن عبد الفتاح', NULL, NULL, NULL, '24l7', '01092378684', '24l7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(475, 11302, 'احمد عمرو احمد علاء الدين', NULL, NULL, NULL, '', '01122581189', '24L9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(476, 11303, 'دعاء شحاته محمد', NULL, NULL, NULL, '24K7', '1127559570', '24K7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(477, 11304, 'احمد نزيه عبدالوهاب', NULL, NULL, NULL, '24L0', '1142904404', '24L0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(478, 11305, 'محمد وليد رزق عامر', NULL, NULL, NULL, '24G6', '01062754148', '24G6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(479, 11306, 'مصطفى كمال حسين السيد', NULL, NULL, NULL, '24G7', '01021002564', '24G7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(480, 11307, 'ابراهيم محمد ابراهيم  علي نوفل', NULL, NULL, NULL, '24G8', '01112212929', '24G8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:28'),
(481, 11308, 'بهاء عادل حنفي محمود', NULL, NULL, NULL, '24G9', '01019053473', '24G9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(482, 11309, 'عمر وائل احمد محمد محمود', NULL, NULL, NULL, '24B1', '000', '24B1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(483, 11310, 'احمد صلاح عبد الحميد حواش', NULL, NULL, NULL, '24D1', '1110199287', '24D1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(484, 11311, 'عبد الرحمن سمير محمد عزب', NULL, NULL, NULL, '24D3', '1151742122', '24D3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29');
INSERT INTO `users` (`id`, `system_id`, `name`, `name_en`, `name_ar`, `image`, `email`, `phone`, `machine_code`, `department_name`, `job_title_id`, `learner_type`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(485, 11312, 'زيد لطفي ابراهيم الدسوقي', NULL, NULL, NULL, '', '1140511131', '24D6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(486, 11313, 'محمد احمد محمود ابراهيم', NULL, NULL, NULL, '24D7', '1092994217', '24D7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(487, 11314, 'مصطفى صلاح سعيد محمد', NULL, NULL, NULL, '24E7', '01558316627', '24E7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(488, 11315, 'احمد حسن عبدالغني قنديل', NULL, NULL, NULL, '24F2', '01140800739', '24F2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(489, 11316, 'مهاب ياسر فتحي محمود', NULL, NULL, NULL, '24A9', '01101382016', '24A9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(490, 11317, 'احمد عنتر عبد الله محمد علي', NULL, NULL, NULL, '24M5', '01099066197', '24M5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:11', '2026-05-31 12:02:29'),
(491, 11434, 'حسن هشام احمد محمود', NULL, NULL, NULL, 'Hassan @2b.com', '01150139736', '25A1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(492, 11435, 'زياد محمد السيد عبد الحميد', NULL, NULL, NULL, '2556', '01112549946', '2556', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(493, 11436, 'نانسي شريف احمد محمد', NULL, NULL, NULL, '2557', '01125975360', '2557', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(494, 11437, 'نور سيد قناوي عدلي', NULL, NULL, NULL, '2539', '01067779915', '2539', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(495, 11438, 'احمد بهاء الدين احمد محمد', NULL, NULL, NULL, '2547', '01110055785', '2547', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(496, 11439, 'عبدالرحمن محمد  موسي محمود', NULL, NULL, NULL, '2560', '01020423556', '2560', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(497, 11440, 'عبد المنعم سيد عبدالمنعم سيد', NULL, NULL, NULL, '2491', '1117526944', '2491', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(498, 11441, 'يوسف احمد رمضان علي الله', NULL, NULL, NULL, '2409', '01101405945', '2409', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(499, 11442, 'محمد اسامه محمد عبد اللطيف محمد', NULL, NULL, NULL, '2459', '01116303865', '2459', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(500, 11443, 'سيف رمضان خضرى على', NULL, NULL, NULL, '2428', '01142345250', '2428', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:12', '2026-05-31 12:02:30'),
(501, 11444, 'محمود مجدى رشيدى محمد عبده', NULL, NULL, NULL, '', '01030725294', '2448', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(502, 11445, 'احمد محمد عبدالنبى محمد', NULL, NULL, NULL, '24I3', '1017151834', '24I3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(503, 11446, 'عبد الرحمن  محمد احمد عبدالله', NULL, NULL, NULL, '24J5', '01018914392', '24J5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(504, 11447, 'احمد مصطفى محمد', NULL, NULL, NULL, '24K3', '1032478242', '24K3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(505, 11448, 'كريم محمد حسين', NULL, NULL, NULL, '24K4', '1011658022', '24K4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(506, 11449, 'اسلام محمد عبد الونيس عبد العظيم', NULL, NULL, NULL, '24B5', '1121590529', '24B5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(507, 11450, 'اميره ذكى محمد ذكي', NULL, NULL, NULL, '24D0', '01156535482', '24D0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(508, 11451, 'محمد رافت عبدالعظيم زكى', NULL, NULL, NULL, '24D5', '01097052113', '24D5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(509, 11452, 'لوى محمد فهيم انور', NULL, NULL, NULL, '24C3', '01011814431', '24C3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(510, 11453, 'زياد احمد محمد احمد', NULL, NULL, NULL, '', '01195908039', '24C8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:32'),
(511, 11454, 'عبد الرحمن فايز محمود عوض', NULL, NULL, NULL, '', '01011031144', '21J3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(512, 11455, 'جمال محمد سيد احمد', NULL, NULL, NULL, '23D2', '01117258372', '23D2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(513, 11456, 'مصطفى علاء  سيد محمود', NULL, NULL, NULL, '23G4', '01011488127', '23G4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(514, 11457, 'مى محمد سند السيد السخاوى', NULL, NULL, NULL, '23A7', '01154223190', '23A7', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(515, 11458, 'احمد محمد الدسوقى حب الله', NULL, NULL, NULL, '23B2', '01122382722', '23B2', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(516, 11459, 'محمد عبد الكريم محمد سيد احمد الشيخ', NULL, NULL, NULL, '2247', '01111613605', '2247', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(517, 11460, 'مصطفى محمد فؤاد محمد عبد الرحيم', NULL, NULL, NULL, '21B3', '01152504043', '21B3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(518, 11461, 'طارق محمد فؤاد حفظ الله', NULL, NULL, NULL, '21D2', '01113207196', '21D2', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(519, 11462, 'محمود رضا بخاطره عبد العزيز', NULL, NULL, NULL, '21000000', '01121994959', '21E6', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(520, 11463, 'محمد فكرى عبد الناصر السباعى', NULL, NULL, NULL, '21L4', '01095775304', '21L4', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:13', '2026-05-31 12:02:33'),
(521, 11464, 'محمد كمال عبد المنعم حسن', NULL, NULL, NULL, '21M0', '01102800070', '21M0', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(522, 11465, 'مصطفى كمال محمد احمد', NULL, NULL, NULL, '21M4', '01114758575', '21M4', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(523, 11466, 'محمد  مرزوق سيد حسين', NULL, NULL, NULL, '2099', '01024590061', '2099', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(524, 11467, 'مصطفي مجدي مصطفي محمد الخولي', NULL, NULL, NULL, '20A3', '01149839858', '20A3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(525, 11468, 'عبد الرحمن هشام محمد', NULL, NULL, NULL, '19B1', '01125846007', '19B1', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(526, 11469, 'رامي كمال مكين بطرس', NULL, NULL, NULL, '', '01220071321', '19J3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(527, 11470, 'محمد مجدى زينهم محمد', NULL, NULL, NULL, '19G1', '01152525332', '19G1', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(528, 11471, 'امين محمد عادل امين محمد', NULL, NULL, NULL, '1948', '01112843960-01000123505', '1948', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(529, 11472, 'اسلام عيد شعبان عبد المعطي', NULL, NULL, NULL, '1969', '01063970887', '1969', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(530, 11473, 'امجد محمد ماجد', NULL, NULL, NULL, '19J5', '01119177166', '19J5', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:14', '2026-05-31 12:02:34'),
(531, 11474, 'ايهاب هشام يونس عطيه', NULL, NULL, NULL, '1632', '01144898984', '1632', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(532, 11475, 'عمرو سعيد محمد سعد غنيم', NULL, NULL, NULL, '1461', '01000901030', '1461', 'ادارة المبيعات الداخلية', 166, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(533, 11476, 'هانى محمد على السيد', NULL, NULL, NULL, '1257', '01093835343', '1257', 'ادارة المبيعات الداخلية', 167, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(534, 11477, 'محمد حامد حلمي أحمد', NULL, NULL, NULL, '', '01009996199', '0506', 'ادارة المبيعات الداخلية', 166, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(535, 11478, 'مصطفي سعيد السيد عثمان رضوان', NULL, NULL, NULL, NULL, '01200960276', '18C4', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(536, 11479, 'محمد مجدى ابرهيم محمد صابر', NULL, NULL, NULL, '2496', '01223454397', '2496', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(537, 11480, 'احمد خالد عبدالسلام قطب', NULL, NULL, NULL, '2187', '01207612279', '2187', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(538, 11481, 'احمد عادل فهمى سيد احمد', NULL, NULL, NULL, '2127', '01146649975', '2127', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(539, 11482, 'محمد حسن عبد الرؤوف على', NULL, NULL, NULL, '2101', '01280747002', '2101', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(540, 11483, 'مهند عادل فتحى محمد', NULL, NULL, NULL, '', '01120067006', '2103', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:15', '2026-05-31 12:02:35'),
(541, 11484, 'مهاب عماد الدين كامل هنداوى', NULL, NULL, NULL, NULL, '01220025490', '2105', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(542, 11485, 'انس محمد عبد العاطى السيد', NULL, NULL, NULL, '', '01271439079', '2106', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(543, 11486, 'محمد عصمت عامر محمد', NULL, NULL, NULL, '2029', '01116208956', '2029', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(544, 11487, 'كريم عبد الحميد محمود مصطفي', NULL, NULL, NULL, '', '01156555511', '19h8', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(545, 11488, 'محمد مجدي محمود احمد زمزم', NULL, NULL, NULL, '1956', '01097759578', '1956', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(546, 11489, 'مارك ملاك ميخائيل اسحاق بطرس', NULL, NULL, NULL, NULL, '01282561548', '1958', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(547, 11490, 'ليلي عادل  مصطفي عبد الغني', NULL, NULL, NULL, '2545', '01122421893', '2545', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(548, 11491, 'محمد سيد عبده سرحان', NULL, NULL, NULL, '2546', '01066351442', '2546', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(549, 11492, 'مهاب طارق عبد المعطي', NULL, NULL, NULL, '2574', '01127442547', '2574', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(550, 11493, 'احمد محمد سعيد سيد', NULL, NULL, NULL, '21C9', '01114317909', '21C9', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:16', '2026-05-31 12:02:37'),
(551, 11494, 'بيتر مشرقي فهمي سليمان', NULL, NULL, NULL, '2438', '01008412344', '2438', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(552, 11495, 'يوسف محمد سعيد محمدى', NULL, NULL, NULL, '24J3', '01208097158', '24J3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(553, 11496, 'عبدالله رفيق  احمد', NULL, NULL, NULL, '', '01157226027', '24K9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(554, 11497, 'عبدالرحمن ابراهيم اسماعيل ابراهيم', NULL, NULL, NULL, '24B6', '01110525996', '24B6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(555, 11498, 'عبد الله عماد محمد المختار طه', NULL, NULL, NULL, '24D2', '1155709869', '24D2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(556, 11499, 'عمر محمد احمد بيومي اسماعيل', NULL, NULL, NULL, '24C5', '01127331576', '24C5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(557, 11500, 'سعيد خالد سيد سيد ابراهيم', NULL, NULL, NULL, '24A7', '1007502191', '24A7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(558, 11501, 'خالد محمود السيد محمد', NULL, NULL, NULL, '2324', '1153712302', '2324', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(559, 11502, 'اسلام محمد صلاح محمد احمد سلامه', NULL, NULL, NULL, '2308', '01110018025', '2308', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(560, 11503, 'محمود احمد محمد سيد', NULL, NULL, NULL, '23H1', '01145966040', '23H1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:38'),
(561, 11504, 'احمد وليد سعيد عيد النقيش', NULL, NULL, NULL, '23H9', '01012262775', '23H9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(562, 11505, 'انس فوزى رشاد ابو الحديد', NULL, NULL, NULL, 'anas@2b.com', '01060159647', '25A3', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(563, 11506, 'احمد هانى احمد عبد الحكم ', NULL, NULL, NULL, 'ahmed hany1 @2b', '0155476311', '25A4', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(564, 11507, 'محمد سعيد عجمى عيد العزيز', NULL, NULL, NULL, '24M3', '01556453231', '24M3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(565, 11508, 'احمد ابراهيم عبدالفتاح محمد منصور', NULL, NULL, NULL, '24J0', '01200095105', '24J0', 'ادارة المبيعات الداخلية', 168, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(566, 11509, 'احمد محمود عاطف محمود', NULL, NULL, NULL, '2385', '01149062030', '2385', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(567, 11510, 'كريم محمد  سيد اسماعيل احمد', NULL, NULL, NULL, '2319', '1228582836', '2319', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(568, 11511, 'الحسن جمعه عبد الله ابراهيم حسن', NULL, NULL, NULL, '2323', '1069563450', '2323', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(569, 11512, 'مهند زين العابدين عثمان هنداوى', NULL, NULL, NULL, '2325', '1011752960', '2325', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(570, 11513, 'احمد مصطفى السيد متولى عسكر', NULL, NULL, NULL, '2326', '1154587514', '2326', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:17', '2026-05-31 12:02:40'),
(571, 11514, 'اندرو نبيل انور ويصا', NULL, NULL, NULL, '2304', '01206451122', '2304', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(572, 11515, 'احمد عادل عبد العزيز محمد ابو القاسم الكردى', NULL, NULL, NULL, '2358', '01121465497', '2358', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(573, 11516, 'اسماء محمد عادل محمد', NULL, NULL, NULL, '2359', '01018051405', '2359', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(574, 11517, 'نور الدين محمد احمد عبد العظيم', NULL, NULL, NULL, '21', '01127274303', '21E0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(575, 11518, 'محمود ايمن عفيفي', NULL, NULL, NULL, '23D5', '01129151994', '23D5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(576, 11519, 'محمد ابراهيم  السيد ابراهيم', NULL, NULL, NULL, '23G8', '01148916014', '23G8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(577, 11520, 'عمر نبيل مرزوق حسن', NULL, NULL, NULL, 'OMAR@GMAAIL', '01151379349', '23G9', 'اداره المشتريات', 80, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(578, 11521, 'شريف هشام  محمد يوسف فراج', NULL, NULL, NULL, '23H0', '01013900071', '23H0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(579, 11522, 'محمد فرج محمد خليل', NULL, NULL, NULL, '23H2', '01111620736', '23H2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(580, 11523, 'اسلام محمد عبدالمرضي', NULL, NULL, NULL, '23G3', '01002036600', '23G3', 'ادارة الموقع الالكترونى', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:18', '2026-05-31 12:02:42'),
(581, 11524, 'احمد محمد حامد احمد ادم', NULL, NULL, NULL, '23I1', '01157210769', '23I1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(582, 11525, 'عبدالله مهدي عبدالوهاب احمد يونس', NULL, NULL, NULL, '23H6', '01140455161', '23H6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(583, 11526, 'عبدالرحمن احمد حسين', NULL, NULL, NULL, '23H7', '01003370443', '23H7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(584, 11527, 'احمد محمد احمد تمام', NULL, NULL, NULL, '23A8', '01112666513', '23A8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(585, 11528, 'محمد حاتم محمد يوسف عامر', NULL, NULL, NULL, '', '01122056003', '23B0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(586, 11529, 'محمد يحيي على ابراهيم على', NULL, NULL, NULL, '18C0', '01270067388', '18C0', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(587, 11530, 'محمد كارم محمود عبد الفتاح', NULL, NULL, NULL, '2397', '01006320245', '2397', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(588, 11531, 'اسلام محمود اسماعيل سيد', NULL, NULL, NULL, '2248', '01159093646', '2248', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(589, 11532, 'عبدالرحمن ابراهيم محمد النجار', NULL, NULL, NULL, '', '01101190890', '2241', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(590, 11533, 'محمود اشرف محمود محمدى', NULL, NULL, NULL, '2216', '01121685895', '2216', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:19', '2026-05-31 12:02:43'),
(591, 11534, 'محمد اسامة سيد احمد خليل', NULL, NULL, NULL, '2295', '01112305480', '2295', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(592, 11535, 'حازم خالد خضرى على غنيم', NULL, NULL, NULL, '22A3', '01129501887', '22A3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(593, 11536, 'عمر اشرف السيد على', NULL, NULL, NULL, '22A5', '01127632753', '22A5', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(594, 11537, 'اسلام سليمان همام محمد سليمان', NULL, NULL, NULL, '22A6', '01030760652', '22A6', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(595, 11538, 'اسلام محمد سيد امين سيد', NULL, NULL, NULL, '22B0', '01125919981', '22B0', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(596, 11539, 'احمد ايمن عبد الحليم مأمون', NULL, NULL, NULL, '22B3', '01100615521', '22B3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(597, 11540, 'حسن احمد محمد عبد المنعم السيد', NULL, NULL, NULL, '21A6', '01118154805', '21A6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(598, 11541, 'عبد الرحمن طارق ابو المجد ربيع', NULL, NULL, NULL, '21N5', '01066756567', '21N5', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(599, 11542, 'احمد محمد محمد عبد الرازق', NULL, NULL, NULL, '21N6', '01226626400', '21N6', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(600, 11543, 'نادر سيد شاذلى بشندى', NULL, NULL, NULL, '210000000', '01110151593', '21E7', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:45'),
(601, 11544, 'احمد عمر محمد النبوى', NULL, NULL, NULL, '21F1', '01154764742', '21F1', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(602, 11545, 'طارق احمد زكريا على', NULL, NULL, NULL, '21G8', '01025886804', '21G8', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(603, 11546, 'رماح العدوى محمود العدوى', NULL, NULL, NULL, '21J4', '01115671905', '21J4', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(604, 11547, 'احمد محمد حمزه احمد حمزه', NULL, NULL, NULL, '21L8', '01110414942', '21L8', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(605, 11548, 'محمد ماهر السيد محمد', NULL, NULL, NULL, '', '01066580449', '21A3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(606, 11549, 'محمد ماجد سيد علي', NULL, NULL, NULL, '2074', '01128274772', '2074', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(607, 11550, 'فادى هانى سمير بغدادى', NULL, NULL, NULL, '2082', '01010988810', '2082', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(608, 11551, 'محمد منصور عبد الفتاح عبد الملك', NULL, NULL, NULL, '20A9', '01122703019', '20A9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(609, 11552, 'احمد بهاء الدين عبد القادر سالم', NULL, NULL, NULL, '', '01118655903', '20B0', 'ادارة اللوجيستك', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(610, 11553, 'مروان عادل حبيب عبد الستار', NULL, NULL, NULL, '20H2', '01149389007', '20H2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:20', '2026-05-31 12:02:46'),
(611, 11554, 'محمد فوزي محمد الديب', NULL, NULL, NULL, '', '01220025024', '19J6', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(612, 11555, 'شريف احمد محمد مهران', NULL, NULL, NULL, '19E1', '01065757665', '19E1', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(613, 11556, 'علي محمد عبد السميع محمود', NULL, NULL, NULL, '19I3', '01155000198', '19I3', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(614, 11557, 'محمد اسامه احمد محمد مسعود', NULL, NULL, NULL, '1949', '01014886608', '1949', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(615, 11558, 'محمود احمد صلاح عبد الرحمن', NULL, NULL, NULL, '1957', '01154781942', '1957', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(616, 11559, 'اسامه حسني حسن مقبل', NULL, NULL, NULL, '1972', '01007921461', '1972', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(617, 11560, 'محمد حلمي راضي عبد الوهاب', NULL, NULL, NULL, '19k3', '01114510686', '19k3', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(618, 11561, 'محمود كمال حسين السيد', NULL, NULL, NULL, '18H2', '01094809888', '18H2', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(619, 11562, 'احمد اسماعيل محمد اسماعيل', NULL, NULL, NULL, '1710', '01033426880', '1710', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(620, 11563, 'شادى فضل محمد بخيت', NULL, NULL, NULL, '', '01005700042', '1422', 'ادارة المبيعات الداخلية', 166, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:21', '2026-05-31 12:02:47'),
(621, 11564, 'هشام عبد الرحمن خليل', NULL, NULL, NULL, '', '01102909202', '1331', 'ادارة المبيعات الداخلية', 166, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(622, 11565, 'عبد الرحمن زكريا محمود ابو الحصين', NULL, NULL, NULL, '2543', '01226341669', '2543', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(623, 11566, 'اسراء وليد احمد احمد', NULL, NULL, NULL, '', '01275602937', '2523', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(624, 11567, 'مريم طارق عبدالله ابراهيم', NULL, NULL, NULL, '24H7', '01211413301', '24H7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(625, 11568, 'محمد صابر عقاب احمد', NULL, NULL, NULL, '24I0', '1280207134', '24I0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(626, 11569, 'منصور عبد الناصر احمد محمدين', NULL, NULL, NULL, 'P234', '000000', 'P234', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(627, 11570, 'عبدالرحمن جمال حسن اسماعيل عماره', NULL, NULL, NULL, '2442', '01030753537', '2442', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(628, 11571, 'محمد عطيه محمد احمد', NULL, NULL, NULL, '2441', '01065805442', '2441', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(629, 11572, 'ابراهيم سليم احمد سليم', NULL, NULL, NULL, NULL, '01020000895', '2443', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(630, 11573, 'محمد السيد محمد عبد العزيز', NULL, NULL, NULL, '21C8', '01099031750', '21C8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:22', '2026-05-31 12:02:49'),
(631, 11574, 'عبدالرحمن حسن ابراهيم  مصطفى', NULL, NULL, NULL, '23H4', '01023410823', '23H4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(632, 11575, 'عمرو احمد كامل حفني', NULL, NULL, NULL, '2097', '01069705034', '2097', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(633, 11592, 'اسلام محمد محمد احمد رضوان', NULL, NULL, NULL, '2456', '01030367868', '2456', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(634, 11593, 'عادل محمود رضا عبدالعزيز', NULL, NULL, NULL, '24J9', '01002996297', '24J9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(635, 11600, 'لبني طارق حامد محمد مصطفى', NULL, NULL, NULL, '2534', '01069688688', '2534', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(636, 11601, 'عبد الرحمن جمال سعيد محمد', NULL, NULL, NULL, '2536', '01022561273', '2536', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(637, 11602, 'محمد عبد الفتاح عبد العظيم عبد المعطى', NULL, NULL, NULL, '2561', '01029073832', '2561', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(638, 11603, 'معاذ محمد جمال الدين هاشم', NULL, NULL, NULL, '2584', '01012812784', '2584', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(639, 11676, 'احمد عبد الرحمن عبدالعزيز عبدالرحمن', NULL, NULL, NULL, '24A4', '01021163887', '24A4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(640, 11677, 'مصطفى عبد الباسط عبد الحميد محمد ندا', NULL, NULL, NULL, '23I0', '01157422244', '23I0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:50'),
(641, 11678, 'محمد عصام محمد يونس', NULL, NULL, NULL, '2185', '01125532045', '2185', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(642, 11679, 'اسامه محمد ابو اليزيد قنديل', NULL, NULL, NULL, '19I2', '01061050014', '19I2', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(643, 11680, 'شريف احمد محمد الكلاوى العشرى', NULL, NULL, NULL, '24E2', '1201190091', '24E2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(644, 11715, 'ياسمين فرج شعبان محمد', NULL, NULL, NULL, '2449', '01067751131', '2449', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(645, 11716, 'نورهان خالد حسن', NULL, NULL, NULL, '24K8', '1005386958', '24K8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(646, 11759, 'احمد على مبارك على', NULL, NULL, NULL, '2294', '01111656439', '2294', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(647, 11760, 'منار احمد ابو الفضل احمد', NULL, NULL, NULL, '24I9', '01140565697', '24I9', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(648, 11761, 'اية عبد المنعم محمد طه', NULL, NULL, NULL, '2336', '01091001399', '2336', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(649, 11762, 'كيرلس مفرح رمزى ظريف', NULL, NULL, NULL, '2362', '01013580100', '2362', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(650, 11763, 'اسراء محمد ابو عبيد سليمان', NULL, NULL, NULL, 'P236', '00000000', 'P236', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:23', '2026-05-31 12:02:51'),
(651, 11764, 'ماهر مجدي ماهر جاد', NULL, NULL, NULL, '23H3', '01284348701', '23H3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(652, 11765, 'يوسف اشرف ابو اليزيد السيد', NULL, NULL, NULL, '2287', '01010186508', '2287', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(653, 11766, 'مصطفى محمد العوضى البلتاجى فرج', NULL, NULL, NULL, '21A2', '01122918380', '21A2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(654, 11767, 'اسلام وصفى عبد الغنى محمود', NULL, NULL, NULL, '21M8', '01094243697', '21M8', 'ادارة المبيعات الداخلية', 160, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(655, 11768, 'احمد عبدالحميد على الشرشابى', NULL, NULL, NULL, '19F9', '01143744743', '19F9', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(656, 11769, 'حسين محمد سليم راشد', NULL, NULL, NULL, '2517', '01019766542', '2517', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(657, 11770, 'يوسف احمد سيد', NULL, NULL, NULL, '24G0', '01210892675', '24G0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(658, 11771, 'احمد السيد محمود محمد', NULL, NULL, NULL, NULL, '01068451237', '25A6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(659, 11772, 'محمود محمد احمد محمود الجمل', NULL, NULL, NULL, '24B3', '01121633802', '24B3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(660, 11773, 'محمد عواد محمدى ابراهيم', NULL, NULL, NULL, '2555', '01033442152', '2555', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:24', '2026-05-31 12:02:53'),
(661, 11774, 'محمد السيد دسوقي عبد الحليم', NULL, NULL, NULL, '2052', '01206468885', '2052', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:54'),
(662, 11775, 'اسماعيل عبد الله سيد ابراهيم', NULL, NULL, NULL, '2585', '01066731029', '2585', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:54'),
(663, 11776, 'محمد سيد احمد شحاته', NULL, NULL, NULL, '2579', '01285546632', '2579', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:54'),
(664, 11777, 'احمد شعبان شلبي الزيات', NULL, NULL, NULL, '2586', '01145948848', '2586', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:54'),
(665, 11778, 'محمد خالد عاشور', NULL, NULL, NULL, '2587', '01279992410', '2587', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(666, 11779, 'احمد عطا  الله محمد بدوي سعد', NULL, NULL, NULL, '2588', '01207788776', '2588', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(667, 11780, 'ابراهيم سيد وهبه', NULL, NULL, NULL, '2589', '01015329221', '2589', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(668, 11781, 'محمد كامل محمد البكري', NULL, NULL, NULL, '2549', '01111236443', '2549', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(669, 11782, 'محمد صلاح محمد', NULL, NULL, NULL, '2402', '01155088565', '2402', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(670, 11783, 'مينا صبحي منصور اسعد', NULL, NULL, NULL, '2450', '01016283791', '2450', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:25', '2026-05-31 12:02:55'),
(671, 11784, 'محمد مدحت كمال حسن محمد', NULL, NULL, NULL, '2497', '01122090748', '2497', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(672, 11785, 'احمد على ابراهيم سيد', NULL, NULL, NULL, '24C0', '01068367327', '24C0', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(673, 11786, 'احمد محمد محمد على الزيات', NULL, NULL, NULL, '2415', '01151501333', '2415', 'ادارة اللوجيستك', 171, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(674, 11787, 'ايهاب عنتر سيد راوى', NULL, NULL, NULL, '23B9', '01159041021', '23B9', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(675, 11788, 'اسلام حنفي محمود عبد الهادي الزهار', NULL, NULL, NULL, '1943', '01144842993', '1943', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(676, 11789, 'سامي ابراهيم محمد ابوالفتوح', NULL, NULL, NULL, '1253', '01000919891', '1253', 'إدارة التسويق', 172, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(677, 11790, 'ادهم فارس ابوزيد ششتاوى', NULL, NULL, NULL, '2342', '01125062888', '2342', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(678, 11791, 'اشرف وحيد شعبان ششتاوى الزيات', NULL, NULL, NULL, '2333', '01140235145', '2333', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(679, 11792, 'عيد سيد عبد الله حسان', NULL, NULL, NULL, '23A0', '01124185628', '23A0', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(680, 11793, 'محمود عويس محمد محمد ابو جمعه', NULL, NULL, NULL, '2256', '01114567071', '2256', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:56'),
(681, 11794, 'محمد اشرف حافظ كامل الشرقاوى', NULL, NULL, NULL, '2261', '01204556822', '2261', 'ادارة اللوجيستك', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(682, 11795, 'رحمه حسن بدوى قاسم', NULL, NULL, NULL, '2201', '01121232205', '2201', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(683, 11796, 'محمد ابراهيم احمد ابراهيم', NULL, NULL, NULL, '2245', '01112874187', '2245', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(684, 11797, 'السيد ابراهيم احمد عوض مسعود', NULL, NULL, NULL, '2264', '01271361934', '2264', 'اداره الحسابات', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(685, 11798, 'محمود حسانين شيشتاوى الزيات', NULL, NULL, NULL, '2268', '01149474040', '2268', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(686, 11799, 'كريم نبيل سليمان احمد زغلول', NULL, NULL, NULL, '2274', '01008821820', '2274', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(687, 11800, 'عبد الغفار احمد عبد الغفار حسن', NULL, NULL, NULL, '2291', '01013908033', '2291', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(688, 11801, 'مؤمن اشرف احمد على', NULL, NULL, NULL, '2262', '01559550231', '2262', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(689, 11802, 'امجد اسامه احمد على', NULL, NULL, NULL, '', '01552248202', '2263', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(690, 11803, 'احمد محمد امين ادم', NULL, NULL, NULL, '22A9', '01116770296 \\ 01151424244', '22A9', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:26', '2026-05-31 12:02:58'),
(691, 11804, 'هشام كامل محمد البكرى', NULL, NULL, NULL, '2290', '01126501010', '2290', 'ادارة اللوجيستك', 174, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(692, 11805, 'ايمن محمد ابراهيم محمد', NULL, NULL, NULL, '21H6', '01020186115', '21H6', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(693, 11806, 'بهاء احمد فاروق احمد', NULL, NULL, NULL, '21C4', '01124451257', '21C4', 'ادارة اللوجيستك', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(694, 11807, 'اسلام اسامه احمد على', NULL, NULL, NULL, '2155', '01152046590', '2155', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(695, 11808, 'اسماء محمد على ابراهيم', NULL, NULL, NULL, '21B8', '01120396736', '21B8', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(696, 11809, 'احمد محمد عبد المنعم دياب', NULL, NULL, NULL, '21I7', '01021416440', '21I7', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(697, 11810, 'احمد عصام عبد القادر ابراهيم السيد', NULL, NULL, NULL, '21I8', '01144400271', '21I8', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(698, 11811, 'احمد فرجانى شعبان ششتاوى', NULL, NULL, NULL, '20j2', '01161545842', '20j2', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(699, 11812, 'عمرو فرجانى شعبان ششتاوى', NULL, NULL, NULL, '21H7', '01118120021', '21H7', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(700, 11813, 'ياسر سيد حجاج سيد الزيات', NULL, NULL, NULL, '21B5', '01200203434', '21B5', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:27', '2026-05-31 12:03:00'),
(701, 11814, 'محمد وفدى رجب عبد الله', NULL, NULL, NULL, '21B7', '01208093112', '21B7', 'ادارة اللوجيستك', 170, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(702, 11815, 'ايمن ناصر فيتورى حامد', NULL, NULL, NULL, '21E2', '01064647330', '21E2', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(703, 11816, 'فهمى رائد فهمى عبد السلام', NULL, NULL, NULL, '21K7', '01158883844', '21K7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(704, 11817, 'امجد محمد مدبولى عثمان', NULL, NULL, NULL, '21F9', '01285000800', '21F9', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(705, 11818, 'محمد خالد سيد حنفى', NULL, NULL, NULL, '1998', '01000335835', '1998', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(706, 11819, 'محمد فتح الله عبدالرحمن محمدين', NULL, NULL, NULL, '2063', '01116896366', '2063', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(707, 11820, 'راوي صلاح سيد عبد الوهاب', NULL, NULL, NULL, '2022', '01004170155', '2022', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(708, 11821, 'محمد حسن عبد الحميد على ريان', NULL, NULL, NULL, '20E8', '01226155185', '20E8', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(709, 11822, 'هشام سمير عبد الباقى بدوى', NULL, NULL, NULL, '20G2', '01050507802', '20G2', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(710, 11823, 'حسن شحاته محمد محمد على', NULL, NULL, NULL, '20E4', '01003302938', '20E4', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:02'),
(711, 11824, 'محمد صبحى عبد الحميد محمد', NULL, NULL, NULL, '20F8', '01118413153', '20F8', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(712, 11825, 'ايمن على فرج موسى', NULL, NULL, NULL, '2076', '01100039919', '2076', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(713, 11826, 'محمود عبد الرحمن جمعه عبد الرحمن', NULL, NULL, NULL, '2077', '01068035876', '2077', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(714, 11827, 'محمد نبيل محمود على', NULL, NULL, NULL, '2000', '01011934880', '2000', 'ادارة اللوجيستك', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(715, 11828, 'احمد سليمان محمد سليمان', NULL, NULL, NULL, '2021', '01555707638', '2021', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(716, 11829, 'محمد يسري عبد العظيم عبد النبي', NULL, NULL, NULL, '2013', '01111123467', '2013', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(717, 11830, 'حسن ابراهيم توفيق مرسي', NULL, NULL, NULL, '2067', '01141045397', '2067', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(718, 11831, 'محمد عبد المعز بخيت احمد', NULL, NULL, NULL, '20B5', '01029400164', '20B5', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(719, 11832, 'محمد وجيه محمد كامل', NULL, NULL, NULL, '20C0', '01011010789', '20C0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(720, 11833, 'احمد مصطفى شحاته', NULL, NULL, NULL, '0505', '01009996171', '0505', 'ادارة اللوجيستك', 176, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:28', '2026-05-31 12:03:04'),
(721, 11834, 'عماد عبد العزيز جودة السيد', NULL, NULL, NULL, '1853', '01011953385', '1853', 'ادارة اللوجيستك', 177, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(722, 11835, 'مصطفى سعيد عبد العزيز مرسى', NULL, NULL, NULL, '20C3', '01068313050', '20C3', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05');
INSERT INTO `users` (`id`, `system_id`, `name`, `name_en`, `name_ar`, `image`, `email`, `phone`, `machine_code`, `department_name`, `job_title_id`, `learner_type`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(723, 11836, 'عبد المنعم صلاح عبد المنعم سليمان', NULL, NULL, NULL, '20f6', '01227860703', '20f6', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(724, 11837, 'عبد الحليم شحاته عبد الحليم ابراهيم', NULL, NULL, NULL, '20F7', '01220101360', '20F7', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(725, 11838, 'سعد السيد سعد احمد', NULL, NULL, NULL, '20B4@2begypt.com', '01121914829', '20B4', 'ادارة اللوجيستك', 178, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(726, 11839, 'توفيق ابراهيم توفيق مرسي', NULL, NULL, NULL, '19h6', '01006303669', '19h6', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(727, 11840, 'محمود السيد علي ابراهيم', NULL, NULL, NULL, '19i8', '01226489275', '19i8', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(728, 11841, 'احمد على محمد فرج', NULL, NULL, NULL, '19h5', '01016776607', '19h5', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(729, 11842, 'احمد السيد احمد السيد خطاب', NULL, NULL, NULL, '1940', '01012440936', '1940', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(730, 12023, 'امير خالد بصير محمد احمد', NULL, NULL, NULL, '19D6', '01068647068', '19D6', 'ادارة اللوجيستك', 177, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:29', '2026-05-31 12:03:05'),
(731, 12024, 'محمد محمد عبدالعزيز', NULL, NULL, NULL, '1909', '01147618882', '1909', 'ادارة اللوجيستك', 179, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(732, 12025, 'عمرو احمد انور علي', NULL, NULL, NULL, '1898', '01153795949', '1898', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(733, 12026, 'احمد عمر محمد رجب', NULL, NULL, NULL, '18D6', '01288548414', '18D6', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(734, 12027, 'محمود سيد عبد اللطيف عماره', NULL, NULL, NULL, '18D7', '012860661892- 01285120671', '18D7', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(735, 12028, 'مصباح فتحي علي محمد بكر', NULL, NULL, NULL, '1836', '01008200466', '1836', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(736, 12029, 'احمد محمد حسن عبد الحميد', NULL, NULL, NULL, '1864', '01003512093', '1864', 'ادارة اللوجيستك', 180, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(737, 12030, 'خالد جلال محمد عبد الغفار', NULL, NULL, NULL, '18A8', '01100169014', '18A8', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(738, 12031, 'احمد سعد سيد احمد', NULL, NULL, NULL, '18A0', '01220664270-01113077851', '18A0', 'ادارة اللوجيستك', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(739, 12032, 'محمد صابر ابو ضيف احمد', NULL, NULL, NULL, '18B6', '01117763136', '18B6', 'ادارة اللوجيستك', 178, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(740, 12033, 'وليد يوسف حجاج مصطفى', NULL, NULL, NULL, '1728', '01225180211', '1728', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:30', '2026-05-31 12:03:07'),
(741, 12034, 'حسين فايز مسعود عبد الله', NULL, NULL, NULL, '1761', '01154002996', '1761', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(742, 12035, 'ابراهيم توفيق حلمي توفيق', NULL, NULL, NULL, '1759', '01207253513- 01010500488', '1759', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(743, 12036, 'ياسر شوقي السيد علي', NULL, NULL, NULL, '1739', '01022181334', '1739', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(744, 12037, 'عبد الرحمن صديق محمد عبد العال', NULL, NULL, NULL, '1702', '01023635316', '1702', 'ادارة اللوجيستك', 181, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(745, 12038, 'احمد عاطف صالح احمد', NULL, NULL, NULL, '1784', '01155585957', '1784', 'ادارة اللوجيستك', 180, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(746, 12039, 'محمد جامع عبد المعطي حسين', NULL, NULL, NULL, '1747', '01280701779', '1747', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(747, 12040, 'سعيد علي جاد الرب علي', NULL, NULL, NULL, '1760', '01000828725', '1760', 'ادارة اللوجيستك', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(748, 12041, 'ريمون داود شوقي بساليوس', NULL, NULL, NULL, '1778', '01229062159', '1778', 'ادارة اللوجيستك', 144, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(749, 12042, 'اسامه كامل راشد غبريال', NULL, NULL, NULL, '1656', '01098886170', '1656', 'ادارة اللوجيستك', 177, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(750, 12043, 'محمد عمار مطاوع فراج', NULL, NULL, NULL, '1628', '01064321144', '1628', 'ادارة اللوجيستك', 182, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:31', '2026-05-31 12:03:08'),
(751, 12044, 'مصطفى محمد عبد الفتاح احمد خليل', NULL, NULL, NULL, '1641', '01009996092', '1641', 'ادارة اللوجيستك', 183, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(752, 12045, 'ابراهيم محمد احمد حسن', NULL, NULL, NULL, '1495', '01098877297', '1495', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(753, 12046, 'السيد على سليمان على الشحات', NULL, NULL, NULL, '1479', '01024251717', '1479', 'ادارة اللوجيستك', 180, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(754, 12047, 'هانى يوسف اسماعيل حسانين', NULL, NULL, NULL, '1405', '01024251524', '1405', 'ادارة اللوجيستك', 182, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(755, 12048, 'رضا محمود احمد حسين', NULL, NULL, NULL, '0851', '01022778678', '851', 'ادارة اللوجيستك', 172, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(756, 12049, 'تامر طه زكي طه', NULL, NULL, NULL, '0842', '01066651720', '0842', 'ادارة اللوجيستك', 172, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(757, 12050, 'محمد احمد محمود مغربى', NULL, NULL, NULL, '0736', '01002183314', '0736', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(758, 12051, 'خالد محمد حارص السيد', NULL, NULL, NULL, '0401', '01099922491', '401', 'ادارة اللوجيستك', 184, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(759, 12054, 'احمد محمود محمد محمود', NULL, NULL, NULL, '1916', '01011603626', '1916', 'اداره الحسابات', 185, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(760, 12055, 'مصطفي محمود حسن ابو العلا', NULL, NULL, NULL, '2593', '01015632103', '2593', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:09'),
(761, 12056, 'عبد الحميد جمال عبد الحميد جاد الكريم', NULL, NULL, NULL, '2236', '01270159995', '2236', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(762, 12057, 'ايمن احمد سالم احمد حسن ', NULL, NULL, NULL, '25a8', '01143520126', '25A8', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(763, 12058, 'هاجر كمال محمد البغدادى', NULL, NULL, NULL, '25A7', '01021088823', '25A7', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(764, 12059, 'سهام ابو عمره جمعة', NULL, NULL, NULL, '18H4', '01100760424', '18H4', 'ادارة اللوجيستك', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(765, 12060, 'خالد مصطفي عبد الظاهر ', NULL, NULL, NULL, '25A9', '1065341178', '25A9', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(766, 12061, 'محمد مصطفي محمد عبد الحميد ', NULL, NULL, NULL, 'MOJ@2B', '1129650404', '25B0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(767, 12062, 'احمد هاني محمد محمد', NULL, NULL, NULL, '', '01116905024', '18B9', 'ادارة المبيعات الداخلية', 186, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(768, 12063, 'محمود محمد على الشلقانى ', NULL, NULL, NULL, '25B1', '01118003614', '25B1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(769, 12064, 'جهاد رفعت احمد عبد السيمع', NULL, NULL, NULL, '25B2', '1105799358', '25B2', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(770, 12065, 'يارا محمد احمد النوبي', NULL, NULL, NULL, '25B3', '1154437013', '25B3', 'ادارة الاستراد ', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:32', '2026-05-31 12:03:11'),
(771, 12066, 'رحمه ياسر حمدي حنفي', NULL, NULL, NULL, '25b4', '01159058077', '25B4', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(772, 12067, 'محمد خالد الداودي السيد', NULL, NULL, NULL, '', '01070017021', '25B5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(773, 12068, 'عصام حمدي محمد عزت', NULL, NULL, NULL, '25B6', '1026369576', '25B6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(774, 12069, 'سيف محمد ناجي', NULL, NULL, NULL, '25B7', '1140004975', '25B7', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(775, 12070, 'محمد شريف محمد عبد المحسن ', NULL, NULL, NULL, '25b8', '01119591089', '25B8', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(776, 12071, 'زياد سامح عوض محمود', NULL, NULL, NULL, NULL, '01505098561', '25B9', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(777, 12072, 'ابو بكر الصديق احمد محمد سر الختم', NULL, NULL, NULL, '25A2', '30208071900775', '25A2', 'ادارة المبيعات الداخلية', 187, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(778, 12073, 'ساندى يوسف احمد يوسف', NULL, NULL, NULL, '2594', '1503007130', '2594', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(779, 12074, 'مصطفى مجدى سعد عبد الحق', NULL, NULL, NULL, '2250', '01018171256', '2250', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(780, 12075, 'داليا السيد عبد الجليل  ', NULL, NULL, NULL, '25c0', '1143072692', '25c0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:33', '2026-05-31 12:03:12'),
(781, 12076, 'احمد وليد سيد عبد النبي ', NULL, NULL, NULL, '25c1', '1095908110', '25c1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(782, 12077, 'جرجس رومانى وهيب عبد المسيح', NULL, NULL, NULL, '25c2', '1220164328', '25c2', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(783, 12078, 'سعد محمد سعد عبد السلام', NULL, NULL, NULL, '25c3', '1273499947', '25c3', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(784, 12079, 'هبة محمد عبد الرسول حسن', NULL, NULL, NULL, '18F9', '01110620279', '18F9', 'ادارة اللوجيستك', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(785, 12080, 'على حمدى على محمود', NULL, NULL, NULL, '25A5', '01111705235', '25A5', 'ادارة اللوجيستك', 173, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(786, 12081, 'ادهم علاء حسنى عبد  المقصود', NULL, NULL, NULL, '25C4', '01143410191', '25C4', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(787, 12082, 'سعيد اشرف سعيد الشحات', NULL, NULL, NULL, '25C5', '01093815933', '25C5', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(788, 12083, 'بيشوى ناصر رمزي حبيب', NULL, NULL, NULL, '25C6', '01009790899', '25C6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(789, 12084, 'محمد طلعت يوسف محمد', NULL, NULL, NULL, '25C7', '01147267084', '25C7', 'ادارة المبيعات الداخلية', 169, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(790, 12085, 'حبيبة سيد رضوان محمد محمد المعز', NULL, NULL, NULL, '', '01206057926', '2345', 'اداره المنتجات اكسسورات', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:13'),
(791, 12086, 'سلمي اسماعيل', NULL, NULL, NULL, '24i7', '0', '24i7', 'اداره المنتجات اكسسورات', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(792, 12087, 'محمد اشرف السيد عبد الله همام', NULL, NULL, NULL, '25c8', '01121552540', '25c8', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(793, 12088, 'أدمن للتجربة', NULL, NULL, NULL, 'mostafa1509733@miuegypt.edu.eg', '1110017724', '0000', 'اداره البرمجه', 175, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(794, 12089, 'سعيد مجدى سعيد عبد الله ', NULL, NULL, NULL, '25C9', '01099162321', '25C9', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(795, 12090, 'زينه عمرو محمد ابراهيم السردى', NULL, NULL, NULL, 'I251', '01204996694', 'I251', NULL, 188, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(796, 12091, 'هيا اسامه جمال الدين حامد', NULL, NULL, NULL, 'I252', '01123098147', 'I252', NULL, 188, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(797, 12092, 'محمد جمال حنفى محمود احمد', NULL, NULL, NULL, '25D0', '01012048858', '25D0', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(798, 12093, 'محمد سيد محمد محمد ', NULL, NULL, NULL, '25D8', '01105768834', '25D8', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(799, 12094, 'محمد نور الدين مصطفي غانم', NULL, NULL, NULL, '25D1', '01012713426', '25D1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(800, 12095, 'حسين عبد الفتاح حسين احمد', NULL, NULL, NULL, '25D5', '01025090163', '25D5', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:34', '2026-05-31 12:03:15'),
(801, 12096, 'تقى عادل عبد السلام احمد', NULL, NULL, NULL, '25D6', '01123544122', '25D6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(802, 12097, 'كارمينا يونان فهمي اسكندر', NULL, NULL, NULL, '25D2', '01275460098', '25D2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(803, 12098, 'محمد حسنى احمد عبد الخالق', NULL, NULL, NULL, '25D3', '01120847370', '25D3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(804, 12099, 'انس محمد فتح الله محمد علي ', NULL, NULL, NULL, '25D7', '01123636144', '25D7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(805, 12100, 'عبد الرحمن ايمن حسن حسين ', NULL, NULL, NULL, '25D4', '01062190066', '25D4', 'اداره المشتريات', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(806, 12101, 'مازن محمد محمود احمد زلط ', NULL, NULL, NULL, '25E0', '01112151862', '25E0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(807, 12102, 'محمود اشرف محمود حنفى عطا ', NULL, NULL, NULL, '25E1', '01144790260', '25E1', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(808, 12103, 'عبد الله هشام حسنى عبد الله', NULL, NULL, NULL, '25E2', '01027145522', '25E2', 'إدارة التسويق', 94, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(809, 12104, 'احمد محمد محمد عدلي', NULL, NULL, NULL, '25E3', '01225700776', '25E3', 'ادارة الاستراد ', 89, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(810, 12105, 'زياد محمد صبحي سيد سيد', NULL, NULL, NULL, '25E4', '01121168279', '25E4', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:35', '2026-05-31 12:03:17'),
(811, 12106, 'اسماعيل على ابراهيم', NULL, NULL, NULL, '25E5', '01105432082', '25E5', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(812, 12107, 'شهاب ايهاب ابراهيم خميس', NULL, NULL, NULL, '25E6', '01128822114', '25E6', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(813, 12108, 'عبد الرحمن خالد عبد المجيد محمود ', NULL, NULL, NULL, '25E7', '01125428182', '25E7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(814, 12109, 'شعبان رشاد عبد الجيد حسنين', NULL, NULL, NULL, '25E8', '01111785074', '25E8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(815, 12110, 'مصطفي عيد سمير موسي', NULL, NULL, NULL, '25E9', '01126801393', '25E9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(816, 12111, 'محمد احمد شديان رويحل', NULL, NULL, NULL, '25f1', '01068183446', '25f1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(817, 12112, 'مينا عادل رفعت نصري', NULL, NULL, NULL, '25f0', '01070605114', '25F0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(818, 12113, 'زياد شريف عبد الفاضل', NULL, NULL, NULL, '25f2', '01157962785', '25f2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(819, 12114, 'شريف هانى عبدالمنعم محمد', NULL, NULL, NULL, '24A0', '01203130937', '24A0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(820, 12115, 'مصطفى محمد احمد احمد على', NULL, NULL, NULL, '25f3', '01227537491', '25F3', 'ادارة المبيعات الداخلية', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:36', '2026-05-31 12:03:19'),
(821, 12116, 'يوسف مصطفى محمد شحاته نعيم', NULL, NULL, NULL, '25f4', '01203395603', '25F4', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(822, 12117, 'ممدوح هانى محمد ممدوح عبد الوهاب ', NULL, NULL, NULL, '25f5', '01016930183', '25f5', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(823, 12118, 'خلود ناصر احمد جلال', NULL, NULL, NULL, '25f6', '01010440991', '25f6', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(824, 12119, 'ضياء محمد اسماعيل محمود', NULL, NULL, NULL, '21N0@2b.com', '01055337447', '21N0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(825, 12120, 'امين محمد محمد امان صافى', NULL, NULL, NULL, '18B2@2b.com', '01152686030', '18B2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(826, 12121, 'موده سيد نشات الدسوقي كمال', NULL, NULL, NULL, '25F7', '01090076104', '25F7', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(827, 12122, 'محمد انور عبد الغنى حسن', NULL, NULL, NULL, '25F8', '01005600487', '25F8', 'اداره المشتريات', 151, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(828, 12123, 'محمد مصطفى محمد امين ', NULL, NULL, NULL, '25F9', '01111994873', '25F9', 'ادارة مبيعات الجملة', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(829, 12124, 'ايمان احمد محمد جابر', NULL, NULL, NULL, '25G3', '01021964338', '25G3', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(830, 12125, 'محمد ياسر سيد محمد', NULL, NULL, NULL, '25G1', '01099943576', '25G1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:24'),
(831, 12132, 'شريف محمد سيد محمود ', NULL, NULL, NULL, '25G2', '01153783446', '25G2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(832, 12133, 'عمر خالد سعيد عبد الحليم ', NULL, NULL, NULL, '25G4', '01030094562', '25G4', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(833, 12134, 'كريم حمايه ابراهيم حسن', NULL, NULL, NULL, '25G5', '01060835018', '25G5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(834, 12135, 'محمد هشام محمود ابراهيم', NULL, NULL, NULL, '', '01556123791', '25G6', 'اداره المشتريات', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(835, 12136, 'محمد مصطفى محمد محمود', NULL, NULL, NULL, '25G7', '01024004950', '25G7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(836, 12137, 'بيتر منير حنا', NULL, NULL, NULL, '25G9', '1154400815', '25G9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(837, 12138, 'هاجر صبحي محمد محمد', NULL, NULL, NULL, '25G8', '01116825232', '25G8', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(838, 12139, 'احمد حسام الدين محمود', NULL, NULL, NULL, '25H0', '1119372229', '25H0', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(839, 12140, 'نرمين حماده احمد محمود', NULL, NULL, NULL, '25H1', '1099662747', '25H1', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(840, 12141, 'اسماعيل حسن يونس', NULL, NULL, NULL, '25H2', '01090204012', '25H2', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:37', '2026-05-31 12:03:27'),
(841, 12145, 'محمد صلاح منصور', NULL, NULL, NULL, '25H3', '01030869534', '25H3', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(842, 12146, 'محمد يحيي سالم محمد', NULL, NULL, NULL, '25H4', '0103949263', '25H4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(843, 12147, 'احمد محمود ربيع محمد المطرى ', NULL, NULL, NULL, '25H5', '01013230986', '25H5', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(844, 12148, 'ياسين خضري', NULL, NULL, NULL, '25H6', '012346789', '25H6', 'ادارة المبيعات الداخلية', 89, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(845, 12149, 'سلوفينى', NULL, NULL, NULL, '25H7', '01234567', '25H7', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(846, 12150, 'حمزه', NULL, NULL, NULL, '25H8', '0123212123', '25H8', 'ادارة التحول الرقمي', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(847, 12151, 'محمود محمد عبد العليم حماد', NULL, NULL, NULL, '25H9', '01279820744', '25H9', 'إدارة التسويق', 99, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(848, 12152, 'عبد الرحمن سامى عبد الحكم الفقي', NULL, NULL, NULL, '25i0', '01125744453', '25i0', 'اداره الحسابات', 65, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(849, 12153, 'احمد  هانى سعد علي يسن', NULL, NULL, NULL, '25I1', '01111713877', '25I1', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(850, 12154, 'خالد صابر مكاوى عوض', NULL, NULL, NULL, '25i2', '01126296159', '25i2', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-08-14 18:30:38', '2026-05-31 12:03:29'),
(852, 12155, 'محمود شريف محمود سعد', NULL, NULL, NULL, '25I3', '01221523884', '25I3', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(853, 12156, 'احمد مجدى محمد احمد', NULL, NULL, NULL, '25I4', '01149484898', '25I4', 'ادارة مبيعات الجملة', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(854, 12157, 'فاطمه مجدى على حسين', NULL, NULL, NULL, '25I5', '01069289238', '25I5', 'ادارة المبيعات الداخلية', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(855, 12158, 'بلال حسن عبد الله مرسى', NULL, NULL, NULL, '25I6', '01112890666', '25I6', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(856, 12159, 'دعاء فواد سيد عبد الله', NULL, NULL, NULL, '25I7', '01119310122', '25I7', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(857, 12160, 'محمود حسن سعد احمد شعرواى ', NULL, NULL, NULL, '25i8', '01200336395', '25I8', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(858, 12161, 'ميشيل غايث رمسيس عطاالله', NULL, NULL, NULL, '25I9', '01277190520', '25I9', 'إدارة التسويق', 189, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(859, 12162, 'محمد احمد  ابراهيم معوض', NULL, NULL, NULL, '25J0', '01021733674', '25J0', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(860, 12163, 'رضوى وائل عبده محمود', NULL, NULL, NULL, '25J1', '01055758290', '25J1', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(861, 12164, 'محمد عبد الحميد محمد عبد المجيد', NULL, NULL, NULL, '', '01015602705', '25J2', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:13', '2026-05-31 12:03:30'),
(862, 12165, 'سما شريف توفيق وهبي', NULL, NULL, NULL, '25J3', '01030998680', '25J3', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(863, 12166, 'عبد الرحمن صديق محمد صديق', NULL, NULL, NULL, '25J5', '01092775571', '25J5', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(864, 12167, 'منه الله عصام محمد عبد الوهاب', NULL, NULL, NULL, '25J6', '01152771387', '25J6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(865, 12168, 'مروان محمد فاروق على ', NULL, NULL, NULL, '25J7', '01014736828', '25J7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(866, 12169, 'محمد احمد محمد حسين الديب', NULL, NULL, NULL, '25J8', '01001108432', '25J8', 'اداره الحسابات', 67, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(867, 12170, 'هاجر مصطفى حسانين محمد حسن ', NULL, NULL, NULL, '25J9', '1224881828', '25J9', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(868, 12171, 'شريف كامل محمد البكري', NULL, NULL, NULL, '25k0', '01145741949', '25k0', 'ادارة الاستراد ', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(869, 12172, 'يوسف حسن عبد الجواد حسين', NULL, NULL, NULL, '25K1', '01281283978', '25k1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(870, 12173, 'بهاء الدين محمد بهاء الدين', NULL, NULL, NULL, '25K2', '01129999795', '25K2 ', 'ادارة التحول الرقمي', 44, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(871, 12174, 'مهند ايمن عبد المولي محمد دسيوقى ', NULL, NULL, NULL, '25k3', '1140755351', '25k3', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:14', '2026-05-31 12:03:32'),
(872, 12175, 'رنا حسام حسن على', NULL, NULL, NULL, '25k4', '1119259911', '25k4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:33'),
(873, 12176, 'عبد الرحمن ايهاب محمود احمد ', NULL, NULL, NULL, '25k5', '1063461654', '25k5', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:33'),
(874, 12177, 'سامى محمود السيد فرج', NULL, NULL, NULL, '25k6', '1104634417', '25k6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(875, 12178, 'نور الدين محمد  ابراهيم هيكل ', NULL, NULL, NULL, '25K7', '1155734552', '25K7', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(876, 12179, 'كيرلس عطا الله رزق عطا الله', NULL, NULL, NULL, '25k8', '1503838447', '25k8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(877, 12180, 'مهاب احمد الصاوى احمد ', NULL, NULL, NULL, '25K9', '1288855674', '25K9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(878, 12181, 'احمد العشري بخيت ', NULL, NULL, NULL, '25L0', '1010949601', '25L0', 'اداره المشتريات', 80, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(879, 12182, 'محمد ياسر سمير', NULL, NULL, NULL, '25L1', '1153631871', '25L1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-10 11:12:15', '2026-05-31 12:03:34'),
(1760, 12183, 'كريم محمد عبد الرحمن محمود', NULL, NULL, NULL, '25L2', '1099341667', '25L2', 'ادارة المبيعات الداخلية', 187, 'online', 'active', NULL, NULL, NULL, NULL, '2025-09-13 09:51:41', '2026-05-31 12:03:34'),
(1761, 1, 'بيتر محسن حسني رياض', NULL, NULL, NULL, 'admin@2b.com', '01021053844', '1610', 'ادارة الموارد البشرية', 35, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:01:29', '2026-05-31 12:01:18'),
(1762, 5, 'مصطفي احمد جامع جمعة', NULL, NULL, NULL, 'Mostafa.ahmed@2b.com.eg', '01110017724', '2297', 'ادارة التحول الرقمي', 36, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:01:29', '2026-05-31 12:01:18'),
(1765, 12184, 'ايناس عبد الله بيومي', NULL, NULL, NULL, '25L3', '1117197567', '25L3', 'إدارة التسويق', 100, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:26', '2026-05-31 12:03:34'),
(1766, 12185, 'احمد عبد الله على محمد قوره', NULL, NULL, NULL, '25L4', '1140535768', '25L4', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1767, 12186, 'ياسين احمد اسماعيل محمد', NULL, NULL, NULL, '25L5', '1066082712', '25L5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1768, 12187, 'محمد سمير محمد عبد الحليم ', NULL, NULL, NULL, '25L6', '1140451990', '25L6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1769, 12188, 'احمد محمد محمود محمد عبد البارى', NULL, NULL, NULL, '25L7', '1099703334', '25L7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1770, 12189, 'الحسن مدحت محمد هلال ', NULL, NULL, NULL, '25L8', '1115981057', '25L8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1771, 12190, 'عبد الله اشرف حسام على ', NULL, NULL, NULL, '25L9', '1033968406', '25L9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1772, 12191, 'محمد سيد رسلان سيف الدين', NULL, NULL, NULL, '25M0', '1030931603', '25M0', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1773, 12192, 'احمد اسماعيل اسماعيل محمد على ', NULL, NULL, NULL, '25M1', '1025592703', '25M1', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1774, 12193, 'عمر حازم محمد سيد ', NULL, NULL, NULL, '25M2', '1112843269', '25M2', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1775, 12194, 'محمد مصطفى فراج عبد الله', NULL, NULL, NULL, '25M3', '1012766898', '25M3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:29', '2026-05-31 12:03:35'),
(1776, 12195, 'عاصم سمير عبد الحميد', NULL, NULL, NULL, '25M4', '01008166130', '25M4', 'ادارة اللوجيستك', 183, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1777, 12196, 'ممدوح وليد ممدوح مصطفى  ', NULL, NULL, NULL, '25U1', '01050730853', '25U1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1778, 12197, 'محمد محمود احمد محمد شاور', NULL, NULL, NULL, '25M6', '01280198696', '25M6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1779, 12198, 'اسلام رضا عباس محمد', NULL, NULL, NULL, '25M5', '01080562478', '25M5', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1780, 12199, 'احمد عمر ناصر على', NULL, NULL, NULL, '25N2', '01140564465', '25N2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1781, 12203, 'عبد الرحمن اشرف عوض حسن', NULL, NULL, NULL, '25P0', '1225150915', '25P0', 'ادارة مبيعات الجملة', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1782, 12207, 'يوسف احمد يوسف سليم', NULL, NULL, NULL, '25N5', '01105174697', '25N5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1783, 12208, 'خالد محمود سعيد محمد', NULL, NULL, NULL, '25N6', '01210087985', '25N6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1784, 12209, 'ناديه اسماعيل رمضان محمود', NULL, NULL, NULL, '25P3', '1147762615', '25P3', 'ادارة المبيعات الداخلية', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1785, 12210, 'عبد الرحمن محمد احمد محمد', NULL, NULL, NULL, '25N7', '01102306116', '25N7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:31', '2026-05-31 12:03:36'),
(1786, 12211, 'مصطفي محمد زيدان', NULL, NULL, NULL, '25N8', '01500078054', '25N8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1787, 12212, 'مصطفى عبد الفتاح ابراهيم ', NULL, NULL, NULL, '25P4', '01554140035', '25P4', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1788, 12213, 'رضوى سيد مرسي', NULL, NULL, NULL, '25N9', '01114115856', '25N9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1789, 12214, 'محمود عبد الله محمد', NULL, NULL, NULL, '25P5@2b.com', '01018824363', '25P5', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1790, 12215, 'محمد احمد محمد بيومي', NULL, NULL, NULL, '25O0', '1126506699', '25O0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1791, 12216, 'احمد علي علي حسين', NULL, NULL, NULL, NULL, '01107355365', '25P6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1792, 12217, 'حسين البدري محمود', NULL, NULL, NULL, '25O1', '1010688224', '25O1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1793, 12218, 'اسراء اشرف يوسف عزيز', NULL, NULL, NULL, '25P1', '1102281378', '25P1', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1794, 12219, 'ياسيمن احمد سعيد عطيه ', NULL, NULL, NULL, '25P2', '1065564236', '25P2', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1795, 12220, 'عبد الله محسن عبدالله', NULL, NULL, NULL, '25O4', '01153214880', '25O4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:33', '2026-05-31 12:03:38'),
(1796, 12221, 'مصطفى عطيه عبد الحفيظ', NULL, NULL, NULL, '25P7', '1121506005', '25P7', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:35', '2026-05-31 12:03:39'),
(1797, 12222, 'حسن خالد حسن عبد المجيد', NULL, NULL, NULL, '25O2@2b.com', '1107319075', '25O2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:35', '2026-05-31 12:03:39'),
(1798, 12223, 'مياده كرم عيد سليم', NULL, NULL, NULL, '25O5', '1508284214', '25O5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:35', '2026-05-31 12:03:39'),
(1799, 12224, 'عبد الرحمن مومن محمود', NULL, NULL, NULL, '25O3', '11257759970', '25O3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:35', '2026-05-31 12:03:39'),
(1800, 12225, 'اميره محمد حسين حسن', NULL, NULL, NULL, '25o6', '01093149894', '25o6', 'إدارة التسويق', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:03:35', '2026-05-31 12:03:39'),
(1801, 4509, 'هاني جريده', NULL, NULL, NULL, '233', '01111001', '1000', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-05 11:07:05', '2026-05-31 12:02:10'),
(1802, 1980, 'احمد زيدان حسن زيدان ', NULL, NULL, NULL, 'Traning@2b', '01115729032', '2531', 'ادارة الموارد البشرية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:31:54', '2026-05-31 12:01:31'),
(1803, 12226, 'مروه سيد حافظ محمد عبد الله', NULL, NULL, NULL, '25O7', '01125045971', '25O7', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:25', '2026-05-31 12:03:39'),
(1804, 12227, 'فرح على عبد السلام السيد', NULL, NULL, NULL, '25O8', '01004723202', '25O8', 'ادارة الموقع الالكترونى', 48, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:25', '2026-05-31 12:03:39'),
(1805, 12228, 'سمر محمد محمد مهدى', NULL, NULL, NULL, '25O9', '01152104535', '25O9', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:25', '2026-05-31 12:03:39'),
(1806, 12229, 'ياسر رمضان طه عبد الرحيم ', NULL, NULL, NULL, '9015', '01066651726', '9015', 'ادارة المبيعات الداخلية', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:25', '2026-05-31 12:03:39'),
(1807, 12230, 'ندى محمد عزت', NULL, NULL, NULL, '25P8', '1095598851', '25P8', 'ادارة مبيعات الشركات', 123, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:25', '2026-05-31 12:03:39'),
(1808, 12231, 'مريم اشرف محمد', NULL, NULL, NULL, '25P9', '1154387408', '25P9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1809, 12232, 'زينب عبد الناصر محمدى', NULL, NULL, NULL, '25Q0', '1004025761', '25Q0', 'ادارة المبيعات الداخلية', 158, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1810, 12233, 'احمد محمد عبده رشاد', NULL, NULL, NULL, '25Q1', '1111466728', '25Q1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1811, 12234, 'ربيع يسن عبد الصادق', NULL, NULL, NULL, '25Q2', '1060572372', '25Q2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1812, 12235, 'مصطفى محمد عبد الملك', NULL, NULL, NULL, '25Q3', '1150854056', '25Q3', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1813, 12236, 'محمد جمال محمد محمد', NULL, NULL, NULL, '25Q4', '1550840583', '25Q4', 'ادارة الموقع الالكترونى', 190, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1814, 12237, 'طارق محمد احمد عبد الحميد', NULL, NULL, NULL, '25Q5', '1055812162', '25Q5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1815, 12238, 'امجد حمدي صالح', NULL, NULL, NULL, NULL, '1123747702', '25Q6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1816, 12239, 'محمود احمد عبدالهادي الصاوي', NULL, NULL, NULL, '25Q7', '1016184302', '25Q7', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1817, 12240, 'كريم سيد فهمى عبد العزيز', NULL, NULL, NULL, '25Q8', '1112901911', '25Q8', 'ادارة اللوجيستك', 174, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:27', '2026-05-31 12:03:40'),
(1818, 12241, 'يوسف حسام حامد حموده ', NULL, NULL, NULL, '25Q9 ', '01114900079', '25Q9 ', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:29', '2026-05-31 12:03:41'),
(1819, 12242, 'محمد علاء الدين محمد', NULL, NULL, NULL, '25u0', '01001787872', '25u0', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-10-26 08:33:29', '2026-05-31 12:03:41'),
(1820, 12243, 'فهد عبد الروف حسن حسن', NULL, NULL, NULL, '25u2', '0155888838', '25u2', 'ادارة الموقع الالكترونى', 130, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1821, 12244, 'سالى سمير محمد ذو الغنى', NULL, NULL, NULL, '25U3', '01156603688', '25U3', 'ادارة مبيعات الشركات', 109, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1822, 12245, 'محمد ابراهيم على على', NULL, NULL, NULL, '25U4@2b.com', '1151971175', '25U4', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1823, 12246, 'محمود عادل احمد نوفل', NULL, NULL, NULL, '25U6', '1224010486', '25U6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1824, 12247, 'محمد ابو العزايم احمد احمد خليفه', NULL, NULL, NULL, '25u7', '1067345155', '25u7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1825, 12248, 'سيعد عزت سعيد  عبد الرحمن  ', NULL, NULL, NULL, '25U5@2begypt.com', '1123997043', '25U5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1826, 12249, 'محمد عاطف ثابت حسن', NULL, NULL, NULL, '25u8', '01276545283', '25u8', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1827, 12250, 'مى نبيل محمد عويس', NULL, NULL, NULL, '25U9', '01221870940', '25U9', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:44', '2026-05-31 12:03:41'),
(1828, 12251, 'جاسر هشام منير محمد على', NULL, NULL, NULL, '25V0', '01155453349', '25V0', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1829, 12252, 'احمد محفوظ سليمان على', NULL, NULL, NULL, '25V1', '01152083613', '25V1', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1830, 12253, 'عبد الله خالد سعد محمد محمد', NULL, NULL, NULL, '25V2', '01064432948', '25V2', 'اداره الحسابات', 59, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1831, 12254, 'محمد هشام يحيي زهدى', NULL, NULL, NULL, '25V3', '01550423354', '25V3', 'إدارة التسويق', 100, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1832, 12255, 'محمد عبد العادل العزب حماد', NULL, NULL, NULL, '25V4', '01026504209', '25V4', 'ادارة الموقع الالكترونى', 155, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1833, 12256, 'وسام عبد المنعم زكى احمد العنانى', NULL, NULL, NULL, '25V5', '01068966008', '25V5', 'الاداره العليا', 110, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1834, 12257, 'سيف الدين ياسر كمال محمود', NULL, NULL, NULL, '25V6', '1128252055', '25V6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1835, 12258, 'احمد سامى على احمد عاشور', NULL, NULL, NULL, '25V7', '1015536080', '25V7', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1836, 12259, 'احمد سعيد امين عبد العاطى', NULL, NULL, NULL, '', '1112017889', '25V8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1837, 12260, 'صهيب ناصر عبد المنعم عبد الله', NULL, NULL, NULL, '', '1126942091', '25V9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:45', '2026-05-31 12:03:43'),
(1838, 12261, 'مينا روءف رمسيس زكى', NULL, NULL, NULL, '25W0', '1277955430', '25W0', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1839, 12262, 'خالد رضا حمزه على ', NULL, NULL, NULL, '25W1', '1026908800', '25W1', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1840, 12263, 'شيماء ممدوح حلمى احمد على', NULL, NULL, NULL, '25W2', '1113746569', '25W2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1841, 12264, 'مريم محمود محمد حليم', NULL, NULL, NULL, '25w3', '1022917653', '25w3', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1842, 12265, 'مى صلاح محمد احمد', NULL, NULL, NULL, '25W4', '1149357917', '25w4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1843, 12266, 'عز مهدى محمد  شافعى', NULL, NULL, NULL, '25w5', '1063002987', '25w5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1844, 12267, 'جرجس ميلاد سعيد عبد المسيح', NULL, NULL, NULL, '25W6', '01065528816', '25W6', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1845, 12268, 'محمد صالح محمد محمود ', NULL, NULL, NULL, '25W7', '01272750503', '25W7', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1846, 12269, 'زهراء حازم محمد احمد', NULL, NULL, NULL, '25w8', '01090778880', '25w8', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44'),
(1847, 12270, 'منى محمود حسن ابو العلا ', NULL, NULL, NULL, '25W9', '01060481340', '25W9', 'ادارة الموقع الالكترونى', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:47', '2026-05-31 12:03:44');
INSERT INTO `users` (`id`, `system_id`, `name`, `name_en`, `name_ar`, `image`, `email`, `phone`, `machine_code`, `department_name`, `job_title_id`, `learner_type`, `status`, `last_active_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1848, 12271, 'عمر عبدالنبي محمد عبدالنبي', NULL, NULL, NULL, '25R0', '1113991185', '25R0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1849, 12272, 'بسنت هاني رزق محمد', NULL, NULL, NULL, '25R1', '1000300799', '25R1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1850, 12273, 'عمرو ابوسريع عبدالرازق سيد', NULL, NULL, NULL, '25R2@2b.com', '1018514303', '25R2', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1851, 12274, 'محمد احمد مصيلحى بدر', NULL, NULL, NULL, '25R3', '1002869967', '25R3', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1852, 12275, 'حسام الدين احمد حسني محمد', NULL, NULL, NULL, '25R4', '1014161817', '25R4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1853, 12276, 'رحمه صلاح زكي ابراهيم', NULL, NULL, NULL, '25R5', '1226856522', '25R5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1854, 12277, 'مريم علاء الدين عطيه حسين', NULL, NULL, NULL, '25R6', '1013452180', '25R6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1855, 12278, 'عبدالمعبود مسعود عبدالمعبود نبوي', NULL, NULL, NULL, '25R8', '1274847183', '25R8', 'ادارة المبيعات الداخلية', 191, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1856, 12279, 'محمد محمد السيد امام', NULL, NULL, NULL, '25R7', '1040994519', '25R7', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1857, 12280, 'محمد حسين عبدالرحمن محمد', NULL, NULL, NULL, '25S3', '1105065359', '25S3', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:49', '2026-05-31 12:03:45'),
(1858, 12281, 'محمد مهدي محمد سالم', NULL, NULL, NULL, '25S4', '1211132302', '25S4', 'ادارة اللوجيستك', 181, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1859, 12282, 'بسنت محمد ابراهيم حامد', NULL, NULL, NULL, '25S5', '1091865500', '25S5', 'إدارة التسويق', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1860, 12283, 'كريم وائل جلال ابراهيم', NULL, NULL, NULL, NULL, '1229051824', '25S6', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1861, 12284, 'سلمى انور محمد  انور', NULL, NULL, NULL, '25S2', '01156622311', '25S2', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1862, 12285, 'باسم عصام محمد محمد', NULL, NULL, NULL, '25S7', '01027073580', '25S7', 'ادارة التحول الرقمي', 42, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1863, 12286, 'منار امام عبد السميع عبد الفتاح', NULL, NULL, NULL, '25S8', '1006123019', '25S8', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1864, 12287, 'دينا ماهر شحاته عاشور', NULL, NULL, NULL, '25T9', '1029785813', '25T9', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1865, 12288, 'مصطفي اشرف مصطفي ابوغربية', NULL, NULL, NULL, '25S9', '1095542250', '25S9', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1866, 12289, 'حازم ابراهيم موسى حسن', NULL, NULL, NULL, '25T0', '1020200501', '25T0', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1867, 12290, 'أحمد محمد خديوى أحمد', NULL, NULL, NULL, '25T2', '1013165799', '25T2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:51', '2026-05-31 12:03:47'),
(1868, 12291, 'حسين هاني حسين احمد', NULL, NULL, NULL, NULL, '1152647079', '25T3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1869, 12292, 'محمود إمام عبد المنعم حسين', NULL, NULL, NULL, '', '1093556416', '25T4', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1870, 12293, 'حسن كامل محمد السيد', NULL, NULL, NULL, '25T6', '1141692075', '25T6', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1871, 12294, 'بسنت احمد سيد مكاوى', NULL, NULL, NULL, '25X0', '1112213564', '25X0', 'ادارة الموقع الالكترونى', 192, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1872, 12295, 'السيد احمد السيد هيكل', NULL, NULL, NULL, '25T8', '1012957652', '25T8', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1873, 12296, 'عزالدين عبدالفتاح عزالرجال عبدالفتاح', NULL, NULL, NULL, '25T1', '1159473751', '25T1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1874, 12297, 'امانى محمد عبد السلام', NULL, NULL, NULL, '25T5', '111', '25T5', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1875, 12298, 'امجد ماهر صابر ', NULL, NULL, NULL, '2522', '01156644271', '2522', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1876, 12299, 'محسن محمد', NULL, NULL, NULL, '2446', '501093083535', '2446', 'ادارة اللوجيستك', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1877, 12300, 'احمد يوسف حسين يوسف', NULL, NULL, NULL, '25X1', '01099827707', '25X1', NULL, NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:53', '2026-05-31 12:03:48'),
(1878, 12301, 'كريم نور الدين عبد التواب محمد علي.', NULL, NULL, NULL, '25X2', '1095937072', '25X2', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:55', '2026-05-31 12:03:50'),
(1879, 12302, 'احمد محمد سيد عبد العزيز', NULL, NULL, NULL, '25x3', '1003325160', '25X3', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:55', '2026-05-31 12:03:50'),
(1880, 12303, 'سندس عماد محمد فرغلي', NULL, NULL, NULL, '25X4', '01018535686', '25X4', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:55', '2026-05-31 12:03:50'),
(1881, 12304, 'اسامه السعيد السعيد احمد', NULL, NULL, NULL, '', '1119591403', '25X5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2025-12-28 11:12:55', '2026-05-31 12:03:50'),
(1882, 4980632, 'محمد سعيد', NULL, NULL, NULL, '4980632@2b.com', '01098541100', 'N0CT', 'test', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:06:11', '2026-02-19 11:06:11'),
(1883, 16056, 'محمد سعيد', NULL, NULL, NULL, '16056@2b.com', '01098541100', 'OTSU', 'test', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:06:49', '2026-02-19 11:06:49'),
(1884, 12305, 'احمد محمد بكر ابراهيم', NULL, NULL, NULL, '25X6', '01033779726', '25X6', 'ادارة توبي السيستم', 193, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1885, 12306, 'ابراهيم فكري عبدالنبي حماد', NULL, NULL, NULL, '2601', '1091356480', '2601', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1886, 12307, 'أحمد سامى عبد الستار السيد', NULL, NULL, NULL, '', '1155501276', '2602', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1887, 12308, 'يوسف أحمد شافعي عبداللاه', NULL, NULL, NULL, '2603', '1122326876', '2603', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1888, 12309, 'سلمي هاني محمد ابراهيم', NULL, NULL, NULL, '2604', '1114038134', '2604', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1889, 12310, 'محمد عبد الله محمود السيد', NULL, NULL, NULL, '2605', '01212439730', '2605', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:07', '2026-05-31 12:03:50'),
(1890, 12311, 'احمد خالد جابر يمن', NULL, NULL, NULL, '', '1145191818', '2606', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1891, 12312, 'محمد خميس محمود أحمد', NULL, NULL, NULL, '2607', '1201344485', '2607', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1892, 12313, 'عبدالرحمن هشام احمد محمد', NULL, NULL, NULL, '2608', '1102584234', '2608', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1893, 12314, 'عمرو حافظ متولي امبابي محمد', NULL, NULL, NULL, '2609', '1110678010', '2609', 'اداره المشتريات', NULL, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1894, 12315, 'ابراهيم محمد  ابراهيم حسن', NULL, NULL, NULL, '2610', '01100506179', '2610', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1895, 12316, 'محمد هاني احمد حسين', NULL, NULL, NULL, '2611', '1024770002', '2611', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1896, 12317, 'ساره حسام الدين زغلول سيد', NULL, NULL, NULL, '2612', '1096067423', '2612', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1897, 12318, 'محمد أشرف علي محمد', NULL, NULL, NULL, '2613', '1022908241', '2613', 'ادارة اللوجيستك', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1898, 12319, 'ايه رمضان عبد المحسن عبد العزيز', NULL, NULL, NULL, '2614', '1069017692', '2614', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1899, 12320, 'إسلام علي عبدالباري عطيه', NULL, NULL, NULL, '2615', '1010994945', '2615', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:08', '2026-05-31 12:03:51'),
(1900, 12321, 'يسرى على ابراهيم خميس', NULL, NULL, NULL, '2616', '01015931740', '2616', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1901, 12322, 'احمد عبد الرحمن  عبد الغفار', NULL, NULL, NULL, '2617', '01033777516', '2617', 'اداره الحسابات', 68, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1902, 12323, 'عبد المحسن رمضان عبد الغنى', NULL, NULL, NULL, '2600', '01005332569', '2600', 'ادارة الشئون القانونية و الادارية', 116, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1903, 12324, 'سيف الدين الشحات عبد الموجود ابراهيم', NULL, NULL, NULL, '2619', '01012098879', '2619', 'ادارة الاستراد ', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1904, 12325, 'نهى احمد محمد الشبراوي ابراهيم', NULL, NULL, NULL, '2620', '01157690322', '2620', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1905, 12326, 'محمد السيد احمد مصطفي', NULL, NULL, NULL, '2621', '01023977417', '2621', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1906, 12327, 'محمد حامد حلمي السعيد العشري', NULL, NULL, NULL, '2622', '1226543072', '2622', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1907, 12328, 'مصطفي مجدي محمود ذكي', NULL, NULL, NULL, '2623', '1129340657', '2623', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1908, 12329, 'كريمان عمرو إسماعيل أبوضيف', NULL, NULL, NULL, '2624', '1553186669', '2624', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1909, 12330, 'شمس ناصر عبدالحميد بدر', NULL, NULL, NULL, '2625', '1148528986', '2625', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:10', '2026-05-31 12:03:52'),
(1910, 12331, 'اسلام خالد حامد شرباش', NULL, NULL, NULL, '2626', '01027952208', '2626', 'ادارة اللوجيستك', 181, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1911, 12332, 'علي محمد علي محمد', NULL, NULL, NULL, '2627', '1119030307', '2627', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1912, 12333, 'عمرو ياسر السيد محمد', NULL, NULL, NULL, '2628', '1069264721', '2628', 'إدارة التسويق', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1913, 12334, 'اسلام أشرف رزق ابراهيم', NULL, NULL, NULL, '2629', '1100909483', '2629', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1914, 12335, 'حسني محمد محمد حسني محمد الحملاوي', NULL, NULL, NULL, '2630', '1011340709', '2630', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1915, 12336, 'شهد ماهر ابراهيم العريني', NULL, NULL, NULL, '2631', '1116277233', '2631', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1916, 12337, 'منى رمضان صابر سيد', NULL, NULL, NULL, '2632', '1127864077', '2632', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1917, 12338, 'ادهم اشرف رمضان علي', NULL, NULL, NULL, '2633', '1157050658', '2633', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1918, 12339, 'فاطمة كمال الدين بديع الشاذلى', NULL, NULL, NULL, '2634', '1064178964', '2634', 'اداره المنتجات اكسسورات', 194, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1919, 12340, 'عبدالخالق مصطفي عبدالخالق مصطفي', NULL, NULL, NULL, '2635', '1101244463', '2635', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:11', '2026-05-31 12:03:53'),
(1920, 12341, 'ادهم محمد رمضان مصطفي', NULL, NULL, NULL, '2636', '1112686941', '2636', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1921, 12342, 'ملك محمد فواد فضالى', NULL, NULL, NULL, '2637', '01153555528', '2637', 'إدارة التسويق', 195, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1922, 12343, 'ريهام مجدي حنفي أمين', NULL, NULL, NULL, '2638', '1140295604', '2638', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1923, 12344, 'رانيا عز العرب سعد الدين عزالعرب', NULL, NULL, NULL, '2639', '1040523900', '2639', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1924, 12345, 'ناجي عاطف ابو العلا طلعت', NULL, NULL, NULL, NULL, '1098429203', '2640', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1925, 12346, 'عبدالرحمن سيد عبدالله حفني', NULL, NULL, NULL, '2641', '1554338718', '2641', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1926, 12347, 'احمد حسام مختار إسماعيل', NULL, NULL, NULL, '2642', '1149175844', '2642', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1927, 12348, 'يوسف احمد محمد احمد', NULL, NULL, NULL, '2643', '1272763525', '2643', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1928, 12349, 'محمد خالد حنفى حسن', NULL, NULL, NULL, '2644', '1020233368', '2644', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1929, 12350, 'محمد اشرف محمد ذكي', NULL, NULL, NULL, '2645', '26شارع مسجد التائبين سيد بشر اسكندريه', '2645', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:12', '2026-05-31 12:03:55'),
(1930, 12351, 'محمد صلاح عطية محمد', NULL, NULL, NULL, '2646', '1554043774', '2646', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1931, 12352, 'اسامه اشرف ابراهيم', NULL, NULL, NULL, '', '1558558085', '2648', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1932, 12353, 'عبدالله حمدي حماد زيان', NULL, NULL, NULL, '2647', '1026812398', '2647', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1933, 12354, 'أسماء جابر محمد خليل', NULL, NULL, NULL, '2649', '1100350225', '2649', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1934, 12355, 'ياسر محمود بكري أحمد', NULL, NULL, NULL, '2650', '1288666987', '2650', 'ادارة توبي السيستم', 196, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1935, 12356, 'سامح محمود مبارك شلقانى', NULL, NULL, NULL, '2651', '1094341976', '2651', 'ادارة اللوجيستك', 147, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1936, 12357, 'مريم كسبان عبدالنبي محمد', NULL, NULL, NULL, '2652', '1550311551', '2652', 'اداره المشتريات', 157, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1937, 12358, 'احمد تامر راتب عبد الحكيم', NULL, NULL, NULL, '2653', '1092122370', '2653', 'ادارة اللوجيستك', 197, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1938, 12359, 'محمدأحمد ياسين عباس', NULL, NULL, NULL, '2654', '01026724849', '2654', 'ادارة مبيعات الجملة', 141, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1939, 12360, 'محمود خالد كمال أحمد', NULL, NULL, NULL, '2656', '01110052258', '2656', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:13', '2026-05-31 12:03:56'),
(1940, 12361, 'هاجر محمد علاء الدين جمال الدين', NULL, NULL, NULL, '2657', '1115177468', '2657', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:14', '2026-05-31 12:03:57'),
(1941, 12362, 'عبدالرحمن بيومي فتحي بيومي', NULL, NULL, NULL, '', '1112742075', '2658', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:14', '2026-05-31 12:03:57'),
(1942, 12363, 'عمرو رضا محمد محمد', NULL, NULL, NULL, '2659', '01012830541', '2659', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:14', '2026-05-31 12:03:57'),
(1943, 12364, 'رادا جريده', NULL, NULL, NULL, '2670', '01006060510', '2670', 'ادارة الاستراد ', 198, 'online', 'active', NULL, NULL, NULL, NULL, '2026-02-19 11:10:14', '2026-05-31 12:03:57'),
(1944, 12365, 'شيماء حسين عطالله الشرقاوي', NULL, NULL, NULL, '2660', '1002918829', '2660', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1945, 12366, 'احمد حسام الدين عبد العظيم حسن', NULL, NULL, NULL, '', '1125226007', '2661', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1946, 12367, 'محمد اشرف محمد احمد', NULL, NULL, NULL, '2662', '1159115761', '2662', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1947, 12368, 'عبدالرحمن عمرو فتحي عبدالعزيز', NULL, NULL, NULL, '2663', '1100124696', '2663', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1948, 12369, 'حازم صلاح احمد عبد الرحيم', NULL, NULL, NULL, '2664', '1096500030', '2664', 'اداره المشتريات', 149, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1949, 12370, 'عمر محمد سعيد عبده', NULL, NULL, NULL, '2665', '1025830351', '2665', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:16', '2026-05-31 12:03:57'),
(1950, 12371, 'محمد اشرف عبدالله هاشم', NULL, NULL, NULL, '2666', '1141502836', '2666', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1951, 12372, 'مصطفى محمود عبدالعزيز أحمد', NULL, NULL, NULL, '2667', '1123863355', '2667', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1952, 12373, 'احمد محمد محمود محمد', NULL, NULL, NULL, '2668', '1033229585', '2668', 'اداره الحسابات', 60, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1953, 12374, 'كريم محمد محى شحاته', NULL, NULL, NULL, '2669', '1007759486', '2669', 'ادارة مبيعات الجملة', 137, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1954, 12375, 'كمال خالد سيد محمد', NULL, NULL, NULL, '', '01159300932', '2671', 'ادارة اللوجيستك', 174, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1955, 12376, 'عبد الرحمن حسين محمد عبد ربه', NULL, NULL, NULL, '2672', '01125842393', '2672', 'ادارة مبيعات الشركات', 117, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1956, 12377, 'عبد المنعم ابراهيم عبد المنعم ', NULL, NULL, NULL, '2675', '01142419349', '2675', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1957, 12378, 'طارق يوسف ابراهيم', NULL, NULL, NULL, '2618', '01113027358', '2618', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1958, 12379, 'بيشوى خالد نصيف الكسان', NULL, NULL, NULL, '2673', '01503767774', '2673', 'اداره الحسابات', 68, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1959, 12380, 'شيماء جمال محمد حسانين', NULL, NULL, NULL, '2674', '01101762269', '2674', 'إدارة التسويق', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:17', '2026-05-31 12:03:59'),
(1960, 12381, 'مى عادل عيد اسماعيل', NULL, NULL, NULL, '2676', '0125899917', '2676', 'ادارة بوفيه و نظافة', 112, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1961, 12382, 'Chatbot user', NULL, NULL, NULL, 'chatbot@2b.com.eg', '1111111111', 'chatbot', 'ادارة التحول الرقمي', 124, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1962, 12422, 'رغد صبري طلبه محمد', NULL, NULL, NULL, '2678', '1005893163', '2678', 'ادارة الموقع الالكترونى', 134, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1963, 12423, 'بسنت محمد جمال الدين', NULL, NULL, NULL, '2677', '01203430836', '2677', 'ادارة المبيعات الداخلية', 162, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1964, 12424, 'احمد محمد عبد المنعم جابر', NULL, NULL, NULL, '2679', '1098991102', '2679', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1965, 12425, 'اسلام محمد السيد حاته', NULL, NULL, NULL, '2680', '1029020239', '2680', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1966, 12426, 'سارة الشحات عبدالعزيز محمد', NULL, NULL, NULL, '2681', '1017209819', '2681', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1967, 12427, 'سمر فتحي طه عبد العزيز', NULL, NULL, NULL, '2682', '1119727775', '2682', 'ادارة الاستراد ', 98, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1968, 12428, 'ابراهيم محمد محمود حسن', NULL, NULL, NULL, '2683', '1100770645', '2683', 'ادارة اللوجيستك', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1969, 12429, 'احمد عادل وجدى محمدى', NULL, NULL, NULL, '2684', '01150395053', '2684', 'اداره البرمجه', 37, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:19', '2026-05-31 12:04:00'),
(1970, 12430, 'يوسف سيد جوده طه عسكر', NULL, NULL, NULL, '', '01097685302', '2685', 'ادارة اللوجيستك', 174, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1971, 12431, 'عبدالرحمن خالد محمد محمد', NULL, NULL, NULL, '2686 ', '1126080936', '2686 ', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1972, 12432, 'محمود باكوش عبد القادر مرسي', NULL, NULL, NULL, '2687', '1018710344', '2687', 'اداره المشتريات', 148, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1973, 12433, 'إيمان أحمد محمد أحمد', NULL, NULL, NULL, '2689', '1226291708', '2689', 'ادارة الاستراد ', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1974, 12434, 'حسن شعبان احمد اسماعيل', NULL, NULL, NULL, '2690', '1117737525', '2690', 'ادارة اللوجيستك', 197, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1975, 12435, 'احمد فكرى سيد على', NULL, NULL, NULL, '', '01009238382', '2691', 'الاداره العليا', 197, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1976, 12436, 'ماريو ميخائيل شاكر', NULL, NULL, NULL, '2693@2b.com.eg', '01010594984', '2693', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1977, 12437, 'محمد احمد ذكي علي', NULL, NULL, NULL, '', '1009183985', '2694', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1978, 12438, 'محمد أحمد قليعي مصطفي', NULL, NULL, NULL, '', '1008697697', '2695', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1979, 12439, 'محمد هانى محمد', NULL, NULL, NULL, '', '1111915864', '2696', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:20', '2026-05-31 12:04:02'),
(1980, 12440, 'محمد سعد يوسف عثمان', NULL, NULL, NULL, '', '1155517297', '2697', 'ادارة اللوجيستك', 174, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1981, 12441, 'كيرلس ممدوح سمير تلميذ', NULL, NULL, NULL, '', '1284444504', '2698', 'اداره المشتريات', 154, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1982, 12442, 'عبدالرحمن اشرف علي محمد', NULL, NULL, NULL, '', '1112788388', '2699', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1983, 12443, 'كريم محمد مراد', NULL, NULL, NULL, '', '01225019737', '26A0', 'ادارة اللوجيستك', 87, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1984, 12444, 'عمرو عماد محمد احمد', NULL, NULL, NULL, '', '1060334017', '26A1', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1985, 12465, 'عمر ايمن عبد المنعم السراج', NULL, NULL, NULL, NULL, '1025547775', '26A2', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1986, 12466, 'عبدالرحمن شعبان عبدالحميد حسن', NULL, NULL, NULL, '', '1117984310', '26A3', 'ادارة اللوجيستك', 197, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1987, 12467, 'هادي مصطفى شعبان عبد الخالق', NULL, NULL, NULL, '', '1000827001', '26A4', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1988, 12468, 'محمد عيد جوهري علي', NULL, NULL, NULL, '', '1279335008', '26A5', 'ادارة المبيعات الداخلية', 120, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1989, 12469, 'يوسف علي احمد محمود', NULL, NULL, NULL, '', '1008168870', '26A6', 'اداره المشتريات', 199, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:21', '2026-05-31 12:04:04'),
(1990, 12470, 'عمرو يوسف حسين محمد', NULL, NULL, NULL, '', '01154733024', '26A7', 'ادارة اللوجيستك', 197, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:22', '2026-05-31 12:04:05'),
(1991, 12471, 'عبد الرحمن محمد عطيه عثمان', NULL, NULL, NULL, '', '01136688195', '26A8', 'ادارة المبيعات الداخلية', 159, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:22', '2026-05-31 12:04:05'),
(1992, 12473, 'ياسمين ثروت احمد احمد', NULL, NULL, NULL, '', '01010875955', '26A9', 'ادارة الموقع الالكترونى', 132, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:22', '2026-05-31 12:04:05'),
(1993, 12474, 'سمر سمير ابو زيد سليمان', NULL, NULL, NULL, '', '01028298337', '26B0', 'اداره الحسابات', 63, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:22', '2026-05-31 12:04:05'),
(1994, 12475, 'السباعى محمد عبد السلام فايد', NULL, NULL, NULL, '', '01229702456', '26B1', 'اداره الحسابات', 62, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-24 10:43:22', '2026-05-31 12:04:05'),
(1995, 12476, 'عبدالله عز فرج محمد', NULL, NULL, NULL, 'ABDAALAH@gmail.com', '1025387195', '26B2', 'ادارة الموقع الالكترونى', 142, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-31 12:04:05', '2026-05-31 12:04:05'),
(1996, 12477, 'رنا ابراهيم النجار احمد', NULL, NULL, NULL, 'Rana@gmail.com', '1553531967', '26B3', 'ادارة الموقع الالكترونى', 131, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-31 12:04:05', '2026-05-31 12:04:05'),
(1997, 12478, 'منار يوسف صالح محمد', NULL, NULL, NULL, 'Mana@GMAIL.COM', '1118134811', '26B4', 'إدارة التسويق', 101, 'online', 'active', NULL, NULL, NULL, NULL, '2026-05-31 12:04:05', '2026-05-31 12:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `users_courses`
--

CREATE TABLE `users_courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users_courses`
--

INSERT INTO `users_courses` (`id`, `user_id`, `course_id`, `group_id`, `created_at`, `updated_at`) VALUES
(46, 793, 6, 20, NULL, NULL),
(47, 793, 7, 23, NULL, NULL),
(48, 1802, 6, 21, NULL, NULL),
(49, 793, 8, 26, NULL, NULL),
(50, 1802, 8, 26, NULL, NULL),
(51, 1761, 8, 26, NULL, NULL),
(52, 1762, 8, 26, NULL, NULL),
(53, 3, 8, 26, NULL, NULL),
(54, 4, 8, 26, NULL, NULL),
(55, 19, 8, 27, NULL, NULL),
(56, 21, 8, 27, NULL, NULL),
(57, 25, 6, 20, '2026-05-31 11:35:27', '2026-05-31 11:35:27'),
(58, 25, 8, 29, '2026-05-31 11:35:27', '2026-05-31 11:35:27'),
(59, 25, 9, 32, '2026-05-31 11:45:18', '2026-05-31 11:45:18'),
(61, 25, 7, 31, '2026-05-31 12:16:24', '2026-05-31 12:16:24'),
(62, 25, 10, 33, '2026-05-31 12:29:49', '2026-05-31 12:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_course_assignments`
--

CREATE TABLE `user_course_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_assignment_id` bigint(20) UNSIGNED NOT NULL,
  `user_file` varchar(191) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `score` varchar(191) DEFAULT NULL,
  `total_score` int(10) UNSIGNED DEFAULT NULL,
  `max_score` int(10) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_course_assignments`
--

INSERT INTO `user_course_assignments` (`id`, `user_id`, `course_assignment_id`, `user_file`, `feedback`, `score`, `total_score`, `max_score`, `submitted_at`, `reviewed_at`, `reviewed_by`, `created_at`, `updated_at`) VALUES
(5, 793, 6, 'UserCourseAssignment/liulDzephdLqOgyH9eLh7f13ee866d81802bd04c02b9f11db998.pdf', 'هذا الملف ليس كامل من فضلك تواصل مع الانستراكتور احمد زيدان', '60', NULL, NULL, NULL, NULL, NULL, '2026-05-09 05:07:01', '2026-05-09 05:34:13'),
(6, 793, 7, 'UserCourseAssignment/qC1HRA8MeQJtST3zjDQr97954ef169c2d38e03778bf83d43240c.pdf', 'ملف ممتاز', '95', NULL, NULL, NULL, NULL, NULL, '2026-05-09 05:07:23', '2026-05-09 06:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_course_assignment_answers`
--

CREATE TABLE `user_course_assignment_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_course_assignment_id` bigint(20) UNSIGNED NOT NULL,
  `course_assignment_question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answer`)),
  `awarded_score` int(10) UNSIGNED DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `user_course_evaluations`
--

CREATE TABLE `user_course_evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_machine_code` varchar(191) DEFAULT NULL,
  `user_department` varchar(191) DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `course_name` varchar(191) DEFAULT NULL,
  `instructor_id` bigint(20) UNSIGNED NOT NULL,
  `instructor_name` varchar(191) DEFAULT NULL,
  `evaluation_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `evaluation_category_name` varchar(191) DEFAULT NULL,
  `evaluation_id` bigint(20) UNSIGNED NOT NULL,
  `evaluation_title` varchar(191) DEFAULT NULL,
  `evaluation_type` varchar(191) DEFAULT '0',
  `answer` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_course_evaluations`
--

INSERT INTO `user_course_evaluations` (`id`, `user_id`, `user_machine_code`, `user_department`, `course_id`, `course_name`, `instructor_id`, `instructor_name`, `evaluation_category_id`, `evaluation_category_name`, `evaluation_id`, `evaluation_title`, `evaluation_type`, `answer`, `created_at`, `updated_at`) VALUES
(32, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 2, 'تقييم  المحاضر', 3, 'هل المحاضر على دراية بالمادة التعليمة التي يقوم بشرحها ؟', '5', '3', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(33, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 2, 'تقييم  المحاضر', 4, 'هل المحاضرشجع الحضور على الاشتراك وتبادل الاراء ؟', '5', '4', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(34, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 2, 'تقييم  المحاضر', 5, 'المدرب دعم المادة العلمية بتدريبات وأنشطة متنوعة وهادفة ووثيقة الصلة بموضوع التدريب ؟', '10', '7', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(35, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 2, 'تقييم  المحاضر', 6, 'قام المحاضر بتغطية كافة الاهداف المرجوة من التدريب ؟', '0', 'test', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(36, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 3, 'تقييم الكورس', 7, 'هل الماده التعليمية معدة بشكل جيد ؟', '5', '4', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(37, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 3, 'تقييم الكورس', 8, 'هل كان مضمون التدريب منظم وسهل المتابعه؟', '10', '7', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(38, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 3, 'تقييم الكورس', 9, 'اذكر/ اذكري النقاط الايجابية في التدريب وفي المحاضر ؟', '0', 'test', '2026-02-09 09:33:59', '2026-02-09 09:33:59'),
(39, 1801, '1000', NULL, 8, 'كورس الإدارة', 1, 'محمد سعيد', 4, 'NPS', 10, 'هل ترشح حضور زملاء اخرين لنفس الكورس مع المحاضر ؟', '10', '7', '2026-02-09 09:33:59', '2026-02-09 09:33:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_exams`
--

CREATE TABLE `user_exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `user_degree` double NOT NULL DEFAULT 0,
  `total_score` int(11) DEFAULT NULL,
  `max_score` int(11) NOT NULL DEFAULT 0,
  `status` varchar(191) NOT NULL DEFAULT 'success' COMMENT 'success or fail',
  `submission_status` varchar(16) NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_exams`
--

INSERT INTO `user_exams` (`id`, `user_id`, `course_id`, `exam_id`, `user_degree`, `total_score`, `max_score`, `status`, `submission_status`, `submitted_at`, `reviewed_at`, `reviewed_by`, `created_at`, `updated_at`) VALUES
(8, 1801, 7, 10, 100, NULL, 0, 'success', 'pending', NULL, NULL, NULL, '2026-02-03 10:19:31', '2026-02-03 10:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_exam_answers`
--

CREATE TABLE `user_exam_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_exam_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question` varchar(191) DEFAULT NULL,
  `answer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `awarded_score` int(11) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `answer_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answer_payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_exam_answers`
--

INSERT INTO `user_exam_answers` (`id`, `user_exam_id`, `question_id`, `question`, `answer_id`, `answer`, `is_correct`, `awarded_score`, `feedback`, `answer_payload`, `created_at`, `updated_at`) VALUES
(31, 8, 42, 'السؤال الأول : ما معنى الإدارة ؟', 140, 'المشروعات', 1, 0, NULL, NULL, '2026-02-03 10:19:31', '2026-02-03 10:19:31'),
(32, 8, 43, 'السؤال الأول : ما معنى التسويق؟', 144, 'المبيعات', 1, 0, NULL, NULL, '2026-02-03 10:19:31', '2026-02-03 10:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_forms`
--

CREATE TABLE `user_forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `machine_code` varchar(191) NOT NULL,
  `mark` bigint(20) NOT NULL DEFAULT 0,
  `duration` bigint(20) DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_forms`
--

INSERT INTO `user_forms` (`id`, `form_id`, `user_id`, `name`, `machine_code`, `mark`, `duration`, `start_at`, `created_at`, `updated_at`) VALUES
(6, 7, 1801, 'هاني جريده', '1000', 100, 20, '2026-02-10 23:26:44', '2026-02-10 19:26:44', '2026-02-10 19:46:33'),
(7, 7, 1802, 'أحمد زيدان', '2531', 67, 20, '2026-02-10 23:26:44', '2026-02-10 19:26:44', '2026-02-10 19:46:33'),
(8, 7, 793, 'أدمن للتجربة', '0000', 22, 1, '2026-04-27 17:14:42', '2026-04-27 11:14:42', '2026-04-27 11:15:28'),
(9, 8, 793, 'أدمن للتجربة', '0000', 0, 61, '2026-04-27 17:39:30', '2026-04-27 11:39:30', '2026-04-27 11:39:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_form_answers`
--

CREATE TABLE `user_form_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_form_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question` varchar(191) DEFAULT NULL,
  `answer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `is_true` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_form_answers`
--

INSERT INTO `user_form_answers` (`id`, `user_form_id`, `question_id`, `question`, `answer_id`, `answer`, `is_true`, `created_at`, `updated_at`) VALUES
(18, 6, 10, 'يسب', 34, 'يسب', 0, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(19, 6, 11, 'dsfsd', 37, 'نعم', 1, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(20, 6, 13, 'تيكست', NULL, 'فثسف', 1, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(21, 7, 10, 'يسب', 34, 'يسب', 0, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(22, 7, 11, 'dsfsd', 37, 'نعم', 1, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(23, 7, 13, 'تيكست', NULL, 'فثسف', 1, '2026-02-10 19:46:33', '2026-02-10 19:46:33'),
(24, 8, 10, 'يسب', 34, 'يب', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(25, 8, 11, 'dsfsd', 38, 'لا', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(26, 8, 13, 'تيكست', NULL, 'test', 1, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(27, 8, 14, 'aa', 42, 'd', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(28, 8, 15, 'يسب', 46, '-', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(29, 8, 16, 'Labore excepteur nem', 49, 'Proident fugiat rep', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(30, 8, 17, 'Possimus architecto', 51, 'نعم', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(31, 8, 18, 'Explicabo Vitae cil', 55, 'Enim ut fugit dolor', 1, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(32, 8, 19, 'Quo atque ut neque s', 58, 'لا', 0, '2026-04-27 11:15:28', '2026-04-27 11:15:28'),
(33, 9, 20, 'yes', 60, 'لا', 0, '2026-04-27 11:39:43', '2026-04-27 11:39:43'),
(34, 9, 21, '3', 62, '2', 0, '2026-04-27 11:39:43', '2026-04-27 11:39:43');

-- --------------------------------------------------------

--
-- Table structure for table `user_lecture_progress`
--

CREATE TABLE `user_lecture_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lecture_id` bigint(20) UNSIGNED NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouts`
--
ALTER TABLE `abouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_messages_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `admin_message_recipients`
--
ALTER TABLE `admin_message_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_message_recipients_admin_message_id_foreign` (`admin_message_id`),
  ADD KEY `admin_message_recipients_user_id_foreign` (`user_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_cohort_session_idx` (`course_id`,`section_id`,`session_id`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_type_user_id_index` (`user_type`,`user_id`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_created_at_index` (`created_at`),
  ADD KEY `audit_logs_actor_role_index` (`actor_role`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificate_templates`
--
ALTER TABLE `certificate_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificate_templates_is_active_index` (`is_active`),
  ADD KEY `certificate_templates_uploaded_by_index` (`uploaded_by`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_active_idx` (`active`),
  ADD KEY `courses_course_type_idx` (`course_type`),
  ADD KEY `courses_category_id_idx` (`category_id`),
  ADD KEY `courses_active_type_idx` (`active`,`course_type`);

--
-- Indexes for table `courses_instructors`
--
ALTER TABLE `courses_instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_instructors_course_id_instructor_id_unique` (`course_id`,`instructor_id`),
  ADD KEY `courses_instructors_instructor_id_foreign` (`instructor_id`);

--
-- Indexes for table `course_assignments`
--
ALTER TABLE `course_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_assignments_course_status_idx` (`course_id`,`status`),
  ADD KEY `course_assignments_created_by_idx` (`created_by`);

--
-- Indexes for table `course_assignment_cohorts`
--
ALTER TABLE `course_assignment_cohorts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cac_unique` (`course_assignment_id`,`course_session_id`),
  ADD KEY `cac_session_fk` (`course_session_id`);

--
-- Indexes for table `course_assignment_questions`
--
ALTER TABLE `course_assignment_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caq_assignment_position_idx` (`course_assignment_id`,`position`);

--
-- Indexes for table `course_exams`
--
ALTER TABLE `course_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_exams_course_id_foreign` (`course_id`),
  ADD KEY `course_exams_section_id_foreign` (`section_id`);

--
-- Indexes for table `course_exam_cohorts`
--
ALTER TABLE `course_exam_cohorts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_exam_cohorts_unique` (`course_exam_id`,`course_session_id`),
  ADD KEY `course_exam_cohorts_course_session_id_foreign` (`course_session_id`);

--
-- Indexes for table `course_exam_questions`
--
ALTER TABLE `course_exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_exam_questions_course_exam_id_foreign` (`course_exam_id`);

--
-- Indexes for table `course_exam_question_answers`
--
ALTER TABLE `course_exam_question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_exam_question_answers_question_id_foreign` (`question_id`);

--
-- Indexes for table `course_lectures`
--
ALTER TABLE `course_lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_lectures_section_id_foreign` (`section_id`),
  ADD KEY `course_lectures_course_content_type_idx` (`course_id`,`content_type`),
  ADD KEY `course_lectures_session_id_idx` (`session_id`);

--
-- Indexes for table `course_lecture_questions`
--
ALTER TABLE `course_lecture_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_lecture_questions_user_id_foreign` (`user_id`),
  ADD KEY `course_lecture_questions_course_id_foreign` (`course_id`),
  ADD KEY `course_lecture_questions_lecture_id_foreign` (`lecture_id`);

--
-- Indexes for table `course_qualification_skills`
--
ALTER TABLE `course_qualification_skills`
  ADD UNIQUE KEY `course_qualification_skill_unique` (`course_id`,`qualification_skill_id`),
  ADD KEY `course_qs_qs_idx` (`qualification_skill_id`);

--
-- Indexes for table `course_ratings`
--
ALTER TABLE `course_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_ratings_user_id_foreign` (`user_id`),
  ADD KEY `course_ratings_course_id_foreign` (`course_id`);

--
-- Indexes for table `course_resources`
--
ALTER TABLE `course_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_resources_course_id_foreign` (`course_id`);

--
-- Indexes for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_sections_course_id_foreign` (`course_id`),
  ADD KEY `course_sections_enrolment_close_idx` (`enrolment_closes_at`);

--
-- Indexes for table `course_sessions`
--
ALTER TABLE `course_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_sessions_course_id_idx` (`course_id`),
  ADD KEY `course_sessions_passcode_expiry_idx` (`passcode_expires_at`),
  ADD KEY `course_sessions_cohort_date_idx` (`section_id`,`session_date`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluations_evaluation_category_id_foreign` (`evaluation_category_id`);

--
-- Indexes for table `evaluation_categories`
--
ALTER TABLE `evaluation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_questions`
--
ALTER TABLE `form_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_questions_form_id_foreign` (`form_id`);

--
-- Indexes for table `form_question_answers`
--
ALTER TABLE `form_question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_question_answers_form_question_id_foreign` (`form_question_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `instructors_email_unique` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_titles_name_unique` (`name`);

--
-- Indexes for table `job_title_qualification_skill`
--
ALTER TABLE `job_title_qualification_skill`
  ADD PRIMARY KEY (`job_title_id`,`qualification_skill_id`),
  ADD KEY `job_title_qualification_skill_qualification_skill_id_foreign` (`qualification_skill_id`);

--
-- Indexes for table `lms_resources`
--
ALTER TABLE `lms_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lms_resources_qualification_skill_id_foreign` (`qualification_skill_id`),
  ADD KEY `lms_resources_created_by_admin_id_foreign` (`created_by_admin_id`);

--
-- Indexes for table `mainlogs`
--
ALTER TABLE `mainlogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `public_notifications`
--
ALTER TABLE `public_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_notification_users`
--
ALTER TABLE `public_notification_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qualification_skills`
--
ALTER TABLE `qualification_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_export_logs`
--
ALTER TABLE `report_export_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_export_logs_report_type_exported_at_index` (`report_type`,`exported_at`),
  ADD KEY `report_export_logs_report_type_index` (`report_type`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_system_id_unique` (`system_id`),
  ADD KEY `users_learner_type_idx` (`learner_type`),
  ADD KEY `users_name_idx` (`name`),
  ADD KEY `users_machine_code_idx` (`machine_code`),
  ADD KEY `users_department_name_idx` (`department_name`),
  ADD KEY `users_job_title_id_foreign` (`job_title_id`);

--
-- Indexes for table `users_courses`
--
ALTER TABLE `users_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_courses_course_id_user_id_unique` (`course_id`,`user_id`),
  ADD KEY `users_courses_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_course_assignments`
--
ALTER TABLE `user_course_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_course_assignments_user_id_foreign` (`user_id`),
  ADD KEY `uca_assignment_submitted_idx` (`course_assignment_id`,`submitted_at`);

--
-- Indexes for table `user_course_assignment_answers`
--
ALTER TABLE `user_course_assignment_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ucaa_unique` (`user_course_assignment_id`,`course_assignment_question_id`),
  ADD KEY `ucaa_question_fk` (`course_assignment_question_id`);

--
-- Indexes for table `user_course_evaluations`
--
ALTER TABLE `user_course_evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_exams`
--
ALTER TABLE `user_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_exams_user_id_foreign` (`user_id`),
  ADD KEY `user_exams_course_id_foreign` (`course_id`),
  ADD KEY `user_exams_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `user_exam_answers`
--
ALTER TABLE `user_exam_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_exam_answers_user_exam_id_foreign` (`user_exam_id`);

--
-- Indexes for table `user_forms`
--
ALTER TABLE `user_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_forms_form_id_foreign` (`form_id`);

--
-- Indexes for table `user_form_answers`
--
ALTER TABLE `user_form_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_form_answers_user_form_id_foreign` (`user_form_id`);

--
-- Indexes for table `user_lecture_progress`
--
ALTER TABLE `user_lecture_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_lecture_progress_user_id_lecture_id_unique` (`user_id`,`lecture_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouts`
--
ALTER TABLE `abouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_message_recipients`
--
ALTER TABLE `admin_message_recipients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `certificate_templates`
--
ALTER TABLE `certificate_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses_instructors`
--
ALTER TABLE `courses_instructors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `course_assignments`
--
ALTER TABLE `course_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_assignment_cohorts`
--
ALTER TABLE `course_assignment_cohorts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_assignment_questions`
--
ALTER TABLE `course_assignment_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_exams`
--
ALTER TABLE `course_exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_exam_cohorts`
--
ALTER TABLE `course_exam_cohorts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_exam_questions`
--
ALTER TABLE `course_exam_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `course_exam_question_answers`
--
ALTER TABLE `course_exam_question_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `course_lectures`
--
ALTER TABLE `course_lectures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_lecture_questions`
--
ALTER TABLE `course_lecture_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_ratings`
--
ALTER TABLE `course_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_resources`
--
ALTER TABLE `course_resources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_sections`
--
ALTER TABLE `course_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `course_sessions`
--
ALTER TABLE `course_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `evaluation_categories`
--
ALTER TABLE `evaluation_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `form_questions`
--
ALTER TABLE `form_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `form_question_answers`
--
ALTER TABLE `form_question_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `lms_resources`
--
ALTER TABLE `lms_resources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mainlogs`
--
ALTER TABLE `mainlogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `public_notifications`
--
ALTER TABLE `public_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `public_notification_users`
--
ALTER TABLE `public_notification_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `qualification_skills`
--
ALTER TABLE `qualification_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `report_export_logs`
--
ALTER TABLE `report_export_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1998;

--
-- AUTO_INCREMENT for table `users_courses`
--
ALTER TABLE `users_courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `user_course_assignments`
--
ALTER TABLE `user_course_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_course_assignment_answers`
--
ALTER TABLE `user_course_assignment_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_course_evaluations`
--
ALTER TABLE `user_course_evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `user_exams`
--
ALTER TABLE `user_exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_exam_answers`
--
ALTER TABLE `user_exam_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user_forms`
--
ALTER TABLE `user_forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_form_answers`
--
ALTER TABLE `user_form_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_lecture_progress`
--
ALTER TABLE `user_lecture_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD CONSTRAINT `admin_messages_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_message_recipients`
--
ALTER TABLE `admin_message_recipients`
  ADD CONSTRAINT `admin_message_recipients_admin_message_id_foreign` FOREIGN KEY (`admin_message_id`) REFERENCES `admin_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_message_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses_instructors`
--
ALTER TABLE `courses_instructors`
  ADD CONSTRAINT `courses_instructors_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_instructors_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_assignments`
--
ALTER TABLE `course_assignments`
  ADD CONSTRAINT `course_assignments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_assignment_cohorts`
--
ALTER TABLE `course_assignment_cohorts`
  ADD CONSTRAINT `cac_assignment_fk` FOREIGN KEY (`course_assignment_id`) REFERENCES `course_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cac_session_fk` FOREIGN KEY (`course_session_id`) REFERENCES `course_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_assignment_questions`
--
ALTER TABLE `course_assignment_questions`
  ADD CONSTRAINT `caq_assignment_fk` FOREIGN KEY (`course_assignment_id`) REFERENCES `course_assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_exams`
--
ALTER TABLE `course_exams`
  ADD CONSTRAINT `course_exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_exams_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_exam_cohorts`
--
ALTER TABLE `course_exam_cohorts`
  ADD CONSTRAINT `course_exam_cohorts_course_exam_id_foreign` FOREIGN KEY (`course_exam_id`) REFERENCES `course_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_exam_cohorts_course_session_id_foreign` FOREIGN KEY (`course_session_id`) REFERENCES `course_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_exam_questions`
--
ALTER TABLE `course_exam_questions`
  ADD CONSTRAINT `course_exam_questions_course_exam_id_foreign` FOREIGN KEY (`course_exam_id`) REFERENCES `course_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_exam_question_answers`
--
ALTER TABLE `course_exam_question_answers`
  ADD CONSTRAINT `course_exam_question_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `course_exam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_lectures`
--
ALTER TABLE `course_lectures`
  ADD CONSTRAINT `course_lectures_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lectures_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_lecture_questions`
--
ALTER TABLE `course_lecture_questions`
  ADD CONSTRAINT `course_lecture_questions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lecture_questions_lecture_id_foreign` FOREIGN KEY (`lecture_id`) REFERENCES `course_lectures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lecture_questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_qualification_skills`
--
ALTER TABLE `course_qualification_skills`
  ADD CONSTRAINT `course_qualification_skills_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_qualification_skills_qualification_skill_id_foreign` FOREIGN KEY (`qualification_skill_id`) REFERENCES `qualification_skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_ratings`
--
ALTER TABLE `course_ratings`
  ADD CONSTRAINT `course_ratings_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_resources`
--
ALTER TABLE `course_resources`
  ADD CONSTRAINT `course_resources_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD CONSTRAINT `course_sections_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sessions`
--
ALTER TABLE `course_sessions`
  ADD CONSTRAINT `course_sessions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_sessions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_evaluation_category_id_foreign` FOREIGN KEY (`evaluation_category_id`) REFERENCES `evaluation_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_questions`
--
ALTER TABLE `form_questions`
  ADD CONSTRAINT `form_questions_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_question_answers`
--
ALTER TABLE `form_question_answers`
  ADD CONSTRAINT `form_question_answers_form_question_id_foreign` FOREIGN KEY (`form_question_id`) REFERENCES `form_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_title_qualification_skill`
--
ALTER TABLE `job_title_qualification_skill`
  ADD CONSTRAINT `job_title_qualification_skill_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_title_qualification_skill_qualification_skill_id_foreign` FOREIGN KEY (`qualification_skill_id`) REFERENCES `qualification_skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_resources`
--
ALTER TABLE `lms_resources`
  ADD CONSTRAINT `lms_resources_created_by_admin_id_foreign` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lms_resources_qualification_skill_id_foreign` FOREIGN KEY (`qualification_skill_id`) REFERENCES `qualification_skills` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users_courses`
--
ALTER TABLE `users_courses`
  ADD CONSTRAINT `users_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_courses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_course_assignments`
--
ALTER TABLE `user_course_assignments`
  ADD CONSTRAINT `user_course_assignments_course_assignment_id_foreign` FOREIGN KEY (`course_assignment_id`) REFERENCES `course_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_course_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_course_assignment_answers`
--
ALTER TABLE `user_course_assignment_answers`
  ADD CONSTRAINT `ucaa_question_fk` FOREIGN KEY (`course_assignment_question_id`) REFERENCES `course_assignment_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ucaa_submission_fk` FOREIGN KEY (`user_course_assignment_id`) REFERENCES `user_course_assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_exams`
--
ALTER TABLE `user_exams`
  ADD CONSTRAINT `user_exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_exams_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `course_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_exams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_exam_answers`
--
ALTER TABLE `user_exam_answers`
  ADD CONSTRAINT `user_exam_answers_user_exam_id_foreign` FOREIGN KEY (`user_exam_id`) REFERENCES `user_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_forms`
--
ALTER TABLE `user_forms`
  ADD CONSTRAINT `user_forms_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_form_answers`
--
ALTER TABLE `user_form_answers`
  ADD CONSTRAINT `user_form_answers_user_form_id_foreign` FOREIGN KEY (`user_form_id`) REFERENCES `user_forms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
