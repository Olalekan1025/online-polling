-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 10:41 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u530952302_poll`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `sn` int(11) NOT NULL,
  `candidateID` varchar(10) NOT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `pollID` varchar(50) DEFAULT NULL,
  `sname` varchar(40) DEFAULT NULL,
  `fname` varchar(40) DEFAULT NULL,
  `oname` varchar(25) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `imagePath` varchar(100) DEFAULT NULL,
  `position` varchar(40) DEFAULT NULL COMMENT 'Vying Position or Office',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `manifesto` text DEFAULT NULL,
  `regDate` datetime NOT NULL DEFAULT current_timestamp(),
  `modifiedDate` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`sn`, `candidateID`, `hostID`, `pollID`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `imagePath`, `position`, `status`, `manifesto`, `regDate`, `modifiedDate`) VALUES
(22, 'CDYZCTC7DU', 're5631022403', 'J7ZZ9BMS9D', 'Daniel', 'Augustina', 'C', 'female', 'augustdc@gmail.com', '05171886262', 'England', 'resources/candidate_images/augustdc@gmail.com-SRC 8.jpeg', 'SOF-H9WBCFXY', 'active', 'I\\\'m a winner.', '2025-04-06 20:53:17', NULL),
(21, 'CDMWH8YAY2', 're5631022403', 'J7ZZ9BMS9D', 'Daud', 'Abubakri', 'Ola', 'male', 'dabubakri7@gmail.com', '07547574205', 'Ashton house, petersfield Rise, london,', 'resources/candidate_images/dabubakri7@gmail.com-SRC 6.jpeg', 'SOF-H9WBCFXY', 'active', 'Our time is now.', '2025-04-06 20:50:00', NULL),
(20, 'CDEYFW4VA2', 're5631022403', 'J7ZZ9BMS9D', 'Lewis', 'Aimee', 'B', 'female', 'aimatit@gmail.com', '0943212238', 'London', 'resources/candidate_images/aimatit@gmail.com-SRC 7.jpeg', 'SOF-TNANEZRW', 'active', 'We\\\\\\\\\\\\\\\'re Winning.', '2025-04-06 20:45:40', '2025-04-06 20:53:33'),
(19, 'CDNDW5JIZD', 're5631022403', 'J7ZZ9BMS9D', 'Olayiwola', 'Dorcas', 'Shirley', 'female', 'docshell@gmail.com', '0678816252', 'Preston', 'resources/candidate_images/docshell@gmail.com-SRC 5.jpeg', 'SOF-TNANEZRW', 'active', 'Winning O\\\' Clock.', '2025-04-06 20:42:34', NULL),
(18, 'CDHOZLHNQ8', 're5631022403', 'J7ZZ9BMS9D', 'Adebanjo', 'Samuel', 'Issac', 'male', 'izzyy11@gmail.com', '07125363732', 'Surrey', 'resources/candidate_images/izzyy11@gmail.com-SRC 4.jpeg', 'SOF-8SRR8A6S', 'active', 'Let\\\'s win together.', '2025-04-06 20:40:32', NULL),
(17, 'CDJXMC7DFB', 're5631022403', 'J7ZZ9BMS9D', 'Ajibola', 'Maliq', 'Tidebe', 'male', 'teddybear@gmail.com', '07861415262', 'Luton', 'resources/candidate_images/teddybear@gmail.com-SRC 3.jpeg', 'SOF-8SRR8A6S', 'active', 'Now or Never.', '2025-04-06 20:38:04', NULL),
(16, 'CDXHH9CELO', 're5631022403', 'J7ZZ9BMS9D', 'Fatai', 'Ruqoyah', 'Bukola', 'female', 'fataibukky09@gmail.com', '0876561717', 'London', 'resources/candidate_images/fataibukky09@gmail.com-SRC 2F.jpeg', 'SOF-97MN1TFH', 'active', 'Joy is here.', '2025-04-06 20:32:21', NULL),
(15, 'CDYAV9YCKT', 're5631022403', 'J7ZZ9BMS9D', 'Oguntola', 'Ridwan', 'Olalekan', 'male', 'olalekanridwan2510@gmail.com', '0738965432', 'Roehampton Lane', 'resources/candidate_images/olalekanridwan2510@gmail.com-SRC 1.jpeg', 'SOF-97MN1TFH', 'active', 'Time To Win', '2025-04-06 20:26:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logId` varchar(12) NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  `userID` varchar(100) DEFAULT NULL,
  `eventType` varchar(255) DEFAULT NULL COMMENT 'A description or code representing the type of event that occurred (e.g., login, logout, course enrollment, course completion).',
  `eventID` varchar(100) NOT NULL COMMENT 'Event ID such as unique ID assigned to the product, session etc.\r\n',
  `eventDescription` text DEFAULT NULL COMMENT 'A more detailed description of the event or action that occurred. This could include information such as the course name, module name, action taken, etc.',
  `ipAddress` varchar(45) DEFAULT NULL COMMENT 'The IP address of the user who triggered the event. This can be helpful for tracking user activity and identifying potential security issues.',
  `userAgent` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `operatingSystem` varchar(255) DEFAULT NULL,
  `deviceType` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL COMMENT 'A status indicator (e.g., success, failure) indicating the outcome of the event.',
  `errorMessage` text DEFAULT NULL COMMENT ' If the event resulted in an error, a description of the error message.'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`logId`, `timestamp`, `userID`, `eventType`, `eventID`, `eventDescription`, `ipAddress`, `userAgent`, `browser`, `operatingSystem`, `deviceType`, `status`, `errorMessage`) VALUES
