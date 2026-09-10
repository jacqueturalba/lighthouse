CREATE TABLE IF NOT EXISTS upload_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    upload_type VARCHAR(30) NOT NULL,
    original_filename VARCHAR(255) NULL,
    file_size BIGINT UNSIGNED NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'preparing',
    progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
    related_id BIGINT UNSIGNED NULL,
    error_message VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    PRIMARY KEY (id),
    INDEX idx_upload_jobs_user_status (user_id, status, updated_at),
    CONSTRAINT fk_upload_jobs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
