-- MariaDB dump 10.19  Distrib 10.10.2-MariaDB, for osx10.17 (arm64)
--
-- Host: localhost    Database: ilios
-- ------------------------------------------------------
-- Server version	10.10.2-MariaDB

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
-- Table structure for table `aamc_method`
--

DROP TABLE IF EXISTS `aamc_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aamc_method` (
  `method_id` varchar(10) NOT NULL,
  `description` longtext NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aamc_method`
--

LOCK TABLES `aamc_method` WRITE;
/*!40000 ALTER TABLE `aamc_method` DISABLE KEYS */;
/*!40000 ALTER TABLE `aamc_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aamc_pcrs`
--

DROP TABLE IF EXISTS `aamc_pcrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aamc_pcrs` (
  `pcrs_id` varchar(21) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`pcrs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aamc_pcrs`
--

LOCK TABLES `aamc_pcrs` WRITE;
/*!40000 ALTER TABLE `aamc_pcrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `aamc_pcrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aamc_resource_type`
--

DROP TABLE IF EXISTS `aamc_resource_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aamc_resource_type` (
  `resource_type_id` varchar(21) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`resource_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aamc_resource_type`
--

LOCK TABLES `aamc_resource_type` WRITE;
/*!40000 ALTER TABLE `aamc_resource_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `aamc_resource_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert`
--

DROP TABLE IF EXISTS `alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert` (
  `alert_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_row_id` int(11) NOT NULL,
  `dispatched` tinyint(1) NOT NULL,
  `table_name` varchar(30) NOT NULL,
  `additional_text` longtext DEFAULT NULL,
  PRIMARY KEY (`alert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert`
--

LOCK TABLES `alert` WRITE;
/*!40000 ALTER TABLE `alert` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_change`
--

DROP TABLE IF EXISTS `alert_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_change` (
  `alert_id` int(11) NOT NULL,
  `alert_change_type_id` int(11) NOT NULL,
  PRIMARY KEY (`alert_id`,`alert_change_type_id`),
  KEY `IDX_77659F2793035F72` (`alert_id`),
  KEY `IDX_77659F2748FCA242` (`alert_change_type_id`),
  CONSTRAINT `FK_77659F2748FCA242` FOREIGN KEY (`alert_change_type_id`) REFERENCES `alert_change_type` (`alert_change_type_id`),
  CONSTRAINT `FK_77659F2793035F72` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_change`
--

LOCK TABLES `alert_change` WRITE;
/*!40000 ALTER TABLE `alert_change` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_change` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_change_type`
--

DROP TABLE IF EXISTS `alert_change_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_change_type` (
  `alert_change_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`alert_change_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_change_type`
--

LOCK TABLES `alert_change_type` WRITE;
/*!40000 ALTER TABLE `alert_change_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_change_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_instigator`
--

DROP TABLE IF EXISTS `alert_instigator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_instigator` (
  `alert_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`alert_id`,`user_id`),
  KEY `IDX_69CBD53C93035F72` (`alert_id`),
  KEY `IDX_69CBD53CA76ED395` (`user_id`),
  CONSTRAINT `FK_69CBD53C93035F72` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_69CBD53CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_instigator`
--

LOCK TABLES `alert_instigator` WRITE;
/*!40000 ALTER TABLE `alert_instigator` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_instigator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_recipient`
--

DROP TABLE IF EXISTS `alert_recipient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alert_recipient` (
  `alert_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  PRIMARY KEY (`alert_id`,`school_id`),
  KEY `IDX_D97AE69D93035F72` (`alert_id`),
  KEY `IDX_D97AE69DC32A47EE` (`school_id`),
  CONSTRAINT `FK_D97AE69D93035F72` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D97AE69DC32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_recipient`
--

LOCK TABLES `alert_recipient` WRITE;
/*!40000 ALTER TABLE `alert_recipient` DISABLE KEYS */;
/*!40000 ALTER TABLE `alert_recipient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application_config`
--

DROP TABLE IF EXISTS `application_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_conf_uniq` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application_config`
--

LOCK TABLES `application_config` WRITE;
/*!40000 ALTER TABLE `application_config` DISABLE KEYS */;
INSERT INTO `application_config` VALUES
(1,'authentication_type','form'),
(2,'legacy_password_salt',''),
(3,'institution_domain','iliosproject.org'),
(4,'ldap_directory_url',''),
(5,'ldap_directory_user',''),
(6,'ldap_directory_password',''),
(7,'ldap_directory_search_base',''),
(8,'ldap_directory_campus_id_property',''),
(9,'ldap_directory_username_property',''),
(10,'timezone','America/Los_Angeles'),
(11,'keep_frontend_updated','1'),
(12,'shibboleth_authentication_login_path',''),
(13,'shibboleth_authentication_logout_path',''),
(14,'shibboleth_authentication_user_id_attribute',''),
(15,'cas_authentication_verify_ssl',''),
(16,'enable_tracking','0'),
(17,'tracking_code','UA-XXXXXXXX-1'),
(18,'requireSecureConnection','false');
/*!40000 ALTER TABLE `application_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_option`
--

DROP TABLE IF EXISTS `assessment_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_option` (
  `assessment_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`assessment_option_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_option`
--

LOCK TABLES `assessment_option` WRITE;
/*!40000 ALTER TABLE `assessment_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `assessment_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(16) NOT NULL,
  `createdAt` datetime NOT NULL,
  `objectId` varchar(255) NOT NULL,
  `valuesChanged` text NOT NULL,
  `objectClass` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F6E1C0F5A76ED395` (`user_id`),
  CONSTRAINT `FK_F6E1C0F5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authentication`
--

DROP TABLE IF EXISTS `authentication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authentication` (
  `person_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `invalidate_token_issued_before` datetime DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `UNIQ_FEB4C9FDF85E0677` (`username`),
  CONSTRAINT `FK_FEB4C9FD217BBB47` FOREIGN KEY (`person_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication`
--

LOCK TABLES `authentication` WRITE;
/*!40000 ALTER TABLE `authentication` DISABLE KEYS */;
INSERT INTO `authentication` VALUES
(16,'first_user','$2y$13$p5N.sboPpH16acPTxgauyejR9ueFlk46RLPDwyu6qLnbQ7YGaVKpO',NULL);
/*!40000 ALTER TABLE `authentication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cohort`
--

DROP TABLE IF EXISTS `cohort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cohort` (
  `cohort_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `program_year_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`cohort_id`),
  UNIQUE KEY `UNIQ_D3B8C16BCB2B0673` (`program_year_id`),
  KEY `whole_k` (`program_year_id`,`cohort_id`,`title`),
  CONSTRAINT `FK_D3B8C16BCB2B0673` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cohort`
--

LOCK TABLES `cohort` WRITE;
/*!40000 ALTER TABLE `cohort` DISABLE KEYS */;
/*!40000 ALTER TABLE `cohort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competency`
--

DROP TABLE IF EXISTS `competency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competency` (
  `competency_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_competency_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`competency_id`),
  KEY `parent_competency_id_k` (`parent_competency_id`),
  KEY `IDX_80D53430C32A47EE` (`school_id`),
  CONSTRAINT `FK_80D53430C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`),
  CONSTRAINT `FK_80D53430D33F961E` FOREIGN KEY (`parent_competency_id`) REFERENCES `competency` (`competency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competency`
--

LOCK TABLES `competency` WRITE;
/*!40000 ALTER TABLE `competency` DISABLE KEYS */;
/*!40000 ALTER TABLE `competency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competency_x_aamc_pcrs`
--

DROP TABLE IF EXISTS `competency_x_aamc_pcrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competency_x_aamc_pcrs` (
  `competency_id` int(11) NOT NULL,
  `pcrs_id` varchar(21) NOT NULL,
  PRIMARY KEY (`competency_id`,`pcrs_id`),
  KEY `IDX_1683F4A7FB9F58C` (`competency_id`),
  KEY `IDX_1683F4A79CFCD25E` (`pcrs_id`),
  CONSTRAINT `FK_1683F4A79CFCD25E` FOREIGN KEY (`pcrs_id`) REFERENCES `aamc_pcrs` (`pcrs_id`),
  CONSTRAINT `FK_1683F4A7FB9F58C` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competency_x_aamc_pcrs`
--

LOCK TABLES `competency_x_aamc_pcrs` WRITE;
/*!40000 ALTER TABLE `competency_x_aamc_pcrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `competency_x_aamc_pcrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_level` smallint(6) NOT NULL,
  `year` smallint(6) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `archived` tinyint(1) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `published_as_tbd` tinyint(1) NOT NULL,
  `clerkship_type_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL,
  `ancestor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  KEY `clerkship_type_id` (`clerkship_type_id`),
  KEY `title_course_k` (`course_id`,`title`),
  KEY `external_id` (`external_id`),
  KEY `IDX_169E6FB9C32A47EE` (`school_id`),
  KEY `IDX_169E6FB9C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_169E6FB954C53094` FOREIGN KEY (`clerkship_type_id`) REFERENCES `course_clerkship_type` (`course_clerkship_type_id`),
  CONSTRAINT `FK_169E6FB9C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`),
  CONSTRAINT `FK_169E6FB9C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_administrator`
--

DROP TABLE IF EXISTS `course_administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_administrator` (
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`user_id`),
  KEY `IDX_9B524288591CC992` (`course_id`),
  KEY `IDX_9B524288A76ED395` (`user_id`),
  CONSTRAINT `FK_9B524288591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9B524288A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_administrator`
--

LOCK TABLES `course_administrator` WRITE;
/*!40000 ALTER TABLE `course_administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_clerkship_type`
--

DROP TABLE IF EXISTS `course_clerkship_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_clerkship_type` (
  `course_clerkship_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`course_clerkship_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_clerkship_type`
--

LOCK TABLES `course_clerkship_type` WRITE;
/*!40000 ALTER TABLE `course_clerkship_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_clerkship_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_director`
--

DROP TABLE IF EXISTS `course_director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_director` (
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`user_id`),
  KEY `IDX_B724BEA6591CC992` (`course_id`),
  KEY `IDX_B724BEA6A76ED395` (`user_id`),
  CONSTRAINT `FK_B724BEA6591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B724BEA6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_director`
--

LOCK TABLES `course_director` WRITE;
/*!40000 ALTER TABLE `course_director` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_learning_material`
--

DROP TABLE IF EXISTS `course_learning_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_learning_material` (
  `course_learning_material_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `learning_material_id` int(11) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `required` tinyint(1) NOT NULL,
  `notes_are_public` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`course_learning_material_id`),
  KEY `IDX_F841D788591CC992` (`course_id`),
  KEY `IDX_F841D788C1D99609` (`learning_material_id`),
  CONSTRAINT `FK_F841D788591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F841D788C1D99609` FOREIGN KEY (`learning_material_id`) REFERENCES `learning_material` (`learning_material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_learning_material`
--

LOCK TABLES `course_learning_material` WRITE;
/*!40000 ALTER TABLE `course_learning_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_learning_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_learning_material_x_mesh`
--

DROP TABLE IF EXISTS `course_learning_material_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_learning_material_x_mesh` (
  `course_learning_material_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`course_learning_material_id`,`mesh_descriptor_uid`),
  KEY `IDX_476BB36FCDB3C93B` (`mesh_descriptor_uid`),
  KEY `IDX_476BB36F46C5AD2E` (`course_learning_material_id`),
  CONSTRAINT `FK_476BB36F46C5AD2E` FOREIGN KEY (`course_learning_material_id`) REFERENCES `course_learning_material` (`course_learning_material_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_476BB36FCDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_learning_material_x_mesh`
--

LOCK TABLES `course_learning_material_x_mesh` WRITE;
/*!40000 ALTER TABLE `course_learning_material_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_learning_material_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_objective_x_mesh`
--

DROP TABLE IF EXISTS `course_objective_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_objective_x_mesh` (
  `course_objective_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`course_objective_id`,`mesh_descriptor_uid`),
  KEY `IDX_16291D94F28231CE` (`course_objective_id`),
  KEY `IDX_16291D94CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_16291D94CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE,
  CONSTRAINT `FK_16291D94F28231CE` FOREIGN KEY (`course_objective_id`) REFERENCES `course_x_objective` (`course_objective_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_objective_x_mesh`
--

LOCK TABLES `course_objective_x_mesh` WRITE;
/*!40000 ALTER TABLE `course_objective_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_objective_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_objective_x_program_year_objective`
--

DROP TABLE IF EXISTS `course_objective_x_program_year_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_objective_x_program_year_objective` (
  `course_objective_id` int(11) NOT NULL,
  `program_year_objective_id` int(11) NOT NULL,
  PRIMARY KEY (`course_objective_id`,`program_year_objective_id`),
  KEY `IDX_CB20F416F28231CE` (`course_objective_id`),
  KEY `IDX_CB20F416BA83A669` (`program_year_objective_id`),
  CONSTRAINT `FK_CB20F416BA83A669` FOREIGN KEY (`program_year_objective_id`) REFERENCES `program_year_x_objective` (`program_year_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CB20F416F28231CE` FOREIGN KEY (`course_objective_id`) REFERENCES `course_x_objective` (`course_objective_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_objective_x_program_year_objective`
--

LOCK TABLES `course_objective_x_program_year_objective` WRITE;
/*!40000 ALTER TABLE `course_objective_x_program_year_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_objective_x_program_year_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_objective_x_term`
--

DROP TABLE IF EXISTS `course_objective_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_objective_x_term` (
  `course_objective_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`course_objective_id`,`term_id`),
  KEY `IDX_5249C04FF28231CE` (`course_objective_id`),
  KEY `IDX_5249C04FE2C35FC` (`term_id`),
  CONSTRAINT `FK_5249C04FE2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5249C04FF28231CE` FOREIGN KEY (`course_objective_id`) REFERENCES `course_x_objective` (`course_objective_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_objective_x_term`
--

LOCK TABLES `course_objective_x_term` WRITE;
/*!40000 ALTER TABLE `course_objective_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_objective_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_student_advisor`
--

DROP TABLE IF EXISTS `course_student_advisor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_student_advisor` (
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`user_id`),
  KEY `IDX_D5D0445E591CC992` (`course_id`),
  KEY `IDX_D5D0445EA76ED395` (`user_id`),
  CONSTRAINT `FK_D5D0445E591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D5D0445EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_student_advisor`
--

LOCK TABLES `course_student_advisor` WRITE;
/*!40000 ALTER TABLE `course_student_advisor` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_student_advisor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_x_cohort`
--

DROP TABLE IF EXISTS `course_x_cohort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_x_cohort` (
  `course_id` int(11) NOT NULL,
  `cohort_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`cohort_id`),
  KEY `IDX_4C4C18C35983C93` (`cohort_id`),
  KEY `IDX_4C4C18C591CC992` (`course_id`),
  CONSTRAINT `FK_4C4C18C35983C93` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4C4C18C591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_x_cohort`
--

LOCK TABLES `course_x_cohort` WRITE;
/*!40000 ALTER TABLE `course_x_cohort` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_x_cohort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_x_mesh`
--

DROP TABLE IF EXISTS `course_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_x_mesh` (
  `course_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`course_id`,`mesh_descriptor_uid`),
  KEY `IDX_82E35212591CC992` (`course_id`),
  KEY `IDX_82E35212CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_82E35212591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_82E35212CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_x_mesh`
--

LOCK TABLES `course_x_mesh` WRITE;
/*!40000 ALTER TABLE `course_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_x_objective`
--

DROP TABLE IF EXISTS `course_x_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_x_objective` (
  `course_objective_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `ancestor_id` int(11) DEFAULT NULL,
  `title` longtext NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`course_objective_id`),
  KEY `IDX_3B37B1AD591CC992` (`course_id`),
  KEY `IDX_4C880AE4C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_3B37B1AD591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4C880AE4C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `course_x_objective` (`course_objective_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_x_objective`
--

LOCK TABLES `course_x_objective` WRITE;
/*!40000 ALTER TABLE `course_x_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_x_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_x_term`
--

DROP TABLE IF EXISTS `course_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_x_term` (
  `course_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`term_id`),
  KEY `IDX_C6838FC9591CC992` (`course_id`),
  KEY `IDX_C6838FC9E2C35FC` (`term_id`),
  CONSTRAINT `FK_C6838FC9591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C6838FC9E2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_x_term`
--

LOCK TABLES `course_x_term` WRITE;
/*!40000 ALTER TABLE `course_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_academic_level`
--

DROP TABLE IF EXISTS `curriculum_inventory_academic_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_academic_level` (
  `academic_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` longtext DEFAULT NULL,
  PRIMARY KEY (`academic_level_id`),
  UNIQUE KEY `report_id_level` (`report_id`,`level`),
  KEY `IDX_B4D3296D4BD2A4C0` (`report_id`),
  CONSTRAINT `FK_B4D3296D4BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_academic_level`
--

LOCK TABLES `curriculum_inventory_academic_level` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_academic_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_academic_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_export`
--

DROP TABLE IF EXISTS `curriculum_inventory_export`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_export` (
  `report_id` int(11) NOT NULL,
  `document` longtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `export_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`export_id`),
  UNIQUE KEY `UNIQ_E892E88E4BD2A4C0` (`report_id`),
  KEY `fkey_curriculum_inventory_export_user_id` (`created_by`),
  CONSTRAINT `FK_E892E88E4BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`),
  CONSTRAINT `FK_E892E88EDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_export`
--

LOCK TABLES `curriculum_inventory_export` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_export` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_export` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_institution`
--

DROP TABLE IF EXISTS `curriculum_inventory_institution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_institution` (
  `school_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `aamc_code` varchar(10) NOT NULL,
  `address_street` varchar(100) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state_or_province` varchar(50) NOT NULL,
  `address_zipcode` varchar(10) NOT NULL,
  `address_country_code` varchar(2) NOT NULL,
  `institution_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`institution_id`),
  UNIQUE KEY `UNIQ_3426AC4BC32A47EE` (`school_id`),
  CONSTRAINT `FK_3426AC4BC32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_institution`
--

LOCK TABLES `curriculum_inventory_institution` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_institution` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_institution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_report`
--

DROP TABLE IF EXISTS `curriculum_inventory_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) DEFAULT NULL,
  `year` smallint(6) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  UNIQUE KEY `idx_ci_report_token_unique` (`token`),
  KEY `IDX_6E31899E3EB8070A` (`program_id`),
  CONSTRAINT `FK_6E31899E3EB8070A` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_report`
--

LOCK TABLES `curriculum_inventory_report` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_report_administrator`
--

DROP TABLE IF EXISTS `curriculum_inventory_report_administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_report_administrator` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`report_id`,`user_id`),
  KEY `IDX_730428DB4BD2A4C0` (`report_id`),
  KEY `IDX_730428DBA76ED395` (`user_id`),
  CONSTRAINT `FK_730428DB4BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_730428DBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_report_administrator`
--

LOCK TABLES `curriculum_inventory_report_administrator` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_report_administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_report_administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_sequence`
--

DROP TABLE IF EXISTS `curriculum_inventory_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_sequence` (
  `report_id` int(11) NOT NULL,
  `description` longtext DEFAULT NULL,
  `sequence_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence_id`),
  UNIQUE KEY `UNIQ_B8AE58F54BD2A4C0` (`report_id`),
  CONSTRAINT `FK_B8AE58F54BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_sequence`
--

LOCK TABLES `curriculum_inventory_sequence` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_sequence` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_sequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_sequence_block`
--

DROP TABLE IF EXISTS `curriculum_inventory_sequence_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_sequence_block` (
  `sequence_block_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) DEFAULT NULL,
  `required` int(11) NOT NULL,
  `child_sequence_order` smallint(6) NOT NULL,
  `order_in_sequence` int(11) NOT NULL,
  `minimum` int(11) NOT NULL,
  `maximum` int(11) NOT NULL,
  `track` tinyint(1) NOT NULL,
  `description` longtext DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `starting_academic_level_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `parent_sequence_block_id` int(11) DEFAULT NULL,
  `ending_academic_level_id` int(11) NOT NULL,
  PRIMARY KEY (`sequence_block_id`),
  KEY `IDX_22E6B680591CC992` (`course_id`),
  KEY `IDX_22E6B680DEB52F47` (`parent_sequence_block_id`),
  KEY `IDX_22E6B6804BD2A4C0` (`report_id`),
  KEY `IDX_22E6B680145CDCE1` (`starting_academic_level_id`),
  KEY `IDX_22E6B68062B4C1B6` (`ending_academic_level_id`),
  CONSTRAINT `FK_22E6B680145CDCE1` FOREIGN KEY (`starting_academic_level_id`) REFERENCES `curriculum_inventory_academic_level` (`academic_level_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_22E6B6804BD2A4C0` FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_22E6B680591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  CONSTRAINT `FK_22E6B68062B4C1B6` FOREIGN KEY (`ending_academic_level_id`) REFERENCES `curriculum_inventory_academic_level` (`academic_level_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_22E6B680DEB52F47` FOREIGN KEY (`parent_sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_sequence_block`
--

LOCK TABLES `curriculum_inventory_sequence_block` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_sequence_block_x_excluded_session`
--

DROP TABLE IF EXISTS `curriculum_inventory_sequence_block_x_excluded_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_sequence_block_x_excluded_session` (
  `sequence_block_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  PRIMARY KEY (`sequence_block_id`,`session_id`),
  KEY `IDX_67E306F861D1D223` (`sequence_block_id`),
  KEY `IDX_67E306F8613FECDF` (`session_id`),
  CONSTRAINT `FK_67E306F8613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_67E306F861D1D223` FOREIGN KEY (`sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_sequence_block_x_excluded_session`
--

LOCK TABLES `curriculum_inventory_sequence_block_x_excluded_session` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block_x_excluded_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block_x_excluded_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curriculum_inventory_sequence_block_x_session`
--

DROP TABLE IF EXISTS `curriculum_inventory_sequence_block_x_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curriculum_inventory_sequence_block_x_session` (
  `sequence_block_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  PRIMARY KEY (`sequence_block_id`,`session_id`),
  KEY `IDX_E1268BFB61D1D223` (`sequence_block_id`),
  KEY `IDX_E1268BFB613FECDF` (`session_id`),
  CONSTRAINT `FK_E1268BFB613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E1268BFB61D1D223` FOREIGN KEY (`sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curriculum_inventory_sequence_block_x_session`
--

LOCK TABLES `curriculum_inventory_sequence_block_x_session` WRITE;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block_x_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `curriculum_inventory_sequence_block_x_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_group_id` int(11) DEFAULT NULL,
  `cohort_id` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `ancestor_id` int(11) DEFAULT NULL,
  `url` varchar(2000) DEFAULT NULL,
  `needs_accommodation` tinyint(1) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `IDX_6DC044C561997596` (`parent_group_id`),
  KEY `IDX_6DC044C535983C93` (`cohort_id`),
  KEY `IDX_6DC044C5C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_6DC044C535983C93` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6DC044C561997596` FOREIGN KEY (`parent_group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6DC044C5C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_x_instructor`
--

DROP TABLE IF EXISTS `group_x_instructor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_x_instructor` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `IDX_8CE57915FE54D947` (`group_id`),
  KEY `IDX_8CE57915A76ED395` (`user_id`),
  CONSTRAINT `FK_8CE57915A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8CE57915FE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_x_instructor`
--

LOCK TABLES `group_x_instructor` WRITE;
/*!40000 ALTER TABLE `group_x_instructor` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_x_instructor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_x_instructor_group`
--

DROP TABLE IF EXISTS `group_x_instructor_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_x_instructor_group` (
  `group_id` int(11) NOT NULL,
  `instructor_group_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`instructor_group_id`),
  KEY `IDX_49AFEA21FE54D947` (`group_id`),
  KEY `IDX_49AFEA21FE367BE2` (`instructor_group_id`),
  CONSTRAINT `FK_49AFEA21FE367BE2` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_49AFEA21FE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_x_instructor_group`
--

LOCK TABLES `group_x_instructor_group` WRITE;
/*!40000 ALTER TABLE `group_x_instructor_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_x_instructor_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_x_user`
--

DROP TABLE IF EXISTS `group_x_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_x_user` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `IDX_93A1A790FE54D947` (`group_id`),
  KEY `IDX_93A1A790A76ED395` (`user_id`),
  CONSTRAINT `FK_93A1A790A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `FK_93A1A790FE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_x_user`
--

LOCK TABLES `group_x_user` WRITE;
/*!40000 ALTER TABLE `group_x_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_x_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ilm_session_facet`
--

DROP TABLE IF EXISTS `ilm_session_facet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ilm_session_facet` (
  `ilm_session_facet_id` int(11) NOT NULL AUTO_INCREMENT,
  `hours` decimal(6,2) NOT NULL,
  `due_date` datetime NOT NULL,
  `session_id` int(11) NOT NULL,
  PRIMARY KEY (`ilm_session_facet_id`),
  UNIQUE KEY `UNIQ_8C070D9613FECDF` (`session_id`),
  CONSTRAINT `FK_8C070D9613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ilm_session_facet`
--

LOCK TABLES `ilm_session_facet` WRITE;
/*!40000 ALTER TABLE `ilm_session_facet` DISABLE KEYS */;
/*!40000 ALTER TABLE `ilm_session_facet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ilm_session_facet_x_group`
--

DROP TABLE IF EXISTS `ilm_session_facet_x_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ilm_session_facet_x_group` (
  `ilm_session_facet_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`ilm_session_facet_id`,`group_id`),
  KEY `IDX_B43B41DC504270C1` (`ilm_session_facet_id`),
  KEY `IDX_B43B41DCFE54D947` (`group_id`),
  CONSTRAINT `FK_B43B41DC504270C1` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B43B41DCFE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ilm_session_facet_x_group`
--

LOCK TABLES `ilm_session_facet_x_group` WRITE;
/*!40000 ALTER TABLE `ilm_session_facet_x_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `ilm_session_facet_x_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ilm_session_facet_x_instructor`
--

DROP TABLE IF EXISTS `ilm_session_facet_x_instructor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ilm_session_facet_x_instructor` (
  `ilm_session_facet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ilm_session_facet_id`,`user_id`),
  KEY `IDX_82B9A47B504270C1` (`ilm_session_facet_id`),
  KEY `IDX_82B9A47BA76ED395` (`user_id`),
  CONSTRAINT `FK_82B9A47B504270C1` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_82B9A47BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ilm_session_facet_x_instructor`
--

LOCK TABLES `ilm_session_facet_x_instructor` WRITE;
/*!40000 ALTER TABLE `ilm_session_facet_x_instructor` DISABLE KEYS */;
/*!40000 ALTER TABLE `ilm_session_facet_x_instructor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ilm_session_facet_x_instructor_group`
--

DROP TABLE IF EXISTS `ilm_session_facet_x_instructor_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ilm_session_facet_x_instructor_group` (
  `ilm_session_facet_id` int(11) NOT NULL,
  `instructor_group_id` int(11) NOT NULL,
  PRIMARY KEY (`ilm_session_facet_id`,`instructor_group_id`),
  KEY `IDX_8171A2F3504270C1` (`ilm_session_facet_id`),
  KEY `IDX_8171A2F3FE367BE2` (`instructor_group_id`),
  CONSTRAINT `FK_8171A2F3504270C1` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_8171A2F3FE367BE2` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ilm_session_facet_x_instructor_group`
--

LOCK TABLES `ilm_session_facet_x_instructor_group` WRITE;
/*!40000 ALTER TABLE `ilm_session_facet_x_instructor_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `ilm_session_facet_x_instructor_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ilm_session_facet_x_learner`
--

DROP TABLE IF EXISTS `ilm_session_facet_x_learner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ilm_session_facet_x_learner` (
  `ilm_session_facet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ilm_session_facet_id`,`user_id`),
  KEY `IDX_E385BC58504270C1` (`ilm_session_facet_id`),
  KEY `IDX_E385BC58A76ED395` (`user_id`),
  CONSTRAINT `FK_E385BC58504270C1` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E385BC58A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ilm_session_facet_x_learner`
--

LOCK TABLES `ilm_session_facet_x_learner` WRITE;
/*!40000 ALTER TABLE `ilm_session_facet_x_learner` DISABLE KEYS */;
/*!40000 ALTER TABLE `ilm_session_facet_x_learner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingestion_exception`
--

DROP TABLE IF EXISTS `ingestion_exception`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingestion_exception` (
  `user_id` int(11) NOT NULL,
  `ingested_wide_uid` varchar(32) NOT NULL,
  `ingestion_exception_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ingestion_exception_id`),
  UNIQUE KEY `UNIQ_65713AFFA76ED395` (`user_id`),
  CONSTRAINT `FK_65713AFFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingestion_exception`
--

LOCK TABLES `ingestion_exception` WRITE;
/*!40000 ALTER TABLE `ingestion_exception` DISABLE KEYS */;
/*!40000 ALTER TABLE `ingestion_exception` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_group`
--

DROP TABLE IF EXISTS `instructor_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_group` (
  `instructor_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`instructor_group_id`),
  KEY `IDX_BF12A389C32A47EE` (`school_id`),
  CONSTRAINT `FK_BF12A389C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_group`
--

LOCK TABLES `instructor_group` WRITE;
/*!40000 ALTER TABLE `instructor_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `instructor_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_group_x_user`
--

DROP TABLE IF EXISTS `instructor_group_x_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_group_x_user` (
  `instructor_group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`instructor_group_id`,`user_id`),
  KEY `IDX_6423CE8CFE367BE2` (`instructor_group_id`),
  KEY `IDX_6423CE8CA76ED395` (`user_id`),
  CONSTRAINT `FK_6423CE8CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `FK_6423CE8CFE367BE2` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_group_x_user`
--

LOCK TABLES `instructor_group_x_user` WRITE;
/*!40000 ALTER TABLE `instructor_group_x_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `instructor_group_x_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_material`
--

DROP TABLE IF EXISTS `learning_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_material` (
  `learning_material_id` int(11) NOT NULL AUTO_INCREMENT,
  `learning_material_status_id` int(11) NOT NULL,
  `learning_material_user_role_id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `mime_type` varchar(96) DEFAULT NULL,
  `relative_file_system_location` varchar(128) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filesize` int(10) unsigned DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `copyright_ownership` tinyint(1) DEFAULT NULL,
  `copyright_rationale` longtext DEFAULT NULL,
  `upload_date` datetime NOT NULL,
  `owning_user_id` int(11) NOT NULL,
  `asset_creator` varchar(80) DEFAULT NULL,
  `web_link` varchar(256) DEFAULT NULL,
  `citation` varchar(512) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`learning_material_id`),
  UNIQUE KEY `idx_learning_material_token_unique` (`token`),
  KEY `IDX_58CE718BA0407615` (`learning_material_status_id`),
  KEY `IDX_58CE718B67A71A40` (`owning_user_id`),
  KEY `IDX_58CE718B7505C8EA` (`learning_material_user_role_id`),
  CONSTRAINT `FK_58CE718B67A71A40` FOREIGN KEY (`owning_user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `FK_58CE718B7505C8EA` FOREIGN KEY (`learning_material_user_role_id`) REFERENCES `learning_material_user_role` (`learning_material_user_role_id`),
  CONSTRAINT `FK_58CE718BA0407615` FOREIGN KEY (`learning_material_status_id`) REFERENCES `learning_material_status` (`learning_material_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_material`
--

LOCK TABLES `learning_material` WRITE;
/*!40000 ALTER TABLE `learning_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_material_status`
--

DROP TABLE IF EXISTS `learning_material_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_material_status` (
  `learning_material_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`learning_material_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_material_status`
--

LOCK TABLES `learning_material_status` WRITE;
/*!40000 ALTER TABLE `learning_material_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_material_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_material_user_role`
--

DROP TABLE IF EXISTS `learning_material_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_material_user_role` (
  `learning_material_user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`learning_material_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_material_user_role`
--

LOCK TABLES `learning_material_user_role` WRITE;
/*!40000 ALTER TABLE `learning_material_user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_material_user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_concept`
--

DROP TABLE IF EXISTS `mesh_concept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_concept` (
  `mesh_concept_uid` varchar(12) NOT NULL,
  `name` varchar(255) NOT NULL,
  `preferred` tinyint(1) NOT NULL,
  `scope_note` longtext DEFAULT NULL,
  `casn_1_name` varchar(512) DEFAULT NULL,
  `registry_number` varchar(30) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`mesh_concept_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_concept`
--

LOCK TABLES `mesh_concept` WRITE;
/*!40000 ALTER TABLE `mesh_concept` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_concept` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_concept_x_term`
--

DROP TABLE IF EXISTS `mesh_concept_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_concept_x_term` (
  `mesh_concept_uid` varchar(12) NOT NULL,
  `mesh_term_id` int(11) NOT NULL,
  PRIMARY KEY (`mesh_term_id`,`mesh_concept_uid`),
  KEY `IDX_100AC50FE34D9FF5` (`mesh_concept_uid`),
  KEY `IDX_100AC50F928873C` (`mesh_term_id`),
  CONSTRAINT `FK_100AC50F928873C` FOREIGN KEY (`mesh_term_id`) REFERENCES `mesh_term` (`mesh_term_id`),
  CONSTRAINT `FK_100AC50FE34D9FF5` FOREIGN KEY (`mesh_concept_uid`) REFERENCES `mesh_concept` (`mesh_concept_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_concept_x_term`
--

LOCK TABLES `mesh_concept_x_term` WRITE;
/*!40000 ALTER TABLE `mesh_concept_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_concept_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_descriptor`
--

DROP TABLE IF EXISTS `mesh_descriptor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_descriptor` (
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  `name` varchar(192) NOT NULL,
  `annotation` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`mesh_descriptor_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_descriptor`
--

LOCK TABLES `mesh_descriptor` WRITE;
/*!40000 ALTER TABLE `mesh_descriptor` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_descriptor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_descriptor_x_concept`
--

DROP TABLE IF EXISTS `mesh_descriptor_x_concept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_descriptor_x_concept` (
  `mesh_concept_uid` varchar(12) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`mesh_concept_uid`,`mesh_descriptor_uid`),
  KEY `IDX_1AF85275E34D9FF5` (`mesh_concept_uid`),
  KEY `IDX_1AF85275CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_1AF85275CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_1AF85275E34D9FF5` FOREIGN KEY (`mesh_concept_uid`) REFERENCES `mesh_concept` (`mesh_concept_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_descriptor_x_concept`
--

LOCK TABLES `mesh_descriptor_x_concept` WRITE;
/*!40000 ALTER TABLE `mesh_descriptor_x_concept` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_descriptor_x_concept` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_descriptor_x_qualifier`
--

DROP TABLE IF EXISTS `mesh_descriptor_x_qualifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_descriptor_x_qualifier` (
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  `mesh_qualifier_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`mesh_qualifier_uid`,`mesh_descriptor_uid`),
  KEY `IDX_FC5A6AD763490620` (`mesh_qualifier_uid`),
  KEY `IDX_FC5A6AD7CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_FC5A6AD763490620` FOREIGN KEY (`mesh_qualifier_uid`) REFERENCES `mesh_qualifier` (`mesh_qualifier_uid`),
  CONSTRAINT `FK_FC5A6AD7CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_descriptor_x_qualifier`
--

LOCK TABLES `mesh_descriptor_x_qualifier` WRITE;
/*!40000 ALTER TABLE `mesh_descriptor_x_qualifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_descriptor_x_qualifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_previous_indexing`
--

DROP TABLE IF EXISTS `mesh_previous_indexing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_previous_indexing` (
  `mesh_descriptor_uid` varchar(12) DEFAULT NULL,
  `previous_indexing` longtext NOT NULL,
  `mesh_previous_indexing_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`mesh_previous_indexing_id`),
  UNIQUE KEY `descriptor_previous` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_32B6E2F4CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_previous_indexing`
--

LOCK TABLES `mesh_previous_indexing` WRITE;
/*!40000 ALTER TABLE `mesh_previous_indexing` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_previous_indexing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_qualifier`
--

DROP TABLE IF EXISTS `mesh_qualifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_qualifier` (
  `mesh_qualifier_uid` varchar(12) NOT NULL,
  `name` varchar(60) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`mesh_qualifier_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_qualifier`
--

LOCK TABLES `mesh_qualifier` WRITE;
/*!40000 ALTER TABLE `mesh_qualifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_qualifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_term`
--

DROP TABLE IF EXISTS `mesh_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_term` (
  `mesh_term_uid` varchar(12) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lexical_tag` varchar(12) DEFAULT NULL,
  `concept_preferred` tinyint(1) DEFAULT NULL,
  `record_preferred` tinyint(1) DEFAULT NULL,
  `permuted` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `mesh_term_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`mesh_term_id`),
  UNIQUE KEY `mesh_term_uid_name` (`mesh_term_uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_term`
--

LOCK TABLES `mesh_term` WRITE;
/*!40000 ALTER TABLE `mesh_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesh_tree`
--

DROP TABLE IF EXISTS `mesh_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesh_tree` (
  `mesh_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `mesh_descriptor_uid` varchar(12) DEFAULT NULL,
  `tree_number` varchar(80) NOT NULL,
  PRIMARY KEY (`mesh_tree_id`),
  KEY `IDX_C63042D9CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_C63042D9CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesh_tree`
--

LOCK TABLES `mesh_tree` WRITE;
/*!40000 ALTER TABLE `mesh_tree` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesh_tree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` VALUES
('Ilios\\Migrations\\Version20150805000000',NULL,NULL),
('Ilios\\Migrations\\Version20150813184053',NULL,NULL),
('Ilios\\Migrations\\Version20150819000940',NULL,NULL),
('Ilios\\Migrations\\Version20150819181346',NULL,NULL),
('Ilios\\Migrations\\Version20150819200000',NULL,NULL),
('Ilios\\Migrations\\Version20150819234126',NULL,NULL),
('Ilios\\Migrations\\Version20150820013804',NULL,NULL),
('Ilios\\Migrations\\Version20150820020125',NULL,NULL),
('Ilios\\Migrations\\Version20150826120000',NULL,NULL),
('Ilios\\Migrations\\Version20150826130000',NULL,NULL),
('Ilios\\Migrations\\Version20150826201107',NULL,NULL),
('Ilios\\Migrations\\Version20150826215733',NULL,NULL),
('Ilios\\Migrations\\Version20150828223306',NULL,NULL),
('Ilios\\Migrations\\Version20150901000000',NULL,NULL),
('Ilios\\Migrations\\Version20150903000000',NULL,NULL),
('Ilios\\Migrations\\Version20150909231235',NULL,NULL),
('Ilios\\Migrations\\Version20150914052024',NULL,NULL),
('Ilios\\Migrations\\Version20150914210711',NULL,NULL),
('Ilios\\Migrations\\Version20150916173024',NULL,NULL),
('Ilios\\Migrations\\Version20151004075931',NULL,NULL),
('Ilios\\Migrations\\Version20151104175441',NULL,NULL),
('Ilios\\Migrations\\Version20151106022409',NULL,NULL),
('Ilios\\Migrations\\Version20151118233516',NULL,NULL),
('Ilios\\Migrations\\Version20151125055054',NULL,NULL),
('Ilios\\Migrations\\Version20151204220022',NULL,NULL),
('Ilios\\Migrations\\Version20160104231711',NULL,NULL),
('Ilios\\Migrations\\Version20160105002207',NULL,NULL),
('Ilios\\Migrations\\Version20160106190703',NULL,NULL),
('Ilios\\Migrations\\Version20160106235355',NULL,NULL),
('Ilios\\Migrations\\Version20160111222451',NULL,NULL),
('Ilios\\Migrations\\Version20160122173943',NULL,NULL),
('Ilios\\Migrations\\Version20160125235702',NULL,NULL),
('Ilios\\Migrations\\Version20160128223548',NULL,NULL),
('Ilios\\Migrations\\Version20160223211000',NULL,NULL),
('Ilios\\Migrations\\Version20160303215618',NULL,NULL),
('Ilios\\Migrations\\Version20160308211000',NULL,NULL),
('Ilios\\Migrations\\Version20160419233601',NULL,NULL),
('Ilios\\Migrations\\Version20160421221726',NULL,NULL),
('Ilios\\Migrations\\Version20160422180552',NULL,NULL),
('Ilios\\Migrations\\Version20160502233707',NULL,NULL),
('Ilios\\Migrations\\Version20160607171112',NULL,NULL),
('Ilios\\Migrations\\Version20160608010345',NULL,NULL),
('Ilios\\Migrations\\Version20160615223611',NULL,NULL),
('Ilios\\Migrations\\Version20160627210338',NULL,NULL),
('Ilios\\Migrations\\Version20160715212648',NULL,NULL),
('Ilios\\Migrations\\Version20160812180141',NULL,NULL),
('Ilios\\Migrations\\Version20160915043119',NULL,NULL),
('Ilios\\Migrations\\Version20160919224121',NULL,NULL),
('Ilios\\Migrations\\Version20160921175624',NULL,NULL),
('Ilios\\Migrations\\Version20161011223733',NULL,NULL),
('Ilios\\Migrations\\Version20161012025413',NULL,NULL),
('Ilios\\Migrations\\Version20161013164943',NULL,NULL),
('Ilios\\Migrations\\Version20161013183010',NULL,NULL),
('Ilios\\Migrations\\Version20161013205034',NULL,NULL),
('Ilios\\Migrations\\Version20161014032909',NULL,NULL),
('Ilios\\Migrations\\Version20161209191650',NULL,NULL),
('Ilios\\Migrations\\Version20170203161914',NULL,NULL),
('Ilios\\Migrations\\Version20170210175148',NULL,NULL),
('Ilios\\Migrations\\Version20170313212150',NULL,NULL),
('Ilios\\Migrations\\Version20170313223659',NULL,NULL),
('Ilios\\Migrations\\Version20170313230000',NULL,NULL),
('Ilios\\Migrations\\Version20170314175718',NULL,NULL),
('Ilios\\Migrations\\Version20170428054237',NULL,NULL),
('Ilios\\Migrations\\Version20170510165532',NULL,NULL),
('Ilios\\Migrations\\Version20170818144244',NULL,NULL),
('Ilios\\Migrations\\Version20170824000000',NULL,NULL),
('Ilios\\Migrations\\Version20170905234026',NULL,NULL),
('Ilios\\Migrations\\Version20170906230101',NULL,NULL),
('Ilios\\Migrations\\Version20170913234324',NULL,NULL),
('Ilios\\Migrations\\Version20170915185745',NULL,NULL),
('Ilios\\Migrations\\Version20170917182423',NULL,NULL),
('Ilios\\Migrations\\Version20170917183718',NULL,NULL),
('Ilios\\Migrations\\Version20171207221415',NULL,NULL),
('Ilios\\Migrations\\Version20180102185950',NULL,NULL),
('Ilios\\Migrations\\Version20180307174249',NULL,NULL),
('Ilios\\Migrations\\Version20180309225012',NULL,NULL),
('Ilios\\Migrations\\Version20180313211111',NULL,NULL),
('Ilios\\Migrations\\Version20180320041519',NULL,NULL),
('Ilios\\Migrations\\Version20180323231620',NULL,NULL),
('Ilios\\Migrations\\Version20180323231630',NULL,NULL),
('Ilios\\Migrations\\Version20180326223500',NULL,NULL),
('Ilios\\Migrations\\Version20180514214501',NULL,NULL),
('Ilios\\Migrations\\Version20180617000000',NULL,NULL),
('Ilios\\Migrations\\Version20180803101835',NULL,NULL),
('Ilios\\Migrations\\Version20180823181914',NULL,NULL),
('Ilios\\Migrations\\Version20181219195341',NULL,NULL),
('Ilios\\Migrations\\Version20181219202348',NULL,NULL),
('Ilios\\Migrations\\Version20190201190000',NULL,NULL),
('Ilios\\Migrations\\Version20190225000000',NULL,NULL),
('Ilios\\Migrations\\Version20190307000000',NULL,NULL),
('Ilios\\Migrations\\Version20190308004110',NULL,NULL),
('Ilios\\Migrations\\Version20190513061851',NULL,NULL),
('Ilios\\Migrations\\Version20190612212532',NULL,NULL),
('Ilios\\Migrations\\Version20190711180817','2019-08-13 11:44:56',NULL),
('Ilios\\Migrations\\Version20191030201837','2019-11-14 06:15:28',NULL),
('Ilios\\Migrations\\Version20200114000000','2020-01-29 11:49:14',NULL),
('Ilios\\Migrations\\Version20200330062405','2020-04-11 18:10:33',NULL),
('Ilios\\Migrations\\Version20200408232411','2020-06-02 07:51:07',NULL),
('Ilios\\Migrations\\Version20200612202533','2020-07-09 07:43:09',NULL),
('Ilios\\Migrations\\Version20200630000000','2020-07-09 07:43:10',NULL),
('Ilios\\Migrations\\Version20200630182857','2020-08-08 17:10:15',NULL),
('Ilios\\Migrations\\Version20200719042101','2020-08-08 17:10:15',NULL),
('Ilios\\Migrations\\Version20200723160803','2020-08-08 17:10:17',NULL),
('Ilios\\Migrations\\Version20200728222336','2020-08-08 17:10:19',NULL),
('Ilios\\Migrations\\Version20200730215836','2020-08-08 17:10:19',NULL),
('Ilios\\Migrations\\Version20200814223927','2020-08-25 11:33:48',NULL),
('Ilios\\Migrations\\Version20200902230650','2020-09-11 17:35:11',NULL),
('Ilios\\Migrations\\Version20201001230658','2020-10-05 05:56:53',NULL),
('Ilios\\Migrations\\Version20201117221343','2020-11-23 22:22:29',NULL),
('Ilios\\Migrations\\Version20210111210836','2021-02-01 09:01:08',NULL),
('Ilios\\Migrations\\Version20210126001058','2021-02-01 09:01:09',NULL),
('Ilios\\Migrations\\Version20210201224131','2021-03-01 07:54:05',NULL),
('Ilios\\Migrations\\Version20210204234902','2021-03-01 07:54:05',NULL),
('Ilios\\Migrations\\Version20210907091228','2021-09-10 04:29:49',162),
('Ilios\\Migrations\\Version20220202010454','2022-02-10 14:45:02',77),
('Ilios\\Migrations\\Version20220204213404','2022-03-15 22:36:37',723),
('Ilios\\Migrations\\Version20220408184354','2022-05-12 12:47:33',162),
('Ilios\\Migrations\\Version20220427070439','2022-05-12 12:47:33',563),
('Ilios\\Migrations\\Version20220610080000','2022-06-30 04:27:38',17),
('Ilios\\Migrations\\Version20220714000000','2022-09-02 13:43:03',225);
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering`
--

DROP TABLE IF EXISTS `offering`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering` (
  `offering_id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(255) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `last_updated_on` datetime NOT NULL,
  `site` varchar(255) DEFAULT NULL,
  `url` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`offering_id`),
  KEY `session_id_k` (`session_id`),
  KEY `offering_dates_session_k` (`offering_id`,`session_id`,`start_date`,`end_date`),
  CONSTRAINT `FK_A5682AB1613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering`
--

LOCK TABLES `offering` WRITE;
/*!40000 ALTER TABLE `offering` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_x_group`
--

DROP TABLE IF EXISTS `offering_x_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering_x_group` (
  `offering_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`offering_id`,`group_id`),
  KEY `IDX_4D68848F8EDF74F0` (`offering_id`),
  KEY `IDX_4D68848FFE54D947` (`group_id`),
  CONSTRAINT `FK_4D68848F8EDF74F0` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4D68848FFE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_x_group`
--

LOCK TABLES `offering_x_group` WRITE;
/*!40000 ALTER TABLE `offering_x_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_x_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_x_instructor`
--

DROP TABLE IF EXISTS `offering_x_instructor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering_x_instructor` (
  `offering_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`offering_id`,`user_id`),
  KEY `IDX_171DC5498EDF74F0` (`offering_id`),
  KEY `IDX_171DC549A76ED395` (`user_id`),
  CONSTRAINT `FK_171DC5498EDF74F0` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_171DC549A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_x_instructor`
--

LOCK TABLES `offering_x_instructor` WRITE;
/*!40000 ALTER TABLE `offering_x_instructor` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_x_instructor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_x_instructor_group`
--

DROP TABLE IF EXISTS `offering_x_instructor_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering_x_instructor_group` (
  `offering_id` int(11) NOT NULL,
  `instructor_group_id` int(11) NOT NULL,
  PRIMARY KEY (`offering_id`,`instructor_group_id`),
  KEY `IDX_5540AEE1FE367BE2` (`instructor_group_id`),
  KEY `IDX_5540AEE18EDF74F0` (`offering_id`),
  CONSTRAINT `FK_5540AEE18EDF74F0` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5540AEE1FE367BE2` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_x_instructor_group`
--

LOCK TABLES `offering_x_instructor_group` WRITE;
/*!40000 ALTER TABLE `offering_x_instructor_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_x_instructor_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offering_x_learner`
--

DROP TABLE IF EXISTS `offering_x_learner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offering_x_learner` (
  `offering_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`offering_id`,`user_id`),
  KEY `IDX_991D7DA38EDF74F0` (`offering_id`),
  KEY `IDX_991D7DA3A76ED395` (`user_id`),
  CONSTRAINT `FK_991D7DA38EDF74F0` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_991D7DA3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offering_x_learner`
--

LOCK TABLES `offering_x_learner` WRITE;
/*!40000 ALTER TABLE `offering_x_learner` DISABLE KEYS */;
/*!40000 ALTER TABLE `offering_x_learner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pending_user_update`
--

DROP TABLE IF EXISTS `pending_user_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_user_update` (
  `exception_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  `property` varchar(32) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`exception_id`),
  KEY `IDX_A6D3A181A76ED395` (`user_id`),
  CONSTRAINT `FK_A6D3A181A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_user_update`
--

LOCK TABLES `pending_user_update` WRITE;
/*!40000 ALTER TABLE `pending_user_update` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_user_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `short_title` varchar(10) DEFAULT NULL,
  `duration` smallint(6) NOT NULL,
  PRIMARY KEY (`program_id`),
  KEY `IDX_92ED7784C32A47EE` (`school_id`),
  CONSTRAINT `FK_92ED7784C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program`
--

LOCK TABLES `program` WRITE;
/*!40000 ALTER TABLE `program` DISABLE KEYS */;
/*!40000 ALTER TABLE `program` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_director`
--

DROP TABLE IF EXISTS `program_director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_director` (
  `program_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`program_id`,`user_id`),
  KEY `IDX_9FA52FC53EB8070A` (`program_id`),
  KEY `IDX_9FA52FC5A76ED395` (`user_id`),
  CONSTRAINT `FK_9FA52FC53EB8070A` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9FA52FC5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_director`
--

LOCK TABLES `program_director` WRITE;
/*!40000 ALTER TABLE `program_director` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year`
--

DROP TABLE IF EXISTS `program_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year` (
  `program_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_year` smallint(6) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `archived` tinyint(1) NOT NULL,
  PRIMARY KEY (`program_year_id`),
  KEY `IDX_B66426303EB8070A` (`program_id`),
  CONSTRAINT `FK_B66426303EB8070A` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year`
--

LOCK TABLES `program_year` WRITE;
/*!40000 ALTER TABLE `program_year` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_director`
--

DROP TABLE IF EXISTS `program_year_director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_director` (
  `program_year_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`program_year_id`,`user_id`),
  KEY `IDX_9212A50DCB2B0673` (`program_year_id`),
  KEY `IDX_9212A50DA76ED395` (`user_id`),
  CONSTRAINT `FK_9212A50DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `FK_9212A50DCB2B0673` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_director`
--

LOCK TABLES `program_year_director` WRITE;
/*!40000 ALTER TABLE `program_year_director` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_objective_x_mesh`
--

DROP TABLE IF EXISTS `program_year_objective_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_objective_x_mesh` (
  `program_year_objective_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`program_year_objective_id`,`mesh_descriptor_uid`),
  KEY `IDX_5FD56ABEBA83A669` (`program_year_objective_id`),
  KEY `IDX_5FD56ABECDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_5FD56ABEBA83A669` FOREIGN KEY (`program_year_objective_id`) REFERENCES `program_year_x_objective` (`program_year_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5FD56ABECDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_objective_x_mesh`
--

LOCK TABLES `program_year_objective_x_mesh` WRITE;
/*!40000 ALTER TABLE `program_year_objective_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_objective_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_objective_x_term`
--

DROP TABLE IF EXISTS `program_year_objective_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_objective_x_term` (
  `program_year_objective_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`program_year_objective_id`,`term_id`),
  KEY `IDX_1BB5B765BA83A669` (`program_year_objective_id`),
  KEY `IDX_1BB5B765E2C35FC` (`term_id`),
  CONSTRAINT `FK_1BB5B765BA83A669` FOREIGN KEY (`program_year_objective_id`) REFERENCES `program_year_x_objective` (`program_year_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1BB5B765E2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_objective_x_term`
--

LOCK TABLES `program_year_objective_x_term` WRITE;
/*!40000 ALTER TABLE `program_year_objective_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_objective_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_x_competency`
--

DROP TABLE IF EXISTS `program_year_x_competency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_x_competency` (
  `program_year_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  PRIMARY KEY (`program_year_id`,`competency_id`),
  KEY `IDX_1841AB9BFB9F58C` (`competency_id`),
  KEY `IDX_1841AB9BCB2B0673` (`program_year_id`),
  CONSTRAINT `FK_1841AB9BCB2B0673` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1841AB9BFB9F58C` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_x_competency`
--

LOCK TABLES `program_year_x_competency` WRITE;
/*!40000 ALTER TABLE `program_year_x_competency` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_x_competency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_x_objective`
--

DROP TABLE IF EXISTS `program_year_x_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_x_objective` (
  `program_year_objective_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_year_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `competency_id` int(11) DEFAULT NULL,
  `ancestor_id` int(11) DEFAULT NULL,
  `title` longtext NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`program_year_objective_id`),
  KEY `IDX_7A16FDD6CB2B0673` (`program_year_id`),
  KEY `IDX_FF29E643FB9F58C` (`competency_id`),
  KEY `IDX_FF29E643C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_7A16FDD6CB2B0673` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FF29E643C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `program_year_x_objective` (`program_year_objective_id`),
  CONSTRAINT `FK_FF29E643FB9F58C` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_x_objective`
--

LOCK TABLES `program_year_x_objective` WRITE;
/*!40000 ALTER TABLE `program_year_x_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_x_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_year_x_term`
--

DROP TABLE IF EXISTS `program_year_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_year_x_term` (
  `program_year_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`program_year_id`,`term_id`),
  KEY `IDX_BCB52AB5CB2B0673` (`program_year_id`),
  KEY `IDX_BCB52AB5E2C35FC` (`term_id`),
  CONSTRAINT `FK_BCB52AB5CB2B0673` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_BCB52AB5E2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_year_x_term`
--

LOCK TABLES `program_year_x_term` WRITE;
/*!40000 ALTER TABLE `program_year_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_year_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(240) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `subject` varchar(32) NOT NULL,
  `prepositional_object` varchar(32) DEFAULT NULL,
  `prepositional_object_table_row_id` varchar(14) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `IDX_C42F7784A76ED395` (`user_id`),
  KEY `IDX_C42F7784C32A47EE` (`school_id`),
  CONSTRAINT `FK_C42F7784A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C42F7784C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school` (
  `school_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_prefix` varchar(8) DEFAULT NULL,
  `title` varchar(60) NOT NULL,
  `ilios_administrator_email` varchar(100) NOT NULL,
  `change_alert_recipients` longtext DEFAULT NULL,
  PRIMARY KEY (`school_id`),
  UNIQUE KEY `UNIQ_F99EDABB2B36786B` (`title`),
  UNIQUE KEY `template_prefix` (`template_prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school`
--

LOCK TABLES `school` WRITE;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` VALUES
(1,'SOM','Medicine','ilios_admin@example.edu','ilios_admin@example.edu');
/*!40000 ALTER TABLE `school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_administrator`
--

DROP TABLE IF EXISTS `school_administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_administrator` (
  `school_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`school_id`,`user_id`),
  KEY `IDX_74CDAA6FC32A47EE` (`school_id`),
  KEY `IDX_74CDAA6FA76ED395` (`user_id`),
  CONSTRAINT `FK_74CDAA6FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_74CDAA6FC32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_administrator`
--

LOCK TABLES `school_administrator` WRITE;
/*!40000 ALTER TABLE `school_administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_config`
--

DROP TABLE IF EXISTS `school_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_conf_uniq` (`school_id`,`name`),
  KEY `IDX_29362CB0C32A47EE` (`school_id`),
  CONSTRAINT `FK_29362CB0C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_config`
--

LOCK TABLES `school_config` WRITE;
/*!40000 ALTER TABLE `school_config` DISABLE KEYS */;
INSERT INTO `school_config` VALUES
(1,2,'showSessionSupplemental','true'),
(2,2,'showSessionSpecialAttireRequired','true'),
(3,2,'showSessionSpecialEquipmentRequired','true'),
(4,2,'showSessionAttendanceRequired','false'),
(5,5,'showSessionSupplemental','true'),
(6,5,'showSessionSpecialAttireRequired','true'),
(7,5,'showSessionSpecialEquipmentRequired','true'),
(8,5,'showSessionAttendanceRequired','false'),
(9,6,'showSessionSupplemental','true'),
(10,6,'showSessionSpecialAttireRequired','true'),
(11,6,'showSessionSpecialEquipmentRequired','true'),
(12,6,'showSessionAttendanceRequired','false'),
(13,1,'showSessionSupplemental','true'),
(14,1,'showSessionSpecialAttireRequired','true'),
(15,1,'showSessionSpecialEquipmentRequired','true'),
(16,1,'showSessionAttendanceRequired','false'),
(17,4,'showSessionSupplemental','true'),
(18,4,'showSessionSpecialAttireRequired','true'),
(19,4,'showSessionSpecialEquipmentRequired','true'),
(20,4,'showSessionAttendanceRequired','false'),
(21,3,'showSessionSupplemental','true'),
(22,3,'showSessionSpecialAttireRequired','true'),
(23,3,'showSessionSpecialEquipmentRequired','true'),
(24,3,'showSessionAttendanceRequired','true'),
(25,7,'showSessionSpecialAttireRequired','false'),
(26,7,'showSessionAttendanceRequired','true'),
(27,7,'showSessionSupplemental','false'),
(28,7,'showSessionSpecialEquipmentRequired','false'),
(29,3,'allowMultipleCourseObjectiveParents','true');
/*!40000 ALTER TABLE `school_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_director`
--

DROP TABLE IF EXISTS `school_director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_director` (
  `school_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`school_id`,`user_id`),
  KEY `IDX_E0D6E7B4C32A47EE` (`school_id`),
  KEY `IDX_E0D6E7B4A76ED395` (`user_id`),
  CONSTRAINT `FK_E0D6E7B4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E0D6E7B4C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_director`
--

LOCK TABLES `school_director` WRITE;
/*!40000 ALTER TABLE `school_director` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_type_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `attire_required` tinyint(1) DEFAULT NULL,
  `equipment_required` tinyint(1) DEFAULT NULL,
  `supplemental` tinyint(1) DEFAULT NULL,
  `published_as_tbd` tinyint(1) NOT NULL,
  `last_updated_on` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `attendance_required` tinyint(1) DEFAULT NULL,
  `instructionalNotes` longtext DEFAULT NULL,
  `postrequisite_id` int(11) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `session_type_id_k` (`session_type_id`),
  KEY `course_id_k` (`course_id`),
  KEY `session_course_type_title_k` (`session_id`,`course_id`,`session_type_id`,`title`),
  KEY `IDX_D044D5D42710C13B` (`postrequisite_id`),
  CONSTRAINT `FK_D044D5D42710C13B` FOREIGN KEY (`postrequisite_id`) REFERENCES `session` (`session_id`) ON DELETE SET NULL,
  CONSTRAINT `FK_D044D5D4591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D044D5D4D7940EC9` FOREIGN KEY (`session_type_id`) REFERENCES `session_type` (`session_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_administrator`
--

DROP TABLE IF EXISTS `session_administrator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_administrator` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`session_id`,`user_id`),
  KEY `IDX_C2AE285A613FECDF` (`session_id`),
  KEY `IDX_C2AE285AA76ED395` (`user_id`),
  CONSTRAINT `FK_C2AE285A613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C2AE285AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_administrator`
--

LOCK TABLES `session_administrator` WRITE;
/*!40000 ALTER TABLE `session_administrator` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_administrator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_learning_material`
--

DROP TABLE IF EXISTS `session_learning_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_learning_material` (
  `session_learning_material_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `learning_material_id` int(11) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `required` tinyint(1) NOT NULL,
  `notes_are_public` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`session_learning_material_id`),
  KEY `session_lm_k` (`session_id`,`learning_material_id`),
  KEY `learning_material_id_k` (`learning_material_id`),
  KEY `IDX_9BE2AF8D613FECDF` (`session_id`),
  CONSTRAINT `FK_9BE2AF8D613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9BE2AF8DC1D99609` FOREIGN KEY (`learning_material_id`) REFERENCES `learning_material` (`learning_material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_learning_material`
--

LOCK TABLES `session_learning_material` WRITE;
/*!40000 ALTER TABLE `session_learning_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_learning_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_learning_material_x_mesh`
--

DROP TABLE IF EXISTS `session_learning_material_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_learning_material_x_mesh` (
  `session_learning_material_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`session_learning_material_id`,`mesh_descriptor_uid`),
  KEY `IDX_EC36AECFCDB3C93B` (`mesh_descriptor_uid`),
  KEY `IDX_EC36AECFE8376E0A` (`session_learning_material_id`),
  CONSTRAINT `FK_EC36AECFCDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE,
  CONSTRAINT `FK_EC36AECFE8376E0A` FOREIGN KEY (`session_learning_material_id`) REFERENCES `session_learning_material` (`session_learning_material_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_learning_material_x_mesh`
--

LOCK TABLES `session_learning_material_x_mesh` WRITE;
/*!40000 ALTER TABLE `session_learning_material_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_learning_material_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_objective_x_course_objective`
--

DROP TABLE IF EXISTS `session_objective_x_course_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_objective_x_course_objective` (
  `session_objective_id` int(11) NOT NULL,
  `course_objective_id` int(11) NOT NULL,
  PRIMARY KEY (`session_objective_id`,`course_objective_id`),
  KEY `IDX_5EB8C49DBDD5F4B2` (`session_objective_id`),
  KEY `IDX_5EB8C49DF28231CE` (`course_objective_id`),
  CONSTRAINT `FK_5EB8C49DBDD5F4B2` FOREIGN KEY (`session_objective_id`) REFERENCES `session_x_objective` (`session_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5EB8C49DF28231CE` FOREIGN KEY (`course_objective_id`) REFERENCES `course_x_objective` (`course_objective_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_objective_x_course_objective`
--

LOCK TABLES `session_objective_x_course_objective` WRITE;
/*!40000 ALTER TABLE `session_objective_x_course_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_objective_x_course_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_objective_x_mesh`
--

DROP TABLE IF EXISTS `session_objective_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_objective_x_mesh` (
  `session_objective_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`session_objective_id`,`mesh_descriptor_uid`),
  KEY `IDX_B33DC189BDD5F4B2` (`session_objective_id`),
  KEY `IDX_B33DC189CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_B33DC189BDD5F4B2` FOREIGN KEY (`session_objective_id`) REFERENCES `session_x_objective` (`session_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B33DC189CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_objective_x_mesh`
--

LOCK TABLES `session_objective_x_mesh` WRITE;
/*!40000 ALTER TABLE `session_objective_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_objective_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_objective_x_term`
--

DROP TABLE IF EXISTS `session_objective_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_objective_x_term` (
  `session_objective_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`session_objective_id`,`term_id`),
  KEY `IDX_F75D1C52BDD5F4B2` (`session_objective_id`),
  KEY `IDX_F75D1C52E2C35FC` (`term_id`),
  CONSTRAINT `FK_F75D1C52BDD5F4B2` FOREIGN KEY (`session_objective_id`) REFERENCES `session_x_objective` (`session_objective_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F75D1C52E2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_objective_x_term`
--

LOCK TABLES `session_objective_x_term` WRITE;
/*!40000 ALTER TABLE `session_objective_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_objective_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_student_advisor`
--

DROP TABLE IF EXISTS `session_student_advisor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_student_advisor` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`session_id`,`user_id`),
  KEY `IDX_CBB93279613FECDF` (`session_id`),
  KEY `IDX_CBB93279A76ED395` (`user_id`),
  CONSTRAINT `FK_CBB93279613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CBB93279A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_student_advisor`
--

LOCK TABLES `session_student_advisor` WRITE;
/*!40000 ALTER TABLE `session_student_advisor` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_student_advisor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_type`
--

DROP TABLE IF EXISTS `session_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_type` (
  `session_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `assessment` tinyint(1) NOT NULL,
  `assessment_option_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `calendar_color` varchar(7) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`session_type_id`),
  KEY `assessment_option_fkey` (`assessment_option_id`),
  KEY `school_id` (`school_id`),
  CONSTRAINT `FK_4AAF570375B5EE83` FOREIGN KEY (`assessment_option_id`) REFERENCES `assessment_option` (`assessment_option_id`),
  CONSTRAINT `FK_4AAF5703C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_type`
--

LOCK TABLES `session_type` WRITE;
/*!40000 ALTER TABLE `session_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_type_x_aamc_method`
--

DROP TABLE IF EXISTS `session_type_x_aamc_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_type_x_aamc_method` (
  `session_type_id` int(11) NOT NULL,
  `method_id` varchar(10) NOT NULL,
  PRIMARY KEY (`session_type_id`,`method_id`),
  KEY `IDX_5E10F74819883967` (`method_id`),
  KEY `IDX_5E10F748D7940EC9` (`session_type_id`),
  CONSTRAINT `FK_5E10F74819883967` FOREIGN KEY (`method_id`) REFERENCES `aamc_method` (`method_id`),
  CONSTRAINT `FK_5E10F748D7940EC9` FOREIGN KEY (`session_type_id`) REFERENCES `session_type` (`session_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_type_x_aamc_method`
--

LOCK TABLES `session_type_x_aamc_method` WRITE;
/*!40000 ALTER TABLE `session_type_x_aamc_method` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_type_x_aamc_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_x_mesh`
--

DROP TABLE IF EXISTS `session_x_mesh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_x_mesh` (
  `session_id` int(11) NOT NULL,
  `mesh_descriptor_uid` varchar(12) NOT NULL,
  PRIMARY KEY (`session_id`,`mesh_descriptor_uid`),
  KEY `IDX_43B09906613FECDF` (`session_id`),
  KEY `IDX_43B09906CDB3C93B` (`mesh_descriptor_uid`),
  CONSTRAINT `FK_43B09906613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_43B09906CDB3C93B` FOREIGN KEY (`mesh_descriptor_uid`) REFERENCES `mesh_descriptor` (`mesh_descriptor_uid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_x_mesh`
--

LOCK TABLES `session_x_mesh` WRITE;
/*!40000 ALTER TABLE `session_x_mesh` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_x_mesh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_x_objective`
--

DROP TABLE IF EXISTS `session_x_objective`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_x_objective` (
  `session_objective_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `ancestor_id` int(11) DEFAULT NULL,
  `title` longtext NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`session_objective_id`),
  KEY `IDX_FA74B40B613FECDF` (`session_id`),
  KEY `IDX_C4BF2447C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_C4BF2447C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `session_x_objective` (`session_objective_id`),
  CONSTRAINT `FK_FA74B40B613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_x_objective`
--

LOCK TABLES `session_x_objective` WRITE;
/*!40000 ALTER TABLE `session_x_objective` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_x_objective` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_x_term`
--

DROP TABLE IF EXISTS `session_x_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_x_term` (
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`session_id`,`term_id`),
  KEY `IDX_7D044DD613FECDF` (`session_id`),
  KEY `IDX_7D044DDE2C35FC` (`term_id`),
  CONSTRAINT `FK_7D044DD613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_7D044DDE2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_x_term`
--

LOCK TABLES `session_x_term` WRITE;
/*!40000 ALTER TABLE `session_x_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_x_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `term`
--

DROP TABLE IF EXISTS `term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `term` (
  `term_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_term_id` int(11) DEFAULT NULL,
  `vocabulary_id` int(11) NOT NULL,
  `description` longtext DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`term_id`),
  UNIQUE KEY `unique_term_title` (`vocabulary_id`,`title`,`parent_term_id`),
  KEY `IDX_A50FE78D7C6441BA` (`parent_term_id`),
  KEY `IDX_A50FE78DAD0E05F6` (`vocabulary_id`),
  CONSTRAINT `FK_A50FE78D7C6441BA` FOREIGN KEY (`parent_term_id`) REFERENCES `term` (`term_id`),
  CONSTRAINT `FK_A50FE78DAD0E05F6` FOREIGN KEY (`vocabulary_id`) REFERENCES `vocabulary` (`vocabulary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `term`
--

LOCK TABLES `term` WRITE;
/*!40000 ALTER TABLE `term` DISABLE KEYS */;
/*!40000 ALTER TABLE `term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `term_x_aamc_resource_type`
--

DROP TABLE IF EXISTS `term_x_aamc_resource_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `term_x_aamc_resource_type` (
  `term_id` int(11) NOT NULL,
  `resource_type_id` varchar(21) NOT NULL,
  PRIMARY KEY (`term_id`,`resource_type_id`),
  KEY `IDX_F4C4B9D6E2C35FC` (`term_id`),
  KEY `IDX_F4C4B9D698EC6B7B` (`resource_type_id`),
  CONSTRAINT `FK_F4C4B9D698EC6B7B` FOREIGN KEY (`resource_type_id`) REFERENCES `aamc_resource_type` (`resource_type_id`),
  CONSTRAINT `FK_F4C4B9D6E2C35FC` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `term_x_aamc_resource_type`
--

LOCK TABLES `term_x_aamc_resource_type` WRITE;
/*!40000 ALTER TABLE `term_x_aamc_resource_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `term_x_aamc_resource_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `primary_cohort_id` int(11) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `added_via_ilios` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `uc_uid` varchar(16) DEFAULT NULL,
  `other_id` varchar(16) DEFAULT NULL,
  `examined` tinyint(1) NOT NULL,
  `user_sync_ignore` tinyint(1) NOT NULL,
  `ics_feed_key` varchar(64) NOT NULL,
  `root` tinyint(1) NOT NULL,
  `preferred_email` varchar(100) DEFAULT NULL,
  `display_name` varchar(200) DEFAULT NULL,
  `pronouns` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UNIQ_8D93D649AAB338A6` (`ics_feed_key`),
  KEY `IDX_8D93D649FF038174` (`primary_cohort_id`),
  KEY `fkey_user_school` (`school_id`),
  CONSTRAINT `FK_8D93D649C32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`),
  CONSTRAINT `FK_8D93D649FF038174` FOREIGN KEY (`primary_cohort_id`) REFERENCES `cohort` (`cohort_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19106 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(16,1,NULL,'Gutierrez','Nicholas','Ann','(415) 555-5785','nicholas.ann.gutierrez@example.edu',1,1,'xx1000016',NULL,1,0,'b58aceae8fc3ed7008a0e8dd6c81fd791fd7032dfac750731193b4282a41bb8e',1,NULL,'Nicholas Ann Gutierrez',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_session_material_status`
--

DROP TABLE IF EXISTS `user_session_material_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_session_material_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_learning_material_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6CC10903A76ED395` (`user_id`),
  KEY `IDX_6CC10903E8376E0A` (`session_learning_material_id`),
  CONSTRAINT `FK_6CC10903A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6CC10903E8376E0A` FOREIGN KEY (`session_learning_material_id`) REFERENCES `session_learning_material` (`session_learning_material_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_session_material_status`
--

LOCK TABLES `user_session_material_status` WRITE;
/*!40000 ALTER TABLE `user_session_material_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_session_material_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_x_cohort`
--

DROP TABLE IF EXISTS `user_x_cohort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_x_cohort` (
  `user_id` int(11) NOT NULL,
  `cohort_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`cohort_id`),
  KEY `IDX_4DFD48DA35983C93` (`cohort_id`),
  KEY `IDX_4DFD48DAA76ED395` (`user_id`),
  CONSTRAINT `FK_4DFD48DA35983C93` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`),
  CONSTRAINT `FK_4DFD48DAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_x_cohort`
--

LOCK TABLES `user_x_cohort` WRITE;
/*!40000 ALTER TABLE `user_x_cohort` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_x_cohort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_x_user_role`
--

DROP TABLE IF EXISTS `user_x_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_x_user_role` (
  `user_id` int(11) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`user_role_id`),
  KEY `IDX_583C407A76ED395` (`user_id`),
  KEY `IDX_583C4078E0E3CA6` (`user_role_id`),
  CONSTRAINT `FK_583C4078E0E3CA6` FOREIGN KEY (`user_role_id`) REFERENCES `user_role` (`user_role_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_583C407A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_x_user_role`
--

LOCK TABLES `user_x_user_role` WRITE;
/*!40000 ALTER TABLE `user_x_user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_x_user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vocabulary`
--

DROP TABLE IF EXISTS `vocabulary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vocabulary` (
  `vocabulary_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`vocabulary_id`),
  UNIQUE KEY `unique_vocabulary_title` (`school_id`,`title`),
  KEY `IDX_9099C97BC32A47EE` (`school_id`),
  CONSTRAINT `FK_9099C97BC32A47EE` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vocabulary`
--

LOCK TABLES `vocabulary` WRITE;
/*!40000 ALTER TABLE `vocabulary` DISABLE KEYS */;
/*!40000 ALTER TABLE `vocabulary` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-12  6:21:27
