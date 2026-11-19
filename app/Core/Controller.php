<?php
namespace App\Core;

class Controller {
    protected function requireLogin(): array {
        $user = current_user();
        if ($user === null) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để tiếp tục.';
            $this->redirect('/login');
        }
        return $user;
    }

    protected function requireAdmin(): array {
        $user = $this->requireLogin();
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            exit('Bạn không có quyền truy cập chức năng quản trị.');
        }
        return $user;
    }

    protected function verifyCsrfToken(): void {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $storedToken = $_SESSION['csrf_token'] ?? '';
        if (!is_string($token) || !is_string($storedToken) || $storedToken === '' || !hash_equals($storedToken, $token)) {
            http_response_code(419);
            exit('Phiên làm việc không hợp lệ. Vui lòng tải lại trang và thử lại.');
        }
    }

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
