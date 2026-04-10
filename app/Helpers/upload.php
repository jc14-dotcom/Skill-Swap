<?php

declare(strict_types=1);

if (!function_exists('store_uploaded_file')) {
	function store_uploaded_file(string $fieldName, string $destinationDir, array $allowedMime = []): ?string
	{
		if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
			return null;
		}

		$file = $_FILES[$fieldName];
		if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			return null;
		}

		$tmpPath = (string) ($file['tmp_name'] ?? '');
		if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
			return null;
		}

		if ($allowedMime !== []) {
			$mime = mime_content_type($tmpPath) ?: '';
			if (!in_array($mime, $allowedMime, true)) {
				return null;
			}
		}

		if (!is_dir($destinationDir)) {
			mkdir($destinationDir, 0777, true);
		}

		$extension = pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION);
		$safeName = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . strtolower($extension) : '');
		$targetPath = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

		if (!move_uploaded_file($tmpPath, $targetPath)) {
			return null;
		}

		return $safeName;
	}
}