('7yhrjznzu311', '2025-04-06 20:17:23', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('6h9mpdtm745k', '2025-04-06 21:28:56', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('lfddcsbp05wb', '2025-04-08 08:25:08', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('jci8sakyd167', '2025-04-08 09:13:49', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('84f6ludg36up', '2025-04-08 10:03:51', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('0hdk03yx5lv6', '2025-04-08 10:16:37', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('m5fda6dyweyw', '2025-04-08 10:20:32', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('kchjuz9ocb4j', '2025-04-08 10:57:17', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('fku64ogl8pv3', '2025-04-11 20:18:34', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('hxt3qoz5s42e', '2025-04-11 20:29:07', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_3_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/135.0.7049.53 Mobile/15E148 Safari/604.1', 'Safari', 'Linux', 'Mobile', 'success', NULL),
('9sscjd32677v', '2025-04-11 21:18:10', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '197.211.63.46', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'Safari', 'Linux', 'Mobile', NULL, NULL),
('2zljiebn6ehs', '2025-04-11 21:28:54', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.46', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'Safari', 'Linux', 'Mobile', 'success', NULL),
('3oi3t3dae6k6', '2025-04-11 21:32:36', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('09dcggogogbx', '2025-04-11 21:34:50', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('tsjwy23ulqhl', '2025-04-11 21:38:03', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('x8hf22v8bcyv', '2025-04-11 21:55:16', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('u37hymq67njw', '2025-04-12 13:06:59', 'homdroidtech@gmail.com', 'sentPasswordResetLink', 'homdroidtech@gmail.com Customer Received A Password Reset Link  ', 'success', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('ckpbhq4ul42k', '2025-04-12 13:26:40', 'homdroidtech@gmail.com', 'sentPasswordResetLink', 'homdroidtech@gmail.com Customer Received A Password Reset Link  ', 'success', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('p8btmrvc287f', '2025-04-12 13:27:15', 'homdroidtech@gmail.com', 'changedPassword', 'homdroidtech@gmail.com password reset was successfully', 'success', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('8rbdf96oanwx', '2025-04-12 13:29:24', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('010u7mjhn34p', '2025-04-12 13:29:32', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('35lq30zikgfm', '2025-04-12 13:30:03', 'homdroidtech@gmail.com', 'sentPasswordResetLink', 'homdroidtech@gmail.com Customer Received A Password Reset Link  ', 'success', '197.211.63.46', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('2qbjyrpfr8sv', '2025-04-14 09:08:08', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('94sptsvfs8r9', '2025-04-14 10:20:56', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('ay5knltmnbze', '2025-04-14 19:32:06', 'homdroidtech@gmail.com', 'sentPasswordResetLink', 'homdroidtech@gmail.com Customer Received A Password Reset Link  ', 'success', '197.211.63.137', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Mobile Safari/537.36', 'Chrome', 'Linux', 'Mobile', NULL, NULL),
('78pp92mkzp5a', '2025-04-14 19:32:38', 'homdroidtech@gmail.com', 'changedPassword', 'homdroidtech@gmail.com password reset was successfully', 'success', '197.211.63.137', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Mobile Safari/537.36', 'Chrome', 'Linux', 'Mobile', NULL, NULL),
('0cpqs4sjcaer', '2025-04-14 19:32:45', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.137', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Mobile Safari/537.36', 'Chrome', 'Linux', 'Mobile', 'success', NULL),
('e97v69tcvi9y', '2025-04-14 20:20:05', 'olalekanridwan2510@gmail.com', 'sentPasswordResetLink', 'olalekanridwan2510@gmail.com Customer Received A Password Reset Link  ', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('nzotdyc6a779', '2025-04-14 20:21:21', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('j8ivexn1suvj', '2025-04-14 20:59:11', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('noajig9jn2fd', '2025-04-14 21:05:57', 'c3586122184', 'logout', 'homdroidtech@gmail.com logged out successfully', 'success', '197.211.63.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('rl9z7y5fgsml', '2025-04-14 21:52:37', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('kq6pdu0tjb7s', '2025-04-15 09:42:18', 'c3586122184', 'login', 'c3586122184', 'homdroidtech@gmail.com logged in successfully', '197.211.63.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('q38y0z0z1ikg', '2025-04-15 20:36:12', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('6cv6h5unuk6z', '2025-04-15 20:58:00', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('28a66b7gt7mj', '2025-04-15 21:06:08', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('7qwkv4j797nh', '2025-04-15 21:07:21', 're5631022403', 'logout', 'olalekanridwan2510@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('mkzz44op7q0e', '2025-04-15 21:13:30', 'olalekanridwan1025@gmail.com', 'userRegistration', 'olalekanridwan1025@gmail.com registered successfully as Olalekan', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('z1qczavl9ra2', '2025-04-15 21:13:58', 'olalekanridwan1025@gmail.com', 'emailVerification', 'olalekanridwan1025@gmail.com verification was successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('d1j96xw8rr87', '2025-04-15 21:14:22', 're1417121344', 'login', 're1417121344', 'olalekanridwan1025@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL),
('bbq8ixisonzp', '2025-04-15 21:21:38', 're1417121344', 'logout', 'olalekanridwan1025@gmail.com logged out successfully', 'success', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', NULL, NULL),
('dw1n5pfgzzd3', '2025-04-15 21:21:50', 're5631022403', 'login', 're5631022403', 'olalekanridwan2510@gmail.com logged in successfully', '194.80.247.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'Chrome', 'Linux', 'Desktop', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `sn` int(11) NOT NULL,
  `pollID` varchar(50) DEFAULT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `startDate` timestamp NULL DEFAULT NULL,
  `endDate` timestamp NULL DEFAULT NULL,
  `visibility` enum('private','public') DEFAULT 'public',
  `link` text DEFAULT NULL,
  `status` enum('active','upcoming','completed','cancelled') DEFAULT 'upcoming',
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`sn`, `pollID`, `hostID`, `title`, `description`, `startDate`, `endDate`, `visibility`, `link`, `status`, `createdAt`, `updatedAt`) VALUES
(19, 'QDHL1I580J', 're1417121344', 'RSU', 'Roehampton Student Union', '2025-04-13 22:16:00', '2025-04-22 23:59:00', 'public', 'https://www.poll.homdroid.com/start-poll?poll_id=QDHL1I580J&token=3sntpgtyab91yzh&poll=RSU', 'upcoming', '2025-04-15 21:17:58', '2025-04-15 21:19:36'),
(18, 'BMDLMUIVQF', 're5631022403', 'SRC', 'Student Representative Council', '2025-04-14 12:00:00', '2025-04-30 23:59:00', 'public', 'https://www.poll.homdroid.com/start-poll?poll_id=BMDLMUIVQF&token=uswt6l686938924&poll=SRC', 'upcoming', '2025-04-14 09:22:39', '2025-04-14 09:29:58'),
(17, '89J4GFRTPR', 're5631022403', 'HOD', 'Head Of Department', '2025-04-14 12:00:00', '2025-04-20 23:59:00', 'private', 'https://www.poll.homdroid.com/start-poll?poll_id=89J4GFRTPR&token=a6zugqygtr544xt&poll=HOD', 'upcoming', '2025-04-14 09:21:02', '2025-04-14 09:30:29'),
(16, 'J7ZZ9BMS9D', 're5631022403', 'RSU', 'Roehampton Student Union', '2025-04-05 00:00:00', '2025-04-30 23:59:00', 'public', 'https://www.poll.homdroid.com/start-poll?poll_id=J7ZZ9BMS9D&token=lholzoh0cu5jd5h&poll=RSU', 'upcoming', '2025-04-06 20:24:23', '2025-04-06 21:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `poll_voters`
--

CREATE TABLE `poll_voters` (
  `sn` int(11) NOT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `pollID` varchar(50) DEFAULT NULL,
  `voterEmail` varchar(100) DEFAULT NULL,
  `status` enum('voted','cancelled') DEFAULT NULL,
  `registrationType` enum('registered','added','uploaded') DEFAULT 'registered',
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_voters`
--

INSERT INTO `poll_voters` (`sn`, `hostID`, `pollID`, `voterEmail`, `status`, `registrationType`, `createdAt`) VALUES
(33, 're5631022403', 'J7ZZ9BMS9D', 'olalekanridwan1025@gmail.com', 'voted', 'registered', '2025-04-08 10:05:06'),
(32, 're5631022403', 'J7ZZ9BMS9D', 'opeyemiolalekan099@gmail.com', 'voted', 'registered', '2025-04-06 21:13:35'),
(31, 're5631022403', 'J7ZZ9BMS9D', 'udog14141@gmail.com', 'voted', 'registered', '2025-04-06 21:11:45'),
(30, 're5631022403', 'J7ZZ9BMS9D', 'xquisiteplug@gmail.com', 'voted', 'registered', '2025-04-06 20:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `sn` int(11) NOT NULL,
  `positionID` varchar(15) NOT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(15) NOT NULL,
  `status` varchar(10) NOT NULL,
  `regDate` datetime NOT NULL DEFAULT current_timestamp(),
  `modifiedDate` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(10, 'SOF-UL1C9UZ5', 'c3586122184', 'New Position', 'newP.', 'active', '2025-02-21 23:39:07', '2025-02-21 23:44:44'),
(11, 'SOF-8D41N4QB', 'c3586122184', 'New Position 4', 'newP4', 'active', '2025-02-28 06:40:17', NULL),
(12, 'SOF-3O5T1ZPD', 're1417926661', 'President', 'Presi', 'active', '2025-03-05 20:50:53', NULL),
(13, 'SOF-BDZAI9J6', 're1417926661', 'Vice President', 'VP', 'active', '2025-03-05 20:51:19', NULL),
(14, 'SOF-C8LNJMGN', 're1417926661', 'Welfare', 'Welf', 'active', '2025-03-05 20:51:38', NULL),
(15, 'SOF-97MN1TFH', 're5631022403', 'President', 'Presi ', 'active', '2025-03-12 22:06:40', NULL),
(16, 'SOF-8SRR8A6S', 're5631022403', 'Vice President ', 'V P', 'active', '2025-03-12 22:06:56', NULL),
(17, 'SOF-TNANEZRW', 're5631022403', 'Welfare', 'Welf ', 'active', '2025-03-12 22:07:26', NULL),
(18, 'SOF-H9WBCFXY', 're5631022403', 'General Secretary ', 'Gen Sec', 'active', '2025-03-12 22:07:46', NULL),
(19, 'SOF-U4PM2958', 're5631022403', 'Financial Secretary', 'Fin Sec', 'active', '2025-03-26 20:19:30', '2025-03-26 21:51:43');

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `sn` int(11) NOT NULL,
  `siteURL` text DEFAULT NULL,
  `portalAccess` varchar(12) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`sn`, `siteURL`, `portalAccess`) VALUES
(1, 'https://www.poll.homdroid.com', 'enable');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `sn` int(11) NOT NULL,
  `hostID` varchar(12) DEFAULT NULL,
  `fname` varchar(30) DEFAULT NULL,
  `lname` varchar(30) DEFAULT NULL,
  `oname` varchar(30) DEFAULT NULL,
  `accountType` varchar(50) DEFAULT NULL COMMENT 'Individual Organisation',
  `role` enum('voter','admin','moderator','') NOT NULL DEFAULT 'admin',
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(225) DEFAULT NULL,
  `passport` mediumtext DEFAULT NULL,
  `onlineStatus` int(11) DEFAULT NULL,
  `linkedinLink` varchar(255) DEFAULT NULL,
  `facebookLink` varchar(255) DEFAULT NULL,
  `twitterLink` varchar(255) DEFAULT NULL,
  `lastAccess` datetime DEFAULT NULL,
  `lastDeviceIP` varchar(30) DEFAULT NULL,
  `verificationCode` varchar(225) DEFAULT NULL,
  `isVerified` int(11) NOT NULL DEFAULT 0,
  `regDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`sn`, `hostID`, `fname`, `lname`, `oname`, `accountType`, `role`, `username`, `email`, `gender`, `phone`, `password`, `passport`, `onlineStatus`, `linkedinLink`, `facebookLink`, `twitterLink`, `lastAccess`, `lastDeviceIP`, `verificationCode`, `isVerified`, `regDate`) VALUES
(1, 'c3586122184', 'Oluwatosin', 'Honfovu', 'Daniel', 'Individual', 'admin', 'homdroid', 'homdroidtech@gmail.com', 'Female', '09102999384', '$2y$10$rGyQtmaz2U5BXjk3sBzNr.DxtDuMg2verA2JjAQTlkttPx368ifWG', '', 1, '', '', '', '2025-04-15 09:42:18', '197.211.63.137', '', 1, '2024-10-16 16:36:14'),
(38, 're1417926661', NULL, NULL, NULL, 'organization', 'admin', 'olalekan ridwan', 'opeyemiolalekan099@gmail.com', NULL, NULL, '$2y$10$KAI79uwJEd533AAwmVwu7.jcrZmbuxwBN20kECjgqWQ3JtS7q7lVS', NULL, 0, NULL, NULL, NULL, '2025-03-06 09:59:07', '194.80.247.247', '', 1, '2025-03-05 20:47:45'),
(39, 're5453493744', NULL, NULL, NULL, 'organization', 'admin', 'Holuwatosin', 'realholuwatosing@gmail.com', NULL, NULL, '$2y$10$MkHGNsv/KfZ9uulUFFwTXe1qk30C18ZPDIBr29VI2jF3mR88CThhK', NULL, 1, NULL, NULL, NULL, '0000-00-00 00:00:00', '197.211.63.97', '', 1, '2025-03-12 22:03:26'),
(40, 're5631022403', NULL, NULL, NULL, 'individual', 'admin', 'Opeyemi', 'olalekanridwan2510@gmail.com', NULL, NULL, '$2y$10$/u8Acds/bCQCSGl.uM.JnebqhfNpmYUTfVjKapx5WW2yCQx.n7gAe', NULL, 1, NULL, NULL, NULL, '2025-04-15 21:21:50', '194.80.247.247', '$2y$10$griOCawd/S8JEBNaON6qx.Isz7jYvuTTvZgbANWJz9h9VhfzZqX/G', 1, '2025-03-12 22:03:39'),
(41, 're1417121344', 'Ridwan', 'Oguntola', 'Olalekan', 'individual', 'admin', 'Olalekan', 'olalekanridwan1025@gmail.com', 'Male', '98725256222', '$2y$10$DgcO2388qKwxeIB6cdu1YuCDCNHH2WWlxkoUv0dsxAndhfiRFOzPG', NULL, 0, '', '', '', '2025-04-15 21:21:38', '194.80.247.247', '', 1, '2025-04-15 21:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `sn` int(11) NOT NULL,
  `voterID` varchar(25) DEFAULT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `sname` varchar(45) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `oname` varchar(45) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `imagePath` text NOT NULL,
  `gender` varchar(15) NOT NULL,
  `regAgent` varchar(100) DEFAULT NULL,
  `regDate` datetime NOT NULL DEFAULT current_timestamp(),
  `lastLog` datetime NOT NULL DEFAULT current_timestamp(),
  `modifiedDate` datetime DEFAULT NULL,
  `voteDate` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `source` enum('registered','uploaded','added') DEFAULT 'added' COMMENT 'Uploaded, registered (By voter using a link) or manually added.',
  `accreditationID` varchar(100) NOT NULL,
  `voteSessionID` text NOT NULL,
  `onlineStatus` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`sn`, `voterID`, `hostID`, `sname`, `fname`, `oname`, `email`, `phone`, `imagePath`, `gender`, `regAgent`, `regDate`, `lastLog`, `modifiedDate`, `voteDate`, `status`, `source`, `accreditationID`, `voteSessionID`, `onlineStatus`) VALUES
(1, '232312fd3', 'c3586122184', 'Nice guy', 'Oluwatosin', 'hvjh', 'homdroidtech@gmail.com', '08146681251', 'resources/voter_images/homdroidtech@gmail.com-images (1).jpg', 'male', '', '2024-10-07 14:38:21', '2024-01-20 15:04:00', '2025-02-24 21:44:31', NULL, 'active', 'registered', '12345678', '$2y$10$Y7S3t8zzq91znqjjeUPg/OM9aONMVDoqXtvZhmycfgF9.d2735v16', 0),
(48, 'VTRGYD5Z5W', '', '', '', '', 'homdroidtech@gmail.com', '', '', '', NULL, '2025-04-12 13:43:17', '2025-04-12 13:43:17', NULL, NULL, NULL, 'added', '', '$2y$10$GcCR34.IiHN6pD9IldIkbOGsgPVAf1wM6MnBY0JeLy/vnNatUplTa', NULL),
(47, 'VT6JBNRJRX', 're5631022403', 'Oguntola', 'Ridwan', '', 'olalekanridwan1025@gmail.com', '', '', 'male', NULL, '2025-04-08 10:04:49', '2025-04-08 10:04:49', NULL, NULL, NULL, 'added', '', '$2y$10$jpCD7Aloy6iqQS89WmXmwufoc695mbBEUz7TZMW1S9v/rJ9zMqctG', NULL),
(46, 'VTQOU3S6EP', 're5631022403', 'Ola', 'Ope', '', 'opeyemiolalekan099@gmail.com', '', '', 'female', NULL, '2025-04-06 21:13:21', '2025-04-06 21:13:21', NULL, NULL, NULL, 'added', '', '$2y$10$pGXzateNySUnpbAWhegZQOI3zsKJv4jdJti3skelNVpPInoIzEcju', NULL),
(45, 'VTI3ZYZELK', 're5631022403', 'Danfodio', 'Usman', '', 'udog14141@gmail.com', '', '', 'male', NULL, '2025-04-06 21:11:21', '2025-04-06 21:11:21', NULL, NULL, NULL, 'added', '', '$2y$10$oc0TgKf7kHaOqdjkwKQw2e/kHmnNmuIEIUAZeelVv2/uuGELzx.JK', NULL),
(44, 'VTAIYNTY2G', 're5631022403', 'Symon', 'Malcom', '', 'xquisiteplug@gmail.com', '', '', 'male', NULL, '2025-04-06 20:55:32', '2025-04-06 20:55:32', NULL, NULL, NULL, 'added', '', '$2y$10$xlcJ.Eu6..Xoo4gqyyJcfe4YBWlFr2/zhBE5xaNnjOzX4RcwSA2sq', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `sn` int(11) NOT NULL,
  `voterEmail` varchar(100) DEFAULT NULL,
  `pollID` varchar(50) DEFAULT NULL,
  `hostID` varchar(50) DEFAULT NULL,
  `candidateID` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `voteSessionID` text DEFAULT NULL,
  `voteDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`sn`, `voterEmail`, `pollID`, `hostID`, `candidateID`, `position`, `voteSessionID`, `voteDate`) VALUES
(59, 'olalekanridwan1025@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDNDW5JIZD', 'SOF-TNANEZRW', '$2y$10$jpCD7Aloy6iqQS89WmXmwufoc695mbBEUz7TZMW1S9v/rJ9zMqctG', '2025-04-08 10:06:18'),
(58, 'olalekanridwan1025@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDYZCTC7DU', 'SOF-H9WBCFXY', '$2y$10$jpCD7Aloy6iqQS89WmXmwufoc695mbBEUz7TZMW1S9v/rJ9zMqctG', '2025-04-08 10:05:48'),
(57, 'olalekanridwan1025@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDXHH9CELO', 'SOF-97MN1TFH', '$2y$10$jpCD7Aloy6iqQS89WmXmwufoc695mbBEUz7TZMW1S9v/rJ9zMqctG', '2025-04-08 10:05:40'),
(56, 'olalekanridwan1025@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDJXMC7DFB', 'SOF-8SRR8A6S', '$2y$10$jpCD7Aloy6iqQS89WmXmwufoc695mbBEUz7TZMW1S9v/rJ9zMqctG', '2025-04-08 10:05:32'),
(55, 'opeyemiolalekan099@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDHOZLHNQ8', 'SOF-8SRR8A6S', '$2y$10$pGXzateNySUnpbAWhegZQOI3zsKJv4jdJti3skelNVpPInoIzEcju', '2025-04-06 21:13:57'),
(54, 'opeyemiolalekan099@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDXHH9CELO', 'SOF-97MN1TFH', '$2y$10$pGXzateNySUnpbAWhegZQOI3zsKJv4jdJti3skelNVpPInoIzEcju', '2025-04-06 21:13:53'),
(53, 'opeyemiolalekan099@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDMWH8YAY2', 'SOF-H9WBCFXY', '$2y$10$pGXzateNySUnpbAWhegZQOI3zsKJv4jdJti3skelNVpPInoIzEcju', '2025-04-06 21:13:49'),
(52, 'opeyemiolalekan099@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDNDW5JIZD', 'SOF-TNANEZRW', '$2y$10$pGXzateNySUnpbAWhegZQOI3zsKJv4jdJti3skelNVpPInoIzEcju', '2025-04-06 21:13:43'),
(51, 'udog14141@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDJXMC7DFB', 'SOF-8SRR8A6S', '$2y$10$oc0TgKf7kHaOqdjkwKQw2e/kHmnNmuIEIUAZeelVv2/uuGELzx.JK', '2025-04-06 21:12:13'),
(50, 'udog14141@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDYAV9YCKT', 'SOF-97MN1TFH', '$2y$10$oc0TgKf7kHaOqdjkwKQw2e/kHmnNmuIEIUAZeelVv2/uuGELzx.JK', '2025-04-06 21:12:08'),
(49, 'udog14141@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDYZCTC7DU', 'SOF-H9WBCFXY', '$2y$10$oc0TgKf7kHaOqdjkwKQw2e/kHmnNmuIEIUAZeelVv2/uuGELzx.JK', '2025-04-06 21:12:00'),
(48, 'udog14141@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDEYFW4VA2', 'SOF-TNANEZRW', '$2y$10$oc0TgKf7kHaOqdjkwKQw2e/kHmnNmuIEIUAZeelVv2/uuGELzx.JK', '2025-04-06 21:11:52'),
(47, 'xquisiteplug@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDEYFW4VA2', 'SOF-TNANEZRW', '$2y$10$xlcJ.Eu6..Xoo4gqyyJcfe4YBWlFr2/zhBE5xaNnjOzX4RcwSA2sq', '2025-04-06 21:10:10'),
(46, 'xquisiteplug@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDYZCTC7DU', 'SOF-H9WBCFXY', '$2y$10$xlcJ.Eu6..Xoo4gqyyJcfe4YBWlFr2/zhBE5xaNnjOzX4RcwSA2sq', '2025-04-06 21:10:06'),
(45, 'xquisiteplug@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDXHH9CELO', 'SOF-97MN1TFH', '$2y$10$xlcJ.Eu6..Xoo4gqyyJcfe4YBWlFr2/zhBE5xaNnjOzX4RcwSA2sq', '2025-04-06 21:10:01'),
(44, 'xquisiteplug@gmail.com', 'J7ZZ9BMS9D', 're5631022403', 'CDHOZLHNQ8', 'SOF-8SRR8A6S', '$2y$10$xlcJ.Eu6..Xoo4gqyyJcfe4YBWlFr2/zhBE5xaNnjOzX4RcwSA2sq', '2025-04-06 21:09:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `poll_voters`
--
ALTER TABLE `poll_voters`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`sn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `poll_voters`
--
ALTER TABLE `poll_voters`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
