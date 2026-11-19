<?php
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

use App\Models\AuthModel;

$email = strtolower(trim(getenv('VIETCAR_ADMIN_EMAIL') ?: ''));
$password = getenv('VIETCAR_ADMIN_PASSWORD');
$name = trim(getenv('VIETCAR_ADMIN_NAME') ?: 'VietCar Admin');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === false || strlen($password) < 12) {
    fwrite(STDERR, "Set VIETCAR_ADMIN_EMAIL and VIETCAR_ADMIN_PASSWORD (at least 12 characters) before running this script.\n");
    exit(1);
}

try {
    (new AuthModel())->createUser($name, $email, $password, 'admin');
    fwrite(STDOUT, "Admin account created: {$email}\n");
} catch (DomainException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
