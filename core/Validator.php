<?php

declare(strict_types=1);

namespace Core;

class Validator
{
	/** @var array<string, callable> */
	private static array $customRules = [];

	/**
	 * Register a custom validation rule.
	 *
	 * @param callable(mixed, ?string, array<string, mixed>): bool $handler Receives ($value, $params, $data) and returns true if valid
	 */
	public static function extend(string $name, callable $handler): void
	{
		self::$customRules[$name] = $handler;
	}

	/**
	 * @param array<string, mixed> $data
	 * @param array<string, string|array<int, string>> $rules
	 * @return array{0: bool, 1: array<string, array<int, string>>, 2: array<string, mixed>}
	 */
	public static function validate(array $data, array $rules): array
	{
		$errors = [];
		$clean = [];

		foreach ($rules as $field => $fieldRules) {
			$ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
			$value = $data[$field] ?? null;

			if (is_string($value)) {
				$value = trim($value);
			}

			$isNullable = in_array('nullable', $ruleList, true);
			$isRequired = in_array('required', $ruleList, true);

			if (($value === null || $value === '') && !$isRequired && $isNullable) {
				$clean[$field] = null;
				continue;
			}

			foreach ($ruleList as $rule) {
				$name = $rule;
				$params = null;

				if (str_contains($rule, ':')) {
					[$name, $params] = explode(':', $rule, 2);
				}

				$name = trim($name);

				if ($name === 'nullable') {
					continue;
				}

				if ($name === 'required') {
					if ($value === null || $value === '') {
						$errors[$field][] = ucfirst($field) . ' is required.';
					}
					continue;
				}

				if (($value === null || $value === '') && !$isRequired) {
					continue;
				}

				$fieldName = ucfirst(str_replace('_', ' ', $field));

				$valid = match ($name) {
					'string' => self::validateString($value),
					'email' => self::validateEmail($value),
					'integer' => self::validateInteger($value),
					'numeric' => self::validateNumeric($value),
					'url' => self::validateUrl($value),
					'alpha' => self::validateAlpha($value),
					'alpha_num' => self::validateAlphaNum($value),
					'alpha_dash' => self::validateAlphaDash($value),
					'date' => self::validateDate($value),
					'date_format' => self::validateDateFormat($value, $params),
					'before' => self::validateBefore($value, $params),
					'after' => self::validateAfter($value, $params),
					'boolean' => self::validateBoolean($value),
					'json' => self::validateJson($value),
					'array' => self::validateArray($value),
					'min' => self::validateMin($value, $params),
					'max' => self::validateMax($value, $params),
					'size' => self::validateSize($value, $params),
					'in' => self::validateIn($value, $params),
					'not_in' => self::validateNotIn($value, $params),
					'same' => self::validateSame($value, $data[$params] ?? null, $params),
					'confirmed' => self::validateConfirmed($value, $data[$field . '_confirmation'] ?? null),
					'different' => self::validateDifferent($value, $data[$params] ?? null, $params),
					'accepted' => self::validateAccepted($value),
					'active_url' => self::validateActiveUrl($value),
					'ip' => self::validateIp($value),
					'ipv4' => self::validateIpv4($value),
					'ipv6' => self::validateIpv6($value),
					'uuid' => self::validateUuid($value),
					'filled' => self::validateFilled($value),
					default => self::validateCustom($name, $value, $params, $data),
				};

				if ($valid === false) {
					$errors[$field][] = self::resolveErrorMessage($name, $fieldName, $params);
				} elseif (is_string($valid)) {
					$errors[$field][] = $valid;
				}
			}

			$clean[$field] = $value;
		}

		return [count($errors) === 0, $errors, $clean];
	}

	private static function validateString(mixed $value): bool
	{
		return is_string($value);
	}

	private static function validateEmail(mixed $value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}

	private static function validateInteger(mixed $value): bool
	{
		return filter_var($value, FILTER_VALIDATE_INT) !== false;
	}

	private static function validateNumeric(mixed $value): bool
	{
		return is_numeric((string) $value);
	}

	private static function validateUrl(mixed $value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false;
	}

	private static function validateAlpha(mixed $value): bool
	{
		return is_string($value) && preg_match('/^[a-zA-Z]+$/', $value) === 1;
	}

	private static function validateAlphaNum(mixed $value): bool
	{
		return is_string($value) && preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
	}

	private static function validateAlphaDash(mixed $value): bool
	{
		return is_string($value) && preg_match('/^[a-zA-Z0-9_\-]+$/', $value) === 1;
	}

	private static function validateDate(mixed $value): bool
	{
		if (!is_string($value)) {
			return false;
		}
		$timestamp = strtotime($value);
		return $timestamp !== false;
	}

	private static function validateDateFormat(mixed $value, ?string $format): bool
	{
		if (!is_string($value) || $format === null) {
			return false;
		}
		$decoded = date_create_from_format($format, $value);
		return $decoded !== false && $decoded->format($format) === $value;
	}

	private static function validateBefore(mixed $value, ?string $compare): bool
	{
		if (!is_string($value) || $compare === null) {
			return false;
		}
		$timestamp = strtotime($value);
		$compareTimestamp = $compare === 'today' ? strtotime('today') : strtotime($compare);
		if ($timestamp === false || $compareTimestamp === false) {
			return false;
		}
		return $timestamp < $compareTimestamp;
	}

