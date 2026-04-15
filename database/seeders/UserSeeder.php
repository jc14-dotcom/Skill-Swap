<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;

class UserSeeder
{
	public function run(): void
	{
		$now = date('Y-m-d H:i:s');

		Database::query(
			'INSERT INTO users (name, email, password, role, created_at, updated_at)
			 VALUES (:name, :email, :password, :role, :created_at, :updated_at)
			 ON DUPLICATE KEY UPDATE
			 	name = VALUES(name),
			 	password = VALUES(password),
			 	role = VALUES(role),
			 	updated_at = VALUES(updated_at)',
			[
				'name' => 'Hybrid Demo',
				'email' => 'provider@example.com',
				'password' => password_hash('[removed-demo-password]', PASSWORD_DEFAULT),
				'role' => 'both',
				'created_at' => $now,
				'updated_at' => $now,
			]
		);

		Database::query(
			'INSERT INTO users (name, email, password, role, created_at, updated_at)
			 VALUES (:name, :email, :password, :role, :created_at, :updated_at)
			 ON DUPLICATE KEY UPDATE
			 	name = VALUES(name),
			 	password = VALUES(password),
			 	role = VALUES(role),
			 	updated_at = VALUES(updated_at)',
			[
				'name' => 'Seeker Demo',
				'email' => 'seeker@example.com',
				'password' => password_hash('[removed-demo-password]', PASSWORD_DEFAULT),
				'role' => 'seeker',
				'created_at' => $now,
				'updated_at' => $now,
			]
		);
	}
}
