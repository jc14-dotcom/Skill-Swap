<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;

class UserSeeder
{
	public function run(): void
	{
		$environment = strtolower((string) (getenv('APP_ENV') ?: 'local'));
		if (!in_array($environment, ['local', 'testing'], true)) {
			throw new \RuntimeException('Demo users may only be seeded in local or testing environments.');
		}

		$demoPassword = (string) (getenv('SEED_DEMO_PASSWORD') ?: '');
		if ($demoPassword === '') {
			throw new \RuntimeException('SEED_DEMO_PASSWORD must be set before seeding demo users.');
		}

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
				'password' => password_hash($demoPassword, PASSWORD_DEFAULT),
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
				'password' => password_hash($demoPassword, PASSWORD_DEFAULT),
				'role' => 'seeker',
				'created_at' => $now,
				'updated_at' => $now,
			]
		);
	}
}
