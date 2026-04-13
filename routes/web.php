<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\MessageController;
use App\Controllers\NotificationController;
use App\Controllers\ProfileController;
use App\Controllers\ReviewController;
use App\Controllers\ServiceController;

if (!isset($router) || !$router instanceof \Core\Router) {
	throw new RuntimeException('Router is not available in routes/web.php');
}

$router->get('/', [ServiceController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin'], ['middleware' => ['guest']]);
$router->post('/login', [AuthController::class, 'login'], ['middleware' => ['guest', 'csrf', 'throttle:login']]);

$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], ['middleware' => ['guest']]);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink'], ['middleware' => ['guest', 'csrf', 'throttle:password_reset']]);
$router->get('/reset-password', [AuthController::class, 'showResetPassword'], ['middleware' => ['guest']]);
$router->post('/reset-password', [AuthController::class, 'resetPassword'], ['middleware' => ['guest', 'csrf', 'throttle:password_reset']]);

$router->get('/register', [AuthController::class, 'showRegister'], ['middleware' => ['guest']]);
$router->post('/register', [AuthController::class, 'register'], ['middleware' => ['guest', 'csrf', 'throttle:register']]);

$router->post('/logout', [AuthController::class, 'logout'], ['middleware' => ['auth', 'csrf']]);

$router->get('/home', [HomeController::class, 'index'], ['middleware' => ['auth']]);
$router->get('/dashboard', [DashboardController::class, 'index'], ['middleware' => ['auth']]);

$router->get('/profile/settings', [ProfileController::class, 'settings'], ['middleware' => ['auth']]);
$router->post('/profile/settings', [ProfileController::class, 'updateSettings'], ['middleware' => ['auth', 'csrf']]);

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

$router->get('/messages', [MessageController::class, 'index'], ['middleware' => ['auth']]);
$router->get('/messages/{bookingId}', [MessageController::class, 'show'], ['middleware' => ['auth']]);
$router->post('/messages/{bookingId}', [MessageController::class, 'store'], ['middleware' => ['auth', 'csrf']]);

$router->get('/notifications', [NotificationController::class, 'index'], ['middleware' => ['auth']]);
$router->post('/notifications/read-all', [NotificationController::class, 'markAllRead'], ['middleware' => ['auth', 'csrf']]);
$router->post('/notifications/{id}/read', [NotificationController::class, 'markRead'], ['middleware' => ['auth', 'csrf']]);

$router->get('/reviews/create', [ReviewController::class, 'create'], ['middleware' => ['auth']]);
$router->post('/reviews', [ReviewController::class, 'store'], ['middleware' => ['auth', 'csrf']]);
