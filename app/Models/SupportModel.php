<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class SupportModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getHistory(int $userId, int $limit = 40): array {
        $stmt = $this->db->prepare(
            'SELECT id, role, message, created_at
             FROM (
                 SELECT id, role, message, created_at
                 FROM support_messages
                 WHERE user_id = :user_id
                 ORDER BY id DESC
                 LIMIT :limit
             ) AS recent_messages
             ORDER BY id ASC'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function saveExchange(int $userId, string $question, string $answer): void {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO support_messages (user_id, role, message) VALUES (:user_id, :role, :message)'
            );
            $stmt->execute([':user_id' => $userId, ':role' => 'user', ':message' => $question]);
            $stmt->execute([':user_id' => $userId, ':role' => 'assistant', ':message' => $answer]);
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function getRecentForAdmin(int $limit = 200): array {
        $stmt = $this->db->prepare(
            'SELECT m.id, m.role, m.message, m.created_at, u.name AS user_name, u.email AS user_email
             FROM support_messages m
             JOIN users u ON u.id = m.user_id
             ORDER BY m.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
