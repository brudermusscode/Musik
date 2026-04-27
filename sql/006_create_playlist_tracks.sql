CREATE TABLE `playlist_tracks` (
  `id` int NOT NULL,
  `playlist_id` int NOT NULL,
  `track_id` int NOT NULL,
  `playlist_index` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `playlist_tracks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `playlist_tracks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;