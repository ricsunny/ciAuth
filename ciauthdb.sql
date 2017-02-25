/*
SQLyog Community v9.63 
MySQL - 5.5.5-10.1.19-MariaDB : Database - ciauthdb
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ciauthdb` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ciauthdb`;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `api_token` varchar(100) DEFAULT NULL,
  `gender` varchar(25) NOT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `cell` varchar(55) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `address` text,
  `city` varchar(55) DEFAULT NULL,
  `state` varchar(55) DEFAULT NULL,
  `country` varchar(55) DEFAULT NULL,
  `remember_token` varchar(200) DEFAULT NULL,
  `verification_code` varchar(200) DEFAULT NULL,
  `password_requested_time` datetime DEFAULT NULL,
  `is_password_requested` bit(1) DEFAULT b'0',
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `is_deleted` bit(1) NOT NULL DEFAULT b'0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
