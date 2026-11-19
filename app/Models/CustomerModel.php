<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class CustomerModel {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(string $keyword = '', string $status = 'all'): array {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM users u WHERE u.customer_id = c.id) AS account_count
                FROM customers c WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (c.full_name LIKE :keyword OR c.email LIKE :keyword OR c.phone LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (in_array($status, ['active', 'inactive'], true)) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY c.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email, ?int $excludeId = null): ?array {
        $sql = "SELECT * FROM customers WHERE email = :email";
        $params = [':email' => $email];
        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO customers (full_name, email, phone, address, identity_number, status)
             VALUES (:full_name, :email, :phone, :address, :identity_number, :status)"
        );
        $stmt->execute([
            ':full_name'      => trim($data['full_name']),
            ':email'          => strtolower(trim($data['email'])),
            ':phone'          => trim($data['phone']),
            ':address'        => trim($data['address'] ?? ''),
            ':identity_number'=> trim($data['identity_number'] ?? ''),
            ':status'         => $data['status'] ?? 'active',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE customers
             SET full_name = :full_name, email = :email, phone = :phone,
                 address = :address, identity_number = :identity_number, status = :status
             WHERE id = :id"
        );
        return $stmt->execute([
            ':full_name'      => trim($data['full_name']),
            ':email'          => strtolower(trim($data['email'])),
            ':phone'          => trim($data['phone']),
            ':address'        => trim($data['address'] ?? ''),
            ':identity_number'=> trim($data['identity_number'] ?? ''),
            ':status'         => $data['status'] ?? 'active',
            ':id'             => $id,
        ]);
    }

    public function setStatus(int $id, string $status): bool {
        if (!in_array($status, ['active', 'inactive'], true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE customers SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
