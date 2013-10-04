-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 28, 2013 at 10:41 AM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `context` varchar(20) NOT NULL,
  `controller` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `description` tinytext,
  `model` varchar(20) DEFAULT NULL,
  `model_id` int(11) unsigned DEFAULT NULL,
  `ip` varchar(20) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`model_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrators (Datiq BV)'),
(2, 'members', 'General users');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE IF NOT EXISTS `organisation` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned DEFAULT NULL,
  `minoto_id` mediumint(8) unsigned DEFAULT NULL,
  `type` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `player_minoto_id` int(4) unsigned NOT NULL,
  `theme` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `logo_url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `color_1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `color_2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `publish_url_1` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `publish_url_2` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`id`, `parent_id`, `minoto_id`, `type`, `name`, `url`, `enabled`, `player_minoto_id`, `theme`, `logo_url`, `color_1`, `color_2`, `publish_url_1`, `publish_url_2`) VALUES
(1, NULL, 1816, 'publisher', 'Datiq Developers', 'http://xiao.datiq.net/', 'true', 3133, 'custom', 'assets/datiq/datiq_logo.png', '#0088CC', '#006DCC', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `controller` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `controller`, `action`, `description`) VALUES
(1, 'publish_video', 'video', 'publish', 'Publish videos and allow for embedding'),
(2, 'announce_video', 'video', 'announce', 'Create and announce videos'),
(3, 'remove_video', 'video', 'remove', 'Remove videos'),
(4, 'view_video', 'video', 'view', 'View video metadata'),
(5, 'authorise_video', 'video', 'authorise', 'Authorise video and ready for publishing'),
(8, 'create_reseller', 'reseller', 'create', 'Create new resellers'),
(9, 'edit_reseller', 'reseller', 'edit', 'Edit resellers'),
(10, 'remove_reseller', 'reseller', 'remove', 'Remove resellers'),
(11, 'view_reseller', 'reseller', 'view', 'View resellers'),
(12, 'create_publisher', 'publisher', 'create', 'Create publishers'),
(13, 'edit_publisher', 'publisher', 'edit', 'Edit publishers'),
(14, 'remove_publisher', 'publisher', 'remove', 'Remove publishers'),
(15, 'view_publisher', 'publisher', 'view', 'View publishers'),
(16, 'create_user', 'user', 'create', 'Create users'),
(17, 'edit_user', 'user', 'edit', 'Edit users'),
(18, 'remove_user', 'user', 'remove', 'Remove users'),
(19, 'view_user', 'user', 'view', 'View users'),
(20, 'edit_video', 'video', 'edit', 'Edit videos'),
(21, 'test', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Manager', 'A manager; allowed to create and assign users.'),
(2, 'Publisher', 'Allowed to view, modify and publish videos'),
(3, 'Editor', 'Allowed to create and modify videos, but not allowed to publish them');

-- --------------------------------------------------------

--
-- Table structure for table `roles_permissions`
--

CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` mediumint(8) unsigned NOT NULL,
  `permission_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_roles_permissions` (`role_id`,`permission_id`),
  KEY `fk_roles_permissions_roles1_idx` (`role_id`),
  KEY `fk_roles_permissions_permissions1_idx` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55 ;

--
-- Dumping data for table `roles_permissions`
--

INSERT INTO `roles_permissions` (`id`, `role_id`, `permission_id`) VALUES
(8, 1, 1),
(9, 1, 2),
(10, 1, 3),
(11, 1, 4),
(12, 1, 5),
(13, 1, 8),
(14, 1, 9),
(15, 1, 10),
(16, 1, 11),
(17, 1, 12),
(18, 1, 13),
(19, 1, 14),
(20, 1, 15),
(21, 1, 16),
(22, 1, 17),
(23, 1, 18),
(24, 1, 19),
(25, 1, 20),
(26, 2, 1),
(27, 2, 2),
(28, 2, 3),
(29, 2, 4),
(30, 2, 5),
(31, 2, 20),
(50, 3, 2),
(51, 3, 3),
(52, 3, 4),
(53, 3, 5),
(54, 3, 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  `business_unit` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `organisation_id`, `business_unit`) VALUES
(1, '\0\0', 'administrator', '968410b3dc7076c9af99dd8a26dfd28c4be9f666', '9462e8eee0', 'xiao@datiq.com', '', 'f164d3b498535c7fbe55776b6d27346f62150acf', 1374583138, '7f3a6746c3ba3b73f7e4f391199532621793b336', 1268889823, 1377595129, 1, 'Xiao-Hu', 'Tai', 'ADMIN', 'xxxxxxxxxxx', NULL, 'Headquarters -- CC&A');

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(4, 1, 1),
(5, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`role_id`),
  KEY `fk_users_roles_users1_idx` (`user_id`),
  KEY `fk_users_roles_roles1_idx` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=139 ;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
(96, 1, 1),
(97, 1, 2),
(98, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_unit` varchar(100) NOT NULL,
  `identifier` tinytext NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `date_release` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `deleted_by` int(11) unsigned DEFAULT NULL,
  `title` tinytext NOT NULL,
  `author` tinytext NOT NULL,
  `caption` tinytext NOT NULL,
  `source` tinytext NOT NULL,
  `description` text NOT NULL,
  `keywords` tinytext NOT NULL,
  `copyright` tinytext NOT NULL,
  `duration` int(11) NOT NULL,
  `thumbnail` tinytext NOT NULL,
  `screenshot` tinytext NOT NULL,
  `metaoption_1` tinytext NOT NULL,
  `metaoption_2` tinytext NOT NULL,
  `metaoption_3` tinytext NOT NULL,
  `metaoption_4` tinytext NOT NULL,
  `metaoption_5` tinytext NOT NULL,
  `metaoption_6` tinytext NOT NULL,
  `metaoption_7` tinytext NOT NULL,
  `minoto_id` tinytext NOT NULL,
  `status` tinytext NOT NULL,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `deleted_by` (`deleted_by`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `video_metadata`
--

CREATE TABLE IF NOT EXISTS `video_metadata` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `label` tinytext NOT NULL,
  `type` tinytext NOT NULL,
  `values` tinytext NOT NULL,
  `value` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `whitelist`
--

CREATE TABLE IF NOT EXISTS `whitelist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  `ip` varchar(32) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `whitelist`
--

INSERT INTO `whitelist` (`id`, `organisation_id`, `ip`, `description`) VALUES
(1, NULL, '194.153.74.10/32', 'Datiq BV, Cessnalaan'),
(2, NULL, '77.173.205.83/32', 'Datiq BV, Developer Xiao, Rotterdam, NL (to RE-ENABLE remove the first 1)');

-- --------------------------------------------------------

--
-- Table structure for table `whitelist_domain`
--

CREATE TABLE IF NOT EXISTS `whitelist_domain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` mediumint(8) unsigned DEFAULT NULL,
  `domain` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_id` (`organisation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `organisation`
--
ALTER TABLE `organisation`
  ADD CONSTRAINT `organisation_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `organisation` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `fk_roles_permissions_roles1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_roles_permissions_permissions1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `fk_users_roles_roles1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_roles_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_7` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `video_ibfk_3` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `video_ibfk_6` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `video_metadata`
--
ALTER TABLE `video_metadata`
  ADD CONSTRAINT `video_metadata_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `whitelist`
--
ALTER TABLE `whitelist`
  ADD CONSTRAINT `whitelist_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
