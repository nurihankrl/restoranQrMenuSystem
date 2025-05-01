-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 01 May 2025, 21:21:01
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `qrmenu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$/018fTNzzjmtu/d.Rr8ir.ahyhoYky01ntiM7zL4zZLBQ2rMsy44y', '2025-05-01 15:52:49');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Kahvaltı', '2025-04-30 19:51:03'),
(2, 'Çorbalar', '2025-04-30 20:16:48'),
(3, 'Izgara Çeşitleri', '2025-04-30 20:34:54'),
(4, 'Atıştırmalıklar', '2025-04-30 20:45:07'),
(5, 'Tatlılar', '2025-05-01 09:18:49'),
(6, 'İçecekler', '2025-05-01 09:25:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `category_images`
--

CREATE TABLE `category_images` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `category_images`
--

INSERT INTO `category_images` (`id`, `category_id`, `image_path`, `is_featured`, `created_at`) VALUES
(1, 1, 'KATE_68127f27f3209_63333.jpg', 1, '2025-04-30 19:51:04'),
(2, 2, 'KATE_6812853022ec5_27698.jpg', 1, '2025-04-30 20:16:48'),
(3, 3, 'KATE_6812896ea598b_21668.jpg', 1, '2025-04-30 20:34:54'),
(4, 4, 'KATE_68128bd39cedb_15369.jpg', 1, '2025-04-30 20:45:07'),
(5, 5, 'KATE_68133c798f130_59592.jpg', 1, '2025-05-01 09:18:49'),
(6, 6, 'KATE_68133e12a200b_35353.jpg', 1, '2025-05-01 09:25:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Serpme Kahvaltı', 'Beyaz peynir, kaşar peyniri, tulum peyniri, siyah zeytin, yeşil zeytin, domates, salatalık, bal, tereyağı, reçel çeşitleri, haşlanmış yumurta, sigara böreği, sucuk, patates kızartması, menemen, taze ekmek çeşitleri, çay.', 490.00, 1, '2025-04-30 19:56:26', '2025-04-30 19:56:26'),
(2, 'Omlet', 'Yumurta, süt, tuz, karabiber, kaşar peyniri, sucuk, mantar.', 150.00, 1, '2025-04-30 20:10:56', '2025-04-30 20:10:56'),
(3, 'Tost', 'Menümüzde 3 farklı tost çeşidi bulunmaktadır: Sucuklu, Kaşar Peynirli ve Salamlı Tost.', 110.00, 1, '2025-04-30 20:13:38', '2025-04-30 20:14:35'),
(4, 'Mercimek Çorbası', 'Kırmızı mercimek, soğan, havuç, patates, salça, tereyağı, sıvı yağ, tuz, karabiber, kimyon, su', 50.00, 2, '2025-04-30 20:21:05', '2025-04-30 20:21:05'),
(5, 'Ezogelin Çorbası', 'Kırmızı mercimek, bulgur, pirinç, soğan, domates salçası, tereyağı, sıvı yağ, nane, pul biber, tuz, karabiber, su', 60.00, 2, '2025-04-30 20:29:22', '2025-04-30 20:29:22'),
(6, 'Şehriye Çorbası', 'Arpa şehriye (veya tel şehriye), domates salçası, tereyağı, sıvı yağ, su (veya tavuk suyu), tuz, karabiber', 50.00, 2, '2025-04-30 20:31:55', '2025-04-30 20:31:55'),
(7, 'Tavuk Şiş', 'Marine edilmiş tavuk parçaları, biber, soğan, zeytinyağı, yoğurt, salça, baharatlar (kimyon, karabiber, pul biber, kekik), tuz, şiş çubukları.', 200.00, 3, '2025-04-30 20:40:29', '2025-04-30 20:40:29'),
(8, 'Izgara Köfte', 'Dana kıyma, soğan, galeta unu (veya ekmek içi), yumurta, sarımsak, tuz, karabiber, kimyon, maydanoz.', 270.00, 3, '2025-04-30 20:42:17', '2025-04-30 20:42:17'),
(9, 'Patates Kızartması', 'Taze patates, sıvı yağ, tuz.', 50.00, 4, '2025-04-30 20:48:55', '2025-04-30 20:48:55'),
(10, 'Sigara Böreği', 'Yufka, beyaz peynir, maydanoz, sıvı yağ', 30.00, 4, '2025-04-30 20:54:32', '2025-04-30 20:54:32'),
(11, 'Baklava', 'Yufka, ceviz içi (veya fıstık), tereyağı, şeker, su, limon suyu', 300.00, 5, '2025-05-01 09:21:56', '2025-05-01 09:21:56'),
(12, 'Cheesecake', 'Bisküvi tabanı, krem peynir, şeker, yumurta, vanilya, krema, limon suyu', 200.00, 5, '2025-05-01 09:24:38', '2025-05-01 09:24:38'),
(13, 'Çay', '', 15.00, 6, '2025-05-01 09:28:37', '2025-05-01 09:28:37'),
(15, 'Türk Kahvesi', '', 70.00, 6, '2025-05-01 09:33:31', '2025-05-01 09:33:31');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_featured`, `created_at`) VALUES
(1, 1, 'URU_6812806a9c8eb_34571.jpg', 1, '2025-04-30 19:56:26'),
(2, 2, 'URU_681283d05499d_67840.jpg', 1, '2025-04-30 20:10:56'),
(3, 3, 'URU_6812847274285_23708.jpg', 1, '2025-04-30 20:13:38'),
(4, 4, 'URU_681286317e8f5_99784.jpg', 0, '2025-04-30 20:21:05'),
(5, 5, 'URU_68128822f175b_92107.jpg', 1, '2025-04-30 20:29:23'),
(6, 6, 'URU_681288bb45937_80926.jpg', 1, '2025-04-30 20:31:55'),
(8, 8, 'URU_68128b29d5190_22904.jpg', 1, '2025-04-30 20:42:17'),
(9, 7, 'URU_7_26397.jpg', 1, '2025-04-30 20:43:20'),
(10, 9, 'URU_68128cb74e747_39644.jpg', 1, '2025-04-30 20:48:55'),
(11, 10, 'URU_68128e08cae84_35576.jpg', 1, '2025-04-30 20:54:32'),
(12, 11, 'URU_68133d346fd95_53821.jpg', 1, '2025-05-01 09:21:56'),
(13, 12, 'URU_68133dd6861f2_10497.jpg', 1, '2025-05-01 09:24:38'),
(14, 13, 'URU_68133ec54709a_33221.jpg', 1, '2025-05-01 09:28:37'),
(16, 15, 'URU_68133feb4ca1f_42575.jpg', 1, '2025-05-01 09:33:31'),
(17, 4, 'URU_4_68275.jpg', 1, '2025-05-01 18:10:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_title` varchar(255) NOT NULL DEFAULT 'Site Başlığı',
  `site_logo` varchar(255) DEFAULT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `site_title`, `site_logo`, `maintenance_mode`) VALUES
(1, 'Restoran QR Menü', 'LOGO_68135720dd1a4.png', 0);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `category_images`
--
ALTER TABLE `category_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `category_images`
--
ALTER TABLE `category_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `category_images`
--
ALTER TABLE `category_images`
  ADD CONSTRAINT `category_images_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
