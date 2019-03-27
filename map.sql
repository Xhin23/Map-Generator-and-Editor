-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2019 at 06:56 PM
-- Server version: 5.5.33-cll-lve
-- PHP Version: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `alestan_map`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `gameid` int(10) NOT NULL AUTO_INCREMENT,
  `seed` varchar(50) DEFAULT NULL,
  `width` int(3) DEFAULT NULL,
  `height` int(3) DEFAULT NULL,
  `image_size` int(2) NOT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `name` text,
  PRIMARY KEY (`gameid`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `game_settings`
--

CREATE TABLE IF NOT EXISTS `game_settings` (
  `gameid` int(10) NOT NULL DEFAULT '0',
  `cat` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`gameid`,`name`),
  KEY `cat` (`cat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE IF NOT EXISTS `regions` (
  `regionid` int(10) NOT NULL AUTO_INCREMENT,
  `gameid` int(10) DEFAULT NULL,
  `name` text,
  `color` varchar(6) DEFAULT NULL,
  `desc` text,
  PRIMARY KEY (`regionid`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `region_tiles`
--

CREATE TABLE IF NOT EXISTS `region_tiles` (
  `regionid` int(10) NOT NULL DEFAULT '0',
  `gameid` int(10) NOT NULL DEFAULT '0',
  `x` int(4) NOT NULL DEFAULT '0',
  `y` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`regionid`,`gameid`,`x`,`y`),
  KEY `regionid` (`regionid`),
  KEY `gameid` (`gameid`),
  KEY `gameid_2` (`gameid`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE IF NOT EXISTS `rules` (
  `ruleid` int(10) NOT NULL AUTO_INCREMENT,
  `gameid` int(10) NOT NULL,
  `entype` enum('terrain','fg','region') DEFAULT NULL,
  `entid` varchar(50) DEFAULT NULL,
  `name` text,
  `ruletype` enum('trait','numeric','items') DEFAULT NULL,
  `chance` int(3) DEFAULT NULL,
  `seed` int(8) DEFAULT NULL,
  `min` int(2) DEFAULT NULL,
  `max` int(2) DEFAULT NULL,
  PRIMARY KEY (`ruleid`),
  KEY `entype` (`entype`),
  KEY `entid` (`entid`),
  KEY `ruletype` (`ruletype`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rule_entries`
--

CREATE TABLE IF NOT EXISTS `rule_entries` (
  `entryid` int(10) NOT NULL AUTO_INCREMENT,
  `gameid` int(10) DEFAULT NULL,
  `ruleid` int(10) DEFAULT NULL,
  `name` text,
  `weight` int(11) DEFAULT NULL,
  PRIMARY KEY (`entryid`),
  KEY `gameid` (`gameid`),
  KEY `ruleid` (`ruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shared`
--

CREATE TABLE IF NOT EXISTS `shared` (
  `shareid` int(10) NOT NULL AUTO_INCREMENT,
  `gameid` int(10) NOT NULL,
  `sid` varchar(32) DEFAULT NULL,
  `obj` varchar(50) DEFAULT NULL,
  `func` varchar(50) DEFAULT NULL,
  `batch_func` varchar(50) NOT NULL,
  `batch_data` text NOT NULL,
  `var1` text,
  `var2` text,
  `var3` text,
  `var4` text,
  `var5` text NOT NULL,
  PRIMARY KEY (`shareid`),
  KEY `sid` (`sid`),
  KEY `id` (`shareid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sprites`
--

CREATE TABLE IF NOT EXISTS `sprites` (
  `spriteid` int(10) NOT NULL AUTO_INCREMENT,
  `gameid` int(10) DEFAULT NULL,
  `type` enum('terrain','fg') DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `url` text,
  PRIMARY KEY (`spriteid`),
  KEY `gameid` (`gameid`),
  KEY `type` (`type`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tiles`
--

CREATE TABLE IF NOT EXISTS `tiles` (
  `gameid` int(10) NOT NULL DEFAULT '0',
  `x` int(4) NOT NULL DEFAULT '0',
  `y` int(4) NOT NULL DEFAULT '0',
  `name` text,
  `desc` text,
  `terrain` varchar(50) DEFAULT NULL,
  `fg` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`gameid`,`x`,`y`),
  KEY `terrain` (`terrain`),
  KEY `fg` (`fg`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
