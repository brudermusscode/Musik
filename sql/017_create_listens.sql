CREATE TABLE IF NOT EXISTS `listens` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `track_id` INT NOT NULL,
  `relation_id` INT NULL,
  `relation_type` VARCHAR(24) NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
