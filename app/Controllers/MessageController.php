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
		// Messages feature is currently disabled.
		Session::flash('errors', ['messages' => ['The messaging feature is not available yet.']]);
		$this->redirect('/home');
	}

	public function show(string $bookingId): void
	{
		// Messages feature is currently disabled.
		Session::flash('errors', ['messages' => ['The messaging feature is not available yet.']]);
		$this->redirect('/home');
	}

	public function store(string $bookingId): void
	{
		// Messages feature is currently disabled.
		Session::flash('errors', ['messages' => ['The messaging feature is not available yet.']]);
		$this->redirect('/home');
	}
}
