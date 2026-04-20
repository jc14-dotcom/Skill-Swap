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

if (!function_exists('asset_image_url')) {
	function asset_image_url(?string $value, string $default = '/assets/images/default-image.svg', string $baseDirectory = '/assets/images/'): string
	{
		$raw = trim((string) $value);
		$defaultPath = trim($default);

		if ($defaultPath === '' || !str_starts_with($defaultPath, '/assets/images/')) {
			$defaultPath = '/assets/images/default-image.svg';
		}

		if ($raw === '') {
			return $defaultPath;
		}

		if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
			return $raw;
		}

		$normalized = str_replace('\\', '/', $raw);
		if (str_contains($normalized, '..')) {
			return $defaultPath;
		}

		if (str_starts_with($normalized, '/')) {
			if (!str_starts_with($normalized, '/assets/images/')) {
				return $defaultPath;
			}

			if (defined('BASE_PATH')) {
				$absolute = BASE_PATH . '/public' . $normalized;
				if (!is_file($absolute)) {
					return $defaultPath;
				}
			}

			return $normalized;
		}

		$base = '/' . trim(str_replace('\\', '/', $baseDirectory), '/') . '/';
		if (!str_starts_with($base, '/assets/images/')) {
			$base = '/assets/images/';
		}

		$parts = array_values(array_filter(explode('/', ltrim($normalized, '/')), static fn (string $part): bool => $part !== ''));
		if ($parts === []) {
			return $defaultPath;
		}

		$encodedParts = array_map(static fn (string $part): string => rawurlencode($part), $parts);
		$candidate = $base . implode('/', $encodedParts);

		if (!str_starts_with($candidate, '/assets/images/')) {
			return $defaultPath;
		}

		if (defined('BASE_PATH')) {
			$candidateAbsolute = BASE_PATH . '/public' . $candidate;
			if (!is_file($candidateAbsolute)) {
				return $defaultPath;
			}
		}

		return $candidate;
	}
}

if (!function_exists('profile_image_url')) {
	function profile_image_url(?string $value): string
	{
		$default = '/assets/images/default-avatar.png';
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
			return asset_image_url($normalized, $default, '/assets/images/');
		}

		if (str_starts_with($normalized, '/')) {
			return $default;
		}

		$fileName = basename($normalized);
		if ($fileName === '') {
			return $default;
		}

		return asset_image_url($fileName, $default, '/assets/images/profiles/');
	}
}
