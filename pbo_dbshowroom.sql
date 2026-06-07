-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2026 at 03:17 AM
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
-- Database: `pbo_dbshowroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `tahun` int NOT NULL,
  `stok` int NOT NULL,
  `harga_dasar` decimal(15,2) NOT NULL,
  `transmisi` enum('Manual','AT','CVT') NOT NULL,
  `jumlah_kursi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `brand`, `model`, `tahun`, `stok`, `harga_dasar`, `transmisi`, `jumlah_kursi`) VALUES
(1, 'Toyota', 'Avanza Veloz', 2022, 5, '290000000.00', 'CVT', 7),
(2, 'Honda', 'Civic Turbo RS', 2023, 2, '610000000.00', 'CVT', 5),
(3, 'Mitsubishi', 'Pajero Sport Dakar', 2021, 3, '580000000.00', 'AT', 7),
(4, 'Suzuki', 'Ertiga GL', 2020, 7, '230000000.00', 'Manual', 7),
(5, 'Toyota', 'Kijang Innova Zenix Hybrid', 2023, 4, '540000000.00', 'CVT', 7),
(6, 'Honda', 'CR-V e:HEV', 2024, 1, '810000000.00', 'CVT', 5),
(7, 'Wuling', 'Almaz Hybrid', 2023, 3, '470000000.00', 'AT', 7),
(8, 'Nissan', 'Kicks e-Power', 2022, 2, '510000000.00', 'AT', 5),
(9, 'Hyundai', 'Ioniq 5 Signature', 2023, 4, '780000000.00', 'AT', 5),
(10, 'Wuling', 'Air EV Long Range', 2022, 10, '270000000.00', 'AT', 4),
(11, 'Tesla', 'Model 3 Highland', 2024, 1, '1200000000.00', 'AT', 5),
(12, 'BYD', 'Seal Performance', 2024, 6, '720000000.00', 'AT', 5),
(13, 'Honda', 'CBR1000RR-R Fireblade', 2022, 1, '1050000000.00', 'Manual', 1),
(14, 'Kawasaki', 'Ninja ZX-10R', 2023, 2, '560000000.00', 'Manual', 1),
(15, 'BMW', 'R 1250 GS Adventure', 2021, 1, '940000000.00', 'Manual', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_mobil_hybrid`
--

CREATE TABLE `tb_mobil_hybrid` (
  `id_kendaraan` int NOT NULL,
  `kapasitas_mesin` int NOT NULL,
  `jenis_bahan_bakar` varchar(30) NOT NULL,
  `kapasitas_tangki` decimal(5,2) NOT NULL,
  `kapasitas_baterai` decimal(5,2) NOT NULL,
  `daya_motor_listrik` int NOT NULL,
  `tipe_hybrid` varchar(30) NOT NULL,
  `mode_berkendara` varchar(100) NOT NULL,
  `konsumsi_bbm` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_mobil_hybrid`
--

INSERT INTO `tb_mobil_hybrid` (`id_kendaraan`, `kapasitas_mesin`, `jenis_bahan_bakar`, `kapasitas_tangki`, `kapasitas_baterai`, `daya_motor_listrik`, `tipe_hybrid`, `mode_berkendara`, `konsumsi_bbm`) VALUES
(5, 2000, 'Bensin', '52.00', '1.31', 113, 'HEV (Full Hybrid)', 'EV, Eco, Normal, Power', '1:21 km/l'),
(6, 2000, 'Bensin', '57.00', '1.06', 184, 'HEV (Full Hybrid)', 'Econ, Normal, Sport', '1:25 km/l'),
(7, 2000, 'Bensin', '52.50', '1.80', 174, 'HEV (Full Hybrid)', 'Eco, Normal, Sport', '1:19 km/l'),
(8, 1200, 'Bensin', '41.00', '2.13', 136, 'Series Hybrid', 'Eco, Normal, Sport, EV', '1:23 km/l');

-- --------------------------------------------------------

--
-- Table structure for table `tb_mobil_konvensional`
--

CREATE TABLE `tb_mobil_konvensional` (
  `id_kendaraan` int NOT NULL,
  `kapasitas_mesin` int NOT NULL,
  `jenis_bahan_bakar` varchar(30) NOT NULL,
  `kapasitas_tangki` decimal(5,2) NOT NULL,
  `konsumsi_bbm` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_mobil_konvensional`
--

INSERT INTO `tb_mobil_konvensional` (`id_kendaraan`, `kapasitas_mesin`, `jenis_bahan_bakar`, `kapasitas_tangki`, `konsumsi_bbm`) VALUES
(1, 1500, 'Bensin', '43.00', '1:15 km/l'),
(2, 1500, 'Bensin', '47.00', '1:13 km/l'),
(3, 2400, 'Diesel', '68.50', '1:11 km/l'),
(4, 1500, 'Bensin', '45.00', '1:14 km/l');

-- --------------------------------------------------------

--
-- Table structure for table `tb_mobil_listrik`
--

CREATE TABLE `tb_mobil_listrik` (
  `id_kendaraan` int NOT NULL,
  `kapasitas_baterai` decimal(5,2) NOT NULL,
  `daya_motor_listrik` int NOT NULL,
  `waktu_pengisian` int NOT NULL,
  `jarak_tempuh` int NOT NULL,
  `kecepatan_maksimum` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_mobil_listrik`
--

INSERT INTO `tb_mobil_listrik` (`id_kendaraan`, `kapasitas_baterai`, `daya_motor_listrik`, `waktu_pengisian`, `jarak_tempuh`, `kecepatan_maksimum`) VALUES
(9, '72.60', 217, 46, 481, 185),
(10, '26.70', 40, 240, 300, 100),
(11, '75.00', 283, 30, 513, 225),
(12, '82.56', 530, 30, 520, 240);

-- --------------------------------------------------------

--
-- Table structure for table `tb_motor_besar`
--

CREATE TABLE `tb_motor_besar` (
  `id_kendaraan` int NOT NULL,
  `jenis_bahan_bakar` varchar(30) NOT NULL,
  `kapasitas_mesin` int NOT NULL,
  `kapasitas_tangki` int NOT NULL,
  `konsumsi_bbm` varchar(30) NOT NULL,
  `tipe_motor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_motor_besar`
--

INSERT INTO `tb_motor_besar` (`id_kendaraan`, `jenis_bahan_bakar`, `kapasitas_mesin`, `kapasitas_tangki`, `konsumsi_bbm`, `tipe_motor`) VALUES
(13, 'Bensin', 1000, 16, '1:12 km/l', 'Super Sport'),
(14, 'Bensin', 998, 17, '1:13 km/l', 'Super Sport'),
(15, 'Bensin', 1254, 30, '1:21 km/l', 'Adventure / Touring');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indexes for table `tb_mobil_hybrid`
--
ALTER TABLE `tb_mobil_hybrid`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indexes for table `tb_mobil_konvensional`
--
ALTER TABLE `tb_mobil_konvensional`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indexes for table `tb_mobil_listrik`
--
ALTER TABLE `tb_mobil_listrik`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indexes for table `tb_motor_besar`
--
ALTER TABLE `tb_motor_besar`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_mobil_hybrid`
--
ALTER TABLE `tb_mobil_hybrid`
  ADD CONSTRAINT `tb_mobil_hybrid_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE CASCADE;

--
-- Constraints for table `tb_mobil_konvensional`
--
ALTER TABLE `tb_mobil_konvensional`
  ADD CONSTRAINT `tb_mobil_konvensional_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE CASCADE;

--
-- Constraints for table `tb_mobil_listrik`
--
ALTER TABLE `tb_mobil_listrik`
  ADD CONSTRAINT `tb_mobil_listrik_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE CASCADE;

--
-- Constraints for table `tb_motor_besar`
--
ALTER TABLE `tb_motor_besar`
  ADD CONSTRAINT `tb_motor_besar_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