	private static function validateAfter(mixed $value, ?string $compare): bool
	{
		if (!is_string($value) || $compare === null) {
			return false;
		}
		$timestamp = strtotime($value);
		$compareTimestamp = $compare === 'today' ? strtotime('today') : strtotime($compare);
		if ($timestamp === false || $compareTimestamp === false) {
			return false;
		}
		return $timestamp > $compareTimestamp;
	}

	private static function validateBoolean(mixed $value): bool
	{
		return in_array($value, [true, false, 0, 1, '0', '1', 'true', 'false'], true);
	}

	private static function validateJson(mixed $value): bool
	{
		if (!is_string($value)) {
			return false;
		}
		json_decode($value);
		return json_last_error() === JSON_ERROR_NONE;
	}

	private static function validateArray(mixed $value): bool
	{
		return is_array($value);
	}

	private static function validateMin(mixed $value, ?string $param): bool
	{
		if ($param === null) {
			return true;
		}
		$min = (int) $param;
		if (is_string($value)) {
			return mb_strlen($value) >= $min;
		}
		if (is_numeric($value)) {
			return (float) $value >= $min;
		}
		if (is_array($value)) {
			return count($value) >= $min;
		}
		return true;
	}

	private static function validateMax(mixed $value, ?string $param): bool
	{
		if ($param === null) {
			return true;
		}
		$max = (int) $param;
		if (is_string($value)) {
			return mb_strlen($value) <= $max;
		}
		if (is_numeric($value)) {
			return (float) $value <= $max;
		}
		if (is_array($value)) {
			return count($value) <= $max;
		}
		return true;
	}

	private static function validateSize(mixed $value, ?string $param): bool
	{
		if ($param === null) {
			return true;
		}
		$size = (int) $param;
		if (is_string($value)) {
			return mb_strlen($value) === $size;
		}
		if (is_numeric($value)) {
			return (float) $value === (float) $size;
		}
		if (is_array($value)) {
			return count($value) === $size;
		}
		return true;
	}

	private static function validateIn(mixed $value, ?string $param): bool
	{
		$allowed = array_map('trim', explode(',', (string) $param));
		return in_array((string) $value, $allowed, true);
	}

	private static function validateNotIn(mixed $value, ?string $param): bool
	{
		$forbidden = array_map('trim', explode(',', (string) $param));
		return !in_array((string) $value, $forbidden, true);
	}

	private static function validateSame(mixed $value, mixed $other, ?string $field): bool
	{
		return $field !== null && $value === $other;
	}

	private static function validateConfirmed(mixed $value, mixed $confirmation): bool
	{
		return $value === $confirmation;
	}

	private static function validateDifferent(mixed $value, mixed $other, ?string $field): bool
	{
		return $field !== null && $value !== $other;
	}

	private static function validateAccepted(mixed $value): bool
	{
		return in_array($value, ['yes', 'on', '1', 1, true], true);
	}

	private static function validateActiveUrl(mixed $value): bool
	{
		if (!is_string($value)) {
			return false;
		}
		$host = parse_url($value, PHP_URL_HOST);
		return $host !== null && checkdnsrr($host);
	}

	private static function validateIp(mixed $value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_IP) !== false;
	}

	private static function validateIpv4(mixed $value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
	}

	private static function validateIpv6(mixed $value): bool
	{
		return is_string($value) && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
	}

	private static function validateUuid(mixed $value): bool
	{
		return is_string($value) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value) === 1;
	}

	private static function validateFilled(mixed $value): bool
	{
		return $value !== null && $value !== '';
	}

	private static function validateCustom(string $name, mixed $value, ?string $params, array $data): bool|string
	{
		if (!isset(self::$customRules[$name])) {
			return true;
		}

		$handler = self::$customRules[$name];
		$result = $handler($value, $params, $data);

		if ($result === true) {
			return true;
		}

		if (is_string($result)) {
			return $result;
		}

		return false;
	}

	private static function resolveErrorMessage(string $rule, string $field, ?string $param): string
	{
		return match ($rule) {
			'string' => "{$field} must be a string.",
			'email' => "{$field} must be a valid email address.",
			'integer' => "{$field} must be an integer.",
			'numeric' => "{$field} must be numeric.",
			'url' => "{$field} must be a valid URL.",
			'alpha' => "{$field} must contain only letters.",
			'alpha_num' => "{$field} must contain only letters and numbers.",
			'alpha_dash' => "{$field} must contain only letters, numbers, dashes, and underscores.",
			'date' => "{$field} must be a valid date.",
			'date_format' => "{$field} must match the format {$param}.",
			'before' => "{$field} must be before {$param}.",
			'after' => "{$field} must be after {$param}.",
			'boolean' => "{$field} must be true or false.",
			'json' => "{$field} must be valid JSON.",
			'array' => "{$field} must be an array.",
			'min' => "{$field} must be at least {$param}.",
			'max' => "{$field} must be at most {$param}.",
			'size' => "{$field} must be {$param}.",
			'in' => "{$field} contains an invalid value.",
			'not_in' => "{$field} contains a restricted value.",
			'same' => "{$field} must match {$param}.",
			'confirmed' => "{$field} confirmation does not match.",
			'different' => "{$field} must be different from {$param}.",
			'accepted' => "{$field} must be accepted.",
			'active_url' => "{$field} must be a valid active URL.",
			'ip' => "{$field} must be a valid IP address.",
			'ipv4' => "{$field} must be a valid IPv4 address.",
			'ipv6' => "{$field} must be a valid IPv6 address.",
			'uuid' => "{$field} must be a valid UUID.",
			'filled' => "{$field} is required.",
			default => "{$field} is invalid.",
		};
	}
}
