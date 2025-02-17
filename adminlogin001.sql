-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 05:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adminlogin001`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `Id` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `E-mail` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`Id`, `Username`, `Password`, `E-mail`) VALUES
(1, 'Bhairav', 'bhairav123', ''),
(2, 'isha', 'isha123', '');

-- --------------------------------------------------------

--
-- Table structure for table `inspections`
--

CREATE TABLE `inspections` (
  `id` int(11) NOT NULL,
  `inspector_name` varchar(255) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `po_number` varchar(255) DEFAULT NULL,
  `product_front_view` varchar(255) DEFAULT NULL,
  `product_back_view` varchar(255) DEFAULT NULL,
  `product_side_view` varchar(255) DEFAULT NULL,
  `gift_box_front_view` varchar(255) DEFAULT NULL,
  `product_barcode` varchar(255) DEFAULT NULL,
  `inner_box_barcode` varchar(255) DEFAULT NULL,
  `outer_box_barcode` varchar(255) DEFAULT NULL,
  `inner_box_front_view` varchar(255) DEFAULT NULL,
  `master_carton_front_view` varchar(255) DEFAULT NULL,
  `right_item` tinyint(1) DEFAULT NULL,
  `wrong_item` tinyint(1) DEFAULT NULL,
  `right_desc` tinyint(1) DEFAULT NULL,
  `wrong_desc` tinyint(1) DEFAULT NULL,
  `right_vendor` tinyint(1) DEFAULT NULL,
  `wrong_vendor` tinyint(1) DEFAULT NULL,
  `right_po` tinyint(1) DEFAULT NULL,
  `wrong_po` tinyint(1) DEFAULT NULL,
  `right_product_details` tinyint(1) DEFAULT NULL,
  `wrong_product_details` tinyint(1) DEFAULT NULL,
  `right_gift_box` tinyint(1) DEFAULT NULL,
  `wrong_gift_box` tinyint(1) DEFAULT NULL,
  `product_barcode_confirmed` tinyint(1) DEFAULT NULL,
  `inner_box_barcode_confirmed` tinyint(1) DEFAULT NULL,
  `outer_box_barcode_confirmed` tinyint(1) DEFAULT NULL,
  `right_inner_box` tinyint(1) DEFAULT NULL,
  `wrong_inner_box` tinyint(1) DEFAULT NULL,
  `right_master_carton` tinyint(1) DEFAULT NULL,
  `wrong_master_carton` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspector`
--

CREATE TABLE `inspector` (
  `id` int(11) NOT NULL,
  `inspectorname` varchar(100) NOT NULL,
  `inspectornum` bigint(100) NOT NULL,
  `inspectormail` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inspector`
--

INSERT INTO `inspector` (`id`, `inspectorname`, `inspectornum`, `inspectormail`, `password`) VALUES
(1, 'om', 7678345014, 'omk5982@gmail.com', ''),
(2, 'deepak', 9832823282, 'deepak@gamil.com', ''),
(3, 'Bhairav', 33238923322, 'bhairav@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ID` int(11) NOT NULL,
  `ItemNumber` varchar(100) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Vendor` varchar(50) NOT NULL,
  `PONumber` varchar(100) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Colour` varchar(100) NOT NULL,
  `Quality` varchar(100) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Weight` int(11) NOT NULL,
  `Dimentions` varchar(100) NOT NULL,
  `DOI` date NOT NULL,
  `inspectorid` int(11) NOT NULL,
  `inspectorname` varchar(100) NOT NULL,
  `inspectornum` bigint(20) NOT NULL,
  `inspectormail` varchar(100) NOT NULL,
  `Admin_Name` varchar(100) NOT NULL,
  `allowed_latitude` decimal(10,7) NOT NULL,
  `allowed_longitude` decimal(10,7) NOT NULL,
  `report_status` varchar(50) DEFAULT 'Pending',
  `status` enum('new','pending','completed') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ID`, `ItemNumber`, `Description`, `Vendor`, `PONumber`, `Name`, `Colour`, `Quality`, `Quantity`, `Weight`, `Dimentions`, `DOI`, `inspectorid`, `inspectorname`, `inspectornum`, `inspectormail`, `Admin_Name`, `allowed_latitude`, `allowed_longitude`, `report_status`, `status`) VALUES
(40, 'RE33', 'Dark Bluer/White Circle Ceramic Drawer Pull', 'Royal Expo.', 'PO03638', 'Drawer Puller', 'Blue/White', 'good', 12, 10, '-, 5cm, 10cm, 3cm, - ', '2024-09-20', 1, 'om', 0, 'omk5982@gmail.com', 'bhairav', 28.4203005, 77.2951612, 'Pending', 'new'),
(41, 'RE332323432aklsefldsljafd', 'Dark Bluer/White Circle Ceramic Drawer Pull', 'Royal Expo.', 'PO03638', 'Drawer Puller', 'asd', 'kjdjf', 31, 0, '-, 5cm, 10cm, 3cm, - ', '2024-12-26', 0, 'om', 0, 'omk5982@gmail.com', 'Bhairav', 28.4203005, 77.2969899, 'Pending', 'new');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspector`
--
ALTER TABLE `inspector`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inspections`
--
ALTER TABLE `inspections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `inspector`
--
ALTER TABLE `inspector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
