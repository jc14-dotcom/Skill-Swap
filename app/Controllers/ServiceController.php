<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BookingService;
use App\Services\ServiceService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class ServiceController extends Controller
{
	private ServiceService $services;
	private BookingService $bookings;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->services = new ServiceService();
		$this->bookings = new BookingService();
	}

	public function index(): void
	{
		$actor = $this->user();
		$searchQuery = trim((string) $this->request->query('q', ''));
		if (strlen($searchQuery) > 120) {
			$searchQuery = substr($searchQuery, 0, 120);
		}

		$services = $this->services->listPublicServices($searchQuery);

		if ($this->wantsJson()) {
			$this->json([
				'query' => $searchQuery,
				'count' => count($services),
				'services' => $this->transformPublicServicesForApi($services, $actor),
			]);
			return;
		}

		if (is_array($actor)) {
			if ($this->request->path() === '/') {
				$this->redirect('/home');
				return;
			}

			$this->view('services.marketplace', [
				'title' => 'Marketplace',
				'services' => $services,
				'searchQuery' => $searchQuery,
			]);
			return;
		}

		$this->view('services.index', [
			'title' => 'Services',
			'services' => $services,
			'searchQuery' => $searchQuery,
		]);
	}

	private function wantsJson(): bool
	{
		$format = strtolower(trim((string) $this->request->query('format', '')));
		if ($format === 'json') {
			return true;
		}

		$accept = strtolower((string) $this->request->header('Accept', ''));
		return str_contains($accept, 'application/json');
	}

	private function transformPublicServicesForApi(array $services, ?array $actor): array
	{
		$items = [];
		$actorId = is_array($actor) ? (int) ($actor['id'] ?? 0) : 0;

		foreach ($services as $service) {
			if (!is_array($service)) {
				continue;
			}

			$serviceId = (int) ($service['id'] ?? 0);
			$title = trim((string) ($service['title'] ?? ''));
			if ($title === '') {
				$title = 'Skill Service';
			}

			$description = trim((string) ($service['description'] ?? ''));
			$providerName = trim((string) ($service['provider_name'] ?? ''));
			if ($providerName === '') {
				$providerName = 'Provider';
			}

			$isOwnService = $actorId > 0 && $actorId === (int) ($service['user_id'] ?? 0);

			$items[] = [
				'id' => $serviceId,
				'title' => $title,
				'description' => $description,
				'provider_name' => $providerName,
				'provider_image_url' => profile_image_url((string) ($service['provider_profile_picture'] ?? '')),
				'price_label' => format_currency((float) ($service['price'] ?? 0)),
				'detail_url' => '/services/' . $serviceId,
				'book_url' => '/bookings/create?service_id=' . $serviceId,
				'is_own_service' => $isOwnService,
			];
		}

		return $items;
	}

	public function show(string $id): void
	{
		$service = $this->services->findService((int) $id);
		$actor = $this->user();

		if (!is_array($service)) {
			http_response_code(404);
			$this->view('errors.404', ['title' => 'Not Found'], null);
			return;
		}

		$canManage = $this->services->canManageService($actor, $service);
		$providerBookings = [];

		if ($canManage && is_array($actor)) {
			$providerBookings = $this->bookings->listForProviderService($actor, $service);
		}

		$this->view('services.show', [
			'title' => $service['title'],
			'service' => $service,
			'canManage' => $canManage,
			'providerBookings' => $providerBookings,
			'bookingActionRedirect' => '/services/' . (int) ($service['id'] ?? 0),
		]);
	}

	public function create(): void
	{
		$this->view('services.create', [
			'title' => 'Create Service',
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
			'title' => 'required|string|min:4|max:120',
			'description' => 'required|string|min:10|max:2000',
			'price' => 'required|numeric|min:1',
		]);

		if (!$valid) {
			$this->redirect('/services/create');
			return;
		}

		try {
			$serviceId = $this->services->createService($actor, $clean);
			Session::flash('success', 'Service created successfully.');
			$this->redirect('/services/' . $serviceId);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['service' => [$exception->getMessage()]]);
			Session::flash('old', $clean);
			$this->redirect('/services/create');
		}
	}

	public function myServices(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$this->view('services.my-services', [
			'title' => 'My Services',
			'services' => $this->services->listOwnServices($actor),
		]);
	}

	public function edit(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		try {
			$service = $this->services->findManagedService($actor, (int) $id);
			$this->view('services.edit', [
				'title' => 'Edit Service',
				'service' => $service,
			]);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['service' => [$exception->getMessage()]]);
			$this->redirect('/services/mine');
		}
	}

	public function update(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'title' => 'required|string|min:4|max:120',
			'description' => 'required|string|min:10|max:2000',
			'price' => 'required|numeric|min:1',
			'status' => 'required|in:active,inactive',
		]);

		if (!$valid) {
			$this->redirect('/services/' . (int) $id . '/edit');
			return;
		}

		try {
			$this->services->updateService($actor, (int) $id, $clean);
			Session::flash('success', 'Service updated successfully.');
			$this->redirect('/services/' . (int) $id);
		} catch (\Throwable $exception) {
			Session::flash('errors', ['service' => [$exception->getMessage()]]);
			Session::flash('old', $clean);
			$this->redirect('/services/' . (int) $id . '/edit');
		}
	}

	public function destroy(string $id): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		try {
			$this->services->deleteService($actor, (int) $id);
			Session::flash('success', 'Service deleted successfully.');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['service' => [$exception->getMessage()]]);
		}

		$this->redirect('/services/mine');
	}
}
