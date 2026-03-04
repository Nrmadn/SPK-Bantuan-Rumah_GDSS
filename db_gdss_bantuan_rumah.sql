-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 09, 2025 at 03:31 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_gdss_bantuan_rumah`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `no_kk` varchar(20) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id`, `kode`, `nama`, `alamat`, `no_kk`, `no_ktp`, `keterangan`, `created_at`) VALUES
(1, 'A1', 'Aminah', 'Gampong Matang Panyang, RT 01/RW 02', '1101012301230001', '1101015001850001', 'Ibu rumah tangga, suami buruh harian', '2025-11-14 17:05:42'),
(2, 'A2', 'Hasanah', 'Gampong Blang Dalam, RT 02/RW 01', '1101012301230002', '1101015502900001', 'Janda dengan 5 anak', '2025-11-14 17:05:42'),
(3, 'A3', 'Baihaki', 'Gampong Keude Aceh, RT 03/RW 02', '1101012301230003', '1101011203750001', 'Petani, rumah sangat rusak', '2025-11-14 17:05:42'),
(4, 'A4', 'Fakri', 'Gampong Paya Bili, RT 01/RW 03', '1101012301230004', '1101011504880001', 'Buruh lepas dengan 5 tanggungan', '2025-11-14 17:05:42');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_borda`
--

CREATE TABLE `hasil_borda` (
  `id` int NOT NULL,
  `alternatif_id` int NOT NULL,
  `rank_dm1` int DEFAULT NULL COMMENT 'Ranking dari Kepala Desa',
  `rank_dm2` int DEFAULT NULL COMMENT 'Ranking dari Sekretaris',
  `rank_dm3` int DEFAULT NULL COMMENT 'Ranking dari Ketua RT',
  `skor_dm1` decimal(10,8) DEFAULT NULL COMMENT 'Skor TOPSIS dari Kepala Desa',
  `skor_dm2` decimal(10,8) DEFAULT NULL COMMENT 'Skor TOPSIS dari Sekretaris',
  `skor_dm3` decimal(10,8) DEFAULT NULL COMMENT 'Skor TOPSIS dari Ketua RT',
  `total_poin` decimal(12,8) NOT NULL COMMENT 'Total Poin Weighted Borda',
  `bobot` decimal(10,8) NOT NULL COMMENT 'Nilai Borda (Normalisasi)',
  `ranking_final` int NOT NULL COMMENT 'Ranking Final Konsensus',
  `tanggal_konsensus` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil_borda`
--

INSERT INTO `hasil_borda` (`id`, `alternatif_id`, `rank_dm1`, `rank_dm2`, `rank_dm3`, `skor_dm1`, `skor_dm2`, `skor_dm3`, `total_poin`, `bobot`, `ranking_final`, `tanggal_konsensus`) VALUES
(29, 4, 1, 3, 1, '0.56208800', '0.48489753', '0.67843253', '5.93187718', '0.36400216', 1, '2025-12-06 16:54:59'),
(30, 2, 2, 1, 2, '0.55120253', '0.65095449', '0.47748764', '5.68988847', '0.34915283', 2, '2025-12-06 16:54:59'),
(31, 1, 4, 2, 3, '0.38016613', '0.51510247', '0.45871366', '2.84290086', '0.17445103', 3, '2025-12-06 16:54:59'),
(32, 3, 3, 4, 4, '0.50506551', '0.38329703', '0.43817445', '1.83160250', '0.11239398', 4, '2025-12-06 16:54:59');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_topsis`
--

CREATE TABLE `hasil_topsis` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `alternatif_id` int NOT NULL,
  `skor_topsis` decimal(10,8) NOT NULL,
  `ranking` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil_topsis`
--

