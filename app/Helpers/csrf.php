<?php

declare(strict_types=1);

use Core\Session;

if (!function_exists('csrf_token')) {
	function csrf_token(): string
	{
		return Session::token();
	}
}

if (!function_exists('csrf_field')) {
	function csrf_field(): string
	{
		return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
	}
}
