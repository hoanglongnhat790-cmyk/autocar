<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class BookingModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function checkPromo(string $code, int $baseTotal): int {
        $stmt = $this->db->prepare("SELECT * FROM promotions WHERE code = :code AND is_active = 1");
        $stmt->execute([':code' => strtoupper(trim($code))]);
        $promo = $stmt->fetch();
        if (!$promo) return 0;

        if ($promo['discount_type'] === 'fixed') {
            return (int)$promo['discount_value'];
        }
        return (int)round($baseTotal * ($promo['discount_value'] / 100));
    }

    public function create(array $data): string {
        $code = 'VC' . rand(100000, 999999);
        $sql = "INSERT INTO bookings (booking_code, car_id, customer_name, customer_phone, driver_license, pickup_date, return_date, rental_days, delivery_type, promo_code, discount_amount, total_amount)
                VALUES (:booking_code, :car_id, :customer_name, :customer_phone, :driver_license, :pickup_date, :return_date, :rental_days, :delivery_type, :promo_code, :discount_amount, :total_amount)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':booking_code'    => $code,
            ':car_id'          => $data['car_id'],
            ':customer_name'   => $data['customer_name'],
            ':customer_phone'  => $data['customer_phone'],
            ':driver_license'  => $data['driver_license'],
            ':pickup_date'     => $data['pickup_date'],
            ':return_date'     => $data['return_date'],
            ':rental_days'     => $data['rental_days'],
            ':delivery_type'   => $data['delivery_type'],
            ':promo_code'      => $data['promo_code'],
            ':discount_amount' => $data['discount_amount'],
            ':total_amount'    => $data['total_amount'],
        ]);

        // Tăng số chuyến của xe
        $this->db->prepare("UPDATE cars SET trips = trips + 1 WHERE id = :id")->execute([':id' => $data['car_id']]);
        return $code;
    }

    public function getAll(): array {
        $sql = "SELECT b.*, c.name AS car_name, c.image AS car_image, c.district AS car_district
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                ORDER BY b.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function cancel(int $id): bool {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
