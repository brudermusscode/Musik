CREATE TABLE `playlists` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `subtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `art` varchar(82) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `playlists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;