-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 31, 2021 at 05:26 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `notes_db`
--
CREATE DATABASE IF NOT EXISTS `notes_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `notes_db`;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` text NOT NULL,
  `path` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`uid`, `name`, `path`) VALUES
(1, 'folder1', '/folder1'),
(1, 'folder2', '/folder2'),
(2, 'folder3', '/folder3'),
(3, 'folder4', '/folder4'),
(3, 'folder5', '/folder5'),
(4, 'folder6', '/folder6'),
(5, 'folder7', '/folder7'),
(5, 'folder8', '/folder8');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `heading` text NOT NULL,
  `text` text NOT NULL,
  `private` tinyint(4) NOT NULL,
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `type` ENUM('text','list') NOT NULL DEFAULT 'text',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`heading`, `text`, `private`, `uid`, `fid`, `type`) VALUES
('Some heading 1', 'Some text 1', 0, 1, 2, 'text'),
('Some heading 2', 'Some text 2', 1, 1, 2, 'text'),
('Some heading 3', 'Some text 3', 1, 1, 1, 'text'),
('Some heading 4', 'Some text 4', 1, 1, 2, 'text'),
('Some heading 5', 'Some text 5', 1, 1, 1, 'text'),
('Some heading 6', 'Some text 6', 1, 2, 1, 'list'),
('Some heading 7', 'Some text 7', 1, 2, 1, 'list'),
('Some heading 8', 'Some text 8', 0, 2, 1, 'list'),
('Some heading 9', 'Some text 9', 0, 3, 1, 'list'),
('Some heading 10', 'Some text 10', 0, 3, 2, 'text'),
('Some heading 11', 'Some text 11', 0, 4, 1, 'text'),
('Some heading 12', 'Some text 12', 0, 4, 1, 'list'),
('Some heading 13', 'Some text 13', 0, 4, 1, 'list'),
('Some heading 14', 'Some text 14', 0, 5, 1, 'list'),
('Some heading 15', 'Some text 15', 0, 5, 1, 'list'),
('Some heading 16', 'Some text 16', 0, 5, 1, 'text'),
('Some heading 17', 'Some text 17', 0, 5, 1, 'text');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `name` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `users.username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `name`) VALUES
('user1', 'b3daa77b4c04a9551b8781d03191fe098f325e67', 'user1name'),
('user2', 'a1881c06eec96db9901c7bbfe41c42a3f08e9cb4', 'user2name'),
('user3', '0b7f849446d3383546d15a480966084442cd2193', 'user3name'),
('user4', '06e6eef6adf2e5f54ea6c43c376d6d36605f810e', 'user4name'),
('user5', '7d112681b8dd80723871a87ff506286613fa9cf6', 'user5name');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
