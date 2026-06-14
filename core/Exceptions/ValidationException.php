<?php

declare(strict_types=1);

namespace Core\Exceptions;

class ValidationException extends AppException
{
	/** @var array<string, array<int, string>> */
	private array $errors;

	/**
	 * @param array<string, array<int, string>> $errors
	 */
	public function __construct(array $errors, ?\Throwable $previous = null)
	{
		$this->errors = $errors;
		parent::__construct('Validation failed', 422, $previous);
	}

	public function getStatusCode(): int
	{
		return 422;
	}

	/**
	 * @return array<string, array<int, string>>
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
