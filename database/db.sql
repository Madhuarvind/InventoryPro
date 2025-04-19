-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: inventory_db2
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `fk_cart_user` (`user_id`),
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_orders` (`order_id`),
  KEY `fk_order_items_products` (`product_id`),
  CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (2,5,1,4,50.00),(3,6,1,1,50.00),(4,6,3,2,180.00),(5,7,1,3,50.00),(6,8,4,1,20.00),(7,9,2,1,45.00),(8,10,2,1,45.00),(9,11,8,1,12.00),(10,12,1,1,50.00),(11,13,1,1,50.00),(12,16,1,1,50.00),(13,17,2,1,45.00),(14,18,2,1,45.00),(15,19,2,1,45.00),(18,22,1,1,50.00),(19,24,1,1,50.00),(20,25,1,1,50.00),(21,27,1,1,50.00),(22,35,2,1,45.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `fk_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (3,NULL,3,90.00,'2025-03-16 06:20:49','Completed'),(5,NULL,8,200.00,'2025-03-16 07:21:38','Completed'),(6,NULL,8,410.00,'2025-03-16 07:22:38','Pending'),(7,NULL,8,150.00,'2025-03-16 07:24:19','Completed'),(8,NULL,8,20.00,'2025-03-16 09:20:08','Pending'),(9,NULL,8,45.00,'2025-03-16 09:38:14','Pending'),(10,NULL,8,45.00,'2025-03-16 09:47:59','Pending'),(11,NULL,8,12.00,'2025-03-16 09:51:01','Pending'),(12,NULL,8,50.00,'2025-03-16 09:56:11','Cancelled'),(13,NULL,8,50.00,'2025-03-16 09:58:52','Completed'),(14,NULL,8,50.00,'2025-03-16 09:59:52','Pending'),(15,NULL,8,50.00,'2025-03-16 10:03:06','Pending'),(16,NULL,8,50.00,'2025-03-16 10:05:33','Completed'),(17,NULL,8,45.00,'2025-03-16 10:20:08','Completed'),(18,NULL,8,45.00,'2025-03-16 10:23:17','Pending'),(19,NULL,8,45.00,'2025-03-16 10:24:33','Completed'),(22,NULL,8,50.00,'2025-03-16 10:31:03','Pending'),(23,NULL,8,50.00,'2025-03-16 10:33:31','Pending'),(24,NULL,8,50.00,'2025-03-16 10:37:10','Completed'),(25,NULL,8,50.00,'2025-03-16 10:37:25','Pending'),(26,NULL,8,50.00,'2025-03-16 10:39:06','Pending'),(27,NULL,8,50.00,'2025-03-16 10:40:25','Pending'),(28,NULL,6,45.00,'2025-03-17 12:22:44','Pending'),(29,NULL,6,45.00,'2025-03-17 12:24:04','Pending'),(30,NULL,6,45.00,'2025-03-17 12:25:24','Pending'),(31,NULL,6,45.00,'2025-03-17 13:23:50','Pending'),(32,NULL,6,45.00,'2025-03-17 13:33:14','Pending'),(33,NULL,6,45.00,'2025-03-17 13:37:16','Pending'),(34,NULL,6,45.00,'2025-03-17 13:40:23','Pending'),(35,NULL,6,45.00,'2025-03-17 15:30:09','Completed');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (2,28,6,45.00,'UPI','Pending','2025-03-17 12:22:44'),(3,29,6,45.00,'UPI','Pending','2025-03-17 12:24:04'),(4,30,6,45.00,'UPI','Pending','2025-03-17 12:25:24'),(5,30,6,45.00,'UPI','Pending','2025-03-17 12:27:37'),(6,30,6,45.00,'UPI','Pending','2025-03-17 12:27:39'),(7,30,6,45.00,'UPI','Pending','2025-03-17 12:28:23'),(8,30,6,45.00,'UPI','Pending','2025-03-17 12:30:33'),(9,30,6,45.00,'UPI','Pending','2025-03-17 12:36:40'),(10,30,6,45.00,'UPI','Pending','2025-03-17 12:36:42'),(11,30,6,45.00,'UPI','Pending','2025-03-17 12:40:19'),(12,30,6,45.00,'UPI','Pending','2025-03-17 12:42:42'),(13,30,6,45.00,'UPI','Pending','2025-03-17 12:50:45'),(14,30,6,45.00,'UPI','Pending','2025-03-17 12:54:44'),(15,30,6,45.00,'UPI','Pending','2025-03-17 12:55:32'),(16,30,6,45.00,'UPI','Pending','2025-03-17 12:58:42'),(17,30,6,45.00,'UPI','Pending','2025-03-17 12:59:47'),(18,30,6,45.00,'UPI','Pending','2025-03-17 13:00:54'),(19,30,6,45.00,'UPI','Pending','2025-03-17 13:00:55');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `barcode` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Biscuit',50.00,69,'Jagun','2025-02-17 13:30:17',NULL,'','uploads/products/biscuit.jpg'),(2,'choco',45.00,44,'sachin','2025-02-18 15:18:24',NULL,'','uploads/products/choco.jpg'),(3,'ABC malt',180.00,70,'Pranesh','2025-02-20 16:21:09',NULL,'','uploads/products/abc malt.jpg'),(4,'ice cream',20.00,25,'Pranesh','2025-02-21 13:43:41',NULL,'','uploads/products/ice cream.jpg'),(5,'Ponds magic talc',120.00,10,'Pranesh','2025-02-23 06:34:19',NULL,'','uploads/products/ponds.jpg'),(8,'jam',12.00,15,'boobalan','2025-02-23 16:18:45',NULL,'','uploads/products/jam.jpg'),(9,'kit kat',11.00,42,'barath','2025-02-23 16:29:58',NULL,'','uploads/products/kitkat.jpg'),(10,'milky bar',11.00,10,'barath','2025-02-23 16:33:57',NULL,'','uploads/products/milkybar.jpg'),(16,'munch',11.00,15,'Dinesh','2025-02-23 16:58:04',NULL,'','uploads/products/munch.jpg'),(22,'mango',110.00,45,'Giri','2025-02-27 13:20:13',NULL,'','uploads/products/mango.jpg'),(40,'pineapple',145.00,25,'Giri','2025-03-01 13:22:26','GRO-67c30a1258a43','GROCERY','uploads/products/pineapple.jpg'),(41,'graps',90.00,15,'Dinesh','2025-03-01 15:56:11','GRO-67c32e1bf2518','GROCERY','uploads/products/grapes.jpg'),(44,'Bag',350.00,4,'Jagun','2025-03-02 14:57:41','91CRE0BFASJU','CLOTHING','uploads/products/bag.jpg'),(47,'laptop',25000.00,15,'sachin','2025-03-18 10:58:51','E2XJSD4ZGMQU','ELECTRONICS','uploads/products/ioq.jpg');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,1,1,5,'Completed','2025-02-21 12:47:12',0.00),(2,1,3,10,'Completed','2025-02-21 12:52:51',0.00),(3,1,2,5,'Completed','2025-02-23 04:53:21',0.00),(4,2,16,10,'Completed','2025-03-02 06:59:08',0.00);
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Praveen','6768798990','praveen122@gmail.com','','2025-02-20 17:06:24'),(2,'Praveen','06768798990','praveen122@gmail.com','','2025-02-27 13:50:25');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Madhu','aravindmadhu12@gmail.com','$2y$10$Y.8oYa5udj5H/.0A0pEGe.rMUchBn49gcO1WgdIFRD966Q7mQDmeu','admin',NULL,NULL,'2025-02-23 04:13:36'),(2,'user1','user1@gmail.com','$2y$10$5Ne3SpVHizHCjeE2JbXy2uH3LkPF6Hopd/o.iL3vZmmki6uolFlqC','staff',NULL,NULL,'2025-03-02 06:46:17'),(3,'customer1','customer1@gmail.com','$2y$10$y2CY6gWuIMuTn/.NTyv.guap7gHggQVBJab4.hPYFtiA1QzBUIpki','customer',NULL,NULL,'2025-03-15 05:45:40'),(4,'customer2','customer2@gmail.com','$2y$10$41gnlMjHUfQV2nfu86ZJX.yPrbReJ2O88v2o7Xwk9HJ9s/jB.tFxe','customer',NULL,NULL,'2025-03-16 06:06:42'),(6,'customer3','customer3@gmail.com','$2y$10$yPAcdoSAHtV3Wvp9xz.Y8.BQ5gqDeVdKpQ1P/jK5ozSzhsXue7IMG','customer',NULL,NULL,'2025-03-16 06:17:18'),(8,'customer4','customer4@gmail.com','$2y$10$GfHoh/nUspPN/OY56KuWP.97Auf.o61tv7bN2h5HLWrGZMbjy9erS','customer','78906754789','muluanur','2025-03-16 07:16:25');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-29 12:16:11
