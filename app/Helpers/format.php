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

if (!function_exists('initials_from_name')) {
	function initials_from_name(?string $name, int $maxLength = 2): string
	{
		$value = trim((string) $name);
		if ($value === '') {
			return 'U';
		}

		$parts = preg_split('/\s+/', $value) ?: [];
		$initials = '';

		foreach ($parts as $part) {
			$clean = preg_replace('/[^A-Za-z0-9]/', '', $part) ?? '';
			if ($clean === '') {
				continue;
			}

			$initials .= strtoupper(substr($clean, 0, 1));
			if (strlen($initials) >= $maxLength) {
				break;
			}
		}

		if ($initials === '') {
			$clean = preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '';
			$initials = strtoupper(substr($clean, 0, $maxLength));
		}

		return $initials !== '' ? $initials : 'U';
	}
}

if (!function_exists('initials_avatar_data_uri')) {
	function initials_avatar_data_uri(?string $name, string $background = '#F5E8E8', string $foreground = '#7A0000'): string
	{
		$initials = initials_from_name($name);
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
			. '<rect width="100" height="100" rx="50" fill="' . $background . '"/>'
			. '<text x="50" y="52" text-anchor="middle" font-family="Manrope, Arial, sans-serif" font-size="40" font-weight="700" fill="' . $foreground . '" dominant-baseline="middle">'
			. $initials
			. '</text>'
			. '</svg>';

		return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
	}
}

if (!function_exists('profile_image_url')) {
	function profile_image_url(?string $value): string
	{
		$raw = trim((string) $value);
		if ($raw === '' || strtolower($raw) === 'default-avatar.png' || strtolower($raw) === '/assets/images/default-avatar.png') {
			return '';
		}

		if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
			return $raw;
		}

		$normalized = str_replace('\\', '/', $raw);
		if (str_contains($normalized, '..')) {
			return '';
		}

		if (str_starts_with($normalized, '/assets/images/')) {
			if (defined('BASE_PATH')) {
				$absolute = BASE_PATH . '/public' . $normalized;
				if (!is_file($absolute)) {
					return '';
				}
			}

			return $normalized;
		}

		if (str_starts_with($normalized, '/')) {
			return '';
		}

		$fileName = basename($normalized);
		if ($fileName === '') {
			return '';
		}

		$encodedName = rawurlencode($fileName);
		$candidate = '/assets/images/profiles/' . $encodedName;
		if (defined('BASE_PATH')) {
			$absolute = BASE_PATH . '/public' . $candidate;
			if (!is_file($absolute)) {
				return '';
			}
		}

		return $candidate;
	}
}
