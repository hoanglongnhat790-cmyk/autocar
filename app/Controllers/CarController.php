<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\CarModel;

class CarController extends Controller {
    private CarModel $carModel;

    public function __construct() {
        $this->carModel = new CarModel();
    }

    public function index(): void {
        $filters = [
            'city'      => $_GET['city'] ?? 'all',
            'category'  => $_GET['category'] ?? 'all',
            'brands'    => $_GET['brands'] ?? [],
            'fuel'      => $_GET['fuel'] ?? 'all',
            'max_price' => isset($_GET['max_price']) ? (int)$_GET['max_price'] : 3000000,
            'features'  => $_GET['features'] ?? [],
        ];
        
        $cars = $this->carModel->getFiltered($filters);
        $this->view('cars/index', [
            'title'   => 'VietCar - Thuê Xe Tự Lái Toàn Quốc',
            'cars'    => $cars,
            'filters' => $filters
        ]);
    }

    public function detail(): void {
        $id = (int)($_GET['id'] ?? 0);
        $car = $this->carModel->findById($id);
        if (!$car) {
            $this->redirect('/');
        }
        $this->view('cars/detail', [
            'title' => $car['name'] . ' - Đặt xe tự lái',
            'car'   => $car
        ]);
    }
}