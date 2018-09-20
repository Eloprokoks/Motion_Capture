-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2018 at 01:07 PM
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
(1, 'nowy.bvh', 926, 0.0333333, '2018-09-20 12:19:47'),
(2, 'Aktor1_Kinect1_bieg_N1.bvh', 926, 0.0333333, '2018-09-20 12:21:08'),
(3, 'noElo.bvh', 1300, 0.025, '2018-09-20 12:21:28'),
(4, 'noElo.bvh', 1300, 0.025, '2018-09-20 13:01:57'),
(5, 'noElo.bvh', 1300, 0.025, '2018-09-20 13:05:10');

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
(1, 'Hips', 0, 0, 0, 6, 3),
(3, 'Hips', 0, 0, 0, 6, 4),
(5, 'Hips', 0, 0, 0, 6, 5),
(6, 'Chest', 0, 10.6789, 0.00628, 3, 5),
(7, 'Chest2', 0, 10.4912, -0.011408, 3, 5),
(8, 'Chest3', 0, 9.47934, 0, 3, 5),
(9, 'Chest4', 0, 9.47934, 0, 3, 5),
(10, 'Neck', 0, 13.5353, 0, 3, 5),
(11, 'Head', 0, 8.81908, -0.027129, 3, 5),
(12, 'End Site 88', 0, 0, 16.9666, 0, 5),
(13, 'RightCollar', -3.01255, 7.54515, 0, 3, 5),
(14, 'RightShould', -13.6831, 0, 0, 3, 5),
(15, 'RightElbow', -26.36, 0, 0, 3, 5),
(16, 'RightWrist', -21.7467, 0, 0.008601, 3, 5),
(17, 'End Site 14', 0, -16.3481, 0, 0, 5),
(18, 'LeftCollar', 3.01255, 7.54515, 0, 3, 5),
(19, 'LeftShoulde', 13.6831, 0, 0, 3, 5),
(20, 'LeftElbow', 26.36, 0, 0, 3, 5),
(21, 'LeftWrist', 21.7467, 0, 0.008601, 3, 5),
(22, 'End Site 20', 0, 16.3481, 0, 0, 5),
(23, 'RightHip', -8.62248, -0.030774, -0.00314, 3, 5),
(24, 'RightKnee', 0, -37.2092, -0.00263, 3, 5),
(25, 'RightAnkle', 0, -37.3433, -0.058479, 3, 5),
(26, 'RightToe', 0, -8.90347, 15.0881, 3, 5),
(27, 'End Site 27', 0, 0, -1.47174, 0, 5),
(28, 'LeftHip', 8.62248, -0.030774, -0.00314, 3, 5),
(29, 'LeftKnee', 0, -37.2092, -0.00263, 3, 5),
(30, 'LeftAnkle', 0, -37.3433, -0.058479, 3, 5),
(31, 'LeftToe', 0, -8.90347, 15.0881, 3, 5),
(32, 'End Site 33', 0, 0, -1.47174, 0, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `channels`
--
ALTER TABLE `channels`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `joint_ID` (`joint_ID`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `joints`
--
ALTER TABLE `joints`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
