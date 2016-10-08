-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: wurst
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.12.04.2

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
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `level_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level_idx` (`level_id`),
  CONSTRAINT `level2` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
/*!40000 ALTER TABLE `game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_2_player`
--

DROP TABLE IF EXISTS `game_2_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_2_player` (
  `game_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`game_id`,`player_id`),
  KEY `player_idx` (`player_id`),
  CONSTRAINT `game` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `player` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_2_player`
--

LOCK TABLES `game_2_player` WRITE;
/*!40000 ALTER TABLE `game_2_player` DISABLE KEYS */;
/*!40000 ALTER TABLE `game_2_player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_2_tile`
--

DROP TABLE IF EXISTS `game_2_tile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_2_tile` (
  `game_id` int(10) unsigned NOT NULL,
  `player_id` int(10) unsigned NOT NULL,
  `tile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `x` int(10) unsigned DEFAULT NULL,
  `y` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`game_id`,`player_id`,`tile_id`),
  UNIQUE KEY `location_idx` (`game_id`,`x`,`y`),
  KEY `player2_idx` (`player_id`),
  KEY `tile_idx` (`tile_id`),
  CONSTRAINT `game2` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `player2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `tile` FOREIGN KEY (`tile_id`) REFERENCES `tile` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_2_tile`
--

LOCK TABLES `game_2_tile` WRITE;
/*!40000 ALTER TABLE `game_2_tile` DISABLE KEYS */;
/*!40000 ALTER TABLE `game_2_tile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `level`
--

LOCK TABLES `level` WRITE;
/*!40000 ALTER TABLE `level` DISABLE KEYS */;
INSERT INTO `level` VALUES (1,'A gentle start'),(2,'Turn baby, turn!');
/*!40000 ALTER TABLE `level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tile`
--

DROP TABLE IF EXISTS `tile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_id` int(10) unsigned NOT NULL,
  `type` enum('WE','NS','NE','ES','SW','WN','elephant','wurst') COLLATE utf8_unicode_ci NOT NULL,
  `x` int(10) unsigned NOT NULL,
  `y` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level_idx` (`level_id`),
  CONSTRAINT `level` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tile`
--

LOCK TABLES `tile` WRITE;
/*!40000 ALTER TABLE `tile` DISABLE KEYS */;
INSERT INTO `tile` VALUES (9,1,'elephant',0,4),(10,1,'WE',1,4),(11,1,'WE',2,4),(12,1,'WE',3,4),(13,1,'WE',4,4),(14,1,'WE',5,4),(15,1,'WE',6,4),(16,1,'wurst',7,4),(17,2,'elephant',0,7),(18,2,'WE',1,7),(19,2,'WE',2,7),(20,2,'WE',3,7),(21,2,'SW',4,7),(22,2,'NS',4,6),(23,2,'NS',4,5),(24,2,'NS',4,4),(25,2,'NS',4,3),(26,2,'NS',4,2),(27,2,'NS',4,1),(28,2,'NE',4,0),(29,2,'WE',5,0),(30,2,'WE',6,0),(31,2,'wurst',7,0);
/*!40000 ALTER TABLE `tile` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-08 13:22:42
