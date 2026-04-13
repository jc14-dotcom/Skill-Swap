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

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->messages = new MessageService();
	}

	public function index(): void
	{
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
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		try {
			$thread = $this->messages->openThread($actor, (int) $bookingId);
			$this->view('messages.chat', [
				'title' => 'Conversation',
				'thread' => $thread,
				'actor' => $actor,
			]);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['message' => [$exception->getMessage()]]);
			$this->redirect('/messages');
		}
	}

	public function store(string $bookingId): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'body' => 'required|string|max:5000',
		]);

		$target = '/messages/' . (int) $bookingId;
		if (!$valid) {
			$this->redirect($target);
			return;
		}

		try {
			$this->messages->sendMessage($actor, (int) $bookingId, (string) $clean['body']);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['message' => [$exception->getMessage()]]);
			Session::flash('old', ['body' => (string) ($clean['body'] ?? '')]);
		}

		$this->redirect($target);
	}
}
