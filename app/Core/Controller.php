<?php
namespace App\Core;

class Controller {
    protected function view(string $viewPath, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            die("View không tồn tại: " . $viewPath);
        }
        require __DIR__ . '/../Views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../Views/layouts/footer.php';
    }

    protected function redirect(string $path): void {
        header("Location: " . base_url($path));
        exit;
    }
}
