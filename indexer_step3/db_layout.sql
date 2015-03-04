-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2014 at 05:04 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `indexer`
--
CREATE DATABASE IF NOT EXISTS `indexer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `indexer`;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) NOT NULL,
  `file_url` varchar(750) NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `file_url` (`file_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_word`
--

CREATE TABLE IF NOT EXISTS `file_word` (
  `file_id` bigint(20) NOT NULL,
  `word_id` bigint(20) NOT NULL,
  `word_count` int(11) NOT NULL,
  PRIMARY KEY (`file_id`,`word_id`),
  KEY `word_id` (`word_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meta_info`
--

CREATE TABLE IF NOT EXISTS `meta_info` (
  `file_id` bigint(20) NOT NULL,
  `type` varchar(100) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`file_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE IF NOT EXISTS `words` (
  `word_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `word` varchar(100) NOT NULL,
  PRIMARY KEY (`word_id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=975 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file_word`
--
ALTER TABLE `file_word`
  ADD CONSTRAINT `file_word_ibfk_2` FOREIGN KEY (`word_id`) REFERENCES `words` (`word_id`),
  ADD CONSTRAINT `file_word_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`);

--
-- Constraints for table `meta_info`
--
ALTER TABLE `meta_info`
  ADD CONSTRAINT `meta_info_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`file_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
