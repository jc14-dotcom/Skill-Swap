<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\DashboardController;
use App\Controllers\ReviewController;
use App\Controllers\ServiceController;

if (!isset($router) || !$router instanceof \Core\Router) {
	throw new RuntimeException('Router is not available in routes/web.php');
}

$router->get('/', [ServiceController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin'], ['middleware' => ['guest']]);
$router->post('/login', [AuthController::class, 'login'], ['middleware' => ['guest', 'csrf', 'throttle:login']]);

$router->get('/register', [AuthController::class, 'showRegister'], ['middleware' => ['guest']]);
$router->post('/register', [AuthController::class, 'register'], ['middleware' => ['guest', 'csrf', 'throttle:register']]);

$router->post('/logout', [AuthController::class, 'logout'], ['middleware' => ['auth', 'csrf']]);

$router->get('/dashboard', [DashboardController::class, 'index'], ['middleware' => ['auth']]);

$router->get('/services', [ServiceController::class, 'index']);
$router->get('/services/create', [ServiceController::class, 'create'], ['middleware' => ['auth']]);
$router->post('/services', [ServiceController::class, 'store'], ['middleware' => ['auth', 'csrf']]);
$router->get('/services/mine', [ServiceController::class, 'myServices'], ['middleware' => ['auth']]);
$router->get('/services/{id}/edit', [ServiceController::class, 'edit'], ['middleware' => ['auth']]);
$router->patch('/services/{id}', [ServiceController::class, 'update'], ['middleware' => ['auth', 'csrf']]);
$router->delete('/services/{id}', [ServiceController::class, 'destroy'], ['middleware' => ['auth', 'csrf']]);
$router->get('/services/{id}', [ServiceController::class, 'show']);

$router->get('/bookings', [BookingController::class, 'index'], ['middleware' => ['auth']]);
$router->get('/bookings/create', [BookingController::class, 'create'], ['middleware' => ['auth']]);
$router->post('/bookings', [BookingController::class, 'store'], ['middleware' => ['auth', 'csrf']]);
$router->get('/bookings/mine', [BookingController::class, 'myBookings'], ['middleware' => ['auth']]);
$router->get('/bookings/provider', [BookingController::class, 'providerBookings'], ['middleware' => ['auth']]);
$router->patch('/bookings/{id}/status', [BookingController::class, 'updateStatus'], ['middleware' => ['auth', 'csrf']]);

$router->get('/reviews/create', [ReviewController::class, 'create'], ['middleware' => ['auth']]);
$router->post('/reviews', [ReviewController::class, 'store'], ['middleware' => ['auth', 'csrf']]);
