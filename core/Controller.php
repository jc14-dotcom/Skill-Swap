<?php

declare(strict_types=1);

namespace Core;

class Controller
{
	protected Request $request;
	protected Response $response;

	public function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	protected function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
	{
		$this->response->view($view, $data, $layout);
	}

	protected function redirect(string $url, int $status = 302): void
	{
		$this->response->redirect($url, $status);
	}

	protected function json(array $payload, int $status = 200): void
	{
		$this->response->json($payload, $status);
	}

	protected function validate(array $data, array $rules): array
	{
		[$valid, $errors, $clean] = Validator::validate($data, $rules);

		if (!$valid) {
			Session::flash('errors', $errors);
			Session::flash('old', $data);
		}

		return [$valid, $errors, $clean];
	}

	protected function user(): ?array
	{
		$user = Session::get('auth.user');
		return is_array($user) ? $user : null;
	}
}
