-- Arcadia database dump (structure + seeder data)
-- Sumber: MySQL-compatible (TiDB Cloud), diekspor dengan mysqldump.
-- Import: mysql -u root -p arcadia < database/arcade.sql
-- Catatan: tabel sessions dan password_reset_tokens hanya struktur (tanpa data).

-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: gateway01.ap-northeast-1.prod.aws.tidbcloud.com    Database: arcadiadb
-- ------------------------------------------------------
-- Server version	8.0.11-TiDB-v8.5.3-serverless

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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30001;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@gmail.com',NULL,'$2y$12$xoiz.OIwhABrbffUVZSJ4OG1hORDbId9z4rJkVzJnsGc8yML/hEhK','admin',NULL,'2026-10-31 04:18:12','2026-10-31 04:18:13'),(2,'Antonio Wisnu','staff1@gmail.com',NULL,'$2y$12$RhVcmaBI07QU6JjzSH4sW.HSxzJfWdy/G5TSAhUXFTRIlY0GwJVGa','staff',NULL,'2026-10-31 04:18:13','2026-10-31 04:18:13'),(3,'Dodo Wiyaja','staff2@gmail.com',NULL,'$2y$12$S16bJur7mULTRG4WR0z0L.mVCpDvJOymMKsR0VmfotLbEi/4ufbka','staff',NULL,'2026-10-31 04:18:14','2026-10-31 04:18:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30001;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Racing','Mesin dengan tema game balapan.','2026-10-31 04:18:14','2026-10-31 04:18:14'),(2,'Fighting','Game pertarungan 1v1 ataupun vs bot.','2026-10-31 04:18:14','2026-10-31 04:18:14'),(3,'Shooting','Game dengan esensi tembak menembak.','2026-10-31 04:18:14','2026-10-31 04:18:14'),(4,'Claw Machine','Mesin capit untuk boneka dan hadiah.','2026-10-31 04:18:15','2026-10-31 04:18:15'),(5,'Ticket','Minigames dengan tujuan utamanya adalah tiket.','2026-10-31 04:18:15','2026-10-31 04:18:15'),(6,'Sport','Permainan yang memiliki element olahraga basket, bola, dll.','2026-10-31 04:18:15','2026-10-31 04:18:15'),(7,'Rhythm','Game musik.','2026-10-31 04:18:15','2026-10-31 04:18:15');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `machines`
--

DROP TABLE IF EXISTS `machines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `machines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_price` decimal(12,2) NOT NULL,
  `price_per_play` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `machines_category_id_foreign` (`category_id`),
  UNIQUE KEY `machines_serial_number_unique` (`serial_number`),
  KEY `machines_status_index` (`status`),
  CONSTRAINT `machines_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30001;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `machines`
--

