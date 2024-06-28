-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2024 at 10:55 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portofolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_admin` varchar(255) NOT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `jurusan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `nama_admin`, `prodi_id`, `jurusan_id`) VALUES
(16, 26, 'dfsd', 14, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cdcs`
--

CREATE TABLE `cdcs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_cdc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurusans`
--

CREATE TABLE `jurusans` (
  `id` int(11) NOT NULL,
  `nama_jurusan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusans`
--

INSERT INTO `jurusans` (`id`, `nama_jurusan`) VALUES
(1, 'Teknik Sipil dan Kebumian'),
(2, 'Teknik Mesin'),
(3, 'Administrasi Bisnis'),
(4, 'Teknik Elektro'),
(5, 'Akuntansi');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswas`
--

CREATE TABLE `mahasiswas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_mahasiswa` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `jurusan_id` int(11) NOT NULL,
  `jk` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `tahun_masuk` int(11) NOT NULL,
  `status` enum('mahasiswa aktif','alumni') NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaans`
--

CREATE TABLE `perusahaans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat_perusahaan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prodis`
--

CREATE TABLE `prodis` (
  `id` int(11) NOT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `jurusan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodis`
--

INSERT INTO `prodis` (`id`, `nama_prodi`, `jurusan_id`) VALUES
(1, 'D4 Sarjana Terapan Teknik Bangunan Rawa', NULL),
(2, 'D4 Sarjana Terapan Teknologi Rekayasa Geomatika dan Survei', NULL),
(3, 'D4 Sarjana Terapan Teknologi Rekayasa Kontruksi Jalan dan Jembatan', NULL),
(4, 'D3 Teknik Sipil', NULL),
(5, 'D3 Teknik Pertambangan', NULL),
(6, 'D4 Sarjana Terapan Teknologi Rekayasa Otomotif', NULL),
(7, 'D3 Alat Berat', NULL),
(8, 'D3 Teknik Mesin', NULL),
(9, 'D2 Fast Track Tata Operasi dan Pemeliharaan Prediktif Alat Berat', NULL),
(10, 'D4 Sarjana Terapan Bisnis Digital', NULL),
(11, 'D3 Administrasi Bisnis', NULL),
(12, 'D3 Manajemen Informatika', NULL),
(13, 'D4 Sarjana Terapan Teknologi Rekayasa Pembangkit Energi', NULL),
(14, 'D4 Sarjana Terapan Sistem Informasi Kota Cerdas', NULL),
(15, 'D4 Sarjana Terapan Teknologi Rekayasa Otomasi', NULL),
(16, 'D3 Teknik Listrik', NULL),
(17, 'D3 Teknik Informatika', NULL),
(18, 'D4 Sarjana Terapan Akuntansi Lembaga Keuangan Syariah', NULL),
(19, 'D3 Akuntansi', NULL),
(20, 'D3 Komputerisasi Akuntansi', NULL),
(21, 'D3 Elektronika', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mahasiswa','perusahaan','cdc') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'admin', '$2y$10$UMzdpYbpz1/3sSHh3nab0ekCCbD/ZfxzSeXw7lP.lerhVH0RiY6Qy', 'admin'),
(4, 'cdc', '$2y$10$MoLBdXr/kW5O58K3dXs1LuEEaTwP4VcrgraOaFR0EzLUk9TR9jveS', 'cdc'),
(5, 'mahasiswa', '$2y$10$VOn9KB4Gqhr9z8MBxc1q2uZamVMs/l65A4OvkpHKz4WAoiBWgHaVW', 'mahasiswa'),
(6, 'perusahaan', '$2y$10$B.D3Z.PxM5ZBRX61pNSB6.e.bdsLaM/7LnILv/R8wupVnCuZ0wIDW', 'perusahaan'),
(22, 'contoh', '$2y$10$tnk0RtT9QXwfe.yw5lit1OhgsY02btJowzajyD81jRoyY1mTYR.p6', 'admin'),
(23, 'adminti', '$2y$10$0/OGOJZ3mTLH.1ZrYGsBOOKCaMiNTNKZ41hCzgMKt02dLPgtgt8V2', 'admin'),
(24, 'adminlula', '$2y$10$BxuNCQEgRo6q47LJZ92gmurH2rEkMbVztRB4ZUwFEiZBEFUjYphKu', 'admin'),
(25, 'admin1', '$2y$10$i8npCyvB20HSplFjdzqCdOIxdpFxG/5NiTxwYWCEwCP4MY9dHxEG.', 'admin'),
(26, 'adasd', '$2y$10$v3amiRaL45UZgfB5O1LsA.SrGA23KR2UO2ywcnUPHYJMAdBeWgzYa', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prodi_id` (`prodi_id`),
  ADD KEY `jurusan_id` (`jurusan_id`),
  ADD KEY `fk_admin_user` (`user_id`);

--
-- Indexes for table `cdcs`
--
ALTER TABLE `cdcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jurusans`
--
ALTER TABLE `jurusans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `prodi_id` (`prodi_id`),
  ADD KEY `jurusan_id` (`jurusan_id`);

--
-- Indexes for table `perusahaans`
--
ALTER TABLE `perusahaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prodis`
--
ALTER TABLE `prodis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurusan_id` (`jurusan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cdcs`
--
ALTER TABLE `cdcs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurusans`
--
ALTER TABLE `jurusans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perusahaans`
--
ALTER TABLE `perusahaans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prodis`
--
ALTER TABLE `prodis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `admins_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`),
  ADD CONSTRAINT `admins_ibfk_3` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`),
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cdcs`
--
ALTER TABLE `cdcs`
  ADD CONSTRAINT `cdcs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  ADD CONSTRAINT `mahasiswas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `mahasiswas_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`),
  ADD CONSTRAINT `mahasiswas_ibfk_3` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`);

--
-- Constraints for table `perusahaans`
--
ALTER TABLE `perusahaans`
  ADD CONSTRAINT `perusahaans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `prodis`
--
ALTER TABLE `prodis`
  ADD CONSTRAINT `prodis_ibfk_1` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusans` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
