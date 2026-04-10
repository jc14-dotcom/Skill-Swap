<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;

class AdminController extends Controller
{
	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
	}

	public function index(): void
	{
		http_response_code(501);
		echo 'Admin module is reserved for a future phase.';
	}
}
