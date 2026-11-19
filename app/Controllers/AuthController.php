<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuthModel;
use DomainException;

class AuthController extends Controller {
    private AuthModel $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function registerForm(): void {
        if (current_user() !== null) {
            $this->redirect('/');
        }
        $this->view('auth/register', ['title' => 'Tạo tài khoản - VietCar']);
    }

    public function register(): void {
        $this->verifyCsrfToken();
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($name === '' || strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($email) > 190 || !is_string($password) || strlen($password) < 8) {
            $_SESSION['flash_error'] = 'Vui lòng nhập tên, email hợp lệ và mật khẩu ít nhất 8 ký tự.';
            $this->redirect('/register');
        }

        try {
            $userId = $this->authModel->createUser($name, $email, $password);
        } catch (DomainException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('/register');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'role' => 'user',
        ];
        $_SESSION['flash_success'] = 'Tạo tài khoản thành công.';
        $this->redirect('/');
    }

    public function loginForm(): void {
        if (current_user() !== null) {
            $this->redirect('/');
        }
        $this->view('auth/login', ['title' => 'Đăng nhập - VietCar']);
    }

    public function login(): void {
        $this->verifyCsrfToken();
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $user = $this->authModel->findByEmail($email);

        if (!$user || !is_string($password) || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Email hoặc mật khẩu không chính xác.';
            $this->redirect('/login');
        }
        if (($user['status'] ?? 'active') === 'locked') {
            $_SESSION['flash_error'] = 'Tài khoản này đã bị khóa. Vui lòng liên hệ quản trị viên.';
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        $_SESSION['flash_success'] = 'Đăng nhập thành công.';
        $this->redirect($user['role'] === 'admin' ? '/admin' : '/');
    }

    public function logout(): void {
        $this->verifyCsrfToken();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirect('/');
    }
}
