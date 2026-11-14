<?php
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

$router = new Router();

$router->get('/', [CarController::class, 'index']);
$router->get('/car/detail', [CarController::class, 'detail']);
$router->post('/booking/store', [BookingController::class, 'store']);
$router->get('/bookings', [BookingController::class, 'index']);
$router->post('/booking/cancel', [BookingController::class, 'cancel']);
$router->get('/admin', [AdminController::class, 'index']);
$router->post('/admin/car/store', [AdminController::class, 'storeCar']);
$router->post('/admin/car/delete', [AdminController::class, 'deleteCar']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
$router->get('/api/cars/locations', [AdminController::class, 'getLocations']);
$router->post('/api/cars/simulate', [AdminController::class, 'simulateMovement']);
// Route cho biên bản bàn giao xe
$router->get('/booking/inspection', [App\Controllers\BookingController::class, 'inspectionForm']);
$router->post('/booking/inspection/save', [App\Controllers\BookingController::class, 'saveInspection']);