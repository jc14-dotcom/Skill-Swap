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
}
