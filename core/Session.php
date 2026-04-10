<?php

declare(strict_types=1);

namespace Core;

class Session
{
	private static bool $started = false;

	public static function start(array $config = []): void
	{
		if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
			self::$started = true;
			return;
		}

		$defaults = [
			'name' => 'SKILLSWAP_SESSION',
			'lifetime' => 0,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Lax',
		];

		$settings = array_merge($defaults, $config);

		session_name((string) $settings['name']);
		session_set_cookie_params([
			'lifetime' => (int) $settings['lifetime'],
			'path' => (string) $settings['path'],
			'domain' => (string) $settings['domain'],
			'secure' => (bool) $settings['secure'],
			'httponly' => (bool) $settings['httponly'],
			'samesite' => (string) $settings['samesite'],
		]);

		session_start();
		self::$started = true;

		self::ageFlashData();

		if (!isset($_SESSION['_csrf_token'])) {
			$_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
		}
	}

	public static function get(string $key, mixed $default = null): mixed
	{
		$value = self::arrayGet($_SESSION, $key, null);
		if ($value !== null) {
			return $value;
		}

		return self::arrayGet($_SESSION['_flash'] ?? [], $key, $default);
	}

	public static function put(string $key, mixed $value): void
	{
		self::arraySet($_SESSION, $key, $value);
	}

	public static function has(string $key): bool
	{
		return self::arrayGet($_SESSION, $key, null) !== null;
	}

	public static function forget(string $key): void
	{
		$segments = explode('.', $key);
		$target = &$_SESSION;

		while (count($segments) > 1) {
			$segment = array_shift($segments);
			if (!isset($target[$segment]) || !is_array($target[$segment])) {
				return;
			}
			$target = &$target[$segment];
		}

		unset($target[array_shift($segments)]);
	}

	public static function flash(string $key, mixed $value): void
	{
		self::arraySet($_SESSION['_flash_next'], $key, $value);
	}

	public static function old(string $key, mixed $default = ''): mixed
	{
		return self::arrayGet($_SESSION['_flash']['old'] ?? [], $key, $default);
	}

	public static function errors(): array
	{
		$errors = $_SESSION['_flash']['errors'] ?? [];
		return is_array($errors) ? $errors : [];
	}

	public static function token(): string
	{
		return (string) ($_SESSION['_csrf_token'] ?? '');
	}

	public static function regenerate(): void
	{
		session_regenerate_id(true);
	}

	public static function destroy(): void
	{
		$_SESSION = [];

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
		}

		session_destroy();
	}

	private static function ageFlashData(): void
	{
		$_SESSION['_flash'] = $_SESSION['_flash_next'] ?? [];
		unset($_SESSION['_flash_next']);
	}

	private static function arrayGet(array $source, string $key, mixed $default = null): mixed
	{
		$segments = explode('.', $key);
		$value = $source;

		foreach ($segments as $segment) {
			if (!is_array($value) || !array_key_exists($segment, $value)) {
				return $default;
			}

			$value = $value[$segment];
		}

		return $value;
	}

	private static function arraySet(array &$source, string $key, mixed $value): void
	{
		$segments = explode('.', $key);
		$target = &$source;

		foreach ($segments as $segment) {
			if (!isset($target[$segment]) || !is_array($target[$segment])) {
				$target[$segment] = [];
			}

			$target = &$target[$segment];
		}

		$target = $value;
	}
}
