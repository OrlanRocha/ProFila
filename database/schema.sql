-- ------------------------------------------------------------
-- Banco de dados ProFila (estrutura refatorada)
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `profila` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `profila`;

-- -----------------------------
-- Tabela: users
-- -----------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','attendant','viewer') NOT NULL DEFAULT 'attendant',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `users_email_unique` (`email`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
('Administrador', 'admin@profila.local', '$2y$12$qtbrrO/wA5EbiPGiHSLk.uy2BKBPZ1j965EGciFyv8La8LFSsoLWC', 'admin');

-- -----------------------------
-- Tabela: sectors
-- -----------------------------
CREATE TABLE IF NOT EXISTS `sectors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `prefix` CHAR(2) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sectors` (`name`, `prefix`) VALUES
('Atendimento Geral', 'A'),
('Serviço Social', 'S'),
('Saúde', 'H');

-- -----------------------------
-- Tabela: counters
-- -----------------------------
CREATE TABLE IF NOT EXISTS `counters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sector_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `counters_sector_id_foreign` (`sector_id`),
  CONSTRAINT `counters_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `counters` (`sector_id`, `name`) VALUES
(1, 'Guichê 1'),
(1, 'Guichê 2'),
(2, 'Guichê Social 1'),
(3, 'Guichê Saúde 1');

-- -----------------------------
-- Tabela: tickets
-- -----------------------------
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sector_id` INT UNSIGNED NOT NULL,
  `counter_id` INT UNSIGNED DEFAULT NULL,
  `number` INT UNSIGNED NOT NULL,
  `code` VARCHAR(12) NOT NULL,
  `priority` ENUM('emergency','priority','normal') NOT NULL DEFAULT 'normal',
  `status` ENUM('waiting','called','finished') NOT NULL DEFAULT 'waiting',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `called_at` DATETIME DEFAULT NULL,
  `finished_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_sector_number_unique` (`sector_id`,`number`),
  KEY `tickets_status_index` (`status`),
  KEY `tickets_sector_index` (`sector_id`),
  CONSTRAINT `tickets_sector_id_foreign` FOREIGN KEY (`sector_id`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tickets` (`sector_id`, `number`, `code`, `priority`, `status`, `created_at`, `called_at`, `finished_at`, `counter_id`) VALUES
(1, 1, 'A001', 'normal', 'finished', NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY + INTERVAL 5 MINUTE, NOW() - INTERVAL 2 DAY + INTERVAL 15 MINUTE, 1),
(1, 2, 'A002', 'priority', 'called', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY + INTERVAL 3 MINUTE, NULL, 2),
(2, 1, 'S001', 'emergency', 'waiting', NOW(), NULL, NULL, NULL);
