<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Core\Session;

class AuthService
{
	private User $users;

	public function __construct()
	{
		$this->users = new User();
	}

	public function register(array $payload): array
	{
		$existing = $this->users->findByEmail((string) $payload['email']);
		if ($existing !== null) {
			throw new \RuntimeException('Email is already registered.');
		}

		$userId = $this->users->createUser([
			'name' => (string) $payload['name'],
			'email' => (string) $payload['email'],
			'password' => password_hash((string) $payload['password'], PASSWORD_DEFAULT),
			'role' => (string) ($payload['role'] ?? 'seeker'),
		]);

		$user = $this->users->find($userId);
		if (!is_array($user)) {
			throw new \RuntimeException('User registration failed.');
		}

		unset($user['password']);
		Session::put('auth.user', $user);
		Session::regenerate();

		return $user;
	}

	public function login(string $email, string $password): array
	{
		$user = $this->users->findByEmail($email);
		if (!is_array($user) || !password_verify($password, (string) ($user['password'] ?? ''))) {
			throw new \RuntimeException('Invalid email or password.');
		}

		unset($user['password']);
		Session::put('auth.user', $user);
		Session::regenerate();

		return $user;
	}

	public function logout(): void
	{
		Session::destroy();
	}
}
