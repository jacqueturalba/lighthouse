CREATE TABLE sflex_post_media (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,post_id BIGINT UNSIGNED NOT NULL,media_path VARCHAR(255) NOT NULL,media_type ENUM('image','video') NOT NULL DEFAULT 'image',sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(id),INDEX(post_id,sort_order),CONSTRAINT fk_sflex_post_media_post FOREIGN KEY(post_id) REFERENCES sflex_posts(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE sflex_comments ADD COLUMN parent_id BIGINT UNSIGNED NULL AFTER post_id;
ALTER TABLE sflex_comments ADD INDEX idx_sflex_comments_parent (parent_id,created_at);
ALTER TABLE sflex_comments ADD CONSTRAINT fk_sflex_comments_parent FOREIGN KEY(parent_id) REFERENCES sflex_comments(id) ON DELETE CASCADE;
