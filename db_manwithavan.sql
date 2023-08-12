-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2023 at 07:37 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_manwithavan`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `driver_id` int NOT NULL,
  `service_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_home_moving` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `other_service_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `address_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `eircode_from` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `eircode_to` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `selected_date` date NOT NULL,
  `selected_hour` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `helper` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `release_another_booking` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `service_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `estimated_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `vehicle_id`, `driver_id`, `service_type`, `is_home_moving`, `other_service_type`, `address_from`, `eircode_from`, `address_to`, `eircode_to`, `selected_date`, `selected_hour`, `notes`, `helper`, `release_another_booking`, `service_status`, `estimated_price`, `feedback`) VALUES
(1, 5, 1, 2, 'Delivery and Collection', '', '', 'IKEA BALLYMUN', 'D13F824', '2 PARNELL STREET UPPER', 'D07AS5D', '2023-08-16', '09:00 - 11:00', 'Order number is 1545656, to collect a bed', 'No', 'No', 'Pending', '55.00', NULL),
(2, 5, 2, 3, 'Motorcycle Transportation', '', '', 'BR MOTORCYCLE', 'D14A25S', '25 CORK ST', 'D015D41', '2023-08-07', '14:00 - 16:00', '', 'No', 'No', 'Canceled', '75.00', NULL),
(3, 5, 1, 2, 'Home Moving', 'Suitcases, Boxes, Bags, Television, Bicycle', '', 'CORBALLY HEATH', 'D24S52F', 'CORK CITY', 'P51FG5', '2023-08-23', '09:00 - 11:00', '', 'Yes', 'No', 'Confirmed', '115.00', NULL),
(4, 6, 3, 2, 'Furniture Removal', '', '', 'DUBLIN CITY', 'D015F2', 'RECYCLE CENTRE', 'D241F5', '2023-08-09', '07:00 - 09:00', '', 'Yes', 'No', 'Completed', '150.00', NULL),
(5, 7, 2, 3, 'Delivery and Collection', '', '', 'WOODIES', 'D01RT1D', '15 BORTOBELLO RD', 'D04FG51', '2023-08-09', '11:00 - 13:00', 'Collect a table, order number 2119864', 'No', 'No', 'Completed', '55.00', NULL),
(6, 7, 0, 0, 'Home Moving', 'Suitcases, Boxes, Television', '', '24 DORSET ST', 'D01A5S2', 'RATHMINES RD', 'D04F51R', '2023-08-16', '11:00 - 13:00', 'It a full house moving', 'Yes', 'no', 'Analyzing', '115.00', NULL),
(7, 8, 0, 0, 'Others', '', 'Transporting a piano', 'CORK CITY', 'PD5478R', '45 RANELAGH RD, DUBLIN', 'D04F15F', '2023-08-16', '14:00 - 16:00', '', 'Yes', 'no', 'Analyzing', '0.00', NULL),
(8, 9, 3, 3, 'Delivery and Collection', '', '', 'CYCLEBIKE', 'D03F4F8', 'AVIVA STADIUM', 'D04S568', '2023-08-30', '07:00 - 09:00', '10 bikes needs to be collect at the store', 'No', 'No', 'Confirmed', '110.00', NULL),
(9, 10, 0, 0, 'Furniture Removal', '', '', '254 CABRA RD', 'D07F2S5', 'RECYCLE CENTRE', 'D241F5', '2023-09-01', '09:00 - 11:00', 'I have one bed and three large furniture to the dump', 'Yes', 'no', 'Analyzing', '150.00', NULL),
(10, 6, 2, 2, 'Motorcycle Transportation', '', '', 'BR MOTORCYCLE', 'D13F824', '45 RANELAGH RD, DUBLIN', 'D04FG51', '2023-08-29', '09:00 - 11:00', 'My moto is Hornet white 600cc 2011', 'No', 'No', 'Pending', '45.00', NULL),
(11, 8, 3, 2, 'Home Moving', 'Bags', '', '204 CABRA RD', 'D07D541', '45 RATHGAR RD', 'D12F5F9', '2023-08-23', '11:00 - 13:00', '', 'Yes', 'No', 'Pending', '150.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

DROP TABLE IF EXISTS `finance`;
CREATE TABLE IF NOT EXISTS `finance` (
  `finance_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `prices` decimal(10,2) NOT NULL DEFAULT '0.00',
  `expenses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transaction_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`finance_id`),
  KEY `booking_id` (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `firstname` varchar(51) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `lastname` varchar(51) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(140) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `passw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nationality` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `aboutus` varchar(41) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `usertype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `title`, `firstname`, `lastname`, `email`, `passw`, `phone`, `nationality`, `aboutus`, `usertype`, `user_status`) VALUES
