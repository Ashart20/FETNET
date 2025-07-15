-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: fetnet
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `activity_tag_id` bigint unsigned DEFAULT NULL,
  `duration` int NOT NULL,
  `practicum_sks` tinyint unsigned DEFAULT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_subject_id_foreign` (`subject_id`),
  KEY `activities_activity_tag_id_foreign` (`activity_tag_id`),
  KEY `activities_prodi_id_foreign` (`prodi_id`),
  CONSTRAINT `activities_activity_tag_id_foreign` FOREIGN KEY (`activity_tag_id`) REFERENCES `activity_tags` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activities_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activities_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_preferred_room`
--

DROP TABLE IF EXISTS `activity_preferred_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_preferred_room` (
  `activity_id` bigint unsigned NOT NULL,
  `master_ruangan_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`activity_id`,`master_ruangan_id`),
  KEY `activity_preferred_room_master_ruangan_id_foreign` (`master_ruangan_id`),
  CONSTRAINT `activity_preferred_room_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_preferred_room_master_ruangan_id_foreign` FOREIGN KEY (`master_ruangan_id`) REFERENCES `master_ruangans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_preferred_room`
--

LOCK TABLES `activity_preferred_room` WRITE;
/*!40000 ALTER TABLE `activity_preferred_room` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_preferred_room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_student_group`
--

DROP TABLE IF EXISTS `activity_student_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_student_group` (
  `activity_id` bigint unsigned NOT NULL,
  `student_group_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`activity_id`,`student_group_id`),
  KEY `activity_student_group_student_group_id_foreign` (`student_group_id`),
  CONSTRAINT `activity_student_group_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_student_group_student_group_id_foreign` FOREIGN KEY (`student_group_id`) REFERENCES `student_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_student_group`
--

LOCK TABLES `activity_student_group` WRITE;
/*!40000 ALTER TABLE `activity_student_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_student_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_tags`
--

DROP TABLE IF EXISTS `activity_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_tags_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_tags`
--

LOCK TABLES `activity_tags` WRITE;
/*!40000 ALTER TABLE `activity_tags` DISABLE KEYS */;
INSERT INTO `activity_tags` VALUES (1,'KELAS_TEORI','2025-07-15 14:37:01','2025-07-15 14:37:01'),(2,'PILIHAN','2025-07-15 14:37:01','2025-07-15 14:37:01'),(3,'PRAKTIKUM','2025-07-15 14:37:01','2025-07-15 14:37:01'),(4,'SEPARATOR','2025-07-15 14:37:01','2025-07-15 14:37:01');
/*!40000 ALTER TABLE `activity_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_teacher`
--

DROP TABLE IF EXISTS `activity_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_teacher` (
  `activity_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`activity_id`,`teacher_id`),
  KEY `activity_teacher_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `activity_teacher_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_teacher`
--

LOCK TABLES `activity_teacher` WRITE;
/*!40000 ALTER TABLE `activity_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buildings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buildings_name_unique` (`name`),
  UNIQUE KEY `buildings_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buildings`
--

LOCK TABLES `buildings` WRITE;
/*!40000 ALTER TABLE `buildings` DISABLE KEYS */;
/*!40000 ALTER TABLE `buildings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('fetnet_cache_0ade7c2cf97f75d009975f4d720d1fa6c19f4897','i:1;',1752592583),('fetnet_cache_0ade7c2cf97f75d009975f4d720d1fa6c19f4897:timer','i:1752592583;',1752592583),('fetnet_cache_902ba3cda1883801594b6e1b452790cc53948fda','i:1;',1752592407),('fetnet_cache_902ba3cda1883801594b6e1b452790cc53948fda:timer','i:1752592407;',1752592407),('fetnet_cache_91032ad7bbcb6cf72875e8e8207dcfba80173f7c','i:2;',1752593264),('fetnet_cache_91032ad7bbcb6cf72875e8e8207dcfba80173f7c:timer','i:1752593264;',1752593264),('fetnet_cache_deewahyu@upi.edu|114.122.101.170','i:2;',1752591079),('fetnet_cache_deewahyu@upi.edu|114.122.101.170:timer','i:1752591079;',1752591079),('fetnet_cache_fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f','i:1;',1752592343),('fetnet_cache_fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f:timer','i:1752592343;',1752592343),('fetnet_cache_te@upi.edu|114.122.101.170','i:1;',1752591091),('fetnet_cache_te@upi.edu|114.122.101.170:timer','i:1752591091;',1752591091);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `clusters`
--

DROP TABLE IF EXISTS `clusters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clusters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clusters_name_unique` (`name`),
  UNIQUE KEY `clusters_code_unique` (`code`),
  KEY `clusters_user_id_foreign` (`user_id`),
  CONSTRAINT `clusters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clusters`
--

LOCK TABLES `clusters` WRITE;
/*!40000 ALTER TABLE `clusters` DISABLE KEYS */;
INSERT INTO `clusters` VALUES (1,2,'DPTE','DPTE','2025-07-15 14:38:34','2025-07-15 14:38:34'),(2,2,'DPTM','DPTM','2025-07-15 14:38:42','2025-07-15 14:38:42'),(3,2,'DPTS','DPTS','2025-07-15 14:38:49','2025-07-15 14:38:49'),(4,2,'DPTA','DPTA','2025-07-15 14:38:55','2025-07-15 14:38:55'),(5,2,'TPAG','TPAG','2025-07-15 15:19:08','2025-07-15 15:23:53');
/*!40000 ALTER TABLE `clusters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `days`
--

DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `days_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `days`
--

LOCK TABLES `days` WRITE;
/*!40000 ALTER TABLE `days` DISABLE KEYS */;
INSERT INTO `days` VALUES (5,'Jumat'),(4,'Kamis'),(3,'Rabu'),(2,'Selasa'),(1,'Senin');
/*!40000 ALTER TABLE `days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `fet_file`
--

DROP TABLE IF EXISTS `fet_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fet_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fet_file`
--

LOCK TABLES `fet_file` WRITE;
/*!40000 ALTER TABLE `fet_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `fet_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `master_ruangans`
--

DROP TABLE IF EXISTS `master_ruangans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_ruangans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_ruangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_ruangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `building_id` bigint unsigned DEFAULT NULL,
  `lantai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `tipe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'KELAS_TEORI',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_ruangans_kode_ruangan_unique` (`kode_ruangan`),
  UNIQUE KEY `master_ruangans_nama_ruangan_building_id_unique` (`nama_ruangan`,`building_id`),
  KEY `master_ruangans_building_id_foreign` (`building_id`),
  KEY `master_ruangans_user_id_foreign` (`user_id`),
  CONSTRAINT `master_ruangans_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `master_ruangans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_ruangans`
--

LOCK TABLES `master_ruangans` WRITE;
/*!40000 ALTER TABLE `master_ruangans` DISABLE KEYS */;
/*!40000 ALTER TABLE `master_ruangans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_07_01_164425_create_permission_tables',1),(5,'2025_07_01_164639_create_prodis_table',1),(6,'2025_07_01_164640_create_days_table',1),(7,'2025_07_01_164645_create_time_slots_table',1),(8,'2025_07_01_164655_create_activity_tags_table',1),(9,'2025_07_01_164665_create_buildings_table',1),(10,'2025_07_01_164675_create_teachers_table',1),(11,'2025_07_01_164685_create_subjects_table',1),(12,'2025_07_01_164695_create_student_groups_table',1),(13,'2025_07_01_164755_create_master_ruangans_table',1),(14,'2025_07_01_181729_create_student_group_time_constraints_table',1),(15,'2025_07_02_165102_add_prodi_id_to_users_table',1),(16,'2025_07_02_165132_add_student_group_id_to_users_table',1),(17,'2025_07_02_165145_create_activities_table',1),(18,'2025_07_02_165165_create_activity_teacher_table',1),(19,'2025_07_03_051916_create_room_time_constraints_table',1),(20,'2025_07_03_072913_create_teacher_time_constraints_table',1),(21,'2025_07_03_134617_create_activity_preferred_room_table',1),(22,'2025_07_03_175827_create_schedules_table',1),(23,'2025_07_04_075539_create_schedule_teacher_table',1),(24,'2025_07_06_032018_add_tipe_to_master_ruangans_table',1),(25,'2025_07_08_094908_create_clusters_table',1),(26,'2025_07_08_100001_add_cluster_id_to_prodis_table',1),(27,'2025_07_08_103929_add_fakultas_id_to_clusters_table',1),(28,'2025_07_08_160646_create_prodi_teacher_pivot_table',1),(29,'2025_07_10_152856_remove_student_group_id_from_activities_table',1),(30,'2025_07_10_152857_create_activity_student_group_table',1),(31,'2025_07_13_173646_add_cluster_id_to_users_table',1),(32,'2025_07_14_185004_add_practicum_sks_to_activities_table',1),(33,'2025_07_15_103442_fet_file_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',2),(5,'App\\Models\\User',2),(4,'App\\Models\\User',3),(4,'App\\Models\\User',4),(4,'App\\Models\\User',5),(4,'App\\Models\\User',6),(2,'App\\Models\\User',7),(2,'App\\Models\\User',8),(2,'App\\Models\\User',9),(2,'App\\Models\\User',10),(2,'App\\Models\\User',11),(2,'App\\Models\\User',12),(2,'App\\Models\\User',13),(2,'App\\Models\\User',14),(2,'App\\Models\\User',15),(2,'App\\Models\\User',16),(4,'App\\Models\\User',17),(2,'App\\Models\\User',18),(2,'App\\Models\\User',19),(2,'App\\Models\\User',20),(2,'App\\Models\\User',21);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodi_teacher`
--

DROP TABLE IF EXISTS `prodi_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodi_teacher` (
  `prodi_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`prodi_id`,`teacher_id`),
  KEY `prodi_teacher_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `prodi_teacher_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prodi_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodi_teacher`
--

LOCK TABLES `prodi_teacher` WRITE;
/*!40000 ALTER TABLE `prodi_teacher` DISABLE KEYS */;
INSERT INTO `prodi_teacher` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(3,7),(3,8),(3,9),(3,10),(3,11),(3,12),(3,13),(3,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(13,24),(13,25),(13,26),(13,27),(13,28),(13,29),(13,30),(13,31),(13,32),(13,33),(13,34),(13,35),(13,36),(13,37),(13,38),(13,39),(13,40);
/*!40000 ALTER TABLE `prodi_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodis`
--

DROP TABLE IF EXISTS `prodis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_prodi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cluster_id` bigint unsigned DEFAULT NULL,
  `abbreviation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prodis_kode_unique` (`kode`),
  KEY `prodis_cluster_id_foreign` (`cluster_id`),
  CONSTRAINT `prodis_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodis`
--

LOCK TABLES `prodis` WRITE;
/*!40000 ALTER TABLE `prodis` DISABLE KEYS */;
INSERT INTO `prodis` VALUES (1,'Teknik Elektro','E505',1,'TE','2025-07-15 14:43:35','2025-07-15 14:43:35'),(2,'Pendidikan Teknik Elektro','E045',1,'PTE','2025-07-15 14:45:17','2025-07-15 14:45:17'),(3,'Pendidikan Teknik Otomasi Industri dan Robotika','E115',1,'PTOIR','2025-07-15 14:46:10','2025-07-15 14:46:10'),(4,'Teknik Energi Terbarukan','E5851',1,'TET','2025-07-15 14:47:35','2025-07-15 14:52:25'),(5,'Teknik Kimia','E585',2,'TK','2025-07-15 14:53:10','2025-07-15 14:53:10'),(6,'Pendidikan Teknik Arsitektur','E015',4,'PTA','2025-07-15 14:55:02','2025-07-15 14:55:02'),(7,'Arsitektur-S2','E516',4,'MARS','2025-07-15 14:55:59','2025-07-15 14:55:59'),(8,'Arsitektur-S1','E515',4,'ARS','2025-07-15 14:56:34','2025-07-15 14:56:34'),(9,'Pendidikan Teknik Mesin','E055',2,'PTM','2025-07-15 14:57:26','2025-07-15 14:57:26'),(10,'Teknik Logistik','E555',2,'TL','2025-07-15 14:58:21','2025-07-15 14:58:21'),(11,'Pendidikan Teknologi Agroindustri','E095',5,'PTAG','2025-07-15 15:20:41','2025-07-15 15:20:41'),(12,'Teknologi Pangan','E565',5,'TP','2025-07-15 15:21:50','2025-07-15 15:21:50'),(13,'Pendidikan Teknik Bangunan','E025',3,'PTB','2025-07-15 15:22:38','2025-07-15 15:22:38'),(14,'Pendidikan Teknik Otomotif','E105',2,'PTO','2025-07-15 15:23:31','2025-07-15 15:23:31');
/*!40000 ALTER TABLE `prodis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'fakultas','web','2025-07-15 14:37:00','2025-07-15 14:37:00'),(2,'prodi','web','2025-07-15 14:37:00','2025-07-15 14:37:00'),(3,'mahasiswa','web','2025-07-15 14:37:00','2025-07-15 14:37:00'),(4,'cluster','web','2025-07-15 14:37:00','2025-07-15 14:37:00'),(5,'superadmin','web','2025-07-15 14:37:00','2025-07-15 14:37:00');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_time_constraints`
--

DROP TABLE IF EXISTS `room_time_constraints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_time_constraints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `master_ruangan_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_day_time_unique` (`master_ruangan_id`,`day_id`,`time_slot_id`),
  KEY `room_time_constraints_day_id_foreign` (`day_id`),
  KEY `room_time_constraints_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `room_time_constraints_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_time_constraints_master_ruangan_id_foreign` FOREIGN KEY (`master_ruangan_id`) REFERENCES `master_ruangans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_time_constraints_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_time_constraints`
--

LOCK TABLES `room_time_constraints` WRITE;
/*!40000 ALTER TABLE `room_time_constraints` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_time_constraints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_teacher`
--

DROP TABLE IF EXISTS `schedule_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_teacher` (
  `schedule_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`schedule_id`,`teacher_id`),
  KEY `schedule_teacher_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `schedule_teacher_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_teacher`
--

LOCK TABLES `schedule_teacher` WRITE;
/*!40000 ALTER TABLE `schedule_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_activity_id_foreign` (`activity_id`),
  KEY `schedules_room_id_foreign` (`room_id`),
  KEY `schedules_day_id_foreign` (`day_id`),
  KEY `schedules_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `schedules_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `master_ruangans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_group_time_constraints`
--

DROP TABLE IF EXISTS `student_group_time_constraints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_group_time_constraints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_group_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_group_day_time_unique` (`student_group_id`,`day_id`,`time_slot_id`),
  KEY `student_group_time_constraints_day_id_foreign` (`day_id`),
  KEY `student_group_time_constraints_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `student_group_time_constraints_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_group_time_constraints_student_group_id_foreign` FOREIGN KEY (`student_group_id`) REFERENCES `student_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_group_time_constraints_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_group_time_constraints`
--

LOCK TABLES `student_group_time_constraints` WRITE;
/*!40000 ALTER TABLE `student_group_time_constraints` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_group_time_constraints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_groups`
--

DROP TABLE IF EXISTS `student_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelompok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_kelompok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `angkatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_mahasiswa` int DEFAULT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_groups_nama_kelompok_parent_id_unique` (`nama_kelompok`,`parent_id`),
  KEY `student_groups_prodi_id_foreign` (`prodi_id`),
  KEY `student_groups_parent_id_foreign` (`parent_id`),
  CONSTRAINT `student_groups_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `student_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_groups_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_groups`
--

LOCK TABLES `student_groups` WRITE;
/*!40000 ALTER TABLE `student_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_matkul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_matkul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks` int NOT NULL,
  `semester` int NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `prodi_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_nama_matkul_prodi_id_unique` (`nama_matkul`,`prodi_id`),
  KEY `subjects_prodi_id_foreign` (`prodi_id`),
  CONSTRAINT `subjects_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'TECHNOPRENEURSHIP','EE208',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(2,'TEKNIK INSTALASI LISTRIK','EE303',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(3,'DESAIN SISTEM DIGITAL','EE305',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(4,'METODE NUMERIK','EE307',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(5,'SISTEM KOMUNIKASI','EE309',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(6,'METODOLOGI PENELITIAN','EE311',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(7,'PRAKTIKUM SISTEM DIGITAL DAN MIKROPROSESOR','EE313',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(8,'SEMINAR PENDIDIKAN AGAMA','KU30X',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(9,'KAJIAN TEKNOLOGI DAN VOKASI','TK302',2,5,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(10,'PENGOLAHAN SINYAL DIGITAL','EE401',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(11,'PENGGUNAAN DAN PENGATURAN MOTOR LISTRIK','EE403',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(12,'DEVAIS SISTEM KOMUNIKASI','EE405',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(13,'PROTEKSI SISTEM TENAGA LISTRIK','EE407',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(14,'SISTEM KOMUNIKASI OPTIK','EE409',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(15,'SISTEM PEMBANGKIT TENAGA LISTRIK','EE411',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(16,'SISTEM KOMUNIKASI BERGERAK DAN NIRKABEL','EE413',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(17,'SISTEM TRANSMISI TENAGA LISTRIK','EE415',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(18,'OTOMASI INDUSTRI (P)','EE501',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(19,'KEAMANAN JARINGAN TELEKOMUNIKASI (P)','EE503',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(20,'PROTEKSI RELAI (P)','EE505',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(21,'SISTEM RADAR (P)','EE507',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(22,'ENERGI TERBARUKAN (P)','EE509',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(23,'SISTEM KOMUNIKASI SATELIT DAN TERESTRIAL (P)','EE511',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(24,'EKONOMI ENERGI (P)','EE513',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(25,'PENGOLAHAN SINYAL DAN APLIKASINYA (P)','EE515',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(26,'PENGGUNAAN KOMPUTER DALAM SISTEM TENAGA LISTRIK (P)','EE517',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(27,'JARINGAN AKSES NIRKABEL (P)','EE519',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(28,'ELEKTRONIKA DAYA LANJUT (P)','EE521',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(29,'SISTEM TELEMETRI (P)','EE523',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(30,'SISTEM TRANSPORTASI LISTRIK (P)','EE525',3,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(31,'SISTEM CERDAS (P)','EE527',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(32,'TOPIK KHUSUS WLAN (P)','EE531',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(33,'VISI KOMPUTER (P)','EE535',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(34,'SISTEM TERTANAM (P)','EE537',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(35,'SCADA DAN MANAJEMEN ENERGI (P)','EE539',2,7,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(36,'ALGORITMA DAN PEMROGRAMAN (+P)','EE101',4,1,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(37,'MATEMATIKA DISKRIT','EE102',4,1,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(38,'KALKULUS VARIABEL TUNGGAL  DAN JAMAK','EE103',4,1,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(39,'FISIKA MEKANIKA KLASIK, FLUIDA, DAN KALOR ','EE104',4,1,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(40,'PROBABILITAS, VARIABEL ACAK, DAN STATISTIK','EE201',3,3,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(41,'PENGOLAHAN SINYAL WAKTU KONTINYU','EE202',3,3,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(42,'DISAIN SISTEM DIGITAL (+P)','EE203',3,3,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(43,'RANGKAIAN LISTRIK II (+P)','EE204',3,3,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(44,'TELEKOMUNIKASI DASAR  (+P)','EE205',4,3,NULL,1,'2025-07-15 15:12:27','2025-07-15 15:12:27'),(45,'EVALUASI PEMBELAJARAN TOIR','ER362',3,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(46,'PRAKTIKUM OTOMASI INDUSTRI & ROBOTIKA','ER470',2,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(47,'METODE PENELITIAN','ER473',2,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(48,'SCADA DAN JARINGAN INDUSTRI','ER462',2,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(49,'DESAIN SISTEM ROBOT CERDAS','ER561',3,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(50,'SEMINAR TEKNIK OTOMASI INDUSTRI DAN ROBOTIKA','ER597',3,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(51,'MICROTEACHING','PT501',4,7,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(52,'PENDIDIKAN JASMANI DAN OLAHRAGA','KU108',2,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(53,'SEMINAR PENDIDIKAN AGAMA ISLAM','KU300',2,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(54,'SENSOR DAN TRANDUSER','ER345',2,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(55,'ELEKTRONIKA DAYA','ER346',2,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(56,'SISTEM MIKROPROSESOR DAN MIKROKONTROLLER','ER450',3,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(57,'PRAKTIKUM MESIN LISTRIK','ER451',2,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(58,'PNEUMATIK DAN HIDROLIK','ER452',3,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(59,'PERENCANAAN PEMBELAJARAN TOIR','ER363',3,5,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(60,'PENDIDIKAN AGAMA ISLAM','KU100',2,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(61,'PENDIDIKAN PANCASILA','KU110',2,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(62,'KAJIAN PENDIDIKAN VOKASIONAL, TEKNOLOGI, DAN INDUSTRI','TK301',3,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(63,'KETERAMPILAN BERBAHASA INGGRIS','PT400',3,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(64,'KALKULUS','ER115',4,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(65,'COMPUTER AIDED DESIGN (GAMBAR TEKNIK)','ER210',3,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(66,'FISIKA','ER116',4,1,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(67,'APRESIASI SENI','KU119',2,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(68,'RANGKAIAN LISTRIK 2','ER230',3,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(69,'MEDAN ELEKTROMAGNETIK','ER344',3,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(70,'PRAKTIKUM BENGKEL DAN DASAR ELEKTRO','ER237',4,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(71,'KEWIRAUSAHAAN','PT503',3,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23'),(72,'PSIKOLOGI PENDIDIKAN DAN BIMBINGAN','DK301',2,3,NULL,3,'2025-07-15 15:15:23','2025-07-15 15:15:23');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_time_constraints`
--

DROP TABLE IF EXISTS `teacher_time_constraints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_time_constraints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `day_id` bigint unsigned NOT NULL,
  `time_slot_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_day_time_unique` (`teacher_id`,`day_id`,`time_slot_id`),
  KEY `teacher_time_constraints_day_id_foreign` (`day_id`),
  KEY `teacher_time_constraints_time_slot_id_foreign` (`time_slot_id`),
  CONSTRAINT `teacher_time_constraints_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_time_constraints_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_time_constraints_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_time_constraints`
--

LOCK TABLES `teacher_time_constraints` WRITE;
/*!40000 ALTER TABLE `teacher_time_constraints` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_time_constraints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_dosen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_depan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_belakang` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_univ` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_kode_dosen_unique` (`kode_dosen`),
  UNIQUE KEY `teachers_kode_univ_unique` (`kode_univ`),
  UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  UNIQUE KEY `teachers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'Budi Mulyanti','BMY','Prof. Dr.','M.Si.','1846','196301091994022000','bmy@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(2,'Didin Wahyudin','DDW',NULL,'Ph.D.','2934','197608272009121000','ddw@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(3,'Iwan Kustiawan','INK',NULL,'Ph.D.','2338','197709082003121000','ink@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(4,'Agus Heri Setya Budi','AHS','Dr.','MT.','2410','197208262005011000','ahs@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(5,'Tommi Hariyadi','TMH',NULL,'Ph.D.','2745','198204282009121000','tmh@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(6,'Aip Saripudin','AIP','Dr.','MT.','2355','197004162005011000','aip@upi.edu',NULL,'2025-07-15 15:02:25','2025-07-15 15:02:25'),(7,'Erik Haritman','ERH','Dr.','S.Pd., M.T.','2407','197605272001121002','ERH@upi.edu','08156232265','2025-07-15 15:04:52','2025-07-15 15:04:52'),(8,'Muhammad Adli Rizqulloh','MAR',NULL,'S.Pd., M.T.','3178','920200419921028101','MAR@upi.edu','08999452728','2025-07-15 15:04:52','2025-07-15 15:04:52'),(9,'Nurul Fahmi Arief Hakim','NFA',NULL,'S.Pd., M.T.','3179','920200419930905101','NFA@upi.edu','08567725533','2025-07-15 15:04:52','2025-07-15 15:04:52'),(10,'Resa Pramudita','RPR',NULL,'S.Pd., M.T.','3172','920200419910418000','RPR@upi.edu','085721528768','2025-07-15 15:04:52','2025-07-15 15:04:52'),(11,'Roer Eka Pawinanto','REP',NULL,'S.Pd., M.Sc., Ph.D.','3186','920200419881019101','REP@upi.edu','08971836780','2025-07-15 15:04:52','2025-07-15 15:04:52'),(12,'Mariya Al Qibtiya','MAQ',NULL,'S.Si., M.T.','3204','920200419890407201','MAQ@upi.edu','081394306837','2025-07-15 15:04:52','2025-07-15 15:04:52'),(13,'Silmi Ath Thahirah Al Azhima','STA',NULL,'S.T., M.T.','3183','920200419960203201','STA@upi.edu','085659163323','2025-07-15 15:04:52','2025-07-15 15:04:52'),(14,'Ibnu Hartopo','IBN',NULL,'M.Pd.','3580','199303142024061001','IBN@upi.edu','087839885579','2025-07-15 15:04:52','2025-07-15 15:04:52'),(15,'Maman Somantri','MMS','Dr.','MT.','2203','197201192001121000','mms@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(16,'Siscka Elvyanti','SSE',NULL,'Ph.D.','2202','197311222001122000','sse@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(17,'Arjuni Budi Pantjawati','ARJ',NULL,'MT.','2108','196406071995122000','arj@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(18,'Tasma Sucita','TSM','Dr.','MT.','1748','196410071991011000','tsm@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(19,'Mukhidin','MKH','Prof. Dr.','M.Pd.','535','195311101980021000','mkh@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(20,'Tuti Suartini','TSR','Prof. Dr.','M.Pd.','1038','196311211986032000','tsr@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(21,'Wasimudin Surya Saputra','WAS',NULL,'MT.','2107','197008081997021000','was@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(22,'Jaja Kustija','JKR','Prof. Dr.','M.Sc.','767','195912311985031000','jkr@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(23,'Wawan Purnama','WWP','Drs.','S.Pd., M.Si.','1848','196710261994031000','wwp@upi.edu',NULL,'2025-07-15 15:11:23','2025-07-15 15:11:23'),(24,'Danny Meirawan','DNM','Prof.Dr.','M.Pd.','1197','196205041988031000','dmeirawan@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(25,'Sudjani','SJN','Dr.','M.Pd.','1198','196306281988031000','sudjani@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(26,'Dedy Suryadi','DDS','Dr.','M.Pd.','2052','196707261997031000','dedysuryadi@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(27,'Sukadi','SKD','Drs.','M.Pd.,M.T.','1637','196409101991011000','sukadi64@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(28,'Dedi Purwanto','DPR',NULL,'S.Pd.,M.P.S.D.A','2655','197704292006041013','depoerwanto@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(29,'Parmono','PAR',NULL,'S.Pd.,M.T.','2929','920200119781016101','parmono97@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(30,'Sri Rahayu','SRH',NULL,'S.Pd.,M.Pd.','3181','920200419880624201','srirahayu@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(31,'Amar Mufhidin','AMR',NULL,'S.Pd.,M.T.','3180','920200419910616101','amar@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(32,'Ahmad Baehaqi','ABH',NULL,'S.Pd.,M.T.','3406','920230219920120101','abaehaqi20@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(33,'Dewi Ayu Sofia','DAS',NULL,'S.Pd.,M.Eng.','3577','199012242024062002','dewiayusofia@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(34,'Nanang Dalil Herman','NDH','Dr. Ir. H.','S.T., M.Pd.','1201','196202021988031000','nanangdalilherman@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(35,'Siti Nurasiyah','SNS',NULL,'S.T., M.T.','2651','19770208 2008122001','siti.nurasiyah@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(36,'Budi Kudwadi','BKD','Drs.','M.T','1415','19630622 1990011001','bkudwadi@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(37,'Herwan Dermawan','HDM','Dr.','S.T., M.T.','2653','19800128 2008121001','herwand@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(38,'Odih Supratman','ODS','Drs.','S.T.,M.T.','1635','196208091991011002','odihsupratman@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(39,'Istiqomah','ITQ',NULL,'S.T.,M.T.','2476','1971121520031200000',NULL,NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45'),(40,'Rieske Iswandhari','RKI',NULL,'M.Pd.',NULL,NULL,'rieske@upi.edu',NULL,'2025-07-15 15:26:45','2025-07-15 15:26:45');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_slots`
--

LOCK TABLES `time_slots` WRITE;
/*!40000 ALTER TABLE `time_slots` DISABLE KEYS */;
INSERT INTO `time_slots` VALUES (1,'Jam ke-1','07:00:00','07:50:00'),(2,'Jam ke-2','07:50:00','08:40:00'),(3,'Jam ke-3','08:40:00','09:30:00'),(4,'Jam ke-4','09:30:00','10:20:00'),(5,'Jam ke-5','11:10:00','12:00:00'),(6,'Jam ke-6','12:00:00','13:00:00'),(7,'Jam ke-7','13:00:00','13:50:00'),(8,'Jam ke-8','13:50:00','14:40:00'),(9,'Jam ke-9','14:40:00','15:30:00'),(10,'Jam ke-10','15:30:00','16:20:00'),(11,'Jam ke-11','16:20:00','17:10:00'),(12,'Jam ke-12','17:10:00','18:00:00');
/*!40000 ALTER TABLE `time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prodi_id` bigint unsigned DEFAULT NULL,
  `cluster_id` bigint unsigned DEFAULT NULL,
  `student_group_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_prodi_id_foreign` (`prodi_id`),
  KEY `users_student_group_id_foreign` (`student_group_id`),
  KEY `users_cluster_id_foreign` (`cluster_id`),
  CONSTRAINT `users_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_student_group_id_foreign` FOREIGN KEY (`student_group_id`) REFERENCES `student_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,NULL,NULL,'FPTI','fpti@upi.edu',NULL,'$2y$12$JGQDzeGgbYiYy6rwfUDOrubjxlvjnGnD9351kKNEFpm8ozsu6jFW.',NULL,'2025-07-15 14:37:01','2025-07-15 14:37:01'),(2,NULL,NULL,NULL,'sa','sa@upi.edu',NULL,'$2y$12$1KnV/MSAVvbuHqgA04gEBenLXGMirrCCsKCBabDvLoCZYWMa2kWni',NULL,'2025-07-15 14:37:01','2025-07-15 14:37:01'),(3,NULL,1,NULL,'dpte','dpte@upi.edu',NULL,'$2y$12$6jqpRDA5gIX55JulHFFLguyu0cke7LxDtGnoBmEy4fd3BU33F/2Wm',NULL,'2025-07-15 14:39:14','2025-07-15 14:39:14'),(4,NULL,4,NULL,'dpta','dpta@upi.edu',NULL,'$2y$12$ER3CGWIKLokx68E/eDLuDeMHoq3nRHclBNth1LFFIViJ5/9038ie2',NULL,'2025-07-15 14:39:40','2025-07-15 14:39:40'),(5,NULL,3,NULL,'dptas','dpts@upi.edu',NULL,'$2y$12$jHTLJqMwUEbOCihwbob9U.C6r7NW.BDCUaJMtgmMHkc3FK.Lr4Iu.',NULL,'2025-07-15 14:41:39','2025-07-15 14:41:39'),(6,NULL,2,NULL,'dptm','dptm@upi.edu',NULL,'$2y$12$FJTueSeo/Z88lxm9xGGen.HuNanzE8ogFgaswFfP4PdctfdBxFtK6',NULL,'2025-07-15 14:42:02','2025-07-15 14:42:02'),(7,1,NULL,NULL,'te','te@upi.edu',NULL,'$2y$12$MaQyFocKxolYgRfPp8d5EuqeM7I19Rw8/wGAvungDt7hyBViHjRXe',NULL,'2025-07-15 14:43:36','2025-07-15 14:43:36'),(8,2,NULL,NULL,'pte','pte@upi.edu',NULL,'$2y$12$ZXAKJEM5ZEr5r8vzKOKyfuAMmqSEcbCCHzxmxk7kxaByqpsKidloC',NULL,'2025-07-15 14:45:17','2025-07-15 14:45:17'),(9,3,NULL,NULL,'ptoir','ptoir@upi.edu',NULL,'$2y$12$gEkHTwRL6eClWerv55DuQuKhg243iV7duGNXTpZNCcrTX0YycGTl2',NULL,'2025-07-15 14:46:10','2025-07-15 14:46:10'),(10,4,NULL,NULL,'tet','tet@upi.edu',NULL,'$2y$12$8ELkwY1Q3ItwwxwWypugkOyoeWXnBuQjc7ILSfShFiWNIsAmOT6UO',NULL,'2025-07-15 14:47:35','2025-07-15 14:47:35'),(11,5,NULL,NULL,'tk','tk@upi.edu',NULL,'$2y$12$STW8fiqAWQVPqZjSN2lXAeiT.nmjOMEozrJVKHfdY9nUNzI655Lwi',NULL,'2025-07-15 14:53:11','2025-07-15 14:53:11'),(12,6,NULL,NULL,'pta','pta@upi.edu',NULL,'$2y$12$V8nIvvknAecmVJ2Pk08bc.dNJAx62C.EH9JZbUj5xXARY2ai8rlfG',NULL,'2025-07-15 14:55:02','2025-07-15 14:55:02'),(13,7,NULL,NULL,'mars','mars@upi.edu',NULL,'$2y$12$xuLVYJwPFeX.jJIAqoaZJOH1PvPJqKlO1tEmVh70YT4k43/xCrgA6',NULL,'2025-07-15 14:55:59','2025-07-15 14:55:59'),(14,8,NULL,NULL,'ars','ars@upi.edu',NULL,'$2y$12$qCEb7T9cqGnGjA3XtoMAj.eiGTYABgnRF7Lh7j8SeaGGwJQAxHtWS',NULL,'2025-07-15 14:56:35','2025-07-15 14:56:35'),(15,9,NULL,NULL,'ptm','ptm@upi.edu',NULL,'$2y$12$hD24KU5r6CAiBleBXK7itefXyL26y/n5eqsIFaS8mWxFBXCLvHfom',NULL,'2025-07-15 14:57:27','2025-07-15 14:57:27'),(16,10,NULL,NULL,'tl','tl@upi.edu',NULL,'$2y$12$rEZizdmJ5dQD1Sqma03hGeHX4eTb21e79H9QcOBhjOipuAIjg/ufm',NULL,'2025-07-15 14:58:21','2025-07-15 14:58:21'),(17,NULL,5,NULL,'tpag','tpag@upi.edu',NULL,'$2y$12$vfoDmi3mV8SFTwTeIzNUJecRliro3nhAM9WLTfE0uPyCgRsDYhEXe',NULL,'2025-07-15 15:19:29','2025-07-15 15:24:23'),(18,11,NULL,NULL,'ptagrin','ptagrin@upi.edu',NULL,'$2y$12$8.CysVa0GpSvOv6YuX/bLOPxq1VRWFjfaLku2gN4NFNzOEsyK7i1G',NULL,'2025-07-15 15:20:42','2025-07-15 15:20:42'),(19,12,NULL,NULL,'tp','tp@upi.edu',NULL,'$2y$12$2iEOnPgmwWmoTKpUS8QS1OQnsPiEdoTMcZVUWJ/Jic2xXB2HjMQ.G',NULL,'2025-07-15 15:21:50','2025-07-15 15:21:50'),(20,13,NULL,NULL,'ptb','ptb@upi.edu',NULL,'$2y$12$dLtJDOjWCXYKxqPkcu4Nn.UqtkGlG/FltOBI9Lkr24EwqaDjBgA6i',NULL,'2025-07-15 15:22:38','2025-07-15 15:22:38'),(21,14,NULL,NULL,'pto','pto@upi.edu',NULL,'$2y$12$g6ippbucheN27.IVx6uY5ulNWVSGF/0/Q0nqVkzpQMLmyrbsYhlEq',NULL,'2025-07-15 15:23:32','2025-07-15 15:23:32');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-15 15:38:01
