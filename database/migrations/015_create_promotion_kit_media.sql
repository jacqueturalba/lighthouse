CREATE TABLE promotion_kit_media (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    promotion_kit_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    original_file_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(150) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_promotion_kit_media_kit (promotion_kit_id, sort_order),
    CONSTRAINT fk_promotion_kit_media_kit FOREIGN KEY (promotion_kit_id) REFERENCES promotion_kits(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
