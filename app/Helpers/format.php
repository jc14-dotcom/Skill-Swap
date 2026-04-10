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
