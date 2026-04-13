<?php

declare(strict_types=1);

if (!function_exists('format_currency')) {
	function format_currency(float|int|string $value, string $currency = 'PHP'): string
	{
		return $currency . ' ' . number_format((float) $value, 2);
	}
}

if (!function_exists('format_datetime')) {
	function format_datetime(?string $value, string $format = 'Y-m-d H:i'): string
	{
		if ($value === null || $value === '') {
			return '-';
		}

		$timestamp = strtotime($value);
		if ($timestamp === false) {
			return $value;
		}

		return date($format, $timestamp);
	}
}

if (!function_exists('profile_image_url')) {
	function profile_image_url(?string $value): string
	{
		$default = '/assets/images/default-avatar.png';
		$defaultAbsolute = defined('BASE_PATH') ? BASE_PATH . '/public' . $default : null;
		$raw = trim((string) $value);

		if ($raw === '' || strtolower($raw) === 'default-avatar.png') {
			return $default;
		}

		if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
			return $raw;
		}

		$normalized = str_replace('\\', '/', $raw);
		if (str_contains($normalized, '..')) {
			return $default;
		}

		if (str_starts_with($normalized, '/assets/images/')) {
			if (is_string($defaultAbsolute)) {
				$absolute = BASE_PATH . '/public' . $normalized;
				if (!is_file($absolute)) {
					return $default;
				}
			}

			return $normalized;
		}

		if (str_starts_with($normalized, '/')) {
			return $default;
		}

		$fileName = basename($normalized);
		if ($fileName === '') {
			return $default;
		}

		$candidate = '/assets/images/profiles/' . rawurlencode($fileName);

		if (is_string($defaultAbsolute)) {
			$candidateAbsolute = BASE_PATH . '/public' . $candidate;
			if (!is_file($candidateAbsolute)) {
				return $default;
			}
		}

		return $candidate;
	}
}
