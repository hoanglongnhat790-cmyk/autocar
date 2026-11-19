<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class AccountModel {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(string $keyword = '', string $role = 'all', string $status = 'all'): array {
        $sql = "SELECT u.id, u.name, u.email, u.role, u.status, u.customer_id, u.last_login_at, u.created_at,
                       c.full_name AS customer_name
                FROM users u
                LEFT JOIN customers c ON u.customer_id = c.id
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (u.name LIKE :keyword OR u.email LIKE :keyword OR c.full_name LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (in_array($role, ['admin', 'user'], true)) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $role;
        }
        if (in_array($status, ['active', 'locked'], true)) {
            $sql .= " AND u.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY u.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT u.*, c.full_name AS customer_name
             FROM users u LEFT JOIN customers c ON u.customer_id = c.id
             WHERE u.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];
        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $email = strtolower(trim($data['email']));
        if ($this->emailExists($email)) {
            throw new \InvalidArgumentException('Email này đã được đăng ký.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, role, status, customer_id)
             VALUES (:name, :email, :password_hash, :role, :status, :customer_id)"
        );
        $stmt->execute([
            ':name'          => trim($data['name']),
            ':email'         => $email,
            ':password_hash' => password_hash((string)$data['password'], PASSWORD_DEFAULT),
            ':role'          => $data['role'] ?? 'user',
            ':status'        => $data['status'] ?? 'active',
            ':customer_id'   => $this->normalizeCustomerId($data['customer_id'] ?? null),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $email = strtolower(trim($data['email']));
        if ($this->emailExists($email, $id)) {
            throw new \InvalidArgumentException('Email này đã được đăng ký.');
        }

        $passwordHash = null;
        if (!empty($data['password'])) {
            $passwordHash = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users
                SET name = :name, email = :email, role = :role,
                    status = :status, customer_id = :customer_id";
        $params = [
            ':name'        => trim($data['name']),
            ':email'       => $email,
            ':role'        => $data['role'] ?? 'user',
            ':status'      => $data['status'] ?? 'active',
            ':customer_id' => $this->normalizeCustomerId($data['customer_id'] ?? null),
            ':id'          => $id,
        ];
        if ($passwordHash !== null) {
            $sql .= ", password_hash = :password_hash";
            $params[':password_hash'] = $passwordHash;
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function setStatus(int $id, string $status): bool {
        if (!in_array($status, ['active', 'locked'], true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function countActiveAdmins(?int $excludeId = null): int {
        $sql = "SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'";
        $params = [];
        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function verifyCredentials(string $email, string $password): ?array {
        $account = $this->findByEmail(strtolower(trim($email)));
        if (!$account || ($account['status'] ?? 'active') !== 'active') {
            return null;
        }
        if (!password_verify($password, $account['password_hash'])) {
            return null;
        }

        $stmt = $this->db->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([':id' => $account['id']]);
        unset($account['password_hash']);
        return $account;
    }

    private function normalizeCustomerId(mixed $customerId): ?int {
        if ($customerId === null || $customerId === '' || (int)$customerId <= 0) {
            return null;
        }
        return (int)$customerId;
    }
}
