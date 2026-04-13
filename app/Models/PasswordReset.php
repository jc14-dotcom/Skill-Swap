<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class PasswordReset extends Model
{
	protected string $table = 'password_resets';

	public function invalidateActiveForUser(int $userId): void
	{
		$this->query(
			'UPDATE password_resets
			 SET used_at = :used_at,
			     updated_at = :updated_at
			 WHERE user_id = :user_id
			   AND used_at IS NULL
			   AND expires_at >= :now',
			[
				'used_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_id' => $userId,
				'now' => date('Y-m-d H:i:s'),
			]
		);
	}

	public function createReset(int $userId, string $tokenHash, string $expiresAt): int
	{
		return $this->insert([
			'user_id' => $userId,
			'token_hash' => $tokenHash,
			'expires_at' => $expiresAt,
			'used_at' => null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function findValidByTokenHash(string $tokenHash): ?array
	{
		$row = $this->query(
			'SELECT pr.*
			 FROM password_resets pr
			 WHERE pr.token_hash = :token_hash
			   AND pr.used_at IS NULL
			   AND pr.expires_at >= :now
			 LIMIT 1',
			[
				'token_hash' => $tokenHash,
				'now' => date('Y-m-d H:i:s'),
			]
		)->fetch();

		return is_array($row) ? $row : null;
	}

	public function markUsed(int $id): bool
	{
		return $this->updateById($id, [
			'used_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}
}
