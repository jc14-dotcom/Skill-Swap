<?php

declare(strict_types=1);

namespace Core\Exceptions;

class AuthorizationException extends AppException
{
	public function __construct(string $message = 'Unauthorized', int $code = 403, ?\Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	public function getStatusCode(): int
	{
		return 403;
	}
}
