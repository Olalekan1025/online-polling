-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 22, 2025 at 04:41 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `poll`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `candidateID` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `pollID` varchar(50) DEFAULT NULL,
  `sname` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `fname` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `oname` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` varchar(90) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `address` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `imagePath` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `position` varchar(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Vying Position or Office',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `manifesto` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `regDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`sn`, `candidateID`, `hostID`, `pollID`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `imagePath`, `position`, `status`, `manifesto`, `regDate`, `modifiedDate`) VALUES
(2, 'CDPCMKA4HM', 'c3586122184', 'MNUIYQYGUC', 'Oluwatosin', 'Daniel', 'Paul', 'male', 'homdroidtech@gmail.com', '09098098090903', 'sdsd', 'resources/candidate_images/homdroidtech@gmail.com-roe.png', 'SOF-306', 'active', 'sdfsd', '2025-02-21 14:52:09', NULL),
(3, 'CDEU6TPJDS', 'c3586122184', 'MNUIYQYGUC', 'Oluwatosin', 'Daniel', 'Paul', 'male', 'homdroidtech2@gmail.com', '09098098090903', 'dfdfgse', 'resources/candidate_images/homdroidtech2@gmail.com-vote.png', 'SOF-306', 'active', 'dfgdfg', '2025-02-21 14:53:28', '2025-02-21 18:02:14');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `logId` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `userID` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eventType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'A description or code representing the type of event that occurred (e.g., login, logout, course enrollment, course completion).',
  `eventID` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Event ID such as unique ID assigned to the product, session etc.\r\n',
  `eventDescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'A more detailed description of the event or action that occurred. This could include information such as the course name, module name, action taken, etc.',
  `ipAddress` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'The IP address of the user who triggered the event. This can be helpful for tracking user activity and identifying potential security issues.',
  `userAgent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `operatingSystem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deviceType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'A status indicator (e.g., success, failure) indicating the outcome of the event.',
  `errorMessage` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT ' If the event resulted in an error, a description of the error message.'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`logId`, `timestamp`, `userID`, `eventType`, `eventID`, `eventDescription`, `ipAddress`, `userAgent`, `browser`, `operatingSystem`, `deviceType`, `status`, `errorMessage`) VALUES
('cw1oxs0vwlw7', '2025-02-19 15:58:36', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', NULL, NULL),
('xawbjcs4wnbr', '2025-02-19 15:58:52', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', 'success', NULL),
('9zslpcu5d67v', '2025-02-19 20:56:09', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', NULL, NULL),
('blgjqqvrt5gl', '2025-02-19 20:56:18', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', 'success', NULL),
('yhlqrm4yfb90', '2025-02-22 15:50:53', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', NULL, NULL),
('4esbpesur1p7', '2025-02-22 15:51:27', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Chrome', 'Windows NT', 'Desktop', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
CREATE TABLE IF NOT EXISTS `polls` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `pollID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hostID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `startDate` timestamp NULL DEFAULT NULL,
  `endDate` timestamp NULL DEFAULT NULL,
  `type` enum('poll','election') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Polls can be Election (Having candidates) or Poll(For Survery or General Questions)',
  `visibility` enum('private','public') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'public',
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('active','upcoming','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'upcoming',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`sn`, `pollID`, `hostID`, `title`, `description`, `startDate`, `endDate`, `type`, `visibility`, `link`, `status`, `createdAt`, `updatedAt`) VALUES
(1, '123456789', 'c3586122184', 'Election 2025', 'Vote for your preferred candidate in the upcoming election.', '2025-02-28 23:00:00', '2025-03-10 22:59:00', 'election', 'public', NULL, 'upcoming', '2025-01-16 08:37:37', '2025-02-20 16:08:46'),
(2, '123456780', 'c3586122184', 'Best Movie 2024', 'Vote for the best movie of 2024.', '2024-11-30 23:00:00', '2024-12-10 22:59:59', 'poll', 'public', NULL, 'active', '2025-01-16 08:37:37', NULL),
(3, '123456781', 'c3586122184', 'Favorite Sports Team', 'Vote for your favorite sports team in the championship.', '2024-10-31 23:00:00', '2024-11-15 22:59:00', 'election', 'private', NULL, 'completed', '2025-01-16 08:37:37', '2025-02-20 22:35:02'),
(4, 'MNUIYQYGUC', 'c3586122184', 'New Poll', 'New Poll', '2025-02-19 23:01:00', '2025-02-21 22:59:00', 'election', 'public', NULL, 'upcoming', '2025-02-19 17:30:16', '2025-02-22 17:18:15');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
CREATE TABLE IF NOT EXISTS `positions` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `positionID` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(15) NOT NULL,
  `status` varchar(10) NOT NULL,
  `regDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedDate` datetime DEFAULT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`sn`, `positionID`, `hostID`, `name`, `abbr`, `status`, `regDate`, `modifiedDate`) VALUES
