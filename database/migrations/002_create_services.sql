CREATE TABLE IF NOT EXISTS services (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	title VARCHAR(140) NOT NULL,
	description TEXT NOT NULL,
	price DECIMAL(12,2) NOT NULL,
	status VARCHAR(20) NOT NULL DEFAULT 'active',
	created_at DATETIME NOT NULL,
	updated_at DATETIME NOT NULL,
	CONSTRAINT fk_services_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
	INDEX idx_services_user_id (user_id),
	INDEX idx_services_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
