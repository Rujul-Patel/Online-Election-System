-- MySQL dump 10.13  Distrib 5.6.17, for Win64 (x86_64)
--
-- Host: localhost    Database: rujul_oes
-- ------------------------------------------------------
-- Server version	5.6.17

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
-- Table structure for table `administrators`
--

DROP TABLE IF EXISTS `administrators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrators` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `election_candidates`
--

DROP TABLE IF EXISTS `election_candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `election_candidates` (
  `el_id` int(11) NOT NULL,
  `post_no` int(11) NOT NULL,
  `candidateName` varchar(255) NOT NULL,
  `votesAcquired` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `el_id` (`el_id`,`post_no`,`candidateName`),
  UNIQUE KEY `el_id_2` (`el_id`,`post_no`,`candidateName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `election_master`
--

DROP TABLE IF EXISTS `election_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `election_master` (
  `el_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `isComplete` tinyint(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_live` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`el_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `election_structure`
--

DROP TABLE IF EXISTS `election_structure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `election_structure` (
  `el_id` int(10) unsigned NOT NULL,
  `post_num` int(11) NOT NULL,
  `post_title` varchar(255) NOT NULL,
  `max_candidates` int(11) NOT NULL DEFAULT '-1',
  `allowed_group` int(11) NOT NULL,
  UNIQUE KEY `el_id` (`el_id`,`post_num`),
  UNIQUE KEY `el_id_2` (`el_id`,`post_num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tmp_pass`
--

DROP TABLE IF EXISTS `tmp_pass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmp_pass` (
  `voter_id` int(11) NOT NULL,
  `tmp_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote_data`
--

DROP TABLE IF EXISTS `vote_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_data` (
  `el_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `vote_time` datetime DEFAULT NULL,
  `vote_casted` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` text,
  `otp` int(11) DEFAULT NULL,
  `otp_time` datetime DEFAULT NULL,
  `otp_validated` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `el_id` (`el_id`,`voter_id`),
  UNIQUE KEY `el_id_2` (`el_id`,`voter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote_logs`
--

DROP TABLE IF EXISTS `vote_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_logs` (
  `pollId` int(11) NOT NULL,
  `post_num` int(11) NOT NULL,
  `voterId` int(11) NOT NULL,
  `candidate_voted` varchar(255) NOT NULL,
  UNIQUE KEY `pollId` (`pollId`,`post_num`,`voterId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voter_groups`
--

DROP TABLE IF EXISTS `voter_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voter_groups` (
  `groupId` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `desc` varchar(255) NOT NULL,
  PRIMARY KEY (`groupId`),
  UNIQUE KEY `groupName` (`groupName`,`parent`),
  UNIQUE KEY `groupName_2` (`groupName`,`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voter_master`
--

DROP TABLE IF EXISTS `voter_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voter_master` (
  `voterId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `voter_uid` varchar(100) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`voterId`),
  UNIQUE KEY `voter_uid` (`voter_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-22  0:44:57
