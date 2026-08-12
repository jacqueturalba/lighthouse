CREATE TABLE IF NOT EXISTS `promotion_kit_downloads` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `promotion_kit_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `request_id` BIGINT UNSIGNED NULL DEFAULT NULL,

    `downloaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    INDEX `idx_downloads_kit` (`promotion_kit_id`),
    INDEX `idx_downloads_user` (`user_id`),
    INDEX `idx_downloads_request` (`request_id`),
    INDEX `idx_downloads_date` (`downloaded_at`),

    CONSTRAINT `fk_downloads_promotion_kit`
        FOREIGN KEY (`promotion_kit_id`)
        REFERENCES `promotion_kits` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_downloads_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_downloads_request`
        FOREIGN KEY (`request_id`)
        REFERENCES `promotion_kit_requests` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;