CREATE TABLE IF NOT EXISTS bookings (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	service_id BIGINT UNSIGNED NOT NULL,
	seeker_id BIGINT UNSIGNED NOT NULL,
	provider_id BIGINT UNSIGNED NOT NULL,
	status VARCHAR(20) NOT NULL DEFAULT 'pending',
	message TEXT NULL,
	scheduled_at DATETIME NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NOT NULL,
	CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
	CONSTRAINT fk_bookings_seeker FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE,
	CONSTRAINT fk_bookings_provider FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE,
	INDEX idx_bookings_service_id (service_id),
	INDEX idx_bookings_seeker_id (seeker_id),
	INDEX idx_bookings_provider_id (provider_id),
	INDEX idx_bookings_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
