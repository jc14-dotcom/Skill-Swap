<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\NotificationService;
use Core\Controller;
use Core\Request;
use Core\Response;

class NotificationController extends Controller
{
	private NotificationService $notifications;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->notifications = new NotificationService();
	}

	public function index(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->json(['message' => 'Unauthorized.'], 401);
			return;
		}

		$feed = $this->notifications->feed($actor, (int) $this->request->query('limit', 10));
		$this->json(['data' => $feed]);
	}

	public function markRead(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->respondUnauthenticated();
			return;
		}

		$updated = $this->notifications->markAsReadForUser($actor, (int) $id);
		if ($this->prefersJson()) {
			$status = $updated ? 200 : 404;
			$this->json([
				'updated' => $updated,
				'unread_count' => $this->notifications->feed($actor, 1)['unread_count'],
			], $status);
			return;
		}

		$this->redirect($this->resolveRedirectPath());
	}

	public function markAllRead(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->respondUnauthenticated();
			return;
		}

		$updatedCount = $this->notifications->markAllAsReadForUser($actor);
		if ($this->prefersJson()) {
			$this->json([
				'updated_count' => $updatedCount,
				'unread_count' => 0,
			]);
			return;
		}

		$this->redirect($this->resolveRedirectPath());
	}

	private function respondUnauthenticated(): void
	{
		if ($this->prefersJson()) {
			$this->json(['message' => 'Unauthorized.'], 401);
			return;
		}

		$this->redirect('/login');
	}

	private function prefersJson(): bool
	{
		$accept = (string) $this->request->header('Accept', '');
		return str_contains(strtolower($accept), 'application/json')
			|| strtolower((string) $this->request->query('format', '')) === 'json';
	}

	private function resolveRedirectPath(): string
	{
		$fallback = '/home';
		$target = trim((string) $this->request->input('redirect', ''));

		if ($target === '') {
			$referer = (string) $this->request->header('Referer', '');
			if ($referer !== '') {
				$path = (string) parse_url($referer, PHP_URL_PATH);
				$query = (string) parse_url($referer, PHP_URL_QUERY);
				$target = $path !== '' ? $path . ($query !== '' ? '?' . $query : '') : '';
			}
		}

		if ($target === '' || !str_starts_with($target, '/') || str_starts_with($target, '//')) {
			return $fallback;
		}

		return $target;
	}
}