LOCK TABLES `machines` WRITE;
/*!40000 ALTER TABLE `machines` DISABLE KEYS */;
INSERT INTO `machines` VALUES (1,1,'Wangan Midnight Maximum Tune','ARC-0001','Lantai 1 - Zona A','2021-01-01',133600000.00,16000.00,'maintenance','2026-10-31 04:18:16','2026-10-31 04:18:16'),(2,1,'Initial D Arcade Stage','ARC-0002','Lantai 1 - Zona A','2022-01-01',38000000.00,10000.00,'active','2026-10-31 04:18:16','2026-10-31 04:18:16'),(3,1,'Horse Racing','ARC-0003','Lantai 1 - Zona A','2019-01-01',76000000.00,13000.00,'active','2026-10-31 04:18:16','2026-10-31 04:18:16'),(4,1,'GTI Club Racer','ARC-0004','Lantai 2 - Zona C','2020-01-01',25000000.00,15000.00,'active','2026-10-31 04:18:16','2026-10-31 04:18:16'),(5,2,'Tekken 7 FR','ARC-0005','Lantai 1 - Zona B','2023-01-01',30000000.00,13000.00,'broken','2026-10-31 04:18:16','2026-10-31 04:18:16'),(6,2,'Street Fighter II','ARC-0006','Lantai 1 - Zona B','2016-01-01',8000000.00,10000.00,'active','2026-10-31 04:18:17','2026-10-31 04:18:17'),(7,2,'Mortal Kombat 4','ARC-0007','Lantai 1 - Zona B','2014-01-01',46500000.00,10000.00,'retired','2026-10-31 04:18:17','2026-10-31 04:18:17'),(8,3,'Time Crisis 4','ARC-0008','Lantai 2 - Zona D','2021-01-01',28000000.00,15000.00,'active','2026-10-31 04:18:17','2026-10-31 04:18:17'),(9,3,'House of the Dead 2','ARC-0009','Lantai 2 - Zona D','2017-01-01',52000000.00,18000.00,'active','2026-10-31 04:18:17','2026-10-31 04:18:17'),(10,4,'Claw Machine Jumbo','ARC-0010','Lantai 2 - Zona E','2024-01-01',19000000.00,10000.00,'active','2026-10-31 04:18:17','2026-10-31 04:18:17'),(11,4,'Mini Claw Pinky','ARC-0011','Lantai 2 - Zona E','2024-01-01',1000000.00,18000.00,'active','2026-10-31 04:18:18','2026-10-31 04:18:18'),(12,7,'Maimai DX','ARC-0012','Lantai 1 - Zona F','2024-01-01',283000000.00,15000.00,'active','2026-10-31 04:18:18','2026-10-31 04:18:18'),(13,7,'Taiko no Tatsujin','ARC-0013','Lantai 1 - Zona F','2023-01-01',270000000.00,15000.00,'active','2026-10-31 04:18:18','2026-10-31 04:18:18'),(14,7,'Pump It Up LX','ARC-0014','Lantai 1 - Zona F','2022-01-01',178000000.00,15000.00,'active','2026-10-31 04:18:18','2026-10-31 04:18:18');
/*!40000 ALTER TABLE `machines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_logs`
--

DROP TABLE IF EXISTS `maintenance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `issue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_taken` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(12,2) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `maintenance_logs_machine_id_foreign` (`machine_id`),
  KEY `maintenance_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `maintenance_logs_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maintenance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=60001;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_logs`
--

LOCK TABLES `maintenance_logs` WRITE;
/*!40000 ALTER TABLE `maintenance_logs` DISABLE KEYS */;
INSERT INTO `maintenance_logs` VALUES (1,1,2,'Gas Cabinet 1 kadang stuck','Bongkar untuk di cek mendalam',450000.00,'done','2026-07-01','2026-10-31 04:18:19','2026-10-31 04:18:19'),(2,5,3,'Layar mendadak mati saat dimainkan','Cek kabel VGA, solder ulang konektor',350000.00,'done','2026-07-03','2026-10-31 04:18:19','2026-10-31 04:18:19'),(3,12,2,'Button 4 P1 macet','Deep cleaning mesin dan apply lube pada tombol',0.00,'done','2026-08-05','2026-10-31 04:18:19','2026-10-31 04:18:19'),(4,10,3,'Hadiah mulai menipis','Isi ulang hadiah boneka',275000.00,'done','2026-08-07','2026-10-31 04:18:19','2026-10-31 04:18:19'),(5,8,2,'Light gun player 1 tidak akurat','Kalibrasi ulang sensor',50000.00,'done','2026-10-09','2026-10-31 04:18:20','2026-10-31 04:18:20'),(6,1,3,'Cabinet 3 gear shift nyangkut',NULL,0.00,'open','2026-10-11','2026-10-31 04:18:20','2026-10-31 04:18:20'),(7,13,2,'Stik untuk P2 patah 1',NULL,0.00,'open','2026-10-13','2026-10-31 04:18:20','2026-10-31 04:18:20'),(8,9,3,'Karti berhasil tap tapi credit ga ke-input','Ganti mesin tap kartu',100000.00,'done','2026-10-15','2026-10-31 04:18:20','2026-10-31 04:18:20'),(9,5,2,'Tombol P1 kadang ga ke input',NULL,0.00,'open','2026-10-17','2026-10-31 04:18:20','2026-10-31 04:18:20'),(10,2,3,'Pedal gas player 1 macet','Bongkar untuk dicek',125000.00,'done','2026-10-19','2026-10-31 04:18:21','2026-10-31 04:18:21'),(30001,12,1,'Sensor P2 kena statis',NULL,0.00,'open','2026-10-31','2026-10-31 04:37:14','2026-10-31 04:37:14');
/*!40000 ALTER TABLE `maintenance_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `income_records`
--

DROP TABLE IF EXISTS `income_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `play_count` int unsigned NOT NULL,
  `total_income` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `income_records_machine_id_foreign` (`machine_id`),
  KEY `income_records_user_id_foreign` (`user_id`),
  CONSTRAINT `income_records_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `income_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=30001;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `income_records`
--

LOCK TABLES `income_records` WRITE;
/*!40000 ALTER TABLE `income_records` DISABLE KEYS */;
INSERT INTO `income_records` VALUES (1,1,2,'2026-07-31',40,640000.00,'2026-10-31 04:18:21','2026-10-31 04:18:21'),(2,1,2,'2026-08-31',52,832000.00,'2026-10-31 04:18:22','2026-10-31 04:18:22'),(3,1,2,'2026-09-30',64,1024000.00,'2026-10-31 04:18:22','2026-10-31 04:18:22'),(4,1,2,'2026-10-31',76,1216000.00,'2026-10-31 04:18:22','2026-10-31 04:18:22'),(5,2,3,'2026-07-31',47,470000.00,'2026-10-31 04:18:22','2026-10-31 04:18:22'),(6,2,3,'2026-08-31',59,590000.00,'2026-10-31 04:18:22','2026-10-31 04:18:22'),(7,2,3,'2026-09-30',71,710000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(8,2,3,'2026-10-31',83,830000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(9,3,2,'2026-07-31',54,702000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(10,3,2,'2026-08-31',66,858000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(11,3,2,'2026-09-30',78,1014000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(12,3,2,'2026-10-31',90,1170000.00,'2026-10-31 04:18:23','2026-10-31 04:18:23'),(13,4,3,'2026-07-31',61,915000.00,'2026-10-31 04:18:24','2026-10-31 04:18:24'),(14,4,3,'2026-08-31',73,1095000.00,'2026-10-31 04:18:24','2026-10-31 04:18:24'),(15,4,3,'2026-09-30',85,1275000.00,'2026-10-31 04:18:24','2026-10-31 04:18:24'),(16,4,3,'2026-10-31',97,1455000.00,'2026-10-31 04:18:24','2026-10-31 04:18:24'),(17,5,2,'2026-07-31',68,884000.00,'2026-10-31 04:18:24','2026-10-31 04:18:24'),(18,5,2,'2026-08-31',80,1040000.00,'2026-10-31 04:18:25','2026-10-31 04:18:25'),(19,5,2,'2026-09-30',92,1196000.00,'2026-10-31 04:18:25','2026-10-31 04:18:25'),(20,5,2,'2026-10-31',104,1352000.00,'2026-10-31 04:18:25','2026-10-31 04:18:25'),(21,6,3,'2026-07-31',75,750000.00,'2026-10-31 04:18:25','2026-10-31 04:18:25'),(22,6,3,'2026-08-31',87,870000.00,'2026-10-31 04:18:25','2026-10-31 04:18:25'),(23,6,3,'2026-09-30',99,990000.00,'2026-10-31 04:18:26','2026-10-31 04:18:26'),(24,6,3,'2026-10-31',111,1110000.00,'2026-10-31 04:18:26','2026-10-31 04:18:26'),(25,7,2,'2026-07-31',82,820000.00,'2026-10-31 04:18:26','2026-10-31 04:18:26'),(26,7,2,'2026-08-31',94,940000.00,'2026-10-31 04:18:26','2026-10-31 04:18:26'),(27,7,2,'2026-09-30',106,1060000.00,'2026-10-31 04:18:26','2026-10-31 04:18:26'),(28,8,3,'2026-07-31',89,1335000.00,'2026-10-31 04:18:27','2026-10-31 04:18:27'),(29,8,3,'2026-08-31',101,1515000.00,'2026-10-31 04:18:27','2026-10-31 04:18:27'),(30,8,3,'2026-09-30',113,1695000.00,'2026-10-31 04:18:27','2026-10-31 04:18:27'),(31,8,3,'2026-10-31',125,1875000.00,'2026-10-31 04:18:27','2026-10-31 04:18:27'),(32,9,2,'2026-07-31',96,1728000.00,'2026-10-31 04:18:27','2026-10-31 04:18:27'),(33,9,2,'2026-08-31',108,1944000.00,'2026-10-31 04:18:28','2026-10-31 04:18:28'),(34,9,2,'2026-09-30',120,2160000.00,'2026-10-31 04:18:28','2026-10-31 04:18:28'),(35,9,2,'2026-10-31',132,2376000.00,'2026-10-31 04:18:28','2026-10-31 04:18:28'),(36,10,3,'2026-07-31',103,1030000.00,'2026-10-31 04:18:28','2026-10-31 04:18:28'),(37,10,3,'2026-08-31',115,1150000.00,'2026-10-31 04:18:28','2026-10-31 04:18:28'),(38,10,3,'2026-09-30',127,1270000.00,'2026-10-31 04:18:29','2026-10-31 04:18:29'),(39,10,3,'2026-10-31',139,1390000.00,'2026-10-31 04:18:29','2026-10-31 04:18:29'),(40,11,2,'2026-07-31',110,1980000.00,'2026-10-31 04:18:29','2026-10-31 04:18:29'),(41,11,2,'2026-08-31',122,2196000.00,'2026-10-31 04:18:29','2026-10-31 04:18:29'),(42,11,2,'2026-09-30',134,2412000.00,'2026-10-31 04:18:29','2026-10-31 04:18:29'),(43,11,2,'2026-10-31',146,2628000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(44,12,3,'2026-07-31',117,1755000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(45,12,3,'2026-08-31',129,1935000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(46,12,3,'2026-09-30',141,2115000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(47,12,3,'2026-10-31',153,2295000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(48,13,2,'2026-07-31',124,1860000.00,'2026-10-31 04:18:30','2026-10-31 04:18:30'),(49,13,2,'2026-08-31',136,2040000.00,'2026-10-31 04:18:31','2026-10-31 04:18:31'),(50,13,2,'2026-09-30',148,2220000.00,'2026-10-31 04:18:31','2026-10-31 04:18:31'),(51,13,2,'2026-10-31',160,2400000.00,'2026-10-31 04:18:31','2026-10-31 04:18:31'),(52,14,3,'2026-07-31',131,1965000.00,'2026-10-31 04:18:31','2026-10-31 04:18:31'),(53,14,3,'2026-08-31',143,2145000.00,'2026-10-31 04:18:31','2026-10-31 04:18:31'),(54,14,3,'2026-09-30',155,2325000.00,'2026-10-31 04:18:32','2026-10-31 04:18:32'),(55,14,3,'2026-10-31',168,2505000.00,'2026-10-31 04:18:32','2026-10-31 06:04:29');
/*!40000 ALTER TABLE `income_records` ENABLE KEYS */;
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
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=84356;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'2026_10_29_105359_create_categories_table',1),(3,'2026_10_29_105400_create_machines_table',1),(4,'2026_10_29_105401_create_maintenance_logs_table',1),(5,'2026_10_29_105402_create_income_records_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-01 11:14:06
-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: gateway01.ap-northeast-1.prod.aws.tidbcloud.com    Database: arcadiadb
-- ------------------------------------------------------
-- Server version	8.0.11-TiDB-v8.5.3-serverless

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
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  PRIMARY KEY (`email`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-01 11:14:09
