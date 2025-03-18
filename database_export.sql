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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (8,'2025-03-19','Nagi Seishiro','09065118019','Delivered',26,2),(9,'2025-03-18','Toyken','09065118019','Delivered',28,2),(10,'2025-03-18','Barkik bobo nabata','09065118019','Delivered',29,2),(11,'2025-03-18','Toyken','09065118019','Delivered',30,2),(12,'2025-03-18','King ina mo boi','09065118019','Delivered',34,2),(13,'2025-03-19','Toyken','09065118019','Delivered',37,2),(14,'2025-03-18','Barkik bobo nabata','09065118019','Delivered',38,2),(15,'2025-03-19','Barkik bobo nabata','09065118109','Delivered',39,2),(16,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',41,2),(17,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',42,2),(18,'2025-03-19','Toyken','09065118019','Delivered',44,2),(19,'2025-03-19','Nagi Seishiro','09065118019','Delivered',45,2),(20,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',46,2),(21,'2025-03-20','Nagi Seishiro','09065118019','Delivered',47,2),(22,'2025-03-19','Nagi Seishiro','09065118019','Delivered',48,2),(23,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',49,2),(24,'2025-03-19','King ina mo boi','09065118019','Delivered',65,2),(25,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',72,2),(26,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',73,2),(27,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',74,2),(28,'2025-03-18','Reo Mikage','09065118019','Delivered',75,2),(29,'2025-03-19','King ina mo boi','09065118019','Delivered',71,2),(30,'2025-03-20','Barkik bobo nabata','09065118019','Delivered',76,2),(31,'2025-03-19','Nagi Seishiro','09065118019','Delivered',77,2),(32,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',78,2),(33,'2025-03-20','Nagi Seishiro','09065118019','Delivered',79,2),(34,'2025-03-20','Barkik bobo nabata','09065118019','Delivered',80,2),(35,'2025-03-19','Barkik bobo nabata','09065118019','Delivered',81,2),(36,'2025-03-19','Nagi Seishiro','09065118019','Delivered',82,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Orders`
--

LOCK TABLES `Orders` WRITE;
/*!40000 ALTER TABLE `Orders` DISABLE KEYS */;
INSERT INTO `Orders` VALUES (26,'2025-03-17','Beddings','51','Wet Cleaning','Beat Naawan','1','Completed',2),(27,'2025-03-17','Curtains','51','Mixed','Hotel','3','Completed',1),(28,'2025-03-17','Curtains','51','Wet Cleaning','Beat Naawan','3','Completed',2),(29,'2025-03-17','Towel','51','Dry Cleaning','Beat Naawan','1','Completed',2),(30,'2025-03-17','Topper','51','Wet Cleaning','Beat Naawan','3','Completed',2),(31,'2025-03-17','Curtains','51','Spot Cleaning','Hotel','3','Completed',1),(32,'2025-03-17','Beddings','51','Dry Cleaning','Hotel','2','Completed',1),(33,'2025-03-17','Curtains','51','Dry Cleaning','Hotel','3','Completed',1),(34,'2025-03-17','Curtains','12','Spot Cleaning','Beat Naawan','2','Completed',2),(35,'2025-03-17','Beddings','51','Wet Cleaning','Hotel','2','Completed',1),(36,'2025-03-17','Towel','51','Wet Cleaning','Hotel','2','Completed',1),(37,'2025-03-17','Curtains','51','Dry Cleaning','Beat Naawan','3','Completed',2),(38,'2025-03-17','Curtains','51','Dry Cleaning','Beat Naawan','2','Completed',2),(39,'2025-03-17','Topper','51','Wet Cleaning','Beat Naawan','2','Picked up',2),(40,'2025-03-18','Topper','51','Wet Cleaning','Hotel','3','Completed',1),(41,'2025-03-18','Mixed','45','Wet Cleaning','Beat Naawan','1','Completed',2),(42,'2025-03-18','Beddings','51','Dry Cleaning','Beat Naawan','1','Completed',2),(43,'2025-03-18','Beddings','51','Dry Cleaning','Hotel','1','Completed',1),(44,'2025-03-18','Towel','51','Wet Cleaning','Beat Naawan','1','Completed',2),(45,'2025-03-18','Table Cloth','51','Wet Cleaning','Beat Naawan','2','Completed',2),(46,'2025-03-18','Topper','51','Dry Cleaning','Beat Naawan','2','Completed',2),(47,'2025-03-18','Topper','51','Dry Cleaning','Beat Naawan','2','Completed',2),(48,'2025-03-18','Table Cloth','51','Dry Cleaning','Beat Naawan','2','Completed',2),(49,'2025-03-18','Towel','51','Dry Cleaning','Beat Naawan','2','Completed',2),(50,'2025-03-18','Topper','51','Dry Cleaning','Hotel','1','Completed',1),(51,'2025-03-18','Towel','51','Wet Cleaning','Hotel','1','Completed',1),(52,'2025-03-18','Towel','51','Dry Cleaning','Hotel','2','Completed',1),(53,'2025-03-18','Towel','51','Wet Cleaning','Hotel','1','Completed',1),(54,'2025-03-18','Beddings','51','Dry Cleaning','Hotel','2','Completed',1),(55,'2025-03-18','Topper','51','Wet Cleaning','Hotel','3','Completed',1),(56,'2025-03-18','Topper','51','Dry Cleaning','Hotel','2','Completed',1),(57,'2025-03-18','Beddings','51','Dry Cleaning','Hotel','1','Completed',1),(58,'2025-03-18','Towel','51','Dry Cleaning','Hotel','2','Completed',1),(59,'2025-03-18','Curtains','51','Dry Cleaning','Hotel','3','Completed',1),(60,'2025-03-18','Topper','51','Dry Cleaning','Hotel','1','Completed',1),(61,'2025-03-18','Topper','51','Dry Cleaning','Hotel','1','Completed',1),(62,'2025-03-18','Table Cloth','51','Dry Cleaning','Hotel','2','Completed',1),(63,'2025-03-18','Topper','51','Wet Cleaning','Hotel','2','Completed',1),(64,'2025-03-18','Towel','51','Dry Cleaning','Hotel','3','Completed',1),(65,'2025-03-18','Curtains','51','Dry Cleaning','Beat Naawan','1','Completed',2),(66,'2025-03-18','Towel','51','Dry Cleaning','Hotel','1','Completed',1),(67,'2025-03-18','Curtains','51','Dry Cleaning','Hotel','2','Completed',1),(68,'2025-03-18','Mixed','51','Dry Cleaning','Hotel','1','Completed',1),(69,'2025-03-18','Topper','51','Wet Cleaning','Hotel','2','Completed',1),(70,'2025-03-18','Curtains','51','Dry Cleaning','Hotel','2','Completed',1),(71,'2025-03-18','Topper','51','Dry Cleaning','Beat Naawan','2','Completed',2),(72,'2025-03-18','Towel','51','Dry Cleaning','Beat Naawan','2','Completed',2),(73,'2025-03-18','Table Cloth','51','Dry Cleaning','Beat Naawan','3','Completed',2),(74,'2025-03-18','Curtains','51','Dry Cleaning','Beat Naawan','2','Completed',2),(75,'2025-03-18','Topper','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(76,'2025-03-18','Beddings','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(77,'2025-03-18','Towel','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(78,'2025-03-18','Table Cloth','51','Dry Cleaning','Beat Naawan','3','Ready for Pick up',2),(79,'2025-03-18','Curtains','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(80,'2025-03-18','Table Cloth','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(81,'2025-03-18','Towel','51','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(82,'2025-03-18','Towel','51','Dry Cleaning','Beat Naawan','2','In Progress',2),(83,'2025-03-18','Beddings','51','Wet Cleaning','Hotel','2','In Progress',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pickups`
--

LOCK TABLES `Pickups` WRITE;
/*!40000 ALTER TABLE `Pickups` DISABLE KEYS */;
INSERT INTO `Pickups` VALUES (6,'2025-03-18','Completed','Barkik bobo nabata','09065118019',26,2),(7,'2025-03-18','Completed','Barkik bobo nabata','09065118019',28,2),(8,'2025-03-18','Completed','Nagi Seishiro','09065118019',29,2),(9,'2025-03-19','Completed','Nagi Seishiro','09065118019',30,2),(10,'2025-03-19','Completed','Toyken','09065118019',34,2),(11,'2025-03-18','Completed','Barkik bobo nabata','09065118019',37,2),(12,'2025-03-18','Completed','Toyken','09065118019',38,2),(13,'2025-03-19','Ready for Pick up','King ina mo boi','09065118019',39,2),(14,'2025-03-19','Completed','Nagi Seishiro','09065118019',41,2),(15,'2025-03-19','Completed','Nagi Seishiro','09065118019',42,2),(16,'2025-03-19','Completed','Nagi Seishiro','09065118019',44,2),(17,'2025-03-19','Completed','Nagi Seishiro','09065118019',45,2),(18,'2025-03-19','Completed','Nagi Seishiro','09065118019',46,2),(19,'2025-03-19','Completed','Barkik bobo nabata','09065118019',47,2),(20,'2025-03-19','Completed','Nagi Seishiro','09065118019',48,2),(21,'2025-03-19','Completed','Barkik bobo nabata','09065118019',49,2),(22,'2025-03-19','Completed','Barkik bobo nabata','09065118019',65,2),(23,'2025-03-20','Completed','Barkik bobo nabata','09065118019',71,2),(24,'2025-03-19','Completed','Barkik bobo nabata','09065118019',72,2),(25,'2025-03-19','Completed','Toyken','09065118019',73,2),(26,'2025-03-19','Completed','Barkik bobo nabata','09065118019',74,2);
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
  `Delivery_ID` int(11) DEFAULT NULL,
  `Pickup_ID` int(11) DEFAULT NULL,
  `Date_completed` date DEFAULT NULL,
  `Time_completed` time DEFAULT NULL,
  `Status` enum('Checked','Unchecked') DEFAULT 'Unchecked',
  PRIMARY KEY (`Receipt_ID`),
  KEY `Orders_Receipts` (`Order_ID`),
  KEY `Delivery_Receipts` (`Delivery_ID`),
  KEY `Pickups_Receipts` (`Pickup_ID`),
  CONSTRAINT `Delivery_Receipts` FOREIGN KEY (`Delivery_ID`) REFERENCES `Delivery` (`Delivery_ID`),
  CONSTRAINT `Orders_Receipts` FOREIGN KEY (`Order_ID`) REFERENCES `Orders` (`Order_ID`),
  CONSTRAINT `Pickups_Receipts` FOREIGN KEY (`Pickup_ID`) REFERENCES `Pickups` (`Pickup_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receipts`
--

LOCK TABLES `Receipts` WRITE;
/*!40000 ALTER TABLE `Receipts` DISABLE KEYS */;
INSERT INTO `Receipts` VALUES (1,35,NULL,NULL,'2025-03-17','04:39:37','Checked'),(2,34,12,10,'2025-03-17','05:12:33','Checked'),(3,36,NULL,NULL,'2025-03-17','13:13:13','Checked'),(4,37,13,11,'2025-03-17','13:15:01','Checked'),(5,38,14,12,'2025-03-17','13:19:45','Checked'),(6,40,NULL,NULL,'2025-03-18','01:18:44','Checked'),(7,41,16,14,'2025-03-18','06:45:07','Checked'),(8,42,17,15,'2025-03-18','08:23:29','Checked'),(9,43,NULL,NULL,'2025-03-18','09:00:29','Checked'),(10,50,NULL,NULL,'2025-03-18','09:02:05','Checked'),(11,51,NULL,NULL,'2025-03-18','09:02:10','Checked'),(12,44,18,16,'2025-03-18','09:02:37','Checked'),(13,45,19,17,'2025-03-18','09:02:41','Checked'),(14,53,NULL,NULL,'2025-03-18','11:50:04','Checked'),(15,57,NULL,NULL,'2025-03-18','11:50:13','Checked'),(16,60,NULL,NULL,'2025-03-18','14:24:20','Unchecked'),(17,61,NULL,NULL,'2025-03-18','14:24:24','Unchecked'),(18,66,NULL,NULL,'2025-03-18','14:24:26','Checked'),(19,68,NULL,NULL,'2025-03-18','14:24:28','Checked'),(20,52,NULL,NULL,'2025-03-18','14:24:30','Unchecked'),(21,54,NULL,NULL,'2025-03-18','14:24:42','Unchecked'),(22,56,NULL,NULL,'2025-03-18','14:24:46','Unchecked'),(23,58,NULL,NULL,'2025-03-18','14:24:49','Unchecked'),(24,62,NULL,NULL,'2025-03-18','14:24:52','Unchecked'),(25,63,NULL,NULL,'2025-03-18','14:24:55','Unchecked'),(26,67,NULL,NULL,'2025-03-18','14:24:58','Checked'),(27,69,NULL,NULL,'2025-03-18','14:25:00','Checked'),(28,70,NULL,NULL,'2025-03-18','14:25:03','Checked'),(29,55,NULL,NULL,'2025-03-18','14:25:23','Unchecked'),(30,59,NULL,NULL,'2025-03-18','14:25:26','Unchecked'),(31,64,NULL,NULL,'2025-03-18','14:25:28','Unchecked'),(32,46,20,18,'2025-03-18','15:16:19','Checked'),(33,47,21,19,'2025-03-18','15:30:05','Checked'),(34,48,22,20,'2025-03-18','15:36:29','Checked'),(35,49,23,21,'2025-03-18','16:55:47','Checked'),(36,65,24,22,'2025-03-18','17:03:55','Checked'),(37,72,25,24,'2025-03-18','17:03:59','Checked'),(38,73,26,25,'2025-03-18','17:04:04','Checked'),(39,74,27,26,'2025-03-18','17:04:06','Checked'),(40,71,29,23,'2025-03-18','17:04:09','Checked');
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

-- Dump completed on 2025-03-18 17:31:34
