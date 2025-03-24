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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Delivery`
--

LOCK TABLES `Delivery` WRITE;
/*!40000 ALTER TABLE `Delivery` DISABLE KEYS */;
INSERT INTO `Delivery` VALUES (1,'2025-03-24','Nagi Seishiro','09065118019','Delivered',1,2),(2,'2025-03-24','Riri Camaso','09065118011','Delivered',8,2),(3,'2025-03-24','Riri Camaso','09065118011','Delivered',9,2),(4,'2025-03-24','Riri Camaso','09065118011','Delivered',10,2),(5,'2025-03-24','Riri Camaso','09065118011','Delivered',11,2),(6,'2025-03-24','Riri Camaso','09065118011','Delivered',12,2),(7,'2025-03-24','Riri Camaso','09065118011','Delivered',13,2),(8,'2025-03-24','Riri Camaso','09065118011','Delivered',14,2),(9,'2025-03-24','Riri Camaso','09065118011','Delivered',15,2),(10,'2025-03-24','Riri Camaso','09065118011','Delivered',16,2),(11,'2025-03-24','Riri Camaso','09065118011','Delivered',17,2),(12,'2025-03-24','Riri Camaso','09065118011','Delivered',18,2),(13,'2025-03-24','Riri Camaso','09065118011','Delivered',19,2),(14,'2025-03-24','Kunigami Camaso','09065118011','Delivered',20,2),(15,'2025-03-24','Magsayo Rin','09065118011','Delivered',21,2),(16,'2025-03-24','Riri Camaso','09065118011','Delivered',22,2),(17,'2025-03-24','Kunigami Camaso','09065118011','Delivered',23,2),(18,'2025-03-24','Magsayo Rin','09065118011','Delivered',24,2),(19,'2025-03-24','Riri Camaso','09065118011','Delivered',25,2),(20,'2025-03-24','Kunigami Camaso','09065118011','Delivered',26,2),(21,'2025-03-24','Magsayo Rin','09065118011','Delivered',27,2),(22,'2025-03-24','Riri Camaso','09065118011','Delivered',28,2),(23,'2025-03-24','Kunigami Camaso','09065118011','Delivered',29,2),(24,'2025-03-24','Riri Camaso','09065118011','Delivered',30,2),(25,'2025-03-24','Kunigami Camaso','','Delivered',31,2),(26,'2025-03-24','Magsayo Rin','09065118011','Delivered',32,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Laundry_Orders`
--

