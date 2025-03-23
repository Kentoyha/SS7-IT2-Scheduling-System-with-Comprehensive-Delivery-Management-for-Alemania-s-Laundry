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
-- Table structure for table `Delivery`
--

DROP TABLE IF EXISTS `Delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Delivery` (
  `Delivery_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Delivery_date` date DEFAULT NULL,
  `Delivery_staff_name` varchar(40) DEFAULT NULL,
  `Contact_info` varchar(40) DEFAULT NULL,
  `Status` varchar(250) DEFAULT NULL,
  `Order_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Delivery_ID`),
  KEY `Laundry_Orders_Delivery` (`Order_ID`),
  KEY `Users_Delivery` (`User_ID`),
  CONSTRAINT `Laundry_Orders_Delivery` FOREIGN KEY (`Order_ID`) REFERENCES `Laundry_Orders` (`Order_ID`),
  CONSTRAINT `Users_Delivery` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (2,'2025-03-23','Barkik bobo nabata','09065118019','Delivered',1,2),(3,'2025-03-23','Nagi Seishiro','09065118019','Delivered',4,2),(4,'2025-03-23','Barkik bobo nabata','09065118019','Delivered',10,2),(5,'2025-03-23','Barkik bobo nabata','09065118019','Delivered',11,2);
/*!40000 ALTER TABLE `Delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Laundry_Orders`
--

DROP TABLE IF EXISTS `Laundry_Orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Laundry_Orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_date` date NOT NULL,
  `Laundry_type` varchar(40) NOT NULL,
  `Laundry_quantity` varchar(40) NOT NULL,
  `Cleaning_type` varchar(40) NOT NULL,
  `Place` varchar(40) NOT NULL,
  `Priority_number` varchar(40) NOT NULL,
  `Status` varchar(250) DEFAULT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `Users_Laundry_Orders` (`User_ID`),
  CONSTRAINT `Users_Laundry_Orders` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Laundry_Orders`
--

LOCK TABLES `Laundry_Orders` WRITE;
/*!40000 ALTER TABLE `Laundry_Orders` DISABLE KEYS */;
INSERT INTO `Laundry_Orders` VALUES (1,'2025-03-23','Towel','51','Dry Cleaning','Beat Naawan','1','Assigned',2),(2,'2025-03-23','Curtains','51','Dry Cleaning','Hotel','1','Completed',3),(3,'2025-03-23','Beddings','51','Dry Cleaning','Hotel','2','Completed',3),(4,'2025-03-23','Topper','51','Wet Cleaning','Beat Naawan','1','Completed',2),(5,'2025-03-23','Curtains','51','Dry Cleaning','Hotel','1','Completed',3),(6,'2025-03-23','Curtains','51','Dry Cleaning','Hotel','1','Completed',3),(7,'2025-03-23','Towel','51','Dry Cleaning','Hotel','1','Completed',3),(8,'2025-03-23','Beddings','51','Wet Cleaning','Hotel','3','Completed',3),(9,'2025-03-23','Beddings','51','Dry Cleaning','Hotel','1','Completed',3),(10,'2025-03-23','Beddings','51','Wet Cleaning','Beat Naawan','1','On the Way',2),(11,'2025-03-23','Curtains','51','Dry Cleaning','Beat Naawan','1','On the Way',2);
/*!40000 ALTER TABLE `Laundry_Orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Pick_ups`
--

DROP TABLE IF EXISTS `Pick_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pick_ups` (
  `Pick_up_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Status` varchar(250) DEFAULT NULL,
  `Pick_up_staff_name` varchar(40) DEFAULT NULL,
  `Contact_info` varchar(11) DEFAULT NULL,
  `Order_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Pick_up_ID`),
  KEY `Laundry_Orders_Pick_ups` (`Order_ID`),
  KEY `Users_Pick_ups` (`User_ID`),
  CONSTRAINT `Laundry_Orders_Pick_ups` FOREIGN KEY (`Order_ID`) REFERENCES `Laundry_Orders` (`Order_ID`),
  CONSTRAINT `Users_Pick_ups` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pick_ups`
--

LOCK TABLES `Pick_ups` WRITE;
/*!40000 ALTER TABLE `Pick_ups` DISABLE KEYS */;
INSERT INTO `Pick_ups` VALUES (2,'2025-03-23','Completed','Barkik bobo nabata','09065118019',4,2),(3,'2025-03-23','On the Way','Barkik bobo nabata','09065118019',10,2),(4,'2025-03-23','On the Way','Nagi Seishiro','09065118019',11,2);
/*!40000 ALTER TABLE `Pick_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Receipts`
--

DROP TABLE IF EXISTS `Receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Receipts` (
  `Receipt_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_ID` int(11) NOT NULL,
  `Delivery_ID` int(11) DEFAULT NULL,
  `Pick_up_ID` int(11) DEFAULT NULL,
  `Date_completed` date DEFAULT NULL,
  `Time_completed` time DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Receipt_ID`),
  KEY `Laundry_Orders_Receipts` (`Order_ID`),
  KEY `Delivery_Receipts` (`Delivery_ID`),
  KEY `Pick_ups_Receipts` (`Pick_up_ID`),
  CONSTRAINT `Delivery_Receipts` FOREIGN KEY (`Delivery_ID`) REFERENCES `Delivery` (`Delivery_ID`),
  CONSTRAINT `Laundry_Orders_Receipts` FOREIGN KEY (`Order_ID`) REFERENCES `Laundry_Orders` (`Order_ID`),
  CONSTRAINT `Pick_ups_Receipts` FOREIGN KEY (`Pick_up_ID`) REFERENCES `Pick_ups` (`Pick_up_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receipts`
--

LOCK TABLES `Receipts` WRITE;
/*!40000 ALTER TABLE `Receipts` DISABLE KEYS */;
INSERT INTO `Receipts` VALUES (1,7,NULL,NULL,'2025-03-23','05:29:56',NULL),(2,8,NULL,NULL,'2025-03-23','05:30:26',NULL),(3,9,NULL,NULL,'2025-03-23','05:31:01',NULL),(4,4,3,2,'2025-03-23','06:10:45',NULL);
/*!40000 ALTER TABLE `Receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `Contact_info` varchar(40) DEFAULT NULL,
  `Usertype` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (2,'Kentoy','$2y$10$pVEJSCjjG1TyxFXu8u7pYOlMoPmZhzcY7hQTHronXXaGDxvI9yiAO','wabalo@gmail.com','09065118019','User'),(3,'Toyken','$2y$10$.HLmVhvcZh8QZmQwy.2BQOKwso4C9XKGY8nmPNHH4x4nDrSeGhkge','Wabalo@gmail.com','09065118019','Admin');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-23  6:20:16
