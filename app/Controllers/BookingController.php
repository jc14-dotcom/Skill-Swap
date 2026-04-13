<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BookingService;
use App\Services\ServiceService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class BookingController extends Controller
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

		$this->view('bookings.index', [
			'title' => 'Bookings',
			'seekerBookings' => $this->bookings->listForSeeker($actor),
			'providerBookings' => $this->bookings->listForProvider($actor),
		]);
	}

	public function create(): void
	{
		$serviceId = (int) $this->request->query('service_id', 0);
		$service = $serviceId > 0 ? $this->services->findService($serviceId) : null;

		$this->view('bookings.create', [
			'title' => 'Create Booking',
			'service' => $service,
		]);
	}

	public function store(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'service_id' => 'required|integer|min:1',
			'message' => 'nullable|string|max:1000',
			'scheduled_at' => 'nullable|string|max:40',
		]);

		if (!$valid) {
			$this->redirect('/bookings/create?service_id=' . (int) ($this->request->input('service_id') ?? 0));
			return;
		}

		try {
			$booking = $this->bookings->createBooking($actor, $clean);
			Session::flash('success', 'Booking submitted. Waiting for provider decision.');
			$this->redirect('/bookings/mine');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['booking' => [$exception->getMessage()]]);
			Session::flash('old', $clean);
			$this->redirect('/bookings/create?service_id=' . (int) ($clean['service_id'] ?? 0));
		}
	}

	public function myBookings(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$this->view('bookings.my-bookings', [
			'title' => 'My Bookings',
			'bookings' => $this->bookings->listForSeeker($actor),
		]);
	}

	public function providerBookings(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$this->view('bookings.provider-bookings', [
			'title' => 'Provider Bookings',
			'bookings' => $this->bookings->listForProvider($actor),
		]);
	}

	public function updateStatus(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$redirectTo = $this->bookingsRedirectForActor($actor);

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'status' => 'required|in:accepted,rejected,completed,cancelled',
		]);

		if (!$valid) {
			$this->redirect($redirectTo);
			return;
		}

		try {
			$this->bookings->transitionStatus($actor, (int) $id, (string) $clean['status']);
			Session::flash('success', 'Booking status updated.');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['booking' => [$exception->getMessage()]]);
		}

		$this->redirect($redirectTo);
	}

	private function bookingsRedirectForActor(array $actor): string
	{
		return (string) ($actor['role'] ?? '') === 'seeker'
			? '/bookings/mine'
			: '/bookings/provider';
	}
}
