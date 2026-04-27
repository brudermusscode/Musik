CREATE TABLE `artists` (
  `id` int NOT NULL,
  `name` varchar(124) NOT NULL,
  `art` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `artists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;