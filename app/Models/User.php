<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class User extends Model
{
	protected string $table = 'users';

	public function findByEmail(string $email): ?array
	{
		return $this->firstWhere('email', $email);
	}

	public function createUser(array $payload): int
	{
		return $this->insert([
			'name' => $payload['name'],
			'email' => $payload['email'],
			'password' => $payload['password'],
			'role' => $payload['role'] ?? 'seeker',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function updatePassword(int $userId, string $passwordHash): bool
	{
		return $this->updateById($userId, [
			'password' => $passwordHash,
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function emailTakenByAnother(string $email, int $userId): bool
	{
		$row = $this->query(
			'SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1',
			[
				'email' => $email,
				'id' => $userId,
			]
		)->fetch();

		return is_array($row);
	}

	public function updateProfile(int $userId, array $payload): bool
	{
		$attributes = [
			'name' => $payload['name'],
			'email' => $payload['email'],
			'updated_at' => date('Y-m-d H:i:s'),
		];

		if (array_key_exists('profile_picture', $payload)) {
			$attributes['profile_picture'] = $payload['profile_picture'];
		}

		if (isset($payload['password_hash']) && is_string($payload['password_hash']) && $payload['password_hash'] !== '') {
			$attributes['password'] = $payload['password_hash'];
		}

		return $this->updateById($userId, $attributes);
	}
}
