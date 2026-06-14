<?php

declare(strict_types=1);

if (!isset($router) || !$router instanceof \Core\Router) {
	throw new RuntimeException('Router is not available in routes/api.php');
}

$router->get('/api/v1/services', function (\Core\Request $request, \Core\Response $response): void {
	$serviceService = new \App\Services\ServiceService();
	$searchQuery = trim((string) $request->query('q', ''));

	$services = $serviceService->listPublicServices($searchQuery);

	$items = [];
	foreach ($services as $service) {
		$items[] = [
			'id' => (int) ($service['id'] ?? 0),
			'title' => (string) ($service['title'] ?? ''),
			'description' => (string) ($service['description'] ?? ''),
			'price' => (float) ($service['price'] ?? 0),
			'provider_name' => (string) ($service['provider_name'] ?? ''),
			'status' => (string) ($service['status'] ?? 'active'),
		];
	}

	$response->json(['data' => $items, 'count' => count($items)]);
});

$router->get('/api/v1/services/{id}', function (\Core\Request $request, \Core\Response $response): void {
	$serviceService = new \App\Services\ServiceService();
	$id = (int) $request->param('id', 0);

	$service = $serviceService->findService($id);

	if ($service === null) {
		$response->json(['error' => 'Service not found'], 404);
		return;
	}

	$response->json([
		'data' => [
			'id' => (int) ($service['id'] ?? 0),
			'title' => (string) ($service['title'] ?? ''),
			'description' => (string) ($service['description'] ?? ''),
			'price' => (float) ($service['price'] ?? 0),
			'provider_name' => (string) ($service['provider_name'] ?? ''),
			'provider_email' => (string) ($service['provider_email'] ?? ''),
			'status' => (string) ($service['status'] ?? 'active'),
		],
	]);
});
