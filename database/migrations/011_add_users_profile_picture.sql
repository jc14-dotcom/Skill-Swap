SET @schema_name = DATABASE();

SET @users_profile_picture_column = 'profile_picture';
SELECT COUNT(*) INTO @users_profile_picture_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @schema_name
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME = @users_profile_picture_column;

SET @users_profile_picture_sql = IF(
  @users_profile_picture_exists = 0,
  'ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER role',
  'SELECT 1'
);
PREPARE users_profile_picture_stmt FROM @users_profile_picture_sql;
EXECUTE users_profile_picture_stmt;
DEALLOCATE PREPARE users_profile_picture_stmt;