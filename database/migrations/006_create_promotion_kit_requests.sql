CREATE TABLE IF NOT EXISTS `promotion_kit_requests` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `promotion_kit_id` BIGINT UNSIGNED NOT NULL,
    `requested_by` BIGINT UNSIGNED NOT NULL,

    `status` ENUM(
        'pending',
        'approved',
        'disapproved'
    ) NOT NULL DEFAULT 'pending',

    `requested_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
    `reviewed_by` BIGINT UNSIGNED NULL DEFAULT NULL,

    `review_reason` TEXT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_kit_request` (
        `promotion_kit_id`,
        `requested_by`
    ),

    INDEX `idx_requests_status` (`status`),
    INDEX `idx_requests_user` (`requested_by`),
    INDEX `idx_requests_kit` (`promotion_kit_id`),
    INDEX `idx_requests_reviewer` (`reviewed_by`),

    CONSTRAINT `fk_requests_promotion_kit`
        FOREIGN KEY (`promotion_kit_id`)
        REFERENCES `promotion_kits` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_requests_user`
        FOREIGN KEY (`requested_by`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_requests_reviewer`
        FOREIGN KEY (`reviewed_by`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;