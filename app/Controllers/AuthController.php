<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller {
    public function loginForm(): void {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('auth/login', [
            'title'    => 'Đăng nhập - VietCar',
            'username' => $_SESSION['old_username'] ?? ''
        ]);
        unset($_SESSION['old_username']);
    }

    public function login(): void {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if (Auth::login($username, $password)) {
            $_SESSION['flash_success'] = 'Đăng nhập thành công. Chào mừng ' . Auth::user()['username'] . '!';
            $this->redirect('/');
        }

        $_SESSION['flash_error'] = 'Sai tên đăng nhập hoặc mật khẩu, hoặc tài khoản đã bị khóa.';
        $_SESSION['old_username'] = $username;
        $this->redirect('/login');
    }

    public function logout(): void {
        Auth::logout();
        header('Location: ' . base_url('/login'));
        exit;
    }
}
