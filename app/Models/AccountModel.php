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
        $sql = "SELECT a.id, a.username, a.role, a.status, a.customer_id, a.last_login_at, a.created_at,
                       c.full_name AS customer_name
                FROM accounts a
                LEFT JOIN customers c ON a.customer_id = c.id
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (a.username LIKE :keyword OR c.full_name LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (in_array($role, ['admin', 'staff', 'customer'], true)) {
            $sql .= " AND a.role = :role";
            $params[':role'] = $role;
        }
        if (in_array($status, ['active', 'locked'], true)) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY a.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT a.*, c.full_name AS customer_name
             FROM accounts a LEFT JOIN customers c ON a.customer_id = c.id
             WHERE a.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM accounts WHERE username = :username";
        $params = [':username' => $username];
        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $username = strtolower(trim($data['username']));
        if ($this->usernameExists($username)) {
            throw new \InvalidArgumentException('Tên đăng nhập đã tồn tại.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO accounts (username, password_hash, role, customer_id, status)
             VALUES (:username, :password_hash, :role, :customer_id, :status)"
        );
        $stmt->execute([
            ':username'      => $username,
            ':password_hash' => password_hash((string)$data['password'], PASSWORD_DEFAULT),
            ':role'          => $data['role'] ?? 'customer',
            ':customer_id'   => $this->normalizeCustomerId($data['customer_id'] ?? null),
            ':status'        => $data['status'] ?? 'active',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $username = strtolower(trim($data['username']));
        if ($this->usernameExists($username, $id)) {
            throw new \InvalidArgumentException('Tên đăng nhập đã tồn tại.');
        }

        $passwordHash = null;
        if (!empty($data['password'])) {
            $passwordHash = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE accounts
                SET username = :username, role = :role, customer_id = :customer_id, status = :status";
        $params = [
            ':username'    => $username,
            ':role'        => $data['role'] ?? 'customer',
            ':customer_id' => $this->normalizeCustomerId($data['customer_id'] ?? null),
            ':status'      => $data['status'] ?? 'active',
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
        $stmt = $this->db->prepare("UPDATE accounts SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM accounts WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function countActiveAdmins(?int $excludeId = null): int {
        $sql = "SELECT COUNT(*) FROM accounts WHERE role = 'admin' AND status = 'active'";
        $params = [];
        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function verifyCredentials(string $username, string $password): ?array {
        $account = $this->findByUsername(strtolower(trim($username)));
        if (!$account || $account['status'] !== 'active') {
            return null;
        }
        if (!password_verify($password, $account['password_hash'])) {
            return null;
        }

        $stmt = $this->db->prepare("UPDATE accounts SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id");
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
