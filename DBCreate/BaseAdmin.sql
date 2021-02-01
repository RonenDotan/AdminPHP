-- --------------------------------------------------------
-- Host:                         165.227.107.49
-- Server version:               8.0.19 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             11.0.0.6061
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for adserver
CREATE DATABASE IF NOT EXISTS `adserver` /*!40100 DEFAULT CHARACTER SET latin1 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `adserver`;

-- Dumping structure for table adserver.cronjobs_processes
CREATE TABLE IF NOT EXISTS `cronjobs_processes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `client_id` int unsigned NOT NULL DEFAULT '1',
  `inform_user_ids` varchar(255) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table adserver.cron_logs
CREATE TABLE IF NOT EXISTS `cron_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cronjob_id` bigint unsigned DEFAULT NULL,
  `run_title` varchar(250) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` date DEFAULT NULL,
  `hour` tinyint unsigned DEFAULT NULL,
  `text` longtext,
  `status` tinyint DEFAULT NULL,
  `duration` bigint unsigned NOT NULL DEFAULT '0',
  `awaiting_action` tinyint unsigned NOT NULL DEFAULT '0',
  `server_ip` varchar(50) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `hour` (`hour`),
  KEY `cronjob_id` (`cronjob_id`),
  KEY `informed` (`awaiting_action`),
  KEY `server_ip` (`server_ip`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table adserver.menu_items
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `order_loc` decimal(10,5) unsigned NOT NULL DEFAULT '0.00000',
  `href` varchar(255) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `visible` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table adserver.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL DEFAULT '0',
  `system_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Admin',
  `name` varchar(255) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL DEFAULT '0',
  `note` text,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `privileges` longtext,
  `user_type` tinyint unsigned NOT NULL DEFAULT '1',
  `allowed_fields` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

-- Dumping structure for table adserver.user_activity
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `client_id` int unsigned NOT NULL DEFAULT '0',
  `url` varchar(2000) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `file` varchar(50) DEFAULT NULL,
  `qs` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`),
  KEY `time` (`time`),
  KEY `module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
