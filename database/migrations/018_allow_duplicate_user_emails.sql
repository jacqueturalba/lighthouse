SET @email_unique_index = (
    SELECT INDEX_NAME
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'email'
      AND NON_UNIQUE = 0
    GROUP BY INDEX_NAME
    HAVING COUNT(*) = 1
    LIMIT 1
);
SET @email_drop_sql = IF(
    @email_unique_index IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE users DROP INDEX `', REPLACE(@email_unique_index, '`', '``'), '`')
);
PREPARE email_drop_statement FROM @email_drop_sql;
EXECUTE email_drop_statement;
DEALLOCATE PREPARE email_drop_statement;

SET @email_index_exists = (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND INDEX_NAME = 'idx_users_email'
);
SET @email_add_sql = IF(
    @email_index_exists = 0,
    'ALTER TABLE users ADD INDEX idx_users_email (email)',
    'SELECT 1'
);
PREPARE email_add_statement FROM @email_add_sql;
EXECUTE email_add_statement;
DEALLOCATE PREPARE email_add_statement;
