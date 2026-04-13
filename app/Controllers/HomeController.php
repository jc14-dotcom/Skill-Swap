<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BookingService;
use App\Services\ServiceService;
use Core\Controller;
use Core\Request;
use Core\Response;

class HomeController extends Controller
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

		$role = (string) ($actor['role'] ?? 'seeker');

		$myBookings = $this->bookings->listForSeeker($actor);
		$providerBookings = $this->bookings->listForProvider($actor);
		$myServices = $this->services->listOwnServices($actor);

		$this->view('home.index', [
			'title' => 'Home',
			'user' => $actor,
			'role' => $role,
			'publicServiceCount' => count($this->services->listPublicServices()),
			'myBookingCount' => count($myBookings),
			'providerBookingCount' => count($providerBookings),
			'myServiceCount' => count($myServices),
		]);
	}
}
