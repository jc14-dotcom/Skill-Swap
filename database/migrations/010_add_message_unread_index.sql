SET @schema_name = DATABASE();

SET @messages_unread_index = 'idx_messages_booking_receiver_read';
SELECT COUNT(*) INTO @messages_unread_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @schema_name
  AND TABLE_NAME = 'messages'
  AND INDEX_NAME = @messages_unread_index;

SET @messages_unread_sql = IF(
  @messages_unread_exists = 0,
  'ALTER TABLE messages ADD INDEX idx_messages_booking_receiver_read (booking_id, receiver_id, read_at)',
  'SELECT 1'
);
PREPARE messages_unread_stmt FROM @messages_unread_sql;
EXECUTE messages_unread_stmt;
DEALLOCATE PREPARE messages_unread_stmt;