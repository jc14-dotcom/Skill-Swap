CREATE TABLE IF NOT EXISTS reviews (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	booking_id BIGINT UNSIGNED NOT NULL,
	service_id BIGINT UNSIGNED NOT NULL,
	reviewer_id BIGINT UNSIGNED NOT NULL,
	reviewee_id BIGINT UNSIGNED NOT NULL,
	rating TINYINT UNSIGNED NOT NULL,
	comment TEXT NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NOT NULL,
	CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
	CONSTRAINT fk_reviews_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
	CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
	CONSTRAINT fk_reviews_reviewee FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
	CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5),
	UNIQUE KEY uniq_reviews_booking_reviewer (booking_id, reviewer_id),
	INDEX idx_reviews_service_id (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
