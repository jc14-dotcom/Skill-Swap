<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BookingService;
use App\Services\ServiceService;
use Core\Controller;
use Core\Request;
use Core\Response;

class DashboardController extends Controller
{
	private BookingService $bookings;
	private ServiceService $services;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->bookings = new BookingService();
		$this->services = new ServiceService();
	}

	public function index(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$this->view('dashboard.index', [
			'title' => 'Dashboard',
			'publicServices' => $this->services->listPublicServices(),
			'myBookings' => $this->bookings->listForSeeker($actor),
			'providerBookings' => $this->bookings->listForProvider($actor),
		]);
	}
}
