-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Apr 2025 pada 07.07
-- Versi server: 10.4.19-MariaDB
-- Versi PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_online`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_wa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `password`, `email`, `no_wa`) VALUES
(1, 'Ishak Marasabessy', 'admin', '0192023a7bbd73250516f069df18b500', 'ishakmarssy@gmail.com', '6281355500104');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT 'smtp.gmail.com',
  `port` int(11) NOT NULL DEFAULT 587,
  `encryption` varchar(10) NOT NULL DEFAULT 'tls'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `email_settings`
--

INSERT INTO `email_settings` (`id`, `username`, `password`, `host`, `port`, `encryption`) VALUES
(1, 'abulekegroup@gmail.com', 'tnbb gfoo xbkq cjre', 'smtp.gmail.com', 587, 'tls');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `total_harga` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `keranjang`
--

INSERT INTO `keranjang` (`id`, `user_id`, `produk_id`, `jumlah`, `total_harga`, `created_at`) VALUES
(1006, 5, 25, 2, '0.00', '2025-04-13 03:22:28'),
(1007, 5, 24, 2, '0.00', '2025-04-13 03:22:36'),
(1008, 5, 18, 3, '0.00', '2025-04-13 03:53:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `license_keys`
--

CREATE TABLE `license_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `expiration_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nomor_rekening` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id`, `nama`, `nomor_rekening`) VALUES
(2, 'DANA', 'Dana - 081355500104  - Ishak Marasabessy'),
(3, 'OVO', 'Ovo - 081355500104 - Ishak Marasabessy'),
(5, 'BRI', 'BRI - 026001055250503 - Ishak Marasabessy'),
(8, 'GoPay', 'GoPay - 081355500104'),
(9, 'COD', 'Bayar Di Tempat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `created_at`) VALUES
(11, 5, '46179769ca51c463a2e1e701dbd198d9', '2025-03-04 23:03:34'),
(12, 5, 'e091a5b481ce16933a547c8d356bef88', '2025-03-04 23:05:02'),
(13, 5, '20bb464ce2ccfc643f4dd049753ead64', '2025-03-04 23:13:43'),
(14, 5, '79e8cc61a42f41626672b5085bf2bfcf', '2025-03-04 23:31:32'),
(15, 5, '8c4c396dbec42703e0dedb37000598b3', '2025-03-04 23:32:52'),
(16, 5, 'd5e5d5bc06ddf21ca46b5500e0e662d0', '2025-03-04 23:48:41'),
(18, 5, '66b2d98d415aa5bb0a3efebc7e99f2dc', '2025-03-10 21:50:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `alamat_pengiriman` text NOT NULL,
  `pengiriman_id` int(11) NOT NULL,
  `metode_pembayaran_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `biaya` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pengiriman`
--

INSERT INTO `pengiriman` (`id`, `nama`, `biaya`) VALUES
(2, 'MASOHI', '6000.00'),
(5, 'LETWARU', '7000.00'),
(6, 'AMAHEI', '13000.00'),
(7, 'WAIPO', '10000.00'),
(9, 'RUMAH RAKYAT', '8000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `alamat` text NOT NULL,
  `pengiriman_id` int(11) NOT NULL,
  `metode_pembayaran_id` int(11) NOT NULL,
  `nomor_rekening` varchar(50) NOT NULL,
  `biaya_pengiriman` decimal(15,0) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `status` enum('Pending','Diproses','Dikirim','Selesai','Gagal','Dibatalkan') DEFAULT 'Pending',
  `hp_id` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id`, `user_id`, `alamat`, `pengiriman_id`, `metode_pembayaran_id`, `nomor_rekening`, `biaya_pengiriman`, `total_harga`, `status`, `hp_id`, `created_at`) VALUES
