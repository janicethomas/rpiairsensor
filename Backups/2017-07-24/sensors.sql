-- MySQL dump 10.13  Distrib 5.5.55, for debian-linux-gnu (armv7l)
--
-- Host: localhost    Database: sensors
-- ------------------------------------------------------
-- Server version	5.5.55-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mailsendlog`
--

DROP TABLE IF EXISTS `mailsendlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailsendlog` (
  `mailsendtime` datetime DEFAULT NULL,
  `triggedsensor` varchar(32) DEFAULT NULL,
  `triggedlimit` varchar(10) DEFAULT NULL,
  `lasttemperature` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailsendlog`
--

LOCK TABLES `mailsendlog` WRITE;
/*!40000 ALTER TABLE `mailsendlog` DISABLE KEYS */;
INSERT INTO `mailsendlog` VALUES ('2017-07-24 12:40:02','Temperature','0.0','13.8'),('2017-07-24 23:30:02','TempHumid','0.0','27.4'),('2017-07-24 23:30:07','TempHumid','5','27.4');
/*!40000 ALTER TABLE `mailsendlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensordata`
--

DROP TABLE IF EXISTS `sensordata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensordata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dateandtime` datetime DEFAULT NULL,
  `sensor` varchar(32) DEFAULT NULL COMMENT 'sensor type ( TempHumid , AirQuality )',
  `temperature` double DEFAULT NULL COMMENT 'Temperature in Celcius',
  `humidity` double DEFAULT NULL COMMENT 'Humidity in %',
  `airquality` double DEFAULT NULL COMMENT 'Air Quality measurement ( tbd )',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensordata`
--

LOCK TABLES `sensordata` WRITE;
/*!40000 ALTER TABLE `sensordata` DISABLE KEYS */;
INSERT INTO `sensordata` VALUES (1,'2017-07-24 12:38:01','TempHumid',27.5,57,NULL),(2,'2017-07-24 12:39:15','TempHumid',27.6,57.1,NULL),(3,'2017-07-24 12:39:31','TempHumid',27.6,57.2,NULL),(4,'2017-07-24 12:42:01','TempHumid',27.6,57,NULL),(5,'2017-07-24 12:44:01','TempHumid',27.6,57.1,NULL),(6,'2017-07-24 12:46:02','TempHumid',27.5,57.1,NULL),(7,'2017-07-24 12:48:01','TempHumid',27.5,57.1,NULL),(8,'2017-07-24 12:50:02','TempHumid',27.5,57.2,NULL),(9,'2017-07-24 12:52:01','TempHumid',27.5,57.5,NULL),(10,'2017-07-24 12:54:01','TempHumid',27.6,57.5,NULL),(11,'2017-07-24 12:56:01','TempHumid',27.6,57,NULL),(12,'2017-07-24 12:58:01','TempHumid',27.6,57.1,NULL),(13,'2017-07-24 13:00:01','TempHumid',27.6,57,NULL),(14,'2017-07-24 13:02:01','TempHumid',27.6,57.2,NULL),(15,'2017-07-24 13:04:01','TempHumid',27.6,57.2,NULL),(16,'2017-07-24 13:06:01','TempHumid',27.6,57.1,NULL),(17,'2017-07-24 13:08:02','TempHumid',27.5,57.1,NULL),(18,'2017-07-24 13:10:01','TempHumid',27.5,57.1,NULL),(19,'2017-07-24 13:12:01','TempHumid',27.5,57,NULL),(20,'2017-07-24 13:14:01','TempHumid',27.4,57,NULL),(21,'2017-07-24 13:16:02','TempHumid',27.4,57,NULL),(22,'2017-07-24 13:18:01','TempHumid',27.4,57.2,NULL),(23,'2017-07-24 13:20:01','TempHumid',27.4,57.4,NULL),(24,'2017-07-24 13:22:02','TempHumid',27.4,57.4,NULL),(25,'2017-07-24 13:24:01','TempHumid',27.4,57.5,NULL),(26,'2017-07-24 13:26:01','TempHumid',27.4,57.6,NULL),(27,'2017-07-24 13:28:02','TempHumid',27.4,57.7,NULL),(28,'2017-07-24 13:30:01','TempHumid',27.4,57.7,NULL),(29,'2017-07-24 13:32:02','TempHumid',27.4,57.7,NULL),(30,'2017-07-24 13:34:01','TempHumid',27.4,57.8,NULL),(31,'2017-07-24 13:36:01','TempHumid',27.5,57.8,NULL),(32,'2017-07-24 13:38:01','TempHumid',27.5,58.3,NULL),(33,'2017-07-24 13:45:02','TempHumid',27.5,58.3,NULL),(34,'2017-07-24 14:00:01','TempHumid',27.6,57.7,NULL),(35,'2017-07-24 14:15:01','TempHumid',27.6,57.6,NULL),(36,'2017-07-24 14:24:01','TempHumid',27.7,57,NULL),(37,'2017-07-24 14:26:01','TempHumid',27.7,56.9,NULL),(38,'2017-07-24 14:28:02','TempHumid',27.8,56.8,NULL),(39,'2017-07-24 14:30:01','TempHumid',27.8,56.8,NULL),(40,'2017-07-24 14:32:02','TempHumid',27.8,56.8,NULL),(41,'2017-07-24 14:45:01','TempHumid',27.8,56.8,NULL),(42,'2017-07-24 15:00:02','TempHumid',27.9,56.5,NULL),(43,'2017-07-24 15:15:01','TempHumid',27.9,56.1,NULL),(44,'2017-07-24 15:30:02','TempHumid',27.9,55.8,NULL),(45,'2017-07-24 15:45:01','TempHumid',28.1,55.7,NULL),(46,'2017-07-24 16:00:02','TempHumid',28.2,55.1,NULL),(47,'2017-07-24 16:15:01','TempHumid',28.2,53.3,NULL),(48,'2017-07-24 16:30:02','TempHumid',27.7,53.9,NULL),(49,'2017-07-24 16:45:01','TempHumid',27.6,54.8,NULL),(50,'2017-07-24 17:00:02','TempHumid',27.5,55.6,NULL),(51,'2017-07-24 17:15:01','TempHumid',27.4,56.1,NULL),(52,'2017-07-24 17:30:01','TempHumid',27.2,56,NULL),(53,'2017-07-24 17:45:02','TempHumid',27.2,56.4,NULL),(54,'2017-07-24 18:00:01','TempHumid',27.3,56.2,NULL),(55,'2017-07-24 18:15:02','TempHumid',27.6,55.7,NULL),(56,'2017-07-24 18:30:01','TempHumid',27.7,56.2,NULL),(57,'2017-07-24 18:45:01','TempHumid',27.7,56,NULL),(58,'2017-07-24 19:00:01','TempHumid',27.7,55.3,NULL),(59,'2017-07-24 19:06:40','TempHumid',27.8,55.4,NULL),(60,'2017-07-24 19:16:01','TempHumid',27.8,55.3,NULL),(61,'2017-07-24 19:18:01','TempHumid',27.8,54.8,NULL),(62,'2017-07-24 19:20:01','TempHumid',27.8,54.7,NULL),(63,'2017-07-24 19:30:02','TempHumid',27.8,54.7,NULL),(64,'2017-07-24 19:45:01','TempHumid',27.8,55.8,NULL),(65,'2017-07-24 20:00:02','TempHumid',27.8,55.6,NULL),(66,'2017-07-24 20:15:02','TempHumid',27.7,55.6,NULL),(67,'2017-07-24 20:30:01','TempHumid',27.8,55.4,NULL),(68,'2017-07-24 20:45:02','TempHumid',27.7,55.1,NULL),(69,'2017-07-24 21:00:01','TempHumid',27.8,55.2,NULL);
/*!40000 ALTER TABLE `sensordata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temperaturedata`
--

DROP TABLE IF EXISTS `temperaturedata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temperaturedata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dateandtime` datetime DEFAULT NULL,
  `sensor` varchar(32) DEFAULT NULL,
  `temperature` double DEFAULT NULL,
  `humidity` double DEFAULT NULL,
  `airquality` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temperaturedata`
--

LOCK TABLES `temperaturedata` WRITE;
/*!40000 ALTER TABLE `temperaturedata` DISABLE KEYS */;
INSERT INTO `temperaturedata` VALUES (1,'2017-07-24 12:38:01','Temperature',27.5,57,NULL),(2,'2017-07-24 12:39:15','Temperature',27.6,57.1,NULL),(3,'2017-07-24 12:39:31','Temperature',27.6,57.2,NULL),(4,'2017-07-24 12:42:01','Temperature',27.6,57,NULL),(5,'2017-07-24 12:44:01','Temperature',27.6,57.1,NULL),(6,'2017-07-24 12:46:02','Temperature',27.5,57.1,NULL),(7,'2017-07-24 12:48:01','Temperature',27.5,57.1,NULL),(8,'2017-07-24 12:50:02','Temperature',27.5,57.2,NULL),(9,'2017-07-24 12:52:01','Temperature',27.5,57.5,NULL),(10,'2017-07-24 12:54:01','Temperature',27.6,57.5,NULL),(11,'2017-07-24 12:56:01','Temperature',27.6,57,NULL),(12,'2017-07-24 12:58:01','Temperature',27.6,57.1,NULL),(13,'2017-07-24 13:00:01','Temperature',27.6,57,NULL),(14,'2017-07-24 13:02:01','Temperature',27.6,57.2,NULL),(15,'2017-07-24 13:04:01','Temperature',27.6,57.2,NULL),(16,'2017-07-24 13:06:01','Temperature',27.6,57.1,NULL),(17,'2017-07-24 13:08:02','Temperature',27.5,57.1,NULL),(18,'2017-07-24 13:10:01','Temperature',27.5,57.1,NULL),(19,'2017-07-24 13:12:01','Temperature',27.5,57,NULL),(20,'2017-07-24 13:14:01','Temperature',27.4,57,NULL),(21,'2017-07-24 13:16:02','Temperature',27.4,57,NULL),(22,'2017-07-24 13:18:01','Temperature',27.4,57.2,NULL),(23,'2017-07-24 13:20:01','Temperature',27.4,57.4,NULL),(24,'2017-07-24 13:22:02','Temperature',27.4,57.4,NULL),(25,'2017-07-24 13:24:01','Temperature',27.4,57.5,NULL),(26,'2017-07-24 13:26:01','Temperature',27.4,57.6,NULL),(27,'2017-07-24 13:28:02','Temperature',27.4,57.7,NULL),(28,'2017-07-24 13:30:01','Temperature',27.4,57.7,NULL),(29,'2017-07-24 13:32:02','Temperature',27.4,57.7,NULL),(30,'2017-07-24 13:34:01','Temperature',27.4,57.8,NULL),(31,'2017-07-24 13:36:01','Temperature',27.5,57.8,NULL),(32,'2017-07-24 13:38:01','Temperature',27.5,58.3,NULL),(33,'2017-07-24 13:45:02','Temperature',27.5,58.3,NULL),(34,'2017-07-24 14:00:01','Temperature',27.6,57.7,NULL),(35,'2017-07-24 14:15:01','Temperature',27.6,57.6,NULL),(36,'2017-07-24 14:24:01','Temperature',27.7,57,NULL),(37,'2017-07-24 14:26:01','Temperature',27.7,56.9,NULL),(38,'2017-07-24 14:28:02','Temperature',27.8,56.8,NULL),(39,'2017-07-24 14:30:01','Temperature',27.8,56.8,NULL),(40,'2017-07-24 14:32:02','Temperature',27.8,56.8,NULL),(41,'2017-07-24 14:45:01','Temperature',27.8,56.8,NULL),(42,'2017-07-24 15:00:02','Temperature',27.9,56.5,NULL),(43,'2017-07-24 15:15:01','Temperature',27.9,56.1,NULL),(44,'2017-07-24 15:30:02','Temperature',27.9,55.8,NULL),(45,'2017-07-24 15:45:01','Temperature',28.1,55.7,NULL),(46,'2017-07-24 16:00:02','Temperature',28.2,55.1,NULL),(47,'2017-07-24 16:15:01','Temperature',28.2,53.3,NULL),(48,'2017-07-24 16:30:02','Temperature',27.7,53.9,NULL),(49,'2017-07-24 16:45:01','Temperature',27.6,54.8,NULL),(50,'2017-07-24 17:00:02','Temperature',27.5,55.6,NULL),(51,'2017-07-24 17:15:01','Temperature',27.4,56.1,NULL),(52,'2017-07-24 17:30:01','Temperature',27.2,56,NULL),(53,'2017-07-24 17:45:02','Temperature',27.2,56.4,NULL),(54,'2017-07-24 18:00:01','Temperature',27.3,56.2,NULL),(55,'2017-07-24 18:15:02','Temperature',27.6,55.7,NULL),(56,'2017-07-24 18:30:01','Temperature',27.7,56.2,NULL),(57,'2017-07-24 18:45:01','Temperature',27.7,56,NULL),(58,'2017-07-24 19:00:01','Temperature',27.7,55.3,NULL),(59,'2017-07-24 19:06:40','Temperature',27.8,55.4,NULL),(60,'2017-07-24 19:16:01','TempHumid',27.8,55.3,NULL),(61,'2017-07-24 19:18:01','TempHumid',27.8,54.8,NULL),(62,'2017-07-24 19:20:01','TempHumid',27.8,54.7,NULL),(63,'2017-07-24 19:30:02','TempHumid',27.8,54.7,NULL),(64,'2017-07-24 19:45:01','TempHumid',27.8,55.8,NULL),(65,'2017-07-24 20:00:02','TempHumid',27.8,55.6,NULL),(66,'2017-07-24 20:15:02','TempHumid',27.7,55.6,NULL),(67,'2017-07-24 20:30:01','TempHumid',27.8,55.4,NULL),(68,'2017-07-24 20:45:02','TempHumid',27.7,55.1,NULL),(69,'2017-07-24 21:00:01','TempHumid',27.8,55.2,NULL),(70,'2017-07-24 21:15:02','TempHumid',27.7,55.4,NULL),(71,'2017-07-24 21:30:01','TempHumid',27.7,55.6,NULL),(72,'2017-07-24 21:45:02','TempHumid',27.6,55.6,NULL),(73,'2017-07-24 22:00:01','TempHumid',27.6,56.3,NULL),(74,'2017-07-24 22:15:01','TempHumid',27.6,56.8,NULL),(75,'2017-07-24 22:30:01','TempHumid',27.6,57.6,NULL),(76,'2017-07-24 22:45:01','TempHumid',27.6,56.7,NULL),(77,'2017-07-24 23:00:01','TempHumid',27.6,56.8,NULL),(78,'2017-07-24 23:15:01','TempHumid',27.5,59.6,NULL),(79,'2017-07-24 23:30:07','TempHumid',27.4,60.3,NULL);
/*!40000 ALTER TABLE `temperaturedata` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-24 23:45:02
