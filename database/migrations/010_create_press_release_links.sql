CREATE TABLE IF NOT EXISTS `press_release_links` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `press_release_id` BIGINT UNSIGNED NOT NULL,

    `news_source` ENUM(
        'Newspaper (Print)',
        'Magazine',
        'Official Website News',
        'Online News Portals',
        'Blogs',
        'Social Media (SNS News)',
        'Influencer/Content Creator Updates',
        'Television News',
        'Radio News',
        'Newsletters / Email Updates',
        'News Aggregators (e.g., Google News)'
    ) NOT NULL DEFAULT 'Online News Portals',

    `news_content_type` ENUM(
        'News Article',
        'Feature Story',
        'Editorial / Opinion Piece',
        'Video Feature',
        'News Report (TV/Radio)',
        'Press Release',
        'Interview Segment',
        'Photojournalism / Image Story',
        'Podcast / Audio News',
        'Live Coverage / Breaking News'
    ) NOT NULL DEFAULT 'Press Release',

    `date_released` DATE NOT NULL DEFAULT (CURRENT_DATE),

    `media_logo` VARCHAR(255) DEFAULT NULL,

    `media_outlet` VARCHAR(150) DEFAULT NULL,

    `link` VARCHAR(2048) NOT NULL,

    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,

    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    KEY `idx_press_release_links_press_release_id` (`press_release_id`),

    CONSTRAINT `fk_press_release_links_press_release`
        FOREIGN KEY (`press_release_id`)
        REFERENCES `press_releases` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;