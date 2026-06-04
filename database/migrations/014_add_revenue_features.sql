ALTER TABLE services
	ADD COLUMN is_boosted TINYINT(1) NOT NULL DEFAULT 0,
	ADD COLUMN boost_started_at DATETIME NULL,
	ADD COLUMN boost_expires_at DATETIME NULL;

ALTER TABLE bookings
	ADD COLUMN service_price DECIMAL(12,2) NOT NULL DEFAULT 0,
	ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
	ADD COLUMN payment_method VARCHAR(30) NULL,
	ADD COLUMN payment_reference VARCHAR(80) NULL,
	ADD COLUMN paid_at DATETIME NULL;

CREATE TABLE IF NOT EXISTS revenue_transactions (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	booking_id BIGINT UNSIGNED NULL,
	service_id BIGINT UNSIGNED NULL,
	type VARCHAR(30) NOT NULL,
	amount DECIMAL(12,2) NOT NULL,
	description VARCHAR(200) NULL,
	created_at DATETIME NOT NULL,
	CONSTRAINT fk_revenue_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
	CONSTRAINT fk_revenue_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
	INDEX idx_revenue_booking (booking_id),
	INDEX idx_revenue_service (service_id),
	INDEX idx_revenue_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