INSERT INTO `hasil_topsis` (`id`, `user_id`, `alternatif_id`, `skor_topsis`, `ranking`, `created_at`) VALUES
(201, 4, 4, '0.67843253', 1, '2025-12-09 01:08:33'),
(202, 4, 2, '0.47748764', 2, '2025-12-09 01:08:33'),
(203, 4, 1, '0.45871366', 3, '2025-12-09 01:08:33'),
(204, 4, 3, '0.43817445', 4, '2025-12-09 01:08:33'),
(205, 2, 4, '0.56208800', 1, '2025-12-09 03:24:57'),
(206, 2, 2, '0.55120253', 2, '2025-12-09 03:24:57'),
(207, 2, 3, '0.50506551', 3, '2025-12-09 03:24:57'),
(208, 2, 1, '0.38016613', 4, '2025-12-09 03:24:57'),
(213, 3, 2, '0.65095449', 1, '2025-12-09 03:29:37'),
(214, 3, 1, '0.51510247', 2, '2025-12-09 03:29:37'),
(215, 3, 4, '0.48489753', 3, '2025-12-09 03:29:37'),
(216, 3, 3, '0.38329703', 4, '2025-12-09 03:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `jenis` enum('cost','benefit') NOT NULL,
  `bobot` decimal(5,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode`, `nama_kriteria`, `jenis`, `bobot`) VALUES
(1, 'C1', 'Pekerjaan Orang Tua', 'cost', '0.278'),
(2, 'C2', 'Jumlah Tanggungan', 'benefit', '0.217'),
(3, 'C3', 'Sumber Penghasilan', 'cost', '0.144'),
(4, 'C4', 'Kondisi Rumah', 'cost', '0.127'),
(5, 'C5', 'Status Rumah', 'cost', '0.129'),
(6, 'C6', 'Kepemilikan Rumah Lain', 'cost', '0.105');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `aktivitas`, `keterangan`, `created_at`) VALUES
(1, 1, 'Login', NULL, '2025-11-15 02:03:16'),
(2, 1, 'Logout', 'User keluar dari sistem', '2025-11-15 02:04:57'),
(3, 2, 'Login', NULL, '2025-11-15 02:05:14'),
(4, 2, 'Logout', 'User keluar dari sistem', '2025-11-15 02:06:02'),
(5, 1, 'Login', NULL, '2025-11-15 02:10:49'),
(6, 1, 'Logout', 'User keluar dari sistem', '2025-11-15 02:11:26'),
(7, 2, 'Login', NULL, '2025-11-15 02:11:37'),
(8, 2, 'Logout', 'User keluar dari sistem', '2025-11-15 02:12:34'),
(9, 4, 'Login', NULL, '2025-11-15 02:12:46'),
(10, 4, 'Logout', 'User keluar dari sistem', '2025-11-15 02:13:05'),
(11, 3, 'Login', NULL, '2025-11-15 02:13:18'),
(12, 1, 'Login', NULL, '2025-11-15 08:51:00'),
(13, 1, 'Logout', 'User keluar dari sistem', '2025-11-15 08:51:26'),
(14, 2, 'Login', NULL, '2025-11-15 08:51:35'),
(15, 2, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 08:53:38'),
(16, 2, 'Input Penilaian', 'Penilaian untuk Hasanah', '2025-11-15 08:54:10'),
(17, 2, 'Input Penilaian', 'Penilaian untuk Baihaki', '2025-11-15 08:54:32'),
(18, 2, 'Input Penilaian', 'Penilaian untuk Fakri', '2025-11-15 08:54:54'),
(19, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-15 08:54:54'),
(20, 2, 'Logout', 'User keluar dari sistem', '2025-11-15 08:55:44'),
(21, 1, 'Login', NULL, '2025-11-15 08:55:59'),
(22, 1, 'Logout', 'User keluar dari sistem', '2025-11-15 09:01:21'),
(23, 2, 'Login', NULL, '2025-11-15 09:01:31'),
(24, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-15 09:07:25'),
(25, 2, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:12:56'),
(26, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-15 09:12:56'),
(27, 2, 'Logout', 'User keluar dari sistem', '2025-11-15 09:15:08'),
(28, 2, 'Login', NULL, '2025-11-15 09:15:35'),
(29, 2, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:16:10'),
(30, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-15 09:16:10'),
(31, 2, 'Logout', 'User keluar dari sistem', '2025-11-15 09:16:14'),
(32, 3, 'Login', NULL, '2025-11-15 09:16:25'),
(33, 3, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:16:59'),
(34, 3, 'Input Penilaian', 'Penilaian untuk Hasanah', '2025-11-15 09:17:36'),
(35, 3, 'Input Penilaian', 'Penilaian untuk Baihaki', '2025-11-15 09:18:00'),
(36, 3, 'Input Penilaian', 'Penilaian untuk Fakri', '2025-11-15 09:18:23'),
(37, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:18:23'),
(38, 3, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:19:05'),
(39, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:19:05'),
(40, 3, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:21:39'),
(41, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:21:39'),
(42, 3, 'Input Penilaian', 'Penilaian untuk Hasanah', '2025-11-15 09:21:55'),
(43, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:21:55'),
(44, 3, 'Input Penilaian', 'Penilaian untuk Baihaki', '2025-11-15 09:22:06'),
(45, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:22:06'),
(46, 3, 'Input Penilaian', 'Penilaian untuk Fakri', '2025-11-15 09:22:19'),
(47, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-15 09:22:19'),
(48, 3, 'Logout', 'User keluar dari sistem', '2025-11-15 09:22:42'),
(49, 4, 'Login', NULL, '2025-11-15 09:22:52'),
(50, 4, 'Input Penilaian', 'Penilaian untuk Aminah', '2025-11-15 09:23:22'),
(51, 4, 'Input Penilaian', 'Penilaian untuk Fakri', '2025-11-15 09:24:19'),
(52, 4, 'Input Penilaian', 'Penilaian untuk Hasanah', '2025-11-15 09:24:42'),
(53, 4, 'Input Penilaian', 'Penilaian untuk Baihaki', '2025-11-15 09:25:02'),
(54, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-15 09:25:02'),
(55, 4, 'Logout', 'User keluar dari sistem', '2025-11-15 09:25:16'),
(56, 1, 'Login', NULL, '2025-11-15 09:25:21'),
(57, 1, 'Konsensus Borda', 'Perhitungan konsensus Borda berhasil dilakukan', '2025-11-15 09:25:41'),
(58, 2, 'Login', NULL, '2025-11-15 17:08:43'),
(59, 2, 'Login', NULL, '2025-11-17 06:46:56'),
(60, 2, 'Logout', 'User keluar dari sistem', '2025-11-17 06:47:23'),
(61, 1, 'Login', NULL, '2025-11-17 06:47:30'),
(62, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-17 06:47:42'),
(63, 1, 'Logout', 'User keluar dari sistem', '2025-11-17 06:49:12'),
(64, 2, 'Login', NULL, '2025-11-17 06:49:23'),
(65, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 06:59:29'),
(66, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 07:02:24'),
(67, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 07:02:29'),
(68, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 07:02:47'),
(69, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 07:04:39'),
(70, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 07:04:43'),
(71, 2, 'Login', NULL, '2025-11-17 13:52:47'),
(72, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 13:57:46'),
(73, 2, 'Logout', 'User keluar dari sistem', '2025-11-17 14:43:16'),
(74, 1, 'Login', NULL, '2025-11-17 14:43:23'),
(75, 1, 'Logout', 'User keluar dari sistem', '2025-11-17 14:58:41'),
(76, 2, 'Login', NULL, '2025-11-17 14:58:52'),
(77, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-17 15:00:54'),
(78, 2, 'Logout', 'User keluar dari sistem', '2025-11-17 15:03:56'),
(79, 1, 'Login', NULL, '2025-11-17 15:04:02'),
(80, 1, 'Logout', 'User keluar dari sistem', '2025-11-17 15:23:09'),
(81, 2, 'Login', NULL, '2025-11-18 00:57:17'),
(82, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-18 00:57:19'),
(83, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-18 00:57:49'),
(84, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-18 00:58:41'),
(85, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-18 00:59:00'),
(86, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-18 01:03:09'),
(87, 2, 'Logout', 'User keluar dari sistem', '2025-11-18 01:05:14'),
(88, 1, 'Login', NULL, '2025-11-18 01:05:25'),
(89, 1, 'Login', NULL, '2025-11-18 10:19:17'),
(90, 1, 'Login', NULL, '2025-11-19 02:57:15'),
(91, 1, 'Logout', 'User keluar dari sistem', '2025-11-19 03:01:21'),
(92, 1, 'Login', NULL, '2025-11-20 03:35:37'),
(93, 1, 'Logout', 'User keluar dari sistem', '2025-11-20 03:36:24'),
(94, 1, 'Login', NULL, '2025-11-22 09:22:29'),
(95, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 09:26:02'),
(96, 4, 'Login', NULL, '2025-11-22 09:26:13'),
(97, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-22 09:26:27'),
(98, 4, 'Logout', 'User keluar dari sistem', '2025-11-22 09:38:59'),
(99, 1, 'Login', NULL, '2025-11-22 09:39:04'),
(100, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 09:44:45'),
(101, 3, 'Login', NULL, '2025-11-22 09:44:57'),
(102, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-22 09:49:11'),
(103, 3, 'Logout', 'User keluar dari sistem', '2025-11-22 10:05:53'),
(104, 1, 'Login', NULL, '2025-11-22 10:06:06'),
(105, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 10:08:33'),
(106, 1, 'Login', NULL, '2025-11-22 10:23:31'),
(107, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 10:32:45'),
(108, 1, 'Login', NULL, '2025-11-22 10:33:50'),
(109, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-22 10:36:01'),
(110, 1, 'Reset Borda', 'Hasil konsensus Borda dihapus', '2025-11-22 10:36:07'),
(111, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-22 10:36:15'),
(112, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-22 10:37:19'),
(113, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-22 10:37:23'),
(114, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-11-22 10:37:41'),
(115, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 10:37:44'),
(116, 4, 'Login', NULL, '2025-11-22 10:37:53'),
(117, 4, 'Logout', 'User keluar dari sistem', '2025-11-22 10:38:56'),
(118, 1, 'Login', NULL, '2025-11-22 10:39:04'),
(119, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 10:57:03'),
(120, 1, 'Login', NULL, '2025-11-22 10:59:31'),
(121, 1, 'Login', NULL, '2025-11-22 11:00:51'),
(122, 1, 'Logout', 'User keluar dari sistem', '2025-11-22 11:02:06'),
(123, 2, 'Login', NULL, '2025-11-22 11:02:16'),
(124, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-22 11:04:32'),
(125, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-22 11:05:13'),
(126, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-22 11:05:51'),
(127, 2, 'Logout', 'User keluar dari sistem', '2025-11-22 11:06:17'),
(128, 1, 'Login', NULL, '2025-11-22 11:06:24'),
(129, 1, 'Login', NULL, '2025-11-23 04:07:37'),
(130, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:15:14'),
(131, 1, 'Login', NULL, '2025-11-23 04:16:15'),
(132, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:17:25'),
(133, 1, 'Login', NULL, '2025-11-23 04:18:52'),
(134, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:20:33'),
(135, 2, 'Login', NULL, '2025-11-23 04:20:49'),
(136, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 04:20:55'),
(137, 2, 'Logout', 'User keluar dari sistem', '2025-11-23 04:21:06'),
(138, 1, 'Login', NULL, '2025-11-23 04:21:14'),
(139, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:21:27'),
(140, 1, 'Login', NULL, '2025-11-23 04:22:54'),
(141, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:24:03'),
(142, 1, 'Login', NULL, '2025-11-23 04:29:33'),
(143, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:37:04'),
(144, 1, 'Login', NULL, '2025-11-23 04:37:15'),
(145, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:46:13'),
(146, 1, 'Login', NULL, '2025-11-23 04:46:24'),
(147, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:50:26'),
(148, 1, 'Login', NULL, '2025-11-23 04:50:42'),
(149, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 04:51:27'),
(150, 2, 'Login', NULL, '2025-11-23 04:51:35'),
(151, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 04:51:39'),
(152, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 04:51:55'),
(153, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 04:52:57'),
(154, 2, 'Logout', 'User keluar dari sistem', '2025-11-23 05:00:48'),
(155, 1, 'Login', NULL, '2025-11-23 05:01:13'),
(156, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 05:03:24'),
(157, 1, 'Login', NULL, '2025-11-23 05:03:35'),
(158, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 06:21:18'),
(159, 3, 'Login', NULL, '2025-11-23 06:21:40'),
(160, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 06:22:07'),
(161, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 06:22:22'),
(162, 3, 'Logout', 'User keluar dari sistem', '2025-11-23 06:22:32'),
(163, 1, 'Login', NULL, '2025-11-23 07:09:04'),
(164, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 07:13:32'),
(165, 1, 'Login', NULL, '2025-11-23 07:13:38'),
(166, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 07:15:50'),
(167, 4, 'Login', NULL, '2025-11-23 07:16:00'),
(168, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 07:16:12'),
(169, 2, 'Login', NULL, '2025-11-23 12:25:44'),
(170, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 12:26:23'),
(171, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-11-23 12:28:01'),
(172, 2, 'Logout', 'User keluar dari sistem', '2025-11-23 12:28:17'),
(173, 4, 'Login', NULL, '2025-11-23 12:28:44'),
(174, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 12:29:19'),
(175, 4, 'Logout', 'User keluar dari sistem', '2025-11-23 12:33:17'),
(176, 4, 'Login', NULL, '2025-11-23 12:33:40'),
(177, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 12:35:27'),
(178, 4, 'Logout', 'User keluar dari sistem', '2025-11-23 12:35:33'),
(179, 1, 'Login', NULL, '2025-11-23 12:35:38'),
(180, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 12:36:04'),
(181, 4, 'Login', NULL, '2025-11-23 12:36:12'),
(182, 4, 'Logout', 'User keluar dari sistem', '2025-11-23 12:45:37'),
(183, 3, 'Login', NULL, '2025-11-23 12:46:03'),
(184, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 12:47:18'),
(185, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 12:54:03'),
(186, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 12:54:33'),
(187, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-11-23 12:54:49'),
(188, 3, 'Logout', 'User keluar dari sistem', '2025-11-23 12:59:20'),
(189, 4, 'Login', NULL, '2025-11-23 13:01:37'),
(190, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 13:01:43'),
(191, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 13:01:59'),
(192, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-11-23 13:02:05'),
(193, 4, 'Logout', 'User keluar dari sistem', '2025-11-23 13:02:45'),
(194, 1, 'Login', NULL, '2025-11-23 13:05:14'),
(195, 1, 'Logout', 'User keluar dari sistem', '2025-11-23 13:18:07'),
(196, 1, 'Login', NULL, '2025-11-23 13:18:15'),
(197, 1, 'Login', NULL, '2025-12-01 07:49:48'),
(198, 1, 'Logout', 'User keluar dari sistem', '2025-12-01 07:51:52'),
(199, 2, 'Login', NULL, '2025-12-01 07:52:14'),
(200, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 07:54:21'),
(201, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:34:43'),
(202, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:35:13'),
(203, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:35:17'),
(204, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:41:23'),
(205, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:46:09'),
(206, 1, 'Reset Borda', 'Hasil konsensus Borda dihapus', '2025-12-01 08:46:36'),
(207, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-12-01 08:46:48'),
(208, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 08:53:35'),
(209, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-01 09:01:08'),
(210, 2, 'Logout', 'User keluar dari sistem', '2025-12-01 09:01:41'),
(211, 3, 'Login', NULL, '2025-12-01 09:01:58'),
(212, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-01 09:02:16'),
(213, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-01 09:02:35'),
(214, 3, 'Logout', 'User keluar dari sistem', '2025-12-01 09:02:45'),
(215, 1, 'Login', NULL, '2025-12-01 09:02:53'),
(216, 1, 'Logout', 'User keluar dari sistem', '2025-12-01 09:14:50'),
(217, 2, 'Login', NULL, '2025-12-01 14:20:07'),
(218, 2, 'Logout', 'User keluar dari sistem', '2025-12-01 14:20:57'),
(219, 1, 'Login', NULL, '2025-12-01 14:21:04'),
(220, 1, 'Logout', 'User keluar dari sistem', '2025-12-01 14:25:47'),
(221, 2, 'Login', NULL, '2025-12-01 14:25:57'),
(222, 2, 'Logout', 'User keluar dari sistem', '2025-12-01 14:27:54'),
(223, 3, 'Login', NULL, '2025-12-01 14:28:05'),
(224, 3, 'Logout', 'User keluar dari sistem', '2025-12-01 14:28:20'),
(225, 4, 'Login', NULL, '2025-12-01 14:28:28'),
(226, 1, 'Login', NULL, '2025-12-02 07:51:16'),
(227, 1, 'Logout', 'User keluar dari sistem', '2025-12-02 08:13:41'),
(228, 1, 'Login', NULL, '2025-12-02 08:14:00'),
(229, 1, 'Logout', 'User keluar dari sistem', '2025-12-02 08:15:19'),
(230, 1, 'Login', NULL, '2025-12-06 10:13:37'),
(231, 1, 'Logout', 'User keluar dari sistem', '2025-12-06 10:14:41'),
(232, 2, 'Login', NULL, '2025-12-06 10:14:48'),
(233, 2, 'Logout', 'User keluar dari sistem', '2025-12-06 10:20:39'),
(234, 1, 'Login', NULL, '2025-12-06 10:20:46'),
(235, 1, 'Logout', 'User keluar dari sistem', '2025-12-06 10:31:50'),
(236, 2, 'Login', NULL, '2025-12-06 10:32:11'),
(237, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-06 10:32:14'),
(238, 2, 'Logout', 'User keluar dari sistem', '2025-12-06 10:34:57'),
(239, 3, 'Login', NULL, '2025-12-06 10:35:05'),
(240, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-06 10:35:09'),
(241, 3, 'Logout', 'User keluar dari sistem', '2025-12-06 10:35:55'),
(242, 4, 'Login', NULL, '2025-12-06 10:36:10'),
(243, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-12-06 10:36:12'),
(244, 4, 'Logout', 'User keluar dari sistem', '2025-12-06 10:41:52'),
(245, 2, 'Login', NULL, '2025-12-06 10:42:01'),
(246, 2, 'Logout', 'User keluar dari sistem', '2025-12-06 10:53:12'),
(247, 1, 'Login', NULL, '2025-12-06 10:53:19'),
(248, 1, 'Logout', 'User keluar dari sistem', '2025-12-06 10:53:39'),
(249, 4, 'Login', NULL, '2025-12-06 10:53:52'),
(250, 4, 'Logout', 'User keluar dari sistem', '2025-12-06 10:57:55'),
(251, 1, 'Login', NULL, '2025-12-06 10:58:03'),
(252, 1, 'Logout', 'User keluar dari sistem', '2025-12-06 11:02:37'),
(253, 2, 'Login', NULL, '2025-12-06 11:02:51'),
(254, 2, 'Logout', 'User keluar dari sistem', '2025-12-06 11:06:24'),
(255, 3, 'Login', NULL, '2025-12-06 11:06:35'),
(256, 3, 'Logout', 'User keluar dari sistem', '2025-12-06 11:10:35'),
(257, 2, 'Login', NULL, '2025-12-06 16:48:47'),
(258, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-06 16:48:55'),
(259, 2, 'Logout', 'User keluar dari sistem', '2025-12-06 16:49:43'),
(260, 3, 'Login', NULL, '2025-12-06 16:49:51'),
(261, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-06 16:50:07'),
(262, 3, 'Logout', 'User keluar dari sistem', '2025-12-06 16:53:57'),
(263, 2, 'Login', NULL, '2025-12-06 16:54:04'),
(264, 1, 'Reset Borda', 'Hasil konsensus Borda dihapus', '2025-12-06 16:54:34'),
(265, 1, 'Konsensus Borda', 'Perhitungan Weighted Borda berhasil dilakukan', '2025-12-06 16:54:59'),
(266, 1, 'Login', NULL, '2025-12-08 08:12:46'),
(267, 1, 'Login', NULL, '2025-12-09 00:30:41'),
(268, 1, 'Logout', 'User keluar dari sistem', '2025-12-09 00:31:17'),
(269, 2, 'Login', NULL, '2025-12-09 00:31:22'),
(270, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-09 00:31:42'),
(271, 1, 'Login', NULL, '2025-12-09 01:00:45'),
(272, 1, 'Logout', 'User keluar dari sistem', '2025-12-09 01:02:05'),
(273, 2, 'Login', NULL, '2025-12-09 01:02:13'),
(274, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-09 01:05:30'),
(275, 2, 'Logout', 'User keluar dari sistem', '2025-12-09 01:07:42'),
(276, 3, 'Login', NULL, '2025-12-09 01:08:03'),
(277, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-09 01:08:09'),
(278, 3, 'Logout', 'User keluar dari sistem', '2025-12-09 01:08:16'),
(279, 4, 'Login', NULL, '2025-12-09 01:08:25'),
(280, 4, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Ketua RT berhasil dihitung', '2025-12-09 01:08:33'),
(281, 4, 'Logout', 'User keluar dari sistem', '2025-12-09 01:08:42'),
(282, 1, 'Login', NULL, '2025-12-09 03:23:07'),
(283, 1, 'Logout', 'User keluar dari sistem', '2025-12-09 03:24:16'),
(284, 2, 'Login', NULL, '2025-12-09 03:24:27'),
(285, 2, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Bapak Kepala Desa berhasil dihitung', '2025-12-09 03:24:57'),
(286, 2, 'Logout', 'User keluar dari sistem', '2025-12-09 03:27:20'),
(287, 3, 'Login', NULL, '2025-12-09 03:27:31'),
(288, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-09 03:27:56'),
(289, 3, 'Perhitungan TOPSIS', 'Hasil TOPSIS untuk Ibu Sekretaris Desa berhasil dihitung', '2025-12-09 03:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `alternatif_id` int NOT NULL,
  `c1_pekerjaan` int NOT NULL,
  `c2_tanggungan` int NOT NULL,
  `c3_penghasilan` int NOT NULL,
  `c4_kondisi_rumah` int NOT NULL,
  `c5_status_rumah` int NOT NULL,
  `c6_kepemilikan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id`, `user_id`, `alternatif_id`, `c1_pekerjaan`, `c2_tanggungan`, `c3_penghasilan`, `c4_kondisi_rumah`, `c5_status_rumah`, `c6_kepemilikan`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 3, 3, 4, 6, 4, 2, '2025-11-15 08:53:38', '2025-11-15 09:16:10'),
(2, 2, 2, 4, 5, 3, 4, 2, 3, '2025-11-15 08:54:10', '2025-11-15 08:54:10'),
(3, 2, 3, 5, 4, 1, 5, 4, 2, '2025-11-15 08:54:32', '2025-11-15 08:54:32'),
(4, 2, 4, 5, 5, 2, 3, 2, 3, '2025-11-15 08:54:54', '2025-11-15 08:54:54'),
(5, 3, 1, 3, 3, 3, 5, 4, 2, '2025-11-15 09:16:59', '2025-11-15 09:19:05'),
(6, 3, 2, 4, 5, 2, 4, 3, 3, '2025-11-15 09:17:36', '2025-11-15 09:21:55'),
(7, 3, 3, 5, 4, 2, 5, 4, 2, '2025-11-15 09:18:00', '2025-11-15 09:22:06'),
(8, 3, 4, 5, 5, 2, 4, 3, 3, '2025-11-15 09:18:23', '2025-11-15 09:22:19'),
(9, 4, 1, 3, 4, 4, 6, 3, 2, '2025-11-15 09:23:22', '2025-11-15 09:23:22'),
(10, 4, 4, 4, 5, 2, 3, 2, 3, '2025-11-15 09:24:19', '2025-11-15 09:24:19'),
(11, 4, 2, 4, 4, 3, 5, 2, 3, '2025-11-15 09:24:42', '2025-11-15 09:24:42'),
(12, 4, 3, 5, 3, 1, 6, 4, 2, '2025-11-15 09:25:02', '2025-11-15 09:25:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','kepala_desa','sekretaris','ketua_rt') NOT NULL,
  `level` int NOT NULL COMMENT '0=admin, 1=kepala_desa, 2=sekretaris, 3=ketua_rt',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `role`, `level`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin', 0, '2025-11-14 17:05:42'),
(2, 'kepaladesa', '1bd5da988b535455b33007aca5bb5b87', 'Bapak Kepala Desa', 'kepala_desa', 1, '2025-11-14 17:05:42'),
(3, 'sekretaris', 'f89bd2a4456bd37c9314a35efd133a57', 'Ibu Sekretaris Desa', 'sekretaris', 2, '2025-11-14 17:05:42'),
(4, 'ketuart', '327f42dc9cc897f17dc63852d31d3a99', 'Bapak Ketua RT', 'ketua_rt', 3, '2025-11-14 17:05:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `hasil_borda`
--
ALTER TABLE `hasil_borda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alternatif_id` (`alternatif_id`);

--
-- Indexes for table `hasil_topsis`
--
ALTER TABLE `hasil_topsis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_hasil` (`user_id`,`alternatif_id`),
  ADD KEY `alternatif_id` (`alternatif_id`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_penilaian` (`user_id`,`alternatif_id`),
  ADD KEY `alternatif_id` (`alternatif_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hasil_borda`
--
ALTER TABLE `hasil_borda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `hasil_topsis`
--
ALTER TABLE `hasil_topsis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_borda`
--
ALTER TABLE `hasil_borda`
  ADD CONSTRAINT `hasil_borda_ibfk_1` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hasil_topsis`
--
ALTER TABLE `hasil_topsis`
  ADD CONSTRAINT `hasil_topsis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_topsis_ibfk_2` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
