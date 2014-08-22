-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2014 at 06:56 AM
-- Server version: 5.1.63-community
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ips_db`
--
CREATE DATABASE `ips_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `ips_db`;

-- --------------------------------------------------------

--
-- Table structure for table `ips_tbl`
--

CREATE TABLE IF NOT EXISTS `ips_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
