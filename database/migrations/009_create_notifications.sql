CREATE TABLE IF NOT EXISTS notifications (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	actor_id BIGINT UNSIGNED NULL,
	type VARCHAR(80) NOT NULL,
	title VARCHAR(180) NOT NULL,
	body TEXT NULL,
	link VARCHAR(255) NULL,
	meta_json TEXT NULL,
	read_at DATETIME NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NOT NULL,
	CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
	CONSTRAINT fk_notifications_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL,
	INDEX idx_notifications_user_id (user_id),
	INDEX idx_notifications_user_read_at (user_id, read_at),
	INDEX idx_notifications_type (type),
	INDEX idx_notifications_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;