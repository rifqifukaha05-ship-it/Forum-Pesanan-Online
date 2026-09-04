-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Sep 2026 pada 05.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pemesanan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(30) NOT NULL,
  `payment` enum('QRIS','Transfer Bank','COD') NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Menunggu Pembayaran','Diproses','Berhasil','Gagal') NOT NULL DEFAULT 'Menunggu Pembayaran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `address`, `phone`, `payment`, `payment_proof`, `status`, `created_at`, `payment_status`) VALUES
(1, 2, 10000.00, 'Jl. Perum Villa Indah Permai Jl. Serayu 1 No.27 Blok E, RT.009/RW.033, Tlk. Pucung, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17121', '081386358074', '', NULL, 'Selesai', '2026-09-03 11:51:59', 'Menunggu Pembayaran'),
(2, 2, 5000.00, 'Jl. Perum Villa Indah Permai Jl. Serayu 1 No.27 Blok E, RT.009/RW.033, Tlk. Pucung, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17121', '88986203', 'QRIS', 'bukti_2_1788486941.png', 'Selesai', '2026-09-04 01:47:54', 'Menunggu Pembayaran'),
(3, 4, 5000.00, 'Jl. Perum Villa Indah Permai Jl. Serayu 1 No.27 Blok E, RT.009/RW.033, Tlk. Pucung, Kec. Bekasi Utara, Kota Bks, Jawa Barat 17121', '88986203', 'QRIS', 'bukti_3_1788490107.png', 'Dikirim', '2026-09-04 02:48:16', 'Berhasil');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `qty`, `price`) VALUES
(1, 1, 3, 2, 5000.00),
(2, 2, 3, 1, 5000.00),
(3, 3, 3, 1, 5000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'Nasi Goreng', 'Nasi goreng spesial dengan telur dan ayam.', 15000.00, 20, 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=800', '2026-09-03 11:10:09'),
(2, 'Mie Ayam', 'Mie ayam lengkap dengan ayam dan sayuran.', 12000.00, 25, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800', '2026-09-03 11:10:09'),
(3, 'Es Teh', 'Es teh manis segar.', 5000.00, 46, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=800', '2026-09-03 11:10:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@pemesanan.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC9e1F7h5sL0p2f2l8y6', 'admin', '2026-09-03 11:10:09'),
(2, 'rifqi', 'rifqifukaha05@gmail.com', '$2y$10$8h7MO8hbh/fGoOvRQSJwAOpyCCPkZyiHl.K/Q8tLmiHt/ZhnA.OQC', 'customer', '2026-09-03 11:32:35'),
(4, 'Rifqi Imam', 'guru@gmail.com', '$2y$10$Oi4rr/hm1XUDVenhnwcS9eAxo1.hSrxTohfKa/uQ9Gj.SpCAqocpu', 'admin', '2026-09-04 02:46:37');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
