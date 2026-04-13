<?php

declare(strict_types=1);

$toBool = static function (string $key, bool $default = false): bool {
	$value = getenv($key);
	if ($value === false) {
		return $default;
	}

	$bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	return $bool ?? $default;
};

return [
	'name' => getenv('APP_NAME') ?: 'SkillSwap',
	'env' => getenv('APP_ENV') ?: 'local',
	'debug' => $toBool('APP_DEBUG', true),
	'url' => getenv('APP_URL') ?: 'http://localhost',
	'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Manila',

	'session' => [
		'name' => getenv('SESSION_NAME') ?: 'SKILLSWAP_SESSION',
		'lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 7200),
		'path' => '/',
		'domain' => getenv('SESSION_DOMAIN') ?: '',
		'secure' => $toBool('SESSION_SECURE', false),
		'httponly' => true,
		'samesite' => getenv('SESSION_SAMESITE') ?: 'Lax',
	],

	'middleware_aliases' => [
		'auth' => 'App\\Middleware\\AuthMiddleware',
		'guest' => 'App\\Middleware\\GuestMiddleware',
		'csrf' => 'App\\Middleware\\CsrfMiddleware',
		'throttle' => 'App\\Middleware\\RateLimitMiddleware',
	],

	'rate_limits' => [
		'login' => [
			'max' => (int) (getenv('RATE_LIMIT_LOGIN_MAX') ?: 5),
			'window' => (int) (getenv('RATE_LIMIT_LOGIN_WINDOW') ?: 60),
		],
		'register' => [
			'max' => (int) (getenv('RATE_LIMIT_REGISTER_MAX') ?: 3),
			'window' => (int) (getenv('RATE_LIMIT_REGISTER_WINDOW') ?: 60),
		],
		'password_reset' => [
			'max' => (int) (getenv('RATE_LIMIT_PASSWORD_RESET_MAX') ?: 5),
			'window' => (int) (getenv('RATE_LIMIT_PASSWORD_RESET_WINDOW') ?: 300),
		],
		'default' => [
			'max' => (int) (getenv('RATE_LIMIT_DEFAULT_MAX') ?: 30),
			'window' => (int) (getenv('RATE_LIMIT_DEFAULT_WINDOW') ?: 60),
		],
	],
];
