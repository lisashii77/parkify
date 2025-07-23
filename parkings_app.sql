-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 23 Jul 2025 pada 07.29
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parkings_app`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `slot_id` int DEFAULT NULL,
  `waktu_booking` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','dibayar','selesai','dibatalkan') DEFAULT 'pending',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `slot_id`, `waktu_booking`, `status`, `waktu_mulai`, `waktu_selesai`) VALUES
(26, 2, 47, '2025-07-13 22:30:08', 'dibatalkan', '2025-07-13 22:31:00', '2025-07-13 00:30:00'),
(27, 2, 57, '2025-07-13 22:49:11', 'dibatalkan', '2025-07-13 22:49:00', '2025-07-13 23:49:00'),
(28, 2, 53, '2025-07-14 08:16:52', 'selesai', '2025-07-14 08:16:00', '2025-07-14 10:16:00'),
(29, 1, 53, '2025-07-14 08:29:50', 'dibatalkan', '2025-07-14 08:16:00', '2025-07-14 10:16:00'),
(30, 1, 48, '2025-07-14 08:39:21', 'selesai', '2025-07-14 08:39:00', '2025-07-14 10:39:00'),
(31, 1, 49, '2025-07-14 08:51:25', 'selesai', '2025-07-14 08:50:00', '2025-07-14 10:51:00'),
(32, 2, 52, '2025-07-14 09:24:05', 'selesai', '2025-07-14 09:23:00', '2025-07-14 11:24:00'),
(33, 2, 58, '2025-07-14 09:25:35', 'selesai', '2025-07-15 09:25:00', '2025-07-14 11:25:00'),
(34, 2, 50, '2025-07-14 09:58:18', 'selesai', '2025-07-14 09:58:00', '2025-07-14 12:58:00'),
(35, 2, 48, '2025-07-14 09:59:05', 'dibayar', '2025-07-28 11:58:00', '2025-07-28 14:58:00'),
(36, 4, 49, '2025-07-15 10:42:39', 'dibayar', '2025-07-29 10:42:00', '2025-07-29 13:42:00'),
(37, 4, 57, '2025-07-15 10:43:56', 'pending', '2025-07-15 10:43:00', '2025-07-15 13:43:00'),
(38, 4, 51, '2025-07-15 11:38:44', 'pending', '2025-07-31 11:38:00', '2025-07-31 14:38:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `parkir_slots`
--

CREATE TABLE `parkir_slots` (
  `id` int NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `jenis` enum('motor','mobil','vip') DEFAULT 'mobil',
  `harga` int NOT NULL DEFAULT '0',
  `tempat_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `parkir_slots`
--

INSERT INTO `parkir_slots` (`id`, `lokasi`, `status`, `jenis`, `harga`, `tempat_id`) VALUES
(47, 'A1', 'available', 'motor', 5000, 1),
(48, 'B1', 'available', 'mobil', 10000, 1),
(49, 'VIP 1', 'available', 'vip', 50000, 1),
(50, 'A1', 'available', 'motor', 5000, 3),
(51, 'B1', 'available', 'mobil', 10000, 3),
(52, 'VIP 1', 'available', 'vip', 50000, 3),
(53, 'A1', 'available', 'motor', 5000, 4),
(55, 'B1', 'available', 'mobil', 5000, 4),
(56, 'VIP 1', 'available', 'vip', 10000, 4),
(57, 'A1', 'available', 'motor', 5000, 5),
(58, 'B1', 'booked', 'mobil', 10000, 5),
(59, 'VIP 1', 'available', 'vip', 50000, 5);

--
-- Trigger `parkir_slots`
--
DELIMITER $$
CREATE TRIGGER `set_default_harga` BEFORE INSERT ON `parkir_slots` FOR EACH ROW BEGIN
  IF NEW.jenis = 'motor' THEN
    SET NEW.harga = 5000;
  ELSEIF NEW.jenis = 'mobil' THEN
    SET NEW.harga = 10000;
  ELSEIF NEW.jenis = 'vip' THEN
    SET NEW.harga = 50000;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `waktu_pembayaran` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','berhasil','gagal','menunggu_verifikasi') DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `no_invoice` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `booking_id`, `harga`, `waktu_pembayaran`, `status`, `metode_pembayaran`, `bukti_pembayaran`, `no_invoice`) VALUES
(24, 32, 50000, '2025-07-14 02:24:13', 'berhasil', 'Transfer Bank', 'bukti_1752459853.png', 'INV20250714-5294'),
(25, 33, 10000, '2025-07-14 02:32:06', 'berhasil', 'QRIS', 'bukti_1752460326.png', 'INV20250714-3843'),
(26, 34, 5000, '2025-07-14 02:58:28', 'berhasil', 'Dompet Digital', 'bukti_1752461908.png', 'INV20250714-7194'),
(27, 35, 10000, '2025-07-14 02:59:13', 'berhasil', 'Dompet Digital', 'bukti_1752461953.png', 'INV20250714-1367'),
(28, 36, 50000, '2025-07-15 03:42:47', 'berhasil', 'QRIS', 'bukti_1752550967.png', 'INV20250715-7592');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempat_parkir`
--

CREATE TABLE `tempat_parkir` (
  `tempat_id` int NOT NULL,
  `nama_tempat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `tempat_parkir`
--

INSERT INTO `tempat_parkir` (`tempat_id`, `nama_tempat`) VALUES
(1, 'Tunjungan Plaza'),
(3, 'Grand City'),
(4, 'Park and Ride Mayjend Sungkono'),
(5, 'Park and Ride Adityawarman');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$hYAgpMintJN7SELw9K7esuEB3c1JWRSAMJEv8YKJnWRA0.rzPdX8O', 'admin'),
(2, 'user', '$2y$10$1XiWpOVyhiGmfxdz.EFWNO0/W38BFKCatIvGQrsDqYo6S76NVZoNG', 'user'),
(4, 'lisa', '$2y$10$6cF1ngMJ03QheROE7GWXNu8MLZGdPjBCBn0yqmWX2rmdQVgArQN9G', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indeks untuk tabel `parkir_slots`
--
ALTER TABLE `parkir_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tempat` (`tempat_id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `tempat_parkir`
--
ALTER TABLE `tempat_parkir`
  ADD PRIMARY KEY (`tempat_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `parkir_slots`
--
ALTER TABLE `parkir_slots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `tempat_parkir`
--
ALTER TABLE `tempat_parkir`
  MODIFY `tempat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`slot_id`) REFERENCES `parkir_slots` (`id`);

--
-- Ketidakleluasaan untuk tabel `parkir_slots`
--
ALTER TABLE `parkir_slots`
  ADD CONSTRAINT `fk_tempat` FOREIGN KEY (`tempat_id`) REFERENCES `tempat_parkir` (`tempat_id`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
