-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 15, 2009 at 10:06 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `twitbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bot`
--

CREATE TABLE IF NOT EXISTS `tbl_bot` (
  `uid` int(11) NOT NULL auto_increment,
  `username` varchar(25) collate utf8_unicode_ci NOT NULL,
  `password` varchar(255) collate utf8_unicode_ci NOT NULL,
  `email` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bot_echo`
--

CREATE TABLE IF NOT EXISTS `tbl_bot_echo` (
  `uid` int(11) NOT NULL,
  `last_data_time` datetime NOT NULL,
  `sender_is_private` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bot_question`
--

CREATE TABLE IF NOT EXISTS `tbl_bot_question` (
  `uid` int(11) NOT NULL,
  `last_data_time` datetime NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  `method` varchar(6) collate utf8_unicode_ci NOT NULL default 'GET',
  `params` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bot_rss`
--

CREATE TABLE IF NOT EXISTS `tbl_bot_rss` (
  `uid` int(11) NOT NULL,
  `last_data_time` datetime NOT NULL,
  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
