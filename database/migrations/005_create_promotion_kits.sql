CREATE TABLE IF NOT EXISTS `promotion_kits` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,

    `original_file_name` VARCHAR(255) NOT NULL,
    `stored_file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,

    `file_extension` VARCHAR(20) NOT NULL,
    `mime_type` VARCHAR(150) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL,

    `cover_photo_path` VARCHAR(500) NULL,

    `uploaded_by` INT UNSIGNED NOT NULL,

    `status` ENUM('active', 'archived') NOT NULL DEFAULT 'active',

    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    INDEX `idx_promotion_kits_status` (`status`),
    INDEX `idx_promotion_kits_uploaded_by` (`uploaded_by`),
    INDEX `idx_promotion_kits_created_at` (`created_at`),

    CONSTRAINT `fk_promotion_kits_uploaded_by`
        FOREIGN KEY (`uploaded_by`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;