/*
SQLyog Ultimate v10.00 Beta1
MySQL - 8.2.0 : Database - fdc_john_lloyd_nc_web
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`fdc_john_lloyd_nc_web` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `fdc_john_lloyd_nc_web`;

/*Table structure for table `conversations` */

DROP TABLE IF EXISTS `conversations`;

CREATE TABLE `conversations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `conversations` */

insert  into `conversations`(`id`,`created`,`modified`) values (1,'2024-05-26 17:29:28','2024-05-26 17:29:28'),(2,'2024-05-26 20:49:06','2024-05-26 20:49:06'),(3,'2024-05-26 20:52:15','2024-05-26 20:52:15');

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `convo_id` int DEFAULT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_convo_id` (`convo_id`),
  KEY `fk_sender_id` (`sender_id`),
  KEY `fk_receiver_id` (`receiver_id`),
  CONSTRAINT `fk_convo_id` FOREIGN KEY (`convo_id`) REFERENCES `conversations` (`id`),
  CONSTRAINT `fk_receiver_id` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `messages` */

insert  into `messages`(`id`,`convo_id`,`sender_id`,`receiver_id`,`content`,`created`,`modified`) values (1,1,1,2,'Hi','2024-05-26 17:29:28','2024-05-26 17:29:28'),(2,1,2,1,'Hello','2024-05-26 17:32:28','2024-05-26 17:32:28'),(3,1,1,2,'How are you today?','2024-05-26 17:33:28','2024-05-26 17:33:28'),(4,1,2,1,'Im good','2024-05-26 17:34:28','2024-05-26 17:34:28'),(5,2,3,1,'Hey','2024-05-26 20:50:06','2024-05-26 20:50:06'),(6,2,1,3,'yow','2024-05-26 20:51:06','2024-05-26 20:51:06'),(7,3,2,3,'....','2024-05-26 20:53:06','2024-05-26 20:53:06'),(8,3,3,2,'?????','2024-05-26 20:54:06','2024-05-26 20:54:06');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bdate` date DEFAULT NULL,
  `gender` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hubby` text COLLATE utf8mb4_unicode_ci,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.webp',
  `last_login` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`fname`,`lname`,`email`,`password`,`bdate`,`gender`,`position`,`hubby`,`profile`,`last_login`,`created`,`modified`) values (1,'Software','Developer','software@gmail.com','$2a$10$qe6mNzBesmYQi.Ctz8O/QeP2h3WL5USrLL7r1SlvJsb/jZba4QxDe','1999-06-04','Male','Software Developer','This is Hubby','profile_66529763bccce.jpg','2024-05-26 03:40:37','2024-05-26 01:57:34','2024-05-26 03:40:37'),(2,'system','Admin','sysadmin@gmail.com','$2a$10$MM5JhbeXoUM047JAs3xfLeEFRtUc6KNHN/9wKV4DwyUyNNiRd2C8m',NULL,NULL,NULL,NULL,'default.webp','2024-05-26 10:57:12','2024-05-26 09:26:53','2024-05-26 10:57:12'),(3,'John Lloyd','Batican','jlbatican@gmail.com','$2a$10$FGwF1AcDD/C986qgx7oBg.0hGWYauVEkNb0eh4jkmDKtomRbwUREO','1999-06-04','Male','Software Developer','hubby','default.webp','2024-05-26 14:16:23','2024-05-26 10:57:41','2024-05-26 14:16:23');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