(1, 'Mr. ', 'Maxwell William', 'Ferreira', 'max.95.jf@gmail.com', '$2y$10$VazXIUDf.xvPR9dgrpmQk.g5Gf21o0A2wIMN8jUErDlLNXBsTemOa', '+353 85 462 3541', 'brazilian', 'Other', 'admin', 'active'),
(2, 'Mr. ', 'Luan', 'Dutra', 'luan@luan.com', '$2y$10$enorNw5GbjwyzMx6ALWz3e4VJgQPVX3MZM2jksjW/9lNZA3mLcnuW', '+353 85 658 7849', 'brazilian', 'Other', 'driver', 'active'),
(3, 'Mr. ', 'Oseias', 'Quintanilha', 'oseias@oseias.com', '$2y$10$zpyqhQv4J4xAXNT8e1RPiuy2uHMw0tuQm2tmAHxKvXm4BGRdqOipC', '+353 84 578 4857', 'brazilian', 'Other', 'driver', 'active'),
(4, 'Mr. ', 'Kleber', 'Silva', 'kleber@kleber.com', '$2y$10$nk1Z0oNDH2TmsqaT3G3DeO.oBQLYh0QPwVxZci9js.yGiqpHaGeWW', '+353 86 584 8785', 'brazilian', 'Other', 'driver', 'inactive'),
(5, 'Mr. ', 'Alisson', 'Rosendo', 'alisson@alisson.com', '$2y$10$pXnJfX5A6nJ3GjiEUDXJ3e9AZkROeneCtjIjTBBU.UmwSbWwZRRJi', '+353 85 485 7845', 'brazilian', 'Friend Recommendation', 'customer', 'active'),
(6, 'Mr. ', 'Victor', 'Costa', 'victor@victor.com', '$2y$10$XA4uV9o/OrVbrTnT6VXFNe4ZvMXeW8rUGOnavrErq8lmOmUNrAquK', '+353 85 955 8476', 'brazilian', 'Instagram', 'customer', 'active'),
(7, 'Mrs. ', 'Patricia', 'Vasconcelos', 'pati@pati.com', '$2y$10$cd.D6p7BwnENpe1F24NgfusUHQc072nmeXcLXYN9PhtFuh1xMoidS', '+353 85 458 5872', 'brazilian', 'Facebook', 'customer', 'active'),
(8, 'Mrs. ', 'Geice', 'Kelly', 'geice@geice.com', '$2y$10$SUjPy4AkOP.C22wkAbwSQOCaY86ckG5mbUDro4ZW/7uqmWPkZZa5K', '+353 85 481 8541', 'brazilian', 'Email', 'customer', 'active'),
(9, 'Mr. ', 'Renan', 'Mergulhao', 'renan@renan.com', '$2y$10$IGD0oHoZDwcYQapx6/nLMOQgdwBT8471imAO9rJBnxPbB46IQbDzi', '+353 86 251 4185', 'brazilian', 'Outdoor Banner', 'customer', 'active'),
(10, 'Mrs. ', 'Dani', 'Reis', 'dani@dani.com', '$2y$10$IM8gWg7sk2RflfG1Bw2o0.5R7ADKex7jHeN2jtPNploQwnZbuUbqW', '+353 83 254 1575', 'brazilian', 'Newspaper', 'customer', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `vehicle_id` int NOT NULL AUTO_INCREMENT,
  `vehicle_reg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `make` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `model` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `colour` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `year` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fuel_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `vehicle_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`vehicle_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `vehicle_reg`, `make`, `model`, `colour`, `year`, `fuel_type`, `vehicle_status`) VALUES
(1, '152D13547', 'RENAULT', 'TRAFIC', 'WHITE', '2015', 'DIESEL', 'active'),
(2, '172D19082', 'OPEL', 'VIVARO', 'WHITE', '2017', 'DIESEL', 'active'),
(3, '192D27669', 'RENAULT', 'MASTER', 'WHITE', '2019', 'DIESEL', 'active'),
(4, '172D54824', 'FORD', 'COURIER', 'WHITE', '2017', 'DIESEL', 'inactive');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
