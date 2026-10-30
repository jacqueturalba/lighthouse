ALTER TABLE upload_jobs
  ADD COLUMN IF NOT EXISTS total_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS processed_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS created_count INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS updated_count INT UNSIGNED NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS bulk_user_upload_rows (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  job_id BIGINT UNSIGNED NOT NULL,
  excel_row INT UNSIGNED NOT NULL,
  row_data JSON NOT NULL,
  status ENUM('pending','created','updated','failed') NOT NULL DEFAULT 'pending',
  error_message VARCHAR(255) NULL,
  serial_no VARCHAR(100) NULL,
  INDEX idx_bulk_user_upload_rows_job (job_id, status, excel_row),
  CONSTRAINT fk_bulk_user_upload_rows_job FOREIGN KEY (job_id) REFERENCES upload_jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
