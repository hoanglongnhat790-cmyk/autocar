<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class CarModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getFiltered(array $filters): array {
        $sql = "SELECT * FROM cars WHERE 1=1";
        $params = [];

        // Lọc theo thành phố
        if (!empty($filters['city']) && $filters['city'] !== 'all') {
            $sql .= " AND city = :city";
            $params[':city'] = $filters['city'];
        }

        // Lọc theo loại xe
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }

        // Lọc theo Hãng xe (mảng checkbox)
        if (!empty($filters['brands']) && is_array($filters['brands'])) {
            $brandPlaceholders = [];
            foreach ($filters['brands'] as $i => $brand) {
                $key = ":brand_$i";
                $brandPlaceholders[] = $key;
                $params[$key] = $brand;
            }
            $sql .= " AND brand IN (" . implode(',', $brandPlaceholders) . ")";
        }

        // Lọc theo Nhiên liệu
        if (!empty($filters['fuel']) && $filters['fuel'] !== 'all') {
            $sql .= " AND fuel = :fuel";
            $params[':fuel'] = $filters['fuel'];
        }

        // Lọc theo mức giá tối đa
        if (!empty($filters['max_price'])) {
            $sql .= " AND price_per_day <= :max_price";
            $params[':max_price'] = (int)$filters['max_price'];
        }

        // Lọc theo Tính năng nổi bật (checkbox)
        if (!empty($filters['features']) && is_array($filters['features'])) {
            foreach ($filters['features'] as $i => $feat) {
                $key = ":feat_$i";
                $sql .= " AND features LIKE $key";
                $params[$key] = "%$feat%";
            }
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $car = $stmt->fetch();
        return $car ?: null;
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO cars (name, brand, category, seats, transmission, fuel, consumption, price_per_day, city, district, image, features, host_name, description)
                VALUES (:name, :brand, :category, :seats, :transmission, :fuel, :consumption, :price_per_day, :city, :district, :image, :features, :host_name, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'          => $data['name'],
            ':brand'         => $data['brand'],
            ':category'      => $data['category'],
            ':seats'         => (int)$data['seats'],
            ':transmission'  => $data['transmission'] ?? 'Số tự động',
            ':fuel'          => $data['fuel'],
            ':consumption'   => $data['consumption'] ?? '6.5L/100km',
            ':price_per_day' => (int)$data['price_per_day'],
            ':city'          => $data['city'],
            ':district'      => $data['district'],
            ':image'         => $data['image'],
            ':features'      => $data['features'] ?? 'Camera 360,Cảm biến lùi,Bản đồ GPS',
            ':host_name'     => $data['host_name'] ?? 'Chủ xe đối tác',
            ':description'   => $data['description'] ?? 'Xe gia đình bảo dưỡng chính hãng, sạch sẽ.'
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM cars WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getLocations(): array {
    $stmt = $this->db->query("SELECT id, name, city, district, status, lat, lng, speed FROM cars");
    return $stmt->fetchAll();
}

public function simulateMove(): void {
    // Giả lập xe di chuyển nhích nhẹ tọa độ mỗi lần gọi
    $sql = "UPDATE cars 
            SET lat = lat + (RAND() - 0.5) * 0.004,
                lng = lng + (RAND() - 0.5) * 0.004,
                speed = FLOOR(25 + RAND() * 45)";
    $this->db->query($sql);
}
}