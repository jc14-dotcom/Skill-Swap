SET @schema_name = DATABASE();

SET @bookings_constraint = 'chk_bookings_status_allowed';
SELECT COUNT(*) INTO @bookings_exists
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = @schema_name
  AND TABLE_NAME = 'bookings'
  AND CONSTRAINT_NAME = @bookings_constraint;

SET @bookings_sql = IF(
  @bookings_exists = 0,
  'ALTER TABLE bookings ADD CONSTRAINT chk_bookings_status_allowed CHECK (status IN (''pending'', ''accepted'', ''completed'', ''cancelled'', ''rejected''))',
  'SELECT 1'
);
PREPARE bookings_stmt FROM @bookings_sql;
EXECUTE bookings_stmt;
DEALLOCATE PREPARE bookings_stmt;

SET @services_constraint = 'chk_services_status_allowed';
SELECT COUNT(*) INTO @services_exists
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = @schema_name
  AND TABLE_NAME = 'services'
  AND CONSTRAINT_NAME = @services_constraint;

SET @services_sql = IF(
  @services_exists = 0,
  'ALTER TABLE services ADD CONSTRAINT chk_services_status_allowed CHECK (status IN (''active'', ''inactive''))',
  'SELECT 1'
);
PREPARE services_stmt FROM @services_sql;
EXECUTE services_stmt;
DEALLOCATE PREPARE services_stmt;
