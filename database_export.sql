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
  KEY `Users_Delivery` (`User_ID`),
  CONSTRAINT `Orders_Delivery` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`),
  CONSTRAINT `Users_Delivery` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (1,'2025-03-15','Barkik bobo nabata','09065118019','Delivered',1,2),(2,'2025-03-15','Barkik bobo nabata','09065118019','Delivered',2,2),(3,'2025-03-16','Toyken','','Delivered',5,2),(4,'2025-03-16','Toyken','09065118019','Delivered',8,2),(5,'2025-03-16','Barkik bobo nabata','09065118019','Out for Delivery',6,2);
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
  KEY `Users_Orders` (`User_ID`),
  CONSTRAINT `Users_Orders` FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Orders`
--

LOCK TABLES `Orders` WRITE;
/*!40000 ALTER TABLE `Orders` DISABLE KEYS */;
INSERT INTO `Orders` VALUES (1,'2025-03-14','Curtains','51','Wet Cleaning','Naawan','1','On the way',2),(2,'2025-03-14','Towel','51','Wet Cleaning','Naawan','1','Ready for Pick up',1),(3,'2025-03-14','Towel','51','Spot Cleaning','Hotel','2','To be Delivered',1),(4,'2025-03-15','Towel','51','Spot Cleaning','Hotel','2','Ready for Pick up',1),(5,'2025-03-15','Curtains','51','Wet Cleaning','Naawan','3','On the way',2),(6,'2025-03-15','Curtains','51','Wet Cleaning','Naawan','3','Out for Delivery',2),(7,'2025-03-15','Curtains','51','Wet Cleaning','Hotel','2','In Progress',1),(8,'2025-03-15','Curtains','51','Dry Cleaning','Naawan','2','Ready for Pick up',2),(9,'2025-03-15','Curtains','51','Dry Cleaning','Beat Naawan','3','Pending',2);
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
  `Usertype` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'Toyken','$2y$10$kWedTcLx1/Zs9GuzdGNii.Y8Wh7p2YHjBEjJK5IvhedJ89GlsoJ2i','Aganap.cliffordkent@ici.edu.ph','09065118019','Admin'),(2,'Kentoy','$2y$10$/mYWCaiRmkePOLetXAHwZ.QgDmvgParIBocwQ3UK5KEFiw.q8vv3m','Aganap.cliffordkent@ici.edu.ph','0906511809','User');
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

-- Dump completed on 2025-03-15 12:21:33
