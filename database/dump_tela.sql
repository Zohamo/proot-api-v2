-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           9.1.0 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage de la structure de table tela. t_logs
CREATE TABLE IF NOT EXISTS `t_logs` (
  `log_id` int unsigned NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL,
  `fk_user_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `log_code` smallint unsigned DEFAULT NULL,
  `log_message` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `log_file` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `log_line` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`log_id`) USING BTREE,
  UNIQUE KEY `log_id_UNIQUE` (`log_id`) USING BTREE,
  KEY `FK_t_log_t_users` (`fk_user_email`),
  CONSTRAINT `FK_t_log_t_users` FOREIGN KEY (`fk_user_email`) REFERENCES `t_users` (`usr_email`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table tela.t_logs : ~0 rows (environ)

-- Listage de la structure de table tela. t_users
CREATE TABLE IF NOT EXISTS `t_users` (
  `usr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `usr_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usr_password` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usr_lastname` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `usr_firstname` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `usr_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fk_role_id` tinyint NOT NULL DEFAULT '1',
  `usr_last_login_at` datetime DEFAULT NULL,
  `usr_is_super_admin` tinyint DEFAULT NULL COMMENT 'Activer pour passer en mode débogage',
  `usr_created_at` datetime DEFAULT (now()),
  `usr_deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`usr_id`) USING BTREE,
  UNIQUE KEY `usr_id_UNIQUE` (`usr_id`) USING BTREE,
  UNIQUE KEY `usr_email_UNIQUE` (`usr_email`),
  UNIQUE KEY `usr_username_UNIQUE` (`usr_username`),
  KEY `fk_role_id` (`fk_role_id`),
  CONSTRAINT `FK_utilisateur_role` FOREIGN KEY (`fk_role_id`) REFERENCES `t_users_roles` (`rol_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage de la structure de table tela. t_users_roles
CREATE TABLE IF NOT EXISTS `t_users_roles` (
  `rol_id` tinyint NOT NULL AUTO_INCREMENT,
  `rol_label` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `rol_description` varchar(2048) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`rol_id`) USING BTREE,
  UNIQUE KEY `rol_id_UNIQUE` (`rol_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table tela.t_users_roles : ~4 rows (environ)
INSERT INTO `t_users_roles` (`rol_id`, `rol_label`, `rol_description`) VALUES
	(1, 'Guest', 'Readonly'),
	(25, 'Admin', 'Administrator'),
	(28, 'Dev', 'Developper');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
