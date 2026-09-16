CREATE TABLE IF NOT EXISTS `album_tracks` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `album_id` int NOT NULL,
  `track_id` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
