<?php
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

require_once __DIR__ . '/../app/Core/helpers.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Controllers\CarController;
use App\Controllers\BookingController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\SupportController;
use App\Controllers\CustomerController;
use App\Controllers\AccountController;

$router = new Router();

$router->get('/', [CarController::class, 'index']);
$router->get('/car/detail', [CarController::class, 'detail']);
$router->post('/booking/store', [BookingController::class, 'store']);
$router->get('/bookings', [BookingController::class, 'index']);
$router->post('/booking/cancel', [BookingController::class, 'cancel']);
$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin/car/store', [AdminController::class, 'storeCar']);
$router->post('/admin/car/delete', [AdminController::class, 'deleteCar']);
$router->get('/api/cars/locations', [AdminController::class, 'getLocations']);
$router->post('/api/cars/simulate', [AdminController::class, 'simulateMovement']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/support', [SupportController::class, 'index']);
$router->post('/support/send', [SupportController::class, 'send']);
$router->get('/admin/support', [AdminController::class, 'supportHistory']);

$router->get('/customers', [CustomerController::class, 'index']);
$router->get('/customers/create', [CustomerController::class, 'createForm']);
$router->post('/customer/store', [CustomerController::class, 'store']);
$router->get('/customers/edit', [CustomerController::class, 'editForm']);
$router->post('/customer/update', [CustomerController::class, 'update']);
$router->post('/customer/status', [CustomerController::class, 'toggleStatus']);
$router->post('/customer/delete', [CustomerController::class, 'delete']);

$router->get('/accounts', [AccountController::class, 'index']);
$router->get('/accounts/create', [AccountController::class, 'createForm']);
$router->post('/account/store', [AccountController::class, 'store']);
$router->get('/accounts/edit', [AccountController::class, 'editForm']);
$router->post('/account/update', [AccountController::class, 'update']);
$router->post('/account/status', [AccountController::class, 'toggleStatus']);
$router->post('/account/delete', [AccountController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);