(1, 'SOF-306', 'c3586122184', 'President', 'Presi', 'active', '2020-03-29 19:46:00', NULL),
(5, 'SOF-856', 'c3586122184', 'Financial Secretary ', 'Fin. Sec', 'active', '2020-03-29 19:46:00', NULL),
(6, 'SOF-119', 'c3586122184', 'Bursar', 'Burs', 'active', '2024-01-20 08:52:00', NULL),
(7, 'SOF-665', 'c3586122184', 'General Secretary', 'Gen. Sec.', 'active', '2024-02-04 23:23:00', NULL),
(8, 'SOF-449', 'c3586122184', 'Treasurer', 'Tres.', 'active', '2024-02-04 23:25:00', NULL),
(9, 'SOF-729', 'c3586122184', 'Deputy General Secretary', 'Dep. Gen. Sec.', 'active', '2024-02-04 23:28:00', NULL),
(10, 'SOF-UL1C9UZ5', 'c3586122184', 'New Position', 'newP.', 'active', '2025-02-21 23:39:07', '2025-02-21 23:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

DROP TABLE IF EXISTS `preferences`;
CREATE TABLE IF NOT EXISTS `preferences` (
  `sn` int NOT NULL,
  `siteURL` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `portalAccess` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`sn`, `siteURL`, `portalAccess`) VALUES
(1, 'https://poll.homdroid.com', 'enable');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `hostID` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `oname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `accountType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Individual Organisation',
  `role` enum('voter','admin','moderator','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin',
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `passport` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `onlineStatus` int DEFAULT NULL,
  `lastAccess` datetime DEFAULT NULL,
  `lastDeviceIP` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verificationCode` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isVerified` int NOT NULL DEFAULT '0',
  `regDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`sn`, `hostID`, `fname`, `lname`, `oname`, `accountType`, `role`, `username`, `email`, `gender`, `phone`, `password`, `passport`, `onlineStatus`, `lastAccess`, `lastDeviceIP`, `verificationCode`, `isVerified`, `regDate`) VALUES
(1, 'c3586122184', 'Oluwatosin', 'Honfovu', 'Daniel ', 'Individual', 'admin', 'homdroid', 'homdroidtech@gmail.com', '', '09102999384', '$2y$10$t8dRjI/exTY2kgSybCZ.K.reJIAKdDhqftuo67gd3jNsEn2gXc7E2', '', 1, '0000-00-00 00:00:00', '::1', '', 1, '2024-10-16 16:36:14'),
(36, 're2628344381', NULL, NULL, NULL, 'individual', 'admin', 'holuwatosing', 'realholuwatosing@gmail.com', NULL, NULL, '$2y$10$UY2.7rfNV5puQN3dx4Pa0.dGEwKaFySUDuPWkSJugs7fBXUF69KVe', NULL, 0, '2025-01-16 10:29:53', '::1', '', 1, '2025-01-16 06:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

DROP TABLE IF EXISTS `voters`;
CREATE TABLE IF NOT EXISTS `voters` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `voterID` varchar(25) DEFAULT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `sname` varchar(45) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `oname` varchar(45) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `imagePath` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `gender` varchar(15) NOT NULL,
  `regAgent` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `regDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastLog` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedDate` datetime DEFAULT NULL,
  `voteDate` datetime DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `accreditationID` varchar(100) NOT NULL,
  `voteSessionID` text NOT NULL,
  `onlineStatus` int DEFAULT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`sn`, `voterID`, `hostID`, `sname`, `fname`, `oname`, `email`, `phone`, `imagePath`, `gender`, `regAgent`, `regDate`, `lastLog`, `modifiedDate`, `voteDate`, `status`, `accreditationID`, `voteSessionID`, `onlineStatus`) VALUES
(1, '232312fd3', 'c3586122184', 'Adedeji ', ' Olawunmi', '', 'homdroidtech@gmail.com', '08146681251', 'passport/e.png', 'male', '', '2024-10-07 14:38:21', '2024-01-20 15:04:00', '2024-02-12 23:31:00', NULL, 'active', '12345678', 'E1S31S6ZihvlwyJLzs40CiCPZsOsaooEfHf7NSmuRbMaXQtAd1', 0),
(2, '645rtrt467j', 'c3586122184', 'Adeagbo', 'Oluwatofunmi ', 'Stephen', 'Tfingers@gmail.com', '080231221212', 'passport/4758551 (1).jpg', 'male', '', '2024-10-07 14:38:25', '2020-04-17 13:13:00', '2024-02-07 23:19:00', NULL, 'active', '12345671', 'VhM2ZS2cmfcrs6SAvT3f4RWmi7EkeVhXHqETNyFwYhEVDb8SdK', 0),
(3, '4khr6565', 'c3586122184', 'Imole', 'Bukola', '', 'Imolebuk@gmail.com', '89978978978989', 'passport/37333f-kvxb6asaaulcg.jpeg', 'female', '', '2020-04-14 13:45:00', '2020-04-24 08:19:00', '2024-02-11 17:56:00', NULL, 'active', '6633470977', 'piq4ZBFpuFiUxYxuI8gpfgMTuafiUHyPojXcRc3zteG8zG91Yo', 0),
(9, 'hut455ft54', 'c3586122184', 'Luli', 'Irewole', '', 'luli@gmail.com', '0908789798789', 'passport/2799221 (1).jpg', 'female', '', '2020-04-14 13:45:00', '2024-10-07 14:37:24', '2024-02-04 21:07:00', NULL, 'active', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
CREATE TABLE IF NOT EXISTS `votes` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `voteID` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `voterID` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pollID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hostID` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `optionID` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `voteDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`),
  UNIQUE KEY `unique_vote` (`voterID`,`pollID`),
  KEY `pollID` (`pollID`),
  KEY `optionID` (`optionID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
