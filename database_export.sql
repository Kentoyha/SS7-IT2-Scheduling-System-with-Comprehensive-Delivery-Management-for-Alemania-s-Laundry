-- MySQL dump 10.19  Distrib 10.3.39-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: mariadb
-- ------------------------------------------------------
-- Server version	10.3.39-MariaDB-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Creating Table: User
DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) NOT NULL DEFAULT '',
  `Password` varchar(40) NOT NULL DEFAULT '',
  `Email` varchar(40) NOT NULL DEFAULT '',
  `Contact_info` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `User` VALUES (1, 'User', 'User', 'Wabalo@gmail.com', '123123123');

-- Creating Table: Admin
DROP TABLE IF EXISTS `Admin`;
CREATE TABLE `Admin` (
  `Admin_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) NOT NULL DEFAULT '',
  `Password` varchar(40) NOT NULL DEFAULT '',
  `Email` varchar(40) NOT NULL DEFAULT '',
  `Contact_info` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`Admin_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Admin` VALUES (1, 'Admin', 'admin', 'toyken@gmail.com', '123123123');

-- Creating Table: Orders
DROP TABLE IF EXISTS `Orders`;
CREATE TABLE `Orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_date` date DEFAULT NULL,
  `Laundry_type` varchar(40) DEFAULT NULL,
  `Laundry_quantity` varchar(40) DEFAULT NULL,
  `Cleaning_type` varchar(40) DEFAULT NULL,
  `Place` varchar(40) DEFAULT NULL,
  `Priority_note` varchar(40) NOT NULL DEFAULT '',
  `Status` varchar(40) DEFAULT NULL,
  `User_ID` int(11) NOT NULL,
  `Admin_ID` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `User_Orders` (`User_ID`),
  KEY `Admin_Orders` (`Admin_ID`),
  CONSTRAINT `Admin_Orders` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `User_Orders` FOREIGN KEY (`User_ID`) REFERENCES `User` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating Table: Schedules
DROP TABLE IF EXISTS `Schedules`;
CREATE TABLE `Schedules` (
  `Schedule_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Schedule_date` date DEFAULT NULL,
  `Place` varchar(40) DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `Order_id` int(11) NOT NULL,
  `Admin_id` int(11) NOT NULL,
  PRIMARY KEY (`Schedule_ID`),
  KEY `Orders_Schedules` (`Order_id`),
  KEY `Admin_Schedules` (`Admin_id`),
  CONSTRAINT `Admin_Schedules` FOREIGN KEY (`Admin_id`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `Orders_Schedules` FOREIGN KEY (`Order_id`) REFERENCES `Orders` (`Order_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating Table: Deliveries
DROP TABLE IF EXISTS `Deliveries`;
CREATE TABLE `Deliveries` (
  `Delivery_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `Delivery_staff_lastname` varchar(40) DEFAULT NULL,
  `Delivery_staff_firstname` varchar(40) DEFAULT NULL,
  `Contact_info` varchar(20) DEFAULT NULL,
  `Schedule_id` int(11) NOT NULL,
  `Admin_id` int(11) NOT NULL,
  PRIMARY KEY (`Delivery_ID`),
  KEY `Schedules_Deliveries` (`Schedule_id`),
  KEY `Admin_Deliveries` (`Admin_id`),
  CONSTRAINT `Admin_Deliveries` FOREIGN KEY (`Admin_id`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `Schedules_Deliveries` FOREIGN KEY (`Schedule_id`) REFERENCES `Schedules` (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Creating Table: Receipts
DROP TABLE IF EXISTS `Receipts`;
CREATE TABLE `Receipts` (
  `Receipt_id` int(11) NOT NULL AUTO_INCREMENT,
  `Date_completed` date DEFAULT NULL,
  `Time_completed` time DEFAULT NULL,
  `Order_id` int(11) NOT NULL,
  `Schedule_id` int(11) NOT NULL,
  `Delivery_id` int(11) NOT NULL,
  PRIMARY KEY (`Receipt_id`),
  KEY `Orders_Receipts` (`Order_id`),
  KEY `Schedules_Receipts` (`Schedule_id`),
  KEY `Deliveries_Receipts` (`Delivery_id`),
  CONSTRAINT `Deliveries_Receipts` FOREIGN KEY (`Delivery_id`) REFERENCES `Deliveries` (`Delivery_ID`),
  CONSTRAINT `Orders_Receipts` FOREIGN KEY (`Order_id`) REFERENCES `Orders` (`Order_ID`),
  CONSTRAINT `Schedules_Receipts` FOREIGN KEY (`Schedule_id`) REFERENCES `Schedules` (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Restoring Foreign Key Checks
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
