<?php

declare(strict_types=1);

use Core\Session;

if (!function_exists('session_get')) {
	function session_get(string $key, mixed $default = null): mixed
	{
		return Session::get($key, $default);
	}
}

if (!function_exists('session_put')) {
	function session_put(string $key, mixed $value): void
	{
		Session::put($key, $value);
	}
}

if (!function_exists('flash_message')) {
	function flash_message(string $key, mixed $value): void
	{
		Session::flash($key, $value);
	}
}

if (!function_exists('old_input')) {
	function old_input(string $key, mixed $default = ''): mixed
	{
		return Session::old($key, $default);
	}
}

if (!function_exists('form_errors')) {
	function form_errors(?string $field = null): array|string
	{
		$errors = Session::errors();
		if ($field === null) {
			return $errors;
		}

		$messages = $errors[$field] ?? [];
		return $messages[0] ?? '';
	}
}
