<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\BookingModel;
use App\Models\CarModel;

class BookingController extends Controller {
    private BookingModel $bookingModel;
    private CarModel $carModel;

    public function __construct() {
        $this->bookingModel = new BookingModel();
        $this->carModel = new CarModel();
    }

    public function store(): void {
        $carId = (int)($_POST['car_id'] ?? 0);
        $car = $this->carModel->findById($carId);
        if (!$car) {
            $this->redirect('/');
        }

        $pickup = $_POST['pickup_date'] ?? date('Y-m-d H:i', strtotime('+1 day'));
        $return = $_POST['return_date'] ?? date('Y-m-d H:i', strtotime('+3 days'));

        $days = (int)ceil((strtotime($return) - strtotime($pickup)) / 86400);
        if ($days < 1) $days = 1;

        $baseTotal = $car['price_per_day'] * $days;
        $insurance = 95000 * $days;
        $deliveryType = $_POST['delivery_type'] ?? 'pickup';
        $deliveryFee = ($deliveryType === 'delivery') ? 100000 : 0;

        $promoCode = strtoupper(trim($_POST['promo_code'] ?? ''));
        $discount = $promoCode ? $this->bookingModel->checkPromo($promoCode, $baseTotal) : 0;
        $totalAmount = max(0, $baseTotal + $insurance + $deliveryFee - $discount);

        $bookingCode = $this->bookingModel->create([
            'car_id'          => $carId,
            'customer_name'   => trim($_POST['customer_name']),
            'customer_phone'  => trim($_POST['customer_phone']),
            'driver_license'  => trim($_POST['driver_license']),
            'pickup_date'     => $pickup,
            'return_date'     => $return,
            'rental_days'     => $days,
            'delivery_type'   => $deliveryType,
            'promo_code'      => $promoCode,
            'discount_amount' => $discount,
            'total_amount'    => $totalAmount
        ]);

        $_SESSION['flash_success'] = "Đặt xe thành công! Mã đơn của bạn là #{$bookingCode}.";
        $this->redirect('/bookings');
    }

    public function index(): void {
        $bookings = $this->bookingModel->getAll();
        $this->view('bookings/index', [
            'title'    => 'Chuyến đi của tôi - VietCar',
            'bookings' => $bookings
        ]);
    }

    public function cancel(): void {
        $id = (int)($_POST['booking_id'] ?? 0);
        if ($id > 0) {
            $this->bookingModel->cancel($id);
            $_SESSION['flash_success'] = "Đã hủy đơn đặt xe.";
        }
        $this->redirect('/bookings');
    }

    public function inspectionForm(): void {
    $bookingId = (int)($_GET['booking_id'] ?? 0);
    // Truy vấn lấy thông tin đơn hàng tương ứng nếu cần
    $this->view('bookings/inspection', [
        'title'   => 'Biên bản bàn giao xe',
        'booking' => ['id' => $bookingId]
    ]);
}
}
