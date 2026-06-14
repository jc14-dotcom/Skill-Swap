<?php

declare(strict_types=1);

namespace Core\Exceptions;

use RuntimeException;

class AppException extends RuntimeException
{
	public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	public function getStatusCode(): int
	{
		return 500;
	}
}
