<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;

class ServiceSeeder
{
	public function run(): void
	{
		$provider = Database::query('SELECT id FROM users WHERE email = :email LIMIT 1', [
			'email' => 'provider@example.com',
		])->fetch();

		if (!is_array($provider)) {
			return;
		}

		$now = date('Y-m-d H:i:s');

		$existing = Database::query(
			'SELECT id FROM services WHERE user_id = :user_id AND title = :title LIMIT 1',
			[
				'user_id' => (int) $provider['id'],
				'title' => 'Basic Graphic Design',
			]
		)->fetch();

		if (is_array($existing)) {
			return;
		}

		Database::query(
			'INSERT INTO services (user_id, title, description, price, status, created_at, updated_at)
			 VALUES (:user_id, :title, :description, :price, :status, :created_at, :updated_at)',
			[
				'user_id' => (int) $provider['id'],
				'title' => 'Basic Graphic Design',
				'description' => 'Logo and simple brand kit for early-stage projects.',
				'price' => 1500,
				'status' => 'active',
				'created_at' => $now,
				'updated_at' => $now,
			]
		);
	}
}
