CREATE TABLE IF NOT EXISTS `press_releases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `description` VARCHAR(1000) NOT NULL,
    `news_source` VARCHAR(100) NOT NULL,
    `news_content_type` VARCHAR(50) NOT NULL,
    `date_released` DATE NOT NULL DEFAULT (CURRENT_DATE),
    `cover_photo` VARCHAR(255) DEFAULT NULL,
    `media_logo` VARCHAR(255) DEFAULT NULL,
    `media_outlet` VARCHAR(150) DEFAULT NULL,
    `link` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;