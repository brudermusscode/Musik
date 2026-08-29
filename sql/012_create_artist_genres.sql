CREATE TABLE `artist_genres` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `artist_id` INT NOT NULL,
  `genre_id` INT NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE artists
  DROP COLUMN genre;
