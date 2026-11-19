<?php
namespace App\Core;

class Validator {
    public static function customer(array $data): array {
        $errors = [];

        $name = trim((string)($data['full_name'] ?? ''));
        if ($name === '') {
            $errors['full_name'] = 'Họ tên không được để trống.';
        } elseif (self::length($name) > 100) {
            $errors['full_name'] = 'Họ tên tối đa 100 ký tự.';
        }

        $email = trim((string)($data['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } elseif (self::length($email) > 150) {
            $errors['email'] = 'Email tối đa 150 ký tự.';
        }

        $phone = trim((string)($data['phone'] ?? ''));
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if ($digits === '') {
            $errors['phone'] = 'Số điện thoại không được để trống.';
        } elseif (strlen($digits) < 9 || strlen($digits) > 15) {
            $errors['phone'] = 'Số điện thoại phải có từ 9 đến 15 chữ số.';
        }

        $identity = trim((string)($data['identity_number'] ?? ''));
        if (self::length($identity) > 30) {
            $errors['identity_number'] = 'Số CCCD tối đa 30 ký tự.';
        }

        $address = trim((string)($data['address'] ?? ''));
        if (self::length($address) > 255) {
            $errors['address'] = 'Địa chỉ tối đa 255 ký tự.';
        }

        $status = (string)($data['status'] ?? 'active');
        if (!in_array($status, ['active', 'inactive'], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return $errors;
    }

    public static function account(array $data, bool $requirePassword = true): array {
        $errors = [];

        $name = trim((string)($data['name'] ?? ''));
        if ($name === '') {
            $errors['name'] = 'Họ tên không được để trống.';
        } elseif (self::length($name) > 100) {
            $errors['name'] = 'Họ tên tối đa 100 ký tự.';
        }

        $email = trim((string)($data['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } elseif (self::length($email) > 190) {
            $errors['email'] = 'Email tối đa 190 ký tự.';
        }

        $password = (string)($data['password'] ?? '');
        if ($requirePassword) {
            if ($password === '') {
                $errors['password'] = 'Mật khẩu không được để trống.';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'Mật khẩu tối thiểu 8 ký tự.';
            }
        }

        $role = (string)($data['role'] ?? 'user');
        if (!in_array($role, ['admin', 'user'], true)) {
            $errors['role'] = 'Vai trò không hợp lệ.';
        }

        $status = (string)($data['status'] ?? 'active');
        if (!in_array($status, ['active', 'locked'], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        $customerId = $data['customer_id'] ?? null;
        if ($customerId !== null && $customerId !== '' && (int)$customerId <= 0) {
            $errors['customer_id'] = 'Khách hàng liên kết không hợp lệ.';
        }

        return $errors;
    }

    private static function length(string $value): int {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
