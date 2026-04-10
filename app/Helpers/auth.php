<?php

declare(strict_types=1);

use Core\Session;

if (!function_exists('auth_user')) {
	function auth_user(): ?array
	{
		$user = Session::get('auth.user');
		return is_array($user) ? $user : null;
	}
}

if (!function_exists('auth_check')) {
	function auth_check(): bool
	{
		return auth_user() !== null;
	}
}

if (!function_exists('auth_id')) {
	function auth_id(): ?int
	{
		$user = auth_user();
		return $user ? (int) $user['id'] : null;
	}
}

if (!function_exists('auth_role')) {
	function auth_role(): string
	{
		$user = auth_user();
		return (string) ($user['role'] ?? 'guest');
	}
}
