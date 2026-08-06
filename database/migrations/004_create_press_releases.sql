CREATE TABLE IF NOT EXISTS `press_releases` (
  `title` varchar(150) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `news_sources` varchar(50) NOT NULL,
  `news_content_type` varchar(50) NOT NULL,
  `date_released` date NOT NULL,
  `cover_photo` varchar(150) DEFAULT NULL,
  `media_logo` varchar(150) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;