-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) NOT NULL COMMENT 'api key, unique for registred device',
  `owner_id` int(10) NOT NULL COMMENT 'user',
  `description` text COMMENT 'users description of device',
  `type` enum('UID','RSA_KEY') DEFAULT NULL COMMENT 'how is device authenticated',
  `private_key` text COMMENT 'private key of RSA, pure optinal; in base64',
  `public_key` text COMMENT 'public key of RSA; if type column = RSA_KEY should to be filled',
  `uid` varchar(20) DEFAULT NULL COMMENT 'UID of device; if type column = UID should be filled',
  PRIMARY KEY (`id`),
  KEY `user_id` (`owner_id`),
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='registred devices of user';


DROP TABLE IF EXISTS `doors`;
CREATE TABLE `doors` (
  `id` varchar(100) NOT NULL COMMENT 'defined by node',
  `node_id` int(10) NOT NULL COMMENT 'where is door connected',
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `doors_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `nodes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='available doors in node';


DROP TABLE IF EXISTS `doors_acl`;
CREATE TABLE `doors_acl` (
  `user_id` int(10) NOT NULL,
  `door_id` varchar(100) NOT NULL,
  `access` tinyint(1) NOT NULL COMMENT 'has access to door?',
  PRIMARY KEY (`user_id`,`door_id`),
  KEY `door_id` (`door_id`),
  CONSTRAINT `doors_acl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `doors_acl_ibfk_2` FOREIGN KEY (`door_id`) REFERENCES `doors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='access list, to doors where has user access';


DROP TABLE IF EXISTS `nodes`;
CREATE TABLE `nodes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `auth_key` varchar(255) NOT NULL COMMENT 'key to authenticate with server',
  `title` varchar(255) DEFAULT NULL COMMENT 'name of terminal',
  `available_nfc` tinyint(1) NOT NULL COMMENT 'has NFC reader?',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='terminal, which can have NFC';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('user','administrator','blocked') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2016-02-04 15:30:11
