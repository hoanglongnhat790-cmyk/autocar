<?php
namespace App\Core;

use App\Models\AccountModel;

class Auth {
    public static function check(): bool {
        return !empty($_SESSION['account']);
    }

    public static function user(): ?array {
        return self::check() ? $_SESSION['account'] : null;
    }

    public static function hasRole(string ...$roles): bool {
        $user = self::user();
        return $user !== null && in_array($user['role'], $roles, true);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: ' . base_url('/login'));
            exit;
        }
    }

    public static function requireRole(string ...$roles): void {
        self::requireLogin();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            echo "<h1>403 - Bạn không có quyền truy cập trang này.</h1><a href='" . base_url('/') . "'>Về trang chủ</a>";
            exit;
        }
    }

    public static function login(string $username, string $password): bool {
        $account = (new AccountModel())->verifyCredentials($username, $password);
        if (!$account) {
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['account'] = $account;
        return true;
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
