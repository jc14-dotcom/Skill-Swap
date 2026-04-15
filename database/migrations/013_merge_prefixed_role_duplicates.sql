SET @schema_name = DATABASE();

-- Detect prefixed duplicate identities such as provider_1234 / seeker_1234 (or provider.1234 / seeker.1234).
DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_derived;
CREATE TEMPORARY TABLE tmp_prefixed_role_derived (
	user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
	role VARCHAR(20) NOT NULL,
	token_key VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL,
	domain_key VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL,
	KEY idx_tmp_prefixed_role_token_domain (token_key, domain_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_prefixed_role_derived (user_id, role, token_key, domain_key)
SELECT
	u.id AS user_id,
	u.role,
	LOWER(
		TRIM(BOTH '._- ' FROM (
			CASE
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'provider.%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 10)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'provider_%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 10)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'provider-%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 10)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'provider%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 9)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'seeker.%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 8)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'seeker_%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 8)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'seeker-%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 8)
				WHEN LOWER(SUBSTRING_INDEX(u.email, '@', 1)) LIKE 'seeker%' THEN SUBSTRING(LOWER(SUBSTRING_INDEX(u.email, '@', 1)), 7)
				ELSE ''
			END
		))
	) COLLATE utf8mb4_unicode_ci AS token_key,
	LOWER(SUBSTRING_INDEX(u.email, '@', -1)) COLLATE utf8mb4_unicode_ci AS domain_key
FROM users u
WHERE u.role IN ('seeker', 'provider', 'both')
	AND u.email LIKE '%@%';

DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_groups;
CREATE TEMPORARY TABLE tmp_prefixed_role_groups (
	token_key VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL,
	domain_key VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL,
	primary_user_id BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (token_key, domain_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_prefixed_role_groups (token_key, domain_key, primary_user_id)
SELECT
	d.token_key,
	d.domain_key,
	MIN(d.user_id) AS primary_user_id
FROM tmp_prefixed_role_derived d
WHERE d.token_key <> ''
	AND CHAR_LENGTH(d.token_key) >= 4
	AND d.token_key REGEXP '[0-9]'
GROUP BY d.token_key, d.domain_key
HAVING COUNT(*) > 1
	AND SUM(CASE WHEN d.role = 'provider' THEN 1 ELSE 0 END) > 0
	AND SUM(CASE WHEN d.role = 'seeker' THEN 1 ELSE 0 END) > 0;

DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_merge_map;
CREATE TEMPORARY TABLE tmp_prefixed_role_merge_map (
	primary_user_id BIGINT UNSIGNED NOT NULL,
	duplicate_user_id BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY (duplicate_user_id),
	KEY idx_tmp_prefixed_role_merge_primary (primary_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_prefixed_role_merge_map (primary_user_id, duplicate_user_id)
SELECT
	g.primary_user_id,
	d.user_id
FROM tmp_prefixed_role_derived d
INNER JOIN tmp_prefixed_role_groups g
	ON d.token_key = g.token_key
	AND d.domain_key = g.domain_key
WHERE d.user_id <> g.primary_user_id;

-- Prevent unique key collisions in reviews before foreign-key rewiring.
DELETE r_duplicate
FROM reviews r_duplicate
INNER JOIN tmp_prefixed_role_merge_map m
	ON r_duplicate.reviewer_id = m.duplicate_user_id
INNER JOIN reviews r_existing
	ON r_existing.booking_id = r_duplicate.booking_id
	AND r_existing.reviewer_id = m.primary_user_id
	AND r_existing.id <> r_duplicate.id;

UPDATE services s
INNER JOIN tmp_prefixed_role_merge_map m ON s.user_id = m.duplicate_user_id
SET s.user_id = m.primary_user_id;

UPDATE bookings b
INNER JOIN tmp_prefixed_role_merge_map m ON b.seeker_id = m.duplicate_user_id
SET b.seeker_id = m.primary_user_id;

UPDATE bookings b
INNER JOIN tmp_prefixed_role_merge_map m ON b.provider_id = m.duplicate_user_id
SET b.provider_id = m.primary_user_id;

UPDATE reviews r
INNER JOIN tmp_prefixed_role_merge_map m ON r.reviewer_id = m.duplicate_user_id
SET r.reviewer_id = m.primary_user_id;

UPDATE reviews r
INNER JOIN tmp_prefixed_role_merge_map m ON r.reviewee_id = m.duplicate_user_id
SET r.reviewee_id = m.primary_user_id;

UPDATE messages msg
INNER JOIN tmp_prefixed_role_merge_map m ON msg.sender_id = m.duplicate_user_id
SET msg.sender_id = m.primary_user_id;

UPDATE messages msg
INNER JOIN tmp_prefixed_role_merge_map m ON msg.receiver_id = m.duplicate_user_id
SET msg.receiver_id = m.primary_user_id;

UPDATE notifications n
INNER JOIN tmp_prefixed_role_merge_map m ON n.user_id = m.duplicate_user_id
SET n.user_id = m.primary_user_id;

UPDATE notifications n
INNER JOIN tmp_prefixed_role_merge_map m ON n.actor_id = m.duplicate_user_id
SET n.actor_id = m.primary_user_id;

UPDATE password_resets pr
INNER JOIN tmp_prefixed_role_merge_map m ON pr.user_id = m.duplicate_user_id
SET pr.user_id = m.primary_user_id;

UPDATE booking_status_history bsh
INNER JOIN tmp_prefixed_role_merge_map m ON bsh.changed_by = m.duplicate_user_id
SET bsh.changed_by = m.primary_user_id;

UPDATE users u
INNER JOIN (
	SELECT DISTINCT primary_user_id
	FROM tmp_prefixed_role_merge_map
) merged ON merged.primary_user_id = u.id
SET u.role = 'both',
	u.updated_at = NOW();

DELETE u
FROM users u
INNER JOIN tmp_prefixed_role_merge_map m ON u.id = m.duplicate_user_id;

DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_merge_map;
DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_groups;
DROP TEMPORARY TABLE IF EXISTS tmp_prefixed_role_derived;
