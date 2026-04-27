CREATE TABLE `bookmarks` (
  `id` int NOT NULL,
  `reference_id` int NOT NULL,
  `type` varchar(24) NOT NULL,
  `view_index` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_id` (`reference_id`,`type`);

ALTER TABLE `bookmarks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;