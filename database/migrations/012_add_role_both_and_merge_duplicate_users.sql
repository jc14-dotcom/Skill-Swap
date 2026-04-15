SET @schema_name = DATABASE();

-- Normalize role values first so downstream checks/migrations are deterministic.
UPDATE users
SET role = LOWER(TRIM(role));

UPDATE users
SET role = 'seeker'
WHERE role NOT IN ('seeker', 'provider', 'both', 'admin');

-- Build high-confidence duplicate groups: same name + password hash with split role records.
DROP TEMPORARY TABLE IF EXISTS tmp_user_role_groups;
CREATE TEMPORARY TABLE tmp_user_role_groups (
	name_key VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
	password_hash VARCHAR(255) NOT NULL,
	primary_user_id BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (name_key, password_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_user_role_groups (name_key, password_hash, primary_user_id)
SELECT
	LOWER(TRIM(u.name)) COLLATE utf8mb4_unicode_ci AS name_key,
	u.password AS password_hash,
	MIN(u.id) AS primary_user_id
FROM users u
WHERE u.role IN ('seeker', 'provider', 'both')
GROUP BY LOWER(TRIM(u.name)) COLLATE utf8mb4_unicode_ci, u.password
HAVING COUNT(*) > 1
	AND (
		(
			SUM(CASE WHEN u.role = 'seeker' THEN 1 ELSE 0 END) > 0
			AND SUM(CASE WHEN u.role = 'provider' THEN 1 ELSE 0 END) > 0
		)
		OR (
			SUM(CASE WHEN u.role = 'both' THEN 1 ELSE 0 END) > 0
			AND (
				SUM(CASE WHEN u.role = 'seeker' THEN 1 ELSE 0 END) > 0
				OR SUM(CASE WHEN u.role = 'provider' THEN 1 ELSE 0 END) > 0
				OR SUM(CASE WHEN u.role = 'both' THEN 1 ELSE 0 END) > 1
			)
		)
	);

DROP TEMPORARY TABLE IF EXISTS tmp_user_role_merge_map;
CREATE TEMPORARY TABLE tmp_user_role_merge_map (
	primary_user_id BIGINT UNSIGNED NOT NULL,
	duplicate_user_id BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (duplicate_user_id),
	KEY idx_tmp_user_role_merge_primary (primary_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_user_role_merge_map (primary_user_id, duplicate_user_id)
SELECT
	g.primary_user_id,
	u.id
FROM users u
INNER JOIN tmp_user_role_groups g
	ON LOWER(TRIM(u.name)) COLLATE utf8mb4_unicode_ci = g.name_key
	AND u.password = g.password_hash
WHERE u.id <> g.primary_user_id
	AND u.role IN ('seeker', 'provider', 'both');

-- Avoid unique collisions when merged users already reviewed the same booking.
DELETE r_duplicate
FROM reviews r_duplicate
INNER JOIN tmp_user_role_merge_map m
	ON r_duplicate.reviewer_id = m.duplicate_user_id
INNER JOIN reviews r_existing
	ON r_existing.booking_id = r_duplicate.booking_id
	AND r_existing.reviewer_id = m.primary_user_id
	AND r_existing.id <> r_duplicate.id;

-- Re-point foreign keys from duplicate users to canonical users.
UPDATE services s
INNER JOIN tmp_user_role_merge_map m ON s.user_id = m.duplicate_user_id
SET s.user_id = m.primary_user_id;

UPDATE bookings b
INNER JOIN tmp_user_role_merge_map m ON b.seeker_id = m.duplicate_user_id
SET b.seeker_id = m.primary_user_id;

UPDATE bookings b
INNER JOIN tmp_user_role_merge_map m ON b.provider_id = m.duplicate_user_id
SET b.provider_id = m.primary_user_id;

UPDATE reviews r
INNER JOIN tmp_user_role_merge_map m ON r.reviewer_id = m.duplicate_user_id
SET r.reviewer_id = m.primary_user_id;

UPDATE reviews r
INNER JOIN tmp_user_role_merge_map m ON r.reviewee_id = m.duplicate_user_id
SET r.reviewee_id = m.primary_user_id;

UPDATE messages msg
INNER JOIN tmp_user_role_merge_map m ON msg.sender_id = m.duplicate_user_id
SET msg.sender_id = m.primary_user_id;

UPDATE messages msg
INNER JOIN tmp_user_role_merge_map m ON msg.receiver_id = m.duplicate_user_id
SET msg.receiver_id = m.primary_user_id;

UPDATE notifications n
INNER JOIN tmp_user_role_merge_map m ON n.user_id = m.duplicate_user_id
SET n.user_id = m.primary_user_id;

UPDATE notifications n
INNER JOIN tmp_user_role_merge_map m ON n.actor_id = m.duplicate_user_id
SET n.actor_id = m.primary_user_id;

UPDATE password_resets pr
INNER JOIN tmp_user_role_merge_map m ON pr.user_id = m.duplicate_user_id
SET pr.user_id = m.primary_user_id;

UPDATE booking_status_history bsh
INNER JOIN tmp_user_role_merge_map m ON bsh.changed_by = m.duplicate_user_id
SET bsh.changed_by = m.primary_user_id;

-- Canonical users in merged groups are explicitly both-role users.
UPDATE users u
INNER JOIN (
	SELECT DISTINCT primary_user_id
	FROM tmp_user_role_merge_map
) merged ON merged.primary_user_id = u.id
SET u.role = 'both',
	u.updated_at = NOW();

-- Remove duplicate user rows after all references are re-pointed.
DELETE u
FROM users u
INNER JOIN tmp_user_role_merge_map m ON u.id = m.duplicate_user_id;

-- Promote single-account users to both when they clearly act as both seeker and provider.
UPDATE users u
SET u.role = 'both',
	u.updated_at = NOW()
WHERE u.role IN ('seeker', 'provider')
	AND EXISTS (
		SELECT 1
		FROM bookings seeker_bookings
		WHERE seeker_bookings.seeker_id = u.id
	)
	AND (
		EXISTS (
			SELECT 1
			FROM services provider_services
			WHERE provider_services.user_id = u.id
		)
		OR EXISTS (
			SELECT 1
			FROM bookings provider_bookings
			WHERE provider_bookings.provider_id = u.id
		)
	);

SET @users_role_constraint = 'chk_users_role_allowed';
SELECT COUNT(*) INTO @users_role_exists
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = @schema_name
	AND TABLE_NAME = 'users'
	AND CONSTRAINT_NAME = @users_role_constraint;

SET @users_role_sql = IF(
	@users_role_exists = 0,
	'ALTER TABLE users ADD CONSTRAINT chk_users_role_allowed CHECK (role IN (''seeker'', ''provider'', ''both'', ''admin''))',
	'SELECT 1'
);
PREPARE users_role_stmt FROM @users_role_sql;
EXECUTE users_role_stmt;
DEALLOCATE PREPARE users_role_stmt;

DROP TEMPORARY TABLE IF EXISTS tmp_user_role_merge_map;
DROP TEMPORARY TABLE IF EXISTS tmp_user_role_groups;
