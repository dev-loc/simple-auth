-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 15, 2016 at 11:06 PM
-- Server version: 5.1.40
-- PHP Version: 5.4.44

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test5`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(15) CHARACTER SET ucs2 NOT NULL COMMENT 'IP адрес',
  `login_time` int(11) NOT NULL COMMENT 'Время попытки логина',
  PRIMARY KEY (`id`),
  KEY `ip_addr` (`ip_addr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Неудачные попытки логинов' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `failed_logins`
--


-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Сессии пользователей';

--
-- Dumping data for table `sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Пользователи' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`) VALUES
(1, 'user1', 'c4ca4238a0b923820dcc509a6f75849b'),
(2, 'user2', 'c81e728d9d4c2f636f067f89cc14862c');
