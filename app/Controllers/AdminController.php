<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CarModel;
use App\Models\BookingModel;
use App\Models\SupportModel;

class AdminController extends Controller {
    private CarModel $carModel;
    private BookingModel $bookingModel;
    private SupportModel $supportModel;

    public function __construct() {
        $this->carModel = new CarModel();
        $this->bookingModel = new BookingModel();
        $this->supportModel = new SupportModel();
    }

    public function index(): void {
        $this->requireAdmin();
        $cars = $this->carModel->getFiltered([]);
        $bookings = $this->bookingModel->getAll();
        $revenue = array_sum(array_map(fn($b) => $b['status'] !== 'cancelled' ? (int)$b['total_amount'] : 0, $bookings));

        $this->view('admin/index', [
            'title'    => 'Quản trị hệ thống & Chủ xe - VietCar',
            'cars'     => $cars,
            'bookings' => $bookings,
            'revenue'  => $revenue
        ]);
    }

    public function storeCar(): void {
        $this->requireAdmin();
        $this->verifyCsrfToken();
        $image = trim($_POST['image'] ?? '');
        if (empty($image)) {
            $image = 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=900&q=80';
        }
        $features = isset($_POST['features']) && is_array($_POST['features']) ? implode(',', $_POST['features']) : '';

        $this->carModel->create([
            'name'          => trim($_POST['name']),
            'brand'         => $_POST['brand'],
            'category'      => $_POST['category'],
            'seats'         => ($_POST['category'] === '7 chỗ') ? 7 : 5,
            'fuel'          => $_POST['fuel'],
            'price_per_day' => (int)$_POST['price_per_day'],
            'city'          => $_POST['city'],
            'district'      => trim($_POST['district']),
            'image'         => $image,
            'features'      => $features,
            'host_name'     => trim($_POST['host_name']),
            'description'   => trim($_POST['description'])
        ]);

        $_SESSION['flash_success'] = "Đã thêm xe mới lên hệ thống!";
        $this->redirect('/admin');
    }

    public function deleteCar(): void {
        $this->requireAdmin();
        $this->verifyCsrfToken();
        $id = (int)($_POST['car_id'] ?? 0);
        if ($id > 0) {
            $this->carModel->delete($id);
            $_SESSION['flash_success'] = "Đã xóa xe khỏi danh sách.";
        }
        $this->redirect('/admin');
    }

    public function getLocations(): void {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->carModel->getLocations());
        exit;
    }

    public function simulateMovement(): void {
        $this->requireAdmin();
        $this->verifyCsrfToken();
        $this->carModel->simulateMove();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'ok']);
        exit;
    }

    public function supportHistory(): void {
        $this->requireAdmin();
        $this->view('admin/support', [
            'title' => 'Lịch sử hỗ trợ - VietCar',
            'messages' => $this->supportModel->getRecentForAdmin(),
        ]);
    }
}
