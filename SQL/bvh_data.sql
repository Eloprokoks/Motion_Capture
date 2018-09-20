-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2018 at 05:09 PM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bvh_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

CREATE TABLE `channels` (
  `ID` int(11) NOT NULL,
  `X_position` float DEFAULT NULL,
  `Y_position` float DEFAULT NULL,
  `Z_position` float DEFAULT NULL,
  `X_rotation` float DEFAULT NULL,
  `Y_rotation` float DEFAULT NULL,
  `Z_rotation` float DEFAULT NULL,
  `joint_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `ID` int(11) NOT NULL,
  `file_name` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `frames` int(11) NOT NULL,
  `frame_time` float NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`ID`, `file_name`, `frames`, `frame_time`, `date`) VALUES
(9, 'nowy.bvh', 926, 0.0333333, '2018-09-20 17:03:23');

-- --------------------------------------------------------

--
-- Table structure for table `joints`
--

CREATE TABLE `joints` (
  `ID` int(11) NOT NULL,
  `name` varchar(11) COLLATE utf8mb4_polish_ci NOT NULL,
  `offset_x` float NOT NULL,
  `offset_y` float NOT NULL,
  `offset_z` float NOT NULL,
  `number_of_channels` int(2) DEFAULT NULL,
  `file_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `joints`
--

INSERT INTO `joints` (`ID`, `name`, `offset_x`, `offset_y`, `offset_z`, `number_of_channels`, `file_ID`) VALUES
(63, 'Hips', 47.861, 50.121, -306.004, 6, 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `joints`
--
ALTER TABLE `joints`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channels`
--
ALTER TABLE `channels`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1652;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `joints`
--
ALTER TABLE `joints`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `channels`
--
ALTER TABLE `channels`
  ADD CONSTRAINT `channels_ibfk_1` FOREIGN KEY (`joint_ID`) REFERENCES `joints` (`ID`);

--
-- Constraints for table `joints`
--
ALTER TABLE `joints`
  ADD CONSTRAINT `joints_ibfk_1` FOREIGN KEY (`file_ID`) REFERENCES `files` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
