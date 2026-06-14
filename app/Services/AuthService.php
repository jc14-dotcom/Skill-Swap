<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PasswordReset;
use App\Models\User;
use Core\App;
use Core\Database;
use Core\Session;

class AuthService
{
	private User $users;
	private PasswordReset $passwordResets;

	public function __construct(?User $users = null, ?PasswordReset $passwordResets = null)
	{
		$this->users = $users ?? new User();
		$this->passwordResets = $passwordResets ?? new PasswordReset();
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

	public function requestPasswordReset(string $email): ?string
	{
		$user = $this->users->findByEmail($email);
		if (!is_array($user)) {
			return null;
		}

		$token = bin2hex(random_bytes(32));
		$tokenHash = hash('sha256', $token);
		$expiresAt = date('Y-m-d H:i:s', time() + 3600);

		Database::transaction(function () use ($user, $tokenHash, $expiresAt): void {
			$this->passwordResets->invalidateActiveForUser((int) $user['id']);
			$this->passwordResets->createReset((int) $user['id'], $tokenHash, $expiresAt);
		});

		App::logger()->info('auth.password_reset.requested', [
			'user_id' => (int) $user['id'],
			'email' => (string) $user['email'],
			'expires_at' => $expiresAt,
		]);

		return $token;
	}

	public function hasValidPasswordResetToken(string $token): bool
	{
		$tokenHash = hash('sha256', $token);
		return is_array($this->passwordResets->findValidByTokenHash($tokenHash));
	}

	public function resetPassword(string $token, string $newPassword): void
	{
		$tokenHash = hash('sha256', $token);
		$reset = $this->passwordResets->findValidByTokenHash($tokenHash);
		if (!is_array($reset)) {
			throw new \RuntimeException('Reset token is invalid or expired.');
		}

		$userId = (int) ($reset['user_id'] ?? 0);
		if ($userId <= 0) {
			throw new \RuntimeException('Reset token is invalid.');
		}

		Database::transaction(function () use ($newPassword, $reset, $userId): void {
			$updated = $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
			if (!$updated) {
				throw new \RuntimeException('Password update failed.');
			}

			$this->passwordResets->markUsed((int) $reset['id']);
		});

		App::logger()->info('auth.password_reset.completed', [
			'user_id' => $userId,
		]);
	}
}
