<?php

declare(strict_types=1);

use Core\Validator;

if (!function_exists('validate_data')) {
	function validate_data(array $data, array $rules): array
	{
		return Validator::validate($data, $rules);
	}
}
