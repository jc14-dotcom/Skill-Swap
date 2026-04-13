<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ServiceService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class ServiceController extends Controller
{
	private ServiceService $services;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->services = new ServiceService();
	}

	public function index(): void
	{
		$actor = $this->user();
		$services = $this->services->listPublicServices();

		if (is_array($actor)) {
			if ($this->request->path() === '/') {
				$this->redirect('/home');
				return;
			}

			$this->view('services.marketplace', [
				'title' => 'Marketplace',
				'services' => $services,
			]);
			return;
		}

		$this->view('services.index', [
			'title' => 'Services',
			'services' => $services,
		]);
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

		$this->view('services.show', [
			'title' => $service['title'],
			'service' => $service,
			'canManage' => $this->services->canManageService($actor, $service),
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
