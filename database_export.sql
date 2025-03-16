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
  KEY `Orders_Delivery` (`Order_ID`),
  KEY `Admin_Delivery` (`Admin_ID`),
  CONSTRAINT `Admin_Delivery` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `Orders_Delivery` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (1,'2025-03-14','Toyken','09065118019','Delivered',1,1),(6,'2025-03-14','Barkik bobo nabata','09065118109','Delivered',2,1),(7,'2025-03-14','Nagi Seishiro','09065118019','Out for Delivery',2,1),(8,'2025-03-14','Toyken','09065118019','Delivered',1,1);
/*!40000 ALTER TABLE `Delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Orders`
--

DROP TABLE IF EXISTS `Orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Orders` (
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
  KEY `User_Orders` (`User_ID`),
  KEY `Admin_Orders` (`Admin_ID`),
  CONSTRAINT `Admin_Orders` FOREIGN KEY (`Admin_ID`) REFERENCES `Admin` (`Admin_ID`),
  CONSTRAINT `User_Orders` FOREIGN KEY (`User_ID`) REFERENCES `User` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Orders`
--

LOCK TABLES `Orders` WRITE;
/*!40000 ALTER TABLE `Orders` DISABLE KEYS */;
INSERT INTO `Orders` VALUES (1,'2025-03-13','Beddings','15','Wet Cleaning','Hotel','1','On the way',NULL,1),(2,'2025-03-13','Topper','13','Spot Cleaning','Naawan','2','Out for Delivery',NULL,1),(3,'2025-03-13','Table Cloth','14','Wet Cleaning','Naawan','3','To be Delivered',NULL,1),(4,'2025-03-13','Table Cloth','18','Wet Cleaning','Kupal ka yata eh','2','On the way',NULL,1);
/*!40000 ALTER TABLE `Orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Pickups`
--

DROP TABLE IF EXISTS `Pickups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pickups` (
  `Pickup_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `Status` varchar(250) DEFAULT NULL,
  `Pickup_staff_name` varchar(40) DEFAULT NULL,
  `Contact_info` varchar(11) DEFAULT NULL,
  `Order_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Pickup_ID`),
  KEY `Orders_Pickups` (`Order_ID`),
  KEY `Users_Pickups` (`User_ID`),
  CONSTRAINT `Orders_Pickups` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`),
  CONSTRAINT `Users_Pickups` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pickups`
--

LOCK TABLES `Pickups` WRITE;
/*!40000 ALTER TABLE `Pickups` DISABLE KEYS */;
INSERT INTO `Pickups` VALUES (1,'2025-03-15','On the way','Barkik bobo nabata','09065118019',1,2),(2,'2025-03-16','On the way','Toyken','09065118019',5,2);
/*!40000 ALTER TABLE `Pickups` ENABLE KEYS */;
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
  `Delivery_ID` int(11) NOT NULL,
  `Pickup_ID` int(11) NOT NULL,
  `Date_completed` date DEFAULT NULL,
  `Time_completed` time DEFAULT NULL,
  PRIMARY KEY (`Receipt_ID`),
  KEY `Orders_Receipts` (`Order_ID`),
  KEY `Delivery_Receipts` (`Delivery_ID`),
  KEY `Pickups_Receipts` (`Pickup_ID`),
  CONSTRAINT `Delivery_Receipts` FOREIGN KEY (`Delivery_ID`) REFERENCES `Delivery` (`Delivery_ID`),
  CONSTRAINT `Orders_Receipts` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`),
  CONSTRAINT `Pickups_Receipts` FOREIGN KEY (`Pickup_ID`) REFERENCES `Pickups` (`Pickup_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receipts`
--

LOCK TABLES `Receipts` WRITE;
/*!40000 ALTER TABLE `Receipts` DISABLE KEYS */;
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
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'Kentoy','$2y$10$cet5YS/R2/ChYfNGokEosuWZNXXzR40VMzR5Al8I2xJM01NrAowU6','Aganap.cliffordkent@ici.edu.ph','09065118019');
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

-- Dump completed on 2025-03-14  0:39:51
