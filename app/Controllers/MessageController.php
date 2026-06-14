<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\MessageService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class MessageController extends Controller
{
	private MessageService $messages;

	private const FEATURE_ENABLED = true;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->messages = new MessageService();
	}

	private function ensureEnabled(): bool
	{
		if (!self::FEATURE_ENABLED) {
			Session::flash('errors', ['messages' => ['The messaging feature is not available yet.']]);
			$this->redirect('/home');
			return false;
		}
		return true;
	}

	public function index(): void
	{
		if (!$this->ensureEnabled()) {
			return;
		}

		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$this->view('messages.index', [
			'title' => 'Messages',
			'conversations' => $this->messages->listConversations($actor),
		]);
	}

	public function show(string $bookingId): void
	{
		if (!$this->ensureEnabled()) {
			return;
		}

		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		try {
			$thread = $this->messages->openThread($actor, (int) $bookingId);
			$this->view('messages.chat', [
				'title' => 'Conversation',
				'booking' => $thread['booking'],
				'counterpart' => $thread['counterpart'],
				'messages' => $thread['messages'],
			]);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['messages' => [$exception->getMessage()]]);
			$this->redirect('/messages');
		}
	}

	public function store(string $bookingId): void
	{
		if (!$this->ensureEnabled()) {
			return;
		}

		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$body = trim((string) $this->request->input('body', ''));

		try {
			$this->messages->sendMessage($actor, (int) $bookingId, $body);
			$this->redirect('/messages/' . $bookingId);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['messages' => [$exception->getMessage()]]);
			$this->redirect('/messages/' . $bookingId);
		}
	}
}
