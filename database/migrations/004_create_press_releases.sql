CREATE TABLE IF NOT EXISTS `press_releases` (

    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `title` VARCHAR(150) NOT NULL,

    `description` VARCHAR(1000) NOT NULL,

    `event_date` DATE NOT NULL DEFAULT (CURRENT_DATE),

    `cover_photo` VARCHAR(255) DEFAULT NULL,

    PRIMARY KEY (`id`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;