LOCK TABLES `Laundry_Orders` WRITE;
/*!40000 ALTER TABLE `Laundry_Orders` DISABLE KEYS */;
INSERT INTO `Laundry_Orders` VALUES (1,'2025-03-24','Tablecloths','60','Wet Cleaning','Beat Naawan','1','Completed',2),(2,'2025-03-24','Beddings','51','Dry Cleaning','Hotel','1','Completed',1),(3,'2025-03-24','Curtains','51','Wet Cleaning','Hotel','1','Completed',1),(4,'2025-03-24','Towel','51','Wet Cleaning','Hotel','3','Completed',1),(5,'2025-03-24','Towels','51','Dry Cleaning','Hotel','1','Completed',1),(6,'2025-03-24','Toppers','51','Wet Cleaning','Hotel','1','Completed',1),(7,'2025-03-24','Tablecloths','51','Wet Cleaning','Hotel','2','Completed',1),(8,'2025-03-24','Towel','54','Dry Cleaning','Beat Naawan','1','Assigned',2),(9,'2025-03-24','Beddings','1233','Wet Cleaning','Beat Naawan','1','Completed',2),(10,'2025-03-24','Curtains','45','Dry Cleaning','Beat Naawan','2','Assigned',2),(11,'2025-03-24','Curtains','54','Wet Cleaning','Beat Naawan','2','On the Way',2),(12,'2025-03-24','Curtains','44','Dry Cleaning','Beat Naawan','2','Assigned',2),(13,'2025-03-24','Curtains','12','Dry Cleaning','Beat Naawan','2','On the Way',2),(14,'2025-03-24','Beddings','23','Dry Cleaning','Beat Naawan','1','On the Way',2),(15,'2025-03-24','Curtains','12','Dry Cleaning','Beat Naawan','2','On the Way',2),(16,'2025-03-24','Towel','12','Dry Cleaning','Beat Naawan','2','On the Way',2),(17,'2025-03-24','Curtains','12','Wet Cleaning','Beat Naawan','2','On the Way',2),(18,'2025-03-24','Curtains','54','Dry Cleaning','Beat Naawan','2','On the Way',2),(19,'2025-03-24','Towels','25','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(20,'2025-03-24','Curtains','26','Dry Cleaning','Beat Naawan','1','Ready for Pick up',2),(21,'2025-03-24','Curtains','26','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(22,'2025-03-24','Curtains','45','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(23,'2025-03-24','Curtains','23','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(24,'2025-03-24','Curtains','15','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(25,'2025-03-24','Beddings','34','Dry Cleaning','Beat Naawan','1','Ready for Pick up',2),(26,'2025-03-24','Curtains','87','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(27,'2025-03-24','Towel','65','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(28,'2025-03-24','Topper','98','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(29,'2025-03-24','Beddings','8','Wet Cleaning','Beat Naawan','2','Ready for Pick up',2),(30,'2025-03-24','Tablecloths','15','Wet Cleaning','Beat Naawan','1','Ready for Pick up',2),(31,'2025-03-24','Toppers','4','Dry Cleaning','Beat Naawan','2','Ready for Pick up',2),(32,'2025-03-24','Towels','3','Dry Cleaning','Beat Naawan','3','Ready for Pick up',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Pick_ups`
--

LOCK TABLES `Pick_ups` WRITE;
/*!40000 ALTER TABLE `Pick_ups` DISABLE KEYS */;
INSERT INTO `Pick_ups` VALUES (1,'2025-03-24','Completed','Nagi Seishiro','09065118019',1,2),(2,'2025-03-25','Assigned','Riri Camaso','09065118011',8,2),(3,'2025-03-24','Completed','Riri Camaso','09065118011',9,2),(4,'2025-03-25','Assigned','Riri Camaso','09065118011',10,2),(5,'2025-03-24','On the Way','Riri Camaso','09065118011',11,2),(6,'2025-03-25','Assigned','Riri Camaso','09065118011',12,2),(7,'2025-03-24','On the Way','Riri Camaso','09065118011',13,2),(8,'2025-03-24','On the Way','Riri Camaso','09065118011',14,2),(9,'2025-03-24','On the Way','Riri Camaso','09065118011',15,2),(10,'2025-03-24','On the Way','Riri Camaso','09065118011',16,2),(11,'2025-03-24','On the Way','Riri Camaso','09065118011',17,2),(12,'2025-03-24','On the Way','Riri Camaso','09065118011',18,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receipts`
--

LOCK TABLES `Receipts` WRITE;
/*!40000 ALTER TABLE `Receipts` DISABLE KEYS */;
INSERT INTO `Receipts` VALUES (1,1,1,1,'2025-03-24','01:55:51','Checked'),(2,3,NULL,NULL,'2025-03-24','01:57:38','Checked'),(3,5,NULL,NULL,'2025-03-24','02:01:37','Checked'),(4,6,NULL,NULL,'2025-03-24','02:01:39','Checked'),(5,7,NULL,NULL,'2025-03-24','02:01:40','Checked'),(6,4,NULL,NULL,'2025-03-24','02:01:42','Checked'),(7,9,3,3,'2025-03-24','04:45:31','Checked');
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
INSERT INTO `Users` VALUES (1,'Toyken','$2y$10$4jDWd9qU3vfln8SDDHi3K.PxCF9aE40LzbQ4iUThI3rBejrVG1yb6','Wabalo@gmail.com','09065118019','Admin'),(2,'Kentoy','$2y$10$lGjTFKSkpj83DUVA5ofLIeIM1G6tNw3HWH28z3Fp656FJPaqu4N0q','wabalo@gmail.com','09065118019','User');
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

-- Dump completed on 2025-03-24  5:07:23
