-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 06:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksampah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID_Admin` int(11) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Nama_Lengkap` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Jabatan` varchar(50) DEFAULT NULL,
  `No_Hp` varchar(15) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID_Admin`, `Username`, `Nama_Lengkap`, `Email`, `Jabatan`, `No_Hp`, `Password`) VALUES
(0, 'yohanis', 'Yohanis Patimang', 'yohanispatimang337@gmail.com', 'Direktur', '081243913691', 'BSUsukses');

-- --------------------------------------------------------

--
-- Table structure for table `nasabah`
--

CREATE TABLE `nasabah` (
  `ID_Nasabah` int(11) NOT NULL,
  `Nama_Nasabah` varchar(100) DEFAULT NULL,
  `Nomor_Induk` varchar(50) DEFAULT NULL,
  `Alamat_Nasabah` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nasabah`
--

INSERT INTO `nasabah` (`ID_Nasabah`, `Nama_Nasabah`, `Nomor_Induk`, `Alamat_Nasabah`) VALUES
(1, 'Amalia Putri', '731504680603002', 'Jl. Racing Center'),
(2, 'Liski', '7320329082484', 'Jl. Racing Center');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `ID_Penjualan` varchar(10) NOT NULL,
  `ID_Setoran` varchar(10) DEFAULT NULL,
  `ID_Sampah` varchar(10) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Pembeli` varchar(100) DEFAULT NULL,
  `Berat_Sampah` decimal(10,2) DEFAULT NULL,
  `Total_Harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `ID_Sampah` varchar(10) NOT NULL,
  `Jenis_Sampah` varchar(50) DEFAULT NULL,
  `Harga_Beli` decimal(10,2) DEFAULT NULL,
  `Harga_Jual` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`ID_Sampah`, `Jenis_Sampah`, `Harga_Beli`, `Harga_Jual`) VALUES
('A01', 'PP Gelas Bening bersih', 5000.00, 6000.00),
('A02', 'PP Gelas Bening kotor', 2500.00, 3200.00),
('A03', 'PP Gelas Warna', 1500.00, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `setoran`
--

CREATE TABLE `setoran` (
  `ID_Setoran` varchar(10) NOT NULL,
  `ID_Nasabah` int(11) DEFAULT NULL,
  `ID_Sampah` varchar(10) DEFAULT NULL,
  `Tanggal` date DEFAULT NULL,
  `Berat_Sampah` decimal(10,2) DEFAULT NULL,
  `Total_Harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setoran`
--

INSERT INTO `setoran` (`ID_Setoran`, `ID_Nasabah`, `ID_Sampah`, `Tanggal`, `Berat_Sampah`, `Total_Harga`) VALUES
('S01', 1, 'A01', '2024-12-12', 10.00, 50000.00),
('S02', 1, 'A01', '2024-12-13', 8.00, 40000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID_Admin`);

--
-- Indexes for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`ID_Nasabah`),
  ADD UNIQUE KEY `Nomor_Induk` (`Nomor_Induk`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`ID_Penjualan`),
  ADD KEY `ID_Setoran` (`ID_Setoran`),
  ADD KEY `ID_Sampah` (`ID_Sampah`);

--
-- Indexes for table `sampah`
--
ALTER TABLE `sampah`
  ADD PRIMARY KEY (`ID_Sampah`);

--
-- Indexes for table `setoran`
--
ALTER TABLE `setoran`
  ADD PRIMARY KEY (`ID_Setoran`),
  ADD KEY `ID_Nasabah` (`ID_Nasabah`),
  ADD KEY `ID_Sampah` (`ID_Sampah`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nasabah`
--
ALTER TABLE `nasabah`
  MODIFY `ID_Nasabah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`ID_Setoran`) REFERENCES `setoran` (`ID_Setoran`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`ID_Sampah`) REFERENCES `sampah` (`ID_Sampah`);

--
-- Constraints for table `setoran`
--
ALTER TABLE `setoran`
  ADD CONSTRAINT `setoran_ibfk_1` FOREIGN KEY (`ID_Nasabah`) REFERENCES `nasabah` (`ID_Nasabah`),
  ADD CONSTRAINT `setoran_ibfk_2` FOREIGN KEY (`ID_Sampah`) REFERENCES `sampah` (`ID_Sampah`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
