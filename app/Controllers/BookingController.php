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

		if ($serviceId <= 0) {
			$this->redirect('/services');
			return;
		}

		$service = $this->services->findService($serviceId);
		if (!is_array($service)) {
			Session::flash('errors', ['booking' => ['Selected skill is no longer available. Please choose another one.']]);
			$this->redirect('/services');
			return;
		}

		$actor = $this->user();
		$actorId = is_array($actor) ? (int) ($actor['id'] ?? 0) : 0;
		$actorRole = is_array($actor) ? (string) ($actor['role'] ?? '') : '';
		$serviceOwnerId = (int) ($service['user_id'] ?? 0);

		if ($actorId > 0 && $actorId === $serviceOwnerId && $actorRole !== 'admin') {
			Session::flash('errors', ['booking' => ['You cannot book your own offered skill.']]);
			$this->redirect('/services/' . $serviceId);
			return;
		}

		$this->view('bookings.create', [
			'title' => 'Confirm Booking',
			'service' => $service,
			'requestedServiceId' => $serviceId,
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
			'title' => 'Provider Schedule',
			'bookings' => $this->bookings->listForProvider($actor),
		]);
	}

	public function checkout(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		try {
			$booking = $this->bookings->getBookingForPayment($actor, (int) $id);
			$breakdown = $this->bookings->buildPaymentBreakdown($booking);
			$this->view('bookings.checkout', [
				'title' => 'Checkout',
				'booking' => $booking,
				'breakdown' => $breakdown,
			]);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['payment' => [$exception->getMessage()]]);
			$this->redirect('/bookings/mine');
		}
	}

	public function pay(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'payment_method' => 'required|in:gcash,paymaya,paypal',
		]);

		if (!$valid) {
			$this->redirect('/bookings/' . (int) $id . '/checkout');
			return;
		}

		try {
			$booking = $this->bookings->getBookingForPayment($actor, (int) $id);
			$booking = $this->bookings->markPaid($booking, (string) $clean['payment_method']);
			$breakdown = $this->bookings->buildPaymentBreakdown($booking);
			Session::flash('payment_receipt', [
				'booking_id' => (int) ($booking['id'] ?? 0),
				'service_title' => (string) ($booking['service_title'] ?? 'Service'),
				'provider_name' => (string) ($booking['provider_name'] ?? 'Provider'),
				'payment_method' => (string) $clean['payment_method'],
				'payment_reference' => (string) ($booking['payment_reference'] ?? ''),
				'paid_at' => (string) ($booking['paid_at'] ?? ''),
				'breakdown' => $breakdown,
			]);
			$this->redirect('/bookings/' . (int) $id . '/payment-success');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['payment' => [$exception->getMessage()]]);
			$this->redirect('/bookings/' . (int) $id . '/checkout');
		}
	}

	public function paymentSuccess(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$receipt = Session::get('payment_receipt');
		try {
			$booking = $this->bookings->getBookingForActor($actor, (int) $id);

			if (!is_array($receipt)) {
				$breakdown = $this->bookings->buildPaymentBreakdown($booking);
				$receipt = [
					'booking_id' => (int) ($booking['id'] ?? 0),
					'service_title' => (string) ($booking['service_title'] ?? 'Service'),
					'provider_name' => (string) ($booking['provider_name'] ?? 'Provider'),
					'payment_method' => (string) ($booking['payment_method'] ?? 'Demo'),
					'payment_reference' => (string) ($booking['payment_reference'] ?? 'PAY-DEMO'),
					'paid_at' => (string) ($booking['paid_at'] ?? ''),
					'breakdown' => $breakdown,
				];
			}

			$this->view('bookings.payment-success', [
				'title' => 'Payment Successful',
				'booking' => $booking,
				'receipt' => $receipt,
			]);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['payment' => [$exception->getMessage()]]);
			$this->redirect('/bookings/mine');
		}
	}

	public function updateStatus(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$redirectTo = $this->resolveStatusRedirect($actor);

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

	private function resolveStatusRedirect(array $actor): string
	{
		$fallback = $this->bookingsRedirectForActor($actor);
		$target = trim((string) $this->request->input('redirect', ''));

		if ($target === '') {
			return $fallback;
		}

		if (!str_starts_with($target, '/')) {
			return $fallback;
		}

		return $target;
	}

	private function bookingsRedirectForActor(array $actor): string
	{
		$role = (string) ($actor['role'] ?? '');

		if ($role === 'seeker') {
			return '/bookings/mine';
		}

		if ($role === 'provider') {
			return '/bookings/provider';
		}

		return '/bookings';
	}
}
