CREATE TABLE `albums` (
  `id` int NOT NULL,
  `artist_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `art` varchar(255) DEFAULT NULL,
  `release_year` year DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `albums`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;