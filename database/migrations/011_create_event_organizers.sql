CREATE TABLE IF NOT EXISTS event_organizers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(160) NOT NULL, normalized_name VARCHAR(160) NOT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#145da0', is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_event_organizers_normalized_name (normalized_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE events ADD COLUMN IF NOT EXISTS organizer_id BIGINT UNSIGNED NULL AFTER organizer;
ALTER TABLE events ADD INDEX IF NOT EXISTS idx_events_organizer_id (organizer_id);
INSERT IGNORE INTO event_organizers (name, normalized_name, color, is_default) VALUES
 ('ZCMC','zcmc','#145da0',1),('SCJ','scj','#6f42c1',1),('HWPL','hwpl','#198754',1),('Area 1','area 1','#d97706',1),('Area 2','area 2','#dc3545',1),('Area 3','area 3','#0d6efd',1);
UPDATE events e JOIN event_organizers o ON o.normalized_name=LOWER(TRIM(e.organizer)) SET e.organizer_id=o.id WHERE e.organizer_id IS NULL;