(239, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '13000', '95000.00', 'Selesai', '081355500104', '2025-03-15 13:29:01'),
(242, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '59000.00', 'Selesai', '081355500104', '2025-03-16 08:08:09'),
(252, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '187000.00', 'Selesai', '081355500104', '2025-03-16 08:32:30'),
(267, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '148000.00', 'Selesai', '081355500104', '2025-03-16 10:09:02'),
(268, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 9, 2, 'Dana - 081355500104  - Ishak Marasabessy', '8000', '231000.00', 'Dibatalkan', '081355500104', '2025-03-16 11:13:45'),
(269, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '243000.00', 'Gagal', '081355500104', '2025-03-16 11:14:41'),
(270, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '96225.00', 'Gagal', '081355500104', '2025-03-19 14:34:15'),
(271, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 2, 'Dana - 081355500104  - Ishak Marasabessy', '7000', '322000.00', 'Gagal', '081355500104', '2025-03-19 14:35:19'),
(272, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '322000.00', 'Gagal', '081355500104', '2025-03-19 14:36:09'),
(273, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '913000.00', 'Gagal', '081355500104', '2025-03-19 14:43:57'),
(274, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '82000.00', 'Selesai', '081355500104', '2025-03-19 14:52:51'),
(275, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '88000.00', 'Gagal', '081355500104', '2025-03-19 14:56:27'),
(276, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '102225.00', 'Gagal', '081355500104', '2025-03-19 15:16:10'),
(277, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '10000', '85000.00', 'Selesai', '081355500104', '2025-03-19 15:28:39'),
(278, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 2, 'Dana - 081355500104  - Ishak Marasabessy', '7000', '96225.00', 'Selesai', '081355500104', '2025-03-19 15:47:18'),
(279, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 2, 'Dana - 081355500104  - Ishak Marasabessy', '10000', '114225.00', 'Selesai', '081355500104', '2025-03-19 16:05:17'),
(280, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '30000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:14:51'),
(281, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '6000', '29000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:18:16'),
(282, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '6000', '75000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:18:47'),
(283, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '105000.00', 'Selesai', '081355500104', '2025-03-19 17:20:13'),
(284, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '36000.00', 'Selesai', '081355500104', '2025-03-19 17:22:03'),
(285, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '6000', '29000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:23:09'),
(286, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '30000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:24:29'),
(287, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '30000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:26:08'),
(288, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '6000', '29000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:26:31'),
(289, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '30000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:27:36'),
(290, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '6000', '29000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:38:13'),
(291, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '13000', '28000.00', 'Selesai', '081355500104', '2025-03-19 17:40:09'),
(292, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '22000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:45:45'),
(293, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '22000.00', 'Dibatalkan', '081355500104', '2025-03-19 17:47:42'),
(294, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 2, 'Dana - 081355500104  - Ishak Marasabessy', '13000', '215535.00', 'Selesai', '081355500104', '2025-03-19 17:56:01'),
(295, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 2, 'Dana - 081355500104  - Ishak Marasabessy', '10000', '42845.00', 'Dibatalkan', '081355500104', '2025-03-19 17:58:48'),
(296, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '6000', '29845.00', 'Dibatalkan', '081355500104', '2025-03-19 18:03:06'),
(297, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '41000.00', 'Dibatalkan', '081355500104', '2025-03-19 18:04:30'),
(298, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '24845.00', 'Dibatalkan', '081355500104', '2025-03-19 18:08:30'),
(299, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 9, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '8000', '48845.00', 'Dibatalkan', '081355500104', '2025-03-19 18:09:34'),
(300, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '30845.00', 'Dibatalkan', '081355500104', '2025-03-19 18:09:54'),
(301, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 2, 'Dana - 081355500104  - Ishak Marasabessy', '7000', '31845.00', 'Dibatalkan', '081355500104', '2025-03-19 18:11:23'),
(302, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 2, 'Dana - 081355500104  - Ishak Marasabessy', '13000', '70845.00', 'Dibatalkan', '081355500104', '2025-03-19 19:25:13'),
(303, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '80000.00', 'Dibatalkan', '081355500104', '2025-03-19 20:35:48'),
(304, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 9, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '8000', '81690.00', 'Dibatalkan', '081355500104', '2025-03-19 20:36:15'),
(305, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 2, 'Dana - 081355500104  - Ishak Marasabessy', '13000', '41000.00', 'Gagal', '081355500104', '2025-03-19 20:37:01'),
(306, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '22000.00', 'Gagal', '081355500104', '2025-03-19 20:37:52'),
(307, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '7000', '37000.00', 'Gagal', '081355500104', '2025-03-19 20:40:50'),
(308, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '41000.00', 'Gagal', '081355500104', '2025-03-19 20:41:44'),
(309, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '13000', '73845.00', 'Gagal', '081355500104', '2025-03-19 20:45:14'),
(310, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 2, 'Dana - 081355500104  - Ishak Marasabessy', '6000', '21000.00', 'Gagal', '081355500104', '2025-03-19 20:46:05'),
(311, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '10000', '40000.00', 'Dibatalkan', '081355500104', '2025-03-19 20:46:34'),
(312, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '10000', '100000.00', 'Dibatalkan', '081355500104', '2025-03-19 20:47:24'),
(313, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '10000', '40000.00', 'Dibatalkan', '081355500104', '2025-03-21 15:42:07'),
(314, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '280000.00', 'Gagal', '081355500104', '2025-03-21 15:43:07'),
(315, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '7000', '67845.00', 'Gagal', '081355500104', '2025-03-21 15:43:36'),
(316, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 5, 'BRI - 026001055250503 - Ishak Marasabessy', '13000', '153000.00', 'Dibatalkan', '081355500104', '2025-03-26 14:31:31'),
(317, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 7, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '10000', '130000.00', 'Dibatalkan', '081355500104', '2025-03-26 14:32:44'),
(318, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 6, 2, 'Dana - 081355500104  - Ishak Marasabessy', '13000', '117000.00', 'Dibatalkan', '081355500104', '2025-03-26 15:04:10'),
(319, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 3, 'Ovo - 081355500104 - Ishak Marasabessy', '6000', '126000.00', 'Gagal', '081355500104', '2025-04-04 11:47:51'),
(320, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 2, 'Dana - 081355500104  - Ishak Marasabessy', '7000', '97000.00', 'Dibatalkan', '081355500104', '2025-04-06 07:17:01'),
(321, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 8, 'GoPay - 081355500104', '7000', '341295.00', 'Selesai', '081355500104', '2025-04-11 03:38:19'),
(322, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 5, 8, 'GoPay - 081355500104', '7000', '57000.00', 'Dibatalkan', '081355500104', '2025-04-12 08:39:17'),
(323, 5, 'Jln. Anggrek, Keluharan Namaelo, No. 14', 2, 9, 'Bayar Di Tempat', '6000', '23845.00', 'Diproses', '081355500104', '2025-04-13 02:45:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan_detail`
--

CREATE TABLE `pesanan_detail` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pesanan_detail`
--

INSERT INTO `pesanan_detail` (`id`, `pesanan_id`, `produk_id`, `jumlah`, `subtotal`) VALUES
(376, 239, 17, 2, 46000),
(377, 239, 18, 2, 36000),
(380, 242, 17, 2, 46000),
(390, 252, 18, 10, 180000),
(405, 267, 20, 3, 135000),
(406, 268, 18, 6, 108000),
(407, 268, 17, 5, 115000),
(408, 269, 17, 10, 230000),
(409, 270, 24, 5, 89225),
(410, 271, 20, 7, 315000),
(411, 272, 20, 7, 315000),
(412, 273, 20, 20, 900000),
(413, 274, 22, 5, 75000),
(414, 275, 22, 5, 75000),
(415, 276, 24, 5, 89225),
(416, 277, 22, 5, 75000),
(417, 278, 24, 5, 89225),
(418, 279, 23, 1, 15000),
(419, 279, 24, 5, 89225),
(420, 280, 17, 1, 23000),
(421, 281, 17, 1, 23000),
(422, 282, 17, 3, 69000),
(423, 283, 17, 4, 92000),
(424, 284, 17, 1, 23000),
(425, 285, 17, 1, 23000),
(426, 286, 17, 1, 23000),
(427, 287, 17, 1, 23000),
(428, 288, 17, 1, 23000),
(429, 289, 17, 1, 23000),
(430, 290, 17, 1, 23000),
(431, 291, 23, 1, 15000),
(432, 292, 23, 1, 15000),
(433, 293, 23, 1, 15000),
(434, 294, 21, 1, 13000),
(435, 294, 18, 1, 18000),
(436, 294, 24, 3, 53535),
(437, 294, 23, 1, 15000),
(438, 294, 20, 1, 45000),
(439, 294, 17, 2, 46000),
(440, 294, 19, 1, 12000),
(441, 295, 23, 1, 15000),
(442, 295, 24, 1, 17845),
(443, 296, 24, 1, 17845),
(444, 297, 23, 1, 15000),
(445, 298, 24, 1, 17845),
(446, 299, 23, 1, 15000),
(447, 299, 24, 1, 17845),
(448, 300, 24, 1, 17845),
(449, 301, 24, 1, 17845),
(450, 302, 23, 1, 15000),
(451, 302, 19, 1, 12000),
(452, 302, 24, 1, 17845),
(453, 303, 21, 1, 13000),
(454, 303, 17, 1, 23000),
(455, 303, 18, 1, 18000),
(456, 304, 23, 2, 30000),
(457, 304, 24, 2, 35690),
(458, 305, 23, 1, 15000),
(459, 306, 23, 1, 15000),
(460, 307, 17, 1, 23000),
(461, 308, 23, 1, 15000),
(462, 309, 23, 2, 30000),
(463, 309, 24, 1, 17845),
(464, 310, 23, 1, 15000),
(465, 311, 23, 2, 30000),
(466, 312, 20, 2, 90000),
(467, 313, 23, 2, 30000),
(468, 314, 23, 2, 30000),
(469, 314, 20, 1, 45000),
(470, 314, 22, 4, 60000),
(471, 314, 17, 6, 138000),
(472, 315, 24, 1, 17845),
(473, 315, 23, 1, 15000),
(474, 315, 22, 1, 15000),
(475, 315, 21, 1, 13000),
(476, 316, 22, 5, 75000),
(477, 316, 21, 5, 65000),
(478, 317, 23, 8, 120000),
(479, 318, 21, 8, 104000),
(480, 319, 23, 8, 120000),
(481, 320, 22, 6, 90000),
(482, 321, 17, 6, 138000),
(483, 321, 24, 11, 196295),
(484, 322, 25, 5, 50000),
(485, 323, 24, 1, 17845);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga_lama` decimal(10,0) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `stok` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `kategori` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama`, `harga_lama`, `harga`, `deskripsi`, `gambar`, `stok`, `admin_id`, `kategori`) VALUES
(17, 'Burger King\'s', '30000', '23000.00', 'Deskripsi 1', 'Iklan-Makanan.jpg', 20, 0, ''),
(18, 'Ayam Goreng HOT Spicy', '0', '18000.00', 'Desk 2', 'WhatsApp-Image-2022-11-13-at-16.51.01-1.jpeg', 25, 0, 'makanan'),
(19, 'Oreo Vanilla', '0', '12000.00', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'pasted image 0.png', 49, 0, ''),
(20, 'Donat\'s Queens', '0', '45000.00', 'Deks 4', 'WhatsApp-Image-2022-11-11-at-14.49.35-1.jpeg', 15, 0, ''),
(21, 'Kentang Goreng', '15000', '13000.00', 'Desk 5', 'WhatsApp-Image-2022-11-13-at-20.46.14-1-2.jpeg', 8, 0, ''),
(22, 'Burger Amira', '0', '15000.00', 'Desk 6', 'contoh-iklan-makanan-dan-minuman-8.jpg', 35, 0, ''),
(23, 'Kopi Kelapa', '0', '15000.00', 'Desk 7', 'images.jpg', 0, 0, 'minuman'),
(24, 'Pesta 17 Agustus', '0', '17845.00', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 'Pesta+Cashback+-+IGF+-+AGUSTUS.jpg', 8, 0, ''),
(25, 'Nasi Goreng 2', '0', '10000.00', 'NAsgor Legend', 'nasgor.jpg', 5, 0, 'makanan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `gmbr` longblob NOT NULL,
  `link` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `slider`
--

INSERT INTO `slider` (`id`, `gmbr`, `link`) VALUES
(3, 0x65636f6d6d657263652e6a7067, '#'),
(4, 0x696b6c616e322e706e67, '#'),
(5, 0x696b6c616e312e706e67, '#'),
(7, 0x696b6c616e342e706e67, '#'),
(8, 0x696b6c616e332e706e67, '#');

-- --------------------------------------------------------

--
-- Struktur dari tabel `store_settings`
--

CREATE TABLE `store_settings` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `store_settings`
--

INSERT INTO `store_settings` (`id`, `store_name`) VALUES
(1, 'Masohi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `topup`
--

CREATE TABLE `topup` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `topup`
--

INSERT INTO `topup` (`id`, `user_id`, `jumlah`, `metode`, `status`, `created_at`) VALUES
(38, 5, 157000, 'Bank Transfer', 'failed', '2025-02-28 17:28:57'),
(39, 5, 10000, 'DANA', 'failed', '2025-02-28 17:34:24'),
(40, 5, 10000, 'DANA', 'success', '2025-02-28 17:42:26'),
(41, 5, 15000, 'OVO', 'success', '2025-02-28 18:11:03'),
(42, 5, 1000000, 'Bank BRI ', 'success', '2025-02-28 19:06:56'),
(43, 5, 10000, 'DANA', 'failed', '2025-03-01 21:05:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status` enum('pending','dibayar','dikirim','selesai') DEFAULT 'pending',
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `total_harga`, `metode_pembayaran`, `status`, `tanggal_transaksi`) VALUES
(1, 5, 55000, 'Transfer Bank', 'pending', '2025-02-24 12:58:40'),
(2, 5, 0, 'Transfer Bank', 'pending', '2025-02-24 12:58:50'),
(3, 5, 0, 'Transfer Bank', 'pending', '2025-02-24 13:00:20'),
(4, 5, 0, 'Transfer Bank', 'pending', '2025-02-24 13:00:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hp` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `foto_profil` varchar(255) DEFAULT NULL,
  `saldo` decimal(10,0) NOT NULL,
  `alamat` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `hp`, `username`, `password`, `role`, `foto_profil`, `saldo`, `alamat`) VALUES
(5, 'Ishak Marasabessy', 'ishak.marasabessy@gmail.com', '081355500104', 'user123', '6ad14ba9986e3615423dfca256d04e3f', 'user', '1741340065_LOGO_UIM_.png', '13000', 'Jln. Anggrek, Keluharan Namaelo, No. 14'),
(6, 'reza', 'nurselawatiwaleuru28@gmail.com', '', 'reza', 'bb98b1d0b523d5e783f931550d7702b6', 'user', NULL, '0', ''),
(7, 'Ishak', 'admin@admin@gmail.com', '', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', NULL, '0', ''),
(8, 'admin1', 'admin@gmail.com', '546567567678', 'admin1', 'admin123', 'admin', NULL, '0', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `license_keys`
--
ALTER TABLE `license_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_key` (`license_key`);

--
-- Indeks untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `topup`
--
ALTER TABLE `topup`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT untuk tabel `license_keys`
--
ALTER TABLE `license_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=486;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `store_settings`
--
ALTER TABLE `store_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `topup`
--
ALTER TABLE `topup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD CONSTRAINT `pesanan_detail_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
