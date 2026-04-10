<?php

declare(strict_types=1);

namespace Core;

class Response
{
	private string $basePath;

	public function __construct(string $basePath)
	{
		$this->basePath = rtrim($basePath, '/\\');
	}

	public function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
	{
		$viewPath = $this->basePath . '/app/Views/' . str_replace('.', '/', $view) . '.php';
		if (!is_file($viewPath)) {
			throw new \RuntimeException('View not found: ' . $view);
		}

		extract($data, EXTR_SKIP);

		ob_start();
		include $viewPath;
		$content = (string) ob_get_clean();

		if ($layout === null) {
			echo $content;
			return;
		}

		$layoutPath = $this->basePath . '/app/Views/' . str_replace('.', '/', $layout) . '.php';
		if (!is_file($layoutPath)) {
			echo $content;
			return;
		}

		include $layoutPath;
	}

	public function json(array $payload, int $status = 200): void
	{
		http_response_code($status);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	public function redirect(string $url, int $status = 302): void
	{
		header('Location: ' . $url, true, $status);
		exit;
	}

	public function abort(int $status, string $message = ''): void
	{
		http_response_code($status);
		echo $message !== '' ? $message : 'HTTP ' . $status;
	}
}
