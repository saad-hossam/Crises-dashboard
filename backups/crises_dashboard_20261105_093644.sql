/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: crises_dashboard
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

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
-- Current Database: `crises_dashboard`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `crises_dashboard` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `crises_dashboard`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_batches`
--

DROP TABLE IF EXISTS `import_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `file_extension` varchar(10) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `file_hash` varchar(64) NOT NULL,
  `target_table` varchar(255) NOT NULL,
  `selected_sheet` varchar(255) DEFAULT NULL,
  `organizational_unit` varchar(255) DEFAULT NULL,
  `total_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `imported_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `failed_rows` int(10) unsigned NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `import_batches_file_hash_unique` (`file_hash`),
  KEY `import_batches_file_hash_index` (`file_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_batches`
--

LOCK TABLES `import_batches` WRITE;
/*!40000 ALTER TABLE `import_batches` DISABLE KEYS */;
INSERT INTO `import_batches` VALUES
(1,'industrial-register.xlsx','industrial-register.xlsx','storage/app/imports/industrial-register.xlsx','xlsx',137143,'9ed33fc760baadabfc0f071183535fe6a6eea2997ce3cfd4922e914198588305','industrial_facilities','البيانات',NULL,1,1,0,'completed',NULL,'2026-11-05 05:11:05','2026-11-05 05:11:06','2026-11-05 05:11:05','2026-11-05 05:11:06');
/*!40000 ALTER TABLE `import_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `industrial_facilities`
--

DROP TABLE IF EXISTS `industrial_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `industrial_facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_organization` varchar(255) DEFAULT NULL,
  `location_scope` varchar(255) DEFAULT NULL,
  `governorate` varchar(255) DEFAULT NULL,
  `facility_number` varchar(255) DEFAULT NULL,
  `industrial_sector` varchar(255) DEFAULT NULL,
  `facility_name` varchar(255) DEFAULT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `investor_code` varchar(255) DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `industrial_register_number` varchar(255) DEFAULT NULL,
  `register_issue_date` varchar(255) DEFAULT NULL,
  `register_expiry_date` varchar(255) DEFAULT NULL,
  `main_industrial_activity` text DEFAULT NULL,
  `main_activity_code` varchar(255) DEFAULT NULL,
  `sub_industrial_activity` text DEFAULT NULL,
  `sub_activity_code` varchar(255) DEFAULT NULL,
  `commercial_register_capital` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `industrial_region` varchar(255) DEFAULT NULL,
  `industrial_zone_status` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_job_title` varchar(255) DEFAULT NULL,
  `official_phone` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `operating_status` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `annual_production_quantity` varchar(255) DEFAULT NULL,
  `production_unit` varchar(255) DEFAULT NULL,
  `raw_materials` text DEFAULT NULL,
  `total_employees` varchar(255) DEFAULT NULL,
  `power_capacity_kw` varchar(255) DEFAULT NULL,
  `annual_electricity_consumption` varchar(255) DEFAULT NULL,
  `annual_water_consumption` varchar(255) DEFAULT NULL,
  `main_fuel_type` varchar(255) DEFAULT NULL,
  `annual_fuel_consumption` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `license_issue_date` varchar(255) DEFAULT NULL,
  `area_sqm` varchar(255) DEFAULT NULL,
  `license_type` varchar(255) DEFAULT NULL,
  `longitude_x` decimal(11,7) DEFAULT NULL,
  `latitude_y` decimal(11,7) DEFAULT NULL,
  `organizational_unit` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `industrial_facilities`
--

LOCK TABLES `industrial_facilities` WRITE;
/*!40000 ALTER TABLE `industrial_facilities` DISABLE KEYS */;
INSERT INTO `industrial_facilities` VALUES
(1,'الهيئة العربية للتصنيع','محافظة القاهرة','القاهرة','معفي بموجب مادة رقم (9) بالقانون رقم 150 لسنة 1976','معفي','الشركة العربية للطاقة المتجددة','معفي','معفي','559-492-450','معفي','معفي','معفي','معفي','معفي','معفي','معفي','معفي','القاهرة','معفي','معفي','الكيلو 17 طريق القاهرة السويس','مهندسه /ماريان ممدوح كمال           technical_office.Areco@aoi.org.eg                  هاتف: 01096377129','مدير عام المكتب الفني .','28476561','لا يوجد','chairman_areco@aoi.org.eg','معفي','معفي','معفي','معفي','معفي','معفي','108','معفي','معفي','معفي','معفي','معفي','معفي','معفي','معفي','معفي',30.0830000,31.4050000,NULL,'2026-11-05 05:11:06','2026-11-05 05:11:06');
/*!40000 ALTER TABLE `industrial_facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_11_04_120652_create_units_table',1),
(5,'2026_11_04_132810_create_permissions_table',1),
(6,'2026_11_04_132811_create_roles_table',1),
(7,'2026_11_04_132812_create_role_user_table',1),
(8,'2026_11_04_132813_create_permission_role_table',1),
(9,'2026_11_04_173406_create_industrial_facilities_table',1),
(10,'2026_11_04_182107_create_import_batches_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES
(1,2),
(1,3),
(1,4),
(1,5),
(2,2),
(3,2),
(4,2),
(5,2),
(6,2),
(7,2),
(8,2),
(9,2),
(10,2),
(11,2),
(12,2),
(13,2),
(14,2),
(15,2),
(16,2),
(16,3),
(16,4),
(16,5),
(17,2),
(17,4),
(18,2),
(18,3),
(18,4),
(19,2),
(19,3),
(20,2),
(21,2),
(21,3),
(21,4),
(21,5),
(22,2),
(22,3),
(22,4),
(23,2),
(23,3),
(24,2),
(25,2),
(25,3),
(25,5),
(26,2),
(26,3),
(29,2),
(29,3),
(29,4),
(29,5),
(30,2),
(31,2),
(32,2);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  KEY `permissions_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'dashboard.view','dashboard','Access the admin dashboard','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(2,'users.view','users','View users','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(3,'users.create','users','Create users','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(4,'users.edit','users','Edit users','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(5,'users.delete','users','Delete users','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(6,'users.assign_roles','users','Assign roles to users','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(7,'roles.view','roles','View roles','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(8,'roles.create','roles','Create roles','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(9,'roles.edit','roles','Edit roles','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(10,'roles.delete','roles','Delete roles','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(11,'roles.assign_permissions','roles','Assign permissions to roles','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(12,'permissions.view','permissions','View permissions','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(13,'permissions.create','permissions','Create permissions','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(14,'permissions.edit','permissions','Edit permissions','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(15,'permissions.delete','permissions','Delete permissions','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(16,'imports.view','imports','View import batches','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(17,'imports.upload','imports','Upload Excel files','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(18,'imports.preview','imports','Preview Excel content','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(19,'imports.execute','imports','Run an import','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(20,'imports.delete','imports','Delete import batches','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(21,'data.view','data','View data records','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(22,'data.create','data','Create data records','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(23,'data.edit','data','Edit data records','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(24,'data.delete','data','Delete data records','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(25,'reports.view','reports','View reports','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(26,'reports.export','reports','Export reports','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(27,'settings.manage','settings','Manage system settings','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(28,'activity_logs.view','activity_logs','View activity logs','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(29,'units.view','units','View units','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(30,'units.create','units','Create units','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(31,'units.edit','units','Edit units','2026-11-05 05:02:39','2026-11-05 05:02:39'),
(32,'units.delete','units','Delete units','2026-11-05 05:02:39','2026-11-05 05:02:39');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES
(1,1);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','Full system access',1,'2026-11-05 05:02:39','2026-11-05 05:02:39'),
(2,'Admin','Administrator',0,'2026-11-05 05:02:39','2026-11-05 05:02:39'),
(3,'Manager','Manager',0,'2026-11-05 05:02:39','2026-11-05 05:02:39'),
(4,'Data Entry','Data entry operator',0,'2026-11-05 05:02:39','2026-11-05 05:02:39'),
(5,'Viewer','Read-only viewer',0,'2026-11-05 05:02:39','2026-11-05 05:02:39');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('tqXGmeAmYqPLwIrI01eaWCjQ7aw1buXfqnxdU18l',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI0Wnd0VUFhdXFtOWhPMWJ2VTJ5ZTBTclBBblhDc1o2ZGdJeUdDM2NLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9pbXBvcnRzXC9oaXN0b3J5P3NlYXJjaD0mc3RhdHVzPSIsInJvdXRlIjoiaW1wb3J0cy5oaXN0b3J5In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1793862955);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@gmail.com',NULL,NULL,'active','2026-11-05 05:02:40',NULL,'$2y$12$eAuaXIe4lnwyTWwLBn4SyOuPO7G4sH7R.q22CCdJUkN04NDhyvEeK',NULL,'2026-11-05 05:02:40','2026-11-05 05:02:40');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'crises_dashboard'
--

--
-- Dumping routines for database 'crises_dashboard'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-05  9:36:44
