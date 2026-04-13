CREATE TABLE IF NOT EXISTS booking_status_history (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	booking_id BIGINT UNSIGNED NOT NULL,
	from_status VARCHAR(20) NULL,
	to_status VARCHAR(20) NOT NULL,
	changed_by BIGINT UNSIGNED NULL,
	created_at DATETIME NOT NULL,
	CONSTRAINT fk_booking_status_history_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
	CONSTRAINT fk_booking_status_history_actor FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
	CONSTRAINT chk_booking_status_history_to_status CHECK (to_status IN ('pending', 'accepted', 'completed', 'cancelled', 'rejected')),
	INDEX idx_booking_status_history_booking_id (booking_id),
	INDEX idx_booking_status_history_changed_by (changed_by),
	INDEX idx_booking_status_history_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
