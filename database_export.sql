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

--
-- Table structure for table `Admin`
--

DROP TABLE IF EXISTS `Admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Admin` (
  `Admin_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) DEFAULT NULL,
  `Password` varchar(40) DEFAULT NULL,
  `Contact_info` int(11) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Admin_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Admin`
--

LOCK TABLES `Admin` WRITE;
/*!40000 ALTER TABLE `Admin` DISABLE KEYS */;
INSERT INTO `Admin` VALUES (10,'Torao','123123123',123123213,'Aganap.cliffordkent@ici.edu.ph');
/*!40000 ALTER TABLE `Admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Deliveries`
--

DROP TABLE IF EXISTS `Deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Deliveries` (
  `Delivery_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delivery_Date` date DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `Delivery_staff_lastname` varchar(40) DEFAULT NULL,
  `Delivery_staff_firstname` varchar(40) DEFAULT NULL,
  `Delivery_staff_middlename` varchar(40) DEFAULT NULL,
  `Admin_ID` int(11) NOT NULL,
  `Schedule_ID` int(11) NOT NULL,
  PRIMARY KEY (`Delivery_ID`),
  KEY `Schedules_Deliveries` (`Schedule_ID`),
  KEY `Admin_Deliveries` (`Admin_ID`),
  CONSTRAINT `Admin_Deliveries` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `Schedules_Deliveries` FOREIGN KEY (`Schedule_ID`) REFERENCES `Schedules` (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Deliveries`
--

LOCK TABLES `Deliveries` WRITE;
/*!40000 ALTER TABLE `Deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `Deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Laundry Orders`
--

DROP TABLE IF EXISTS `Laundry Orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Laundry Orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_date` varchar(40) DEFAULT NULL,
  `Laundry_amount` varchar(40) DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `User_ID` int(11) NOT NULL,
  `Admin_ID` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `User_Laundry Orders` (`User_ID`),
  KEY `Admin_Laundry Orders` (`Admin_ID`),
  CONSTRAINT `Admin_Laundry Orders` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `User_Laundry Orders` FOREIGN KEY (`User_ID`) REFERENCES `User` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Laundry Orders`
--

LOCK TABLES `Laundry Orders` WRITE;
/*!40000 ALTER TABLE `Laundry Orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `Laundry Orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Receipt`
--

DROP TABLE IF EXISTS `Receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Receipt` (
  `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `Order_ID` int(11) NOT NULL,
  `Schedule_ID` int(11) NOT NULL,
  `Delivery_ID` int(11) NOT NULL,
  PRIMARY KEY (`Receipt_ID`),
  KEY `Laundry Orders_Receipt` (`Order_ID`),
  KEY `Schedules_Receipt` (`Schedule_ID`),
  KEY `Deliveries_Receipt` (`Delivery_ID`),
  CONSTRAINT `Deliveries_Receipt` FOREIGN KEY (`Delivery_ID`) REFERENCES `Deliveries` (`Delivery_ID`),
  CONSTRAINT `Laundry Orders_Receipt` FOREIGN KEY (`Order_ID`) REFERENCES `Laundry Orders` (`Order_ID`),
  CONSTRAINT `Schedules_Receipt` FOREIGN KEY (`Schedule_ID`) REFERENCES `Schedules` (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receipt`
--

LOCK TABLES `Receipt` WRITE;
/*!40000 ALTER TABLE `Receipt` DISABLE KEYS */;
/*!40000 ALTER TABLE `Receipt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Schedules`
--

DROP TABLE IF EXISTS `Schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Schedules` (
  `Schedule_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Schedule_Date` date DEFAULT NULL,
  `Timeslot` time DEFAULT NULL,
  `Order_ID` int(11) NOT NULL,
  `Admin_ID` int(11) NOT NULL,
  PRIMARY KEY (`Schedule_ID`),
  KEY `Laundry Orders_Schedules` (`Order_ID`),
  KEY `Admin_Schedules` (`Admin_ID`),
  CONSTRAINT `Admin_Schedules` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `Laundry Orders_Schedules` FOREIGN KEY (`Order_ID`) REFERENCES `Laundry Orders` (`Order_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Schedules`
--

LOCK TABLES `Schedules` WRITE;
/*!40000 ALTER TABLE `Schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `Schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) DEFAULT NULL,
  `Password` varchar(40) DEFAULT NULL,
  `Contact_info` int(11) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (6,'Torao1','123123123',123123213,'Aganap.cliffordkent@ici.edu.ph');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-04  9:58:04
