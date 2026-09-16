CREATE TABLE IF NOT EXISTS `tracks` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int DEFAULT '1',
  `artist_id` int DEFAULT NULL,
  `file_name` varchar(324) NOT NULL,
  `video` varchar(32) DEFAULT NULL,
  `artist` varchar(242) DEFAULT NULL,
  `title` text,
  `genre` varchar(42) DEFAULT NULL,
  `year` year DEFAULT NULL,
  `mime` varchar(24) NOT NULL DEFAULT 'audio/mpeg',
  `listens` int DEFAULT NULL,
  `length_seconds` float(242,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
