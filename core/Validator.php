<?php

declare(strict_types=1);

namespace Core;

class Validator
{
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

				switch ($name) {
					case 'string':
						if (!is_string($value)) {
							$errors[$field][] = ucfirst($field) . ' must be a string.';
						}
						break;

					case 'email':
						if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$errors[$field][] = ucfirst($field) . ' must be a valid email address.';
						}
						break;

					case 'integer':
						if (filter_var($value, FILTER_VALIDATE_INT) === false) {
							$errors[$field][] = ucfirst($field) . ' must be an integer.';
						}
						break;

					case 'numeric':
						if (!is_numeric((string) $value)) {
							$errors[$field][] = ucfirst($field) . ' must be numeric.';
						}
						break;

					case 'min':
						$min = (int) $params;
						if (is_string($value) && mb_strlen($value) < $min) {
							$errors[$field][] = ucfirst($field) . ' must be at least ' . $min . ' characters.';
						}
						if (is_numeric($value) && (float) $value < $min) {
							$errors[$field][] = ucfirst($field) . ' must be at least ' . $min . '.';
						}
						break;

					case 'max':
						$max = (int) $params;
						if (is_string($value) && mb_strlen($value) > $max) {
							$errors[$field][] = ucfirst($field) . ' must be at most ' . $max . ' characters.';
						}
						if (is_numeric($value) && (float) $value > $max) {
							$errors[$field][] = ucfirst($field) . ' must be at most ' . $max . '.';
						}
						break;

					case 'in':
						$allowed = array_map('trim', explode(',', (string) $params));
						if (!in_array((string) $value, $allowed, true)) {
							$errors[$field][] = ucfirst($field) . ' contains an invalid value.';
						}
						break;

					case 'confirmed':
						$confirmationKey = $field . '_confirmation';
						if (($data[$confirmationKey] ?? null) !== $value) {
							$errors[$field][] = ucfirst($field) . ' confirmation does not match.';
						}
						break;

					default:
						break;
				}
			}

			$clean[$field] = $value;
		}

		return [count($errors) === 0, $errors, $clean];
	}
}
