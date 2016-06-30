-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-05-27 09:36:02
-- 服务器版本： 10.1.9-MariaDB
-- PHP Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hsvoice`
--

-- --------------------------------------------------------

--
-- 表的结构 `authz`
--

CREATE TABLE `authz` (
  `id` int(10) UNSIGNED NOT NULL,
  `seckey` varchar(255) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `timeout` int(10) UNSIGNED NOT NULL,
  `authz` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `authz`
--

INSERT INTO `authz` (`id`, `seckey`, `type`, `timeout`, `authz`) VALUES
(1, 'x0XwZovrAaG9VISk4kL12ujT1yLb+1twm+qbsqGQXERXMOuTMDAwNVhyZ2c1OE80cTNkRlpUdhJFITByTSZQLgI2JBEGBQ==', 1, 1465415827, 'b:0;'),
(2, 'wn/lOkXbRytMN+qJSjmzq9qnRdaVhFH8HfwA689+hApXMjIwMDAwNVJVTEtBampBcWtES3FXTCE3Hj0AHwMlLloXEkAtew==', 1, 1465499440, 'b:0;'),
(3, '3+6Tl8l/Ezbe78FEMXPGtC3LiyDPtDff3fKZAG6CmbdXMjNTMDAwNUd5THlBSWFPYllGZEt2NQhOIDATPAgrPWgXIHsZBg==', 1, 1465499731, 'b:0;');

-- --------------------------------------------------------

--
-- 表的结构 `devices`
--

CREATE TABLE `devices` (
  `id` int(10) UNSIGNED NOT NULL,
  `hid` varchar(255) NOT NULL COMMENT 'hardware_address',
  `name` varchar(500) NOT NULL COMMENT 'display name',
  `owner` int(10) UNSIGNED NOT NULL,
  `authz` text NOT NULL,
  `shiftcode` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `devices`
--

INSERT INTO `devices` (`id`, `hid`, `name`, `owner`, `authz`, `shiftcode`) VALUES
(1, 'test_50e433f2658', 'Test Device', 1, 'a:3:{s:6:"global";b:0;s:8:"location";a:1:{i:0;s:7:"bedroom";}s:6:"object";a:1:{i:0;i:1003;}}', '');

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `authz` text NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `groups`
--

INSERT INTO `groups` (`id`, `name`, `authz`, `description`) VALUES
(1, 'root', '', 'Built-in Superuser Group'),
(2, 'Administrator', '', 'The build in administration group, User in this group will have access to Administration panel'),
(3, 'Device Administrator', '', 'User in this group will have access to edit or register devices'),
(1000, 'Ghost', 'a:1:{s:6:"global";b:1;}', 'Build in account have all access to all location and objects'),
(1001, 'users', '', 'Build-in Groups of All Users');

-- --------------------------------------------------------

--
-- 表的结构 `hsobjects`
--

CREATE TABLE `hsobjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `hsref` int(10) UNSIGNED NOT NULL COMMENT 'ref of homeseer',
  `location` varchar(255) NOT NULL,
  `location2` varchar(255) NOT NULL,
  `object` text NOT NULL COMMENT 'Serialized HSObject'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `hsobjects`
--

INSERT INTO `hsobjects` (`id`, `name`, `hsref`, `location`, `location2`, `object`) VALUES
(1, 'door', 1001, 'bedroom', 'first floor', ''),
(2, 'light', 1002, 'kitchen', 'ground floor', ''),
(3, 'television', 1003, 'living room', 'ground floor', ''),
(4, 'light', 1004, 'toliet', 'first floor', '');

-- --------------------------------------------------------

--
-- 表的结构 `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1: location 2:location2'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `member`
--

CREATE TABLE `member` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `extra` text NOT NULL,
  `authz` text NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `email`, `groups`, `extra`, `authz`, `description`) VALUES
(1, 'root', '$2y$10$1g436OmZNxJnu8noraHTqOUeebC0JHwVSfwTz4qfTk5YecDcNpH86', 'test@greatqq.com', '1', 'a:1:{s:4:"name";s:12:"Root Account";}', '', 'Build in superuser account');

-- --------------------------------------------------------

--
-- 表的结构 `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `from` int(10) UNSIGNED NOT NULL,
  `target` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authz`
--
ALTER TABLE `authz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hid` (`hid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hsobjects`
--
ALTER TABLE `hsobjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `USER` (`username`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fuser` (`from`),
  ADD KEY `tuser` (`target`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `authz`
--
ALTER TABLE `authz`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- 使用表AUTO_INCREMENT `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;
--
-- 使用表AUTO_INCREMENT `hsobjects`
--
ALTER TABLE `hsobjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- 使用表AUTO_INCREMENT `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `member`
--
ALTER TABLE `member`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;
--
-- 使用表AUTO_INCREMENT `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
