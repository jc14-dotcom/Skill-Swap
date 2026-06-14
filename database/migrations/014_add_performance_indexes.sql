-- Additional performance indexes for common query patterns
-- Uses ALTER TABLE ... ADD INDEX which is more compatible

ALTER TABLE bookings ADD INDEX idx_bookings_provider_service (provider_id, service_id);
ALTER TABLE bookings ADD INDEX idx_bookings_status_created (status, created_at DESC);
ALTER TABLE reviews ADD INDEX idx_reviews_reviewer_id (reviewer_id);
ALTER TABLE reviews ADD INDEX idx_reviews_booking_reviewee (booking_id, reviewee_id);
ALTER TABLE services ADD INDEX idx_services_status_created (status, created_at DESC);
ALTER TABLE messages ADD INDEX idx_messages_receiver_read (receiver_id, read_at);
ALTER TABLE password_resets ADD INDEX idx_password_resets_user_used (user_id, used_at);
