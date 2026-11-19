<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;
use DomainException;

class AuthModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createUser(string $name, string $email, string $password, string $role = 'user'): int {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
        );

        try {
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => $role,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('Email này đã được đăng ký.');
            }
            throw $e;
        }

        return (int)$this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
