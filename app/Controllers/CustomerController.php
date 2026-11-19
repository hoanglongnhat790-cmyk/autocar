<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\CustomerModel;

class CustomerController extends Controller {
    private CustomerModel $customerModel;

    public function __construct() {
        $this->requireAdmin();
        $this->customerModel = new CustomerModel();
    }

    public function index(): void {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $status = (string)($_GET['status'] ?? 'all');

        $this->view('customers/index', [
            'title'    => 'Quản lý khách hàng - VietCar',
            'customers' => $this->customerModel->getAll($keyword, $status),
            'keyword'  => $keyword,
            'status'   => $status
        ]);
    }

    public function createForm(): void {
        $this->view('customers/form', [
            'title'    => 'Thêm khách hàng - VietCar',
            'customer' => null,
            'errors'   => [],
            'old'      => []
        ]);
    }

    public function store(): void {
        $this->verifyCsrfToken();
        $data = $this->postData();
        $errors = Validator::customer($data);
        if (!$errors) {
            if ($this->customerModel->findByEmail($data['email'])) {
                $errors['email'] = 'Email này đã được đăng ký.';
            }
        }

        if ($errors) {
            $this->view('customers/form', [
                'title'    => 'Thêm khách hàng - VietCar',
                'customer' => null,
                'errors'   => $errors,
                'old'      => $data
            ]);
            return;
        }

        $this->customerModel->create($data);
        $_SESSION['flash_success'] = 'Đã thêm khách hàng mới.';
        $this->redirect('/customers');
    }

    public function editForm(): void {
        $customer = $this->customerModel->findById((int)($_GET['id'] ?? 0));
        if (!$customer) {
            $this->redirect('/customers');
        }
        $this->view('customers/form', [
            'title'    => 'Sửa khách hàng - VietCar',
            'customer' => $customer,
            'errors'   => [],
            'old'      => $customer
        ]);
    }

    public function update(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        $customer = $this->customerModel->findById($id);
        if (!$customer) {
            $this->redirect('/customers');
        }

        $data = $this->postData();
        $errors = Validator::customer($data);
        if (!$errors) {
            if ($this->customerModel->findByEmail($data['email'], $id)) {
                $errors['email'] = 'Email này đã được đăng ký.';
            }
        }

        if ($errors) {
            $this->view('customers/form', [
                'title'    => 'Sửa khách hàng - VietCar',
                'customer' => $customer,
                'errors'   => $errors,
                'old'      => $data
            ]);
            return;
        }

        $this->customerModel->update($id, $data);
        $_SESSION['flash_success'] = 'Đã cập nhật thông tin khách hàng.';
        $this->redirect('/customers');
    }

    public function toggleStatus(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        if ($id > 0 && $this->customerModel->setStatus($id, $status)) {
            $_SESSION['flash_success'] = 'Đã cập nhật trạng thái khách hàng.';
        }
        $this->redirect('/customers');
    }

    public function delete(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->customerModel->delete($id);
            $_SESSION['flash_success'] = 'Đã xóa khách hàng.';
        }
        $this->redirect('/customers');
    }

    private function postData(): array {
        return [
            'full_name'      => trim((string)($_POST['full_name'] ?? '')),
            'email'          => strtolower(trim((string)($_POST['email'] ?? ''))),
            'phone'          => trim((string)($_POST['phone'] ?? '')),
            'address'        => trim((string)($_POST['address'] ?? '')),
            'identity_number'=> trim((string)($_POST['identity_number'] ?? '')),
            'status'         => (string)($_POST['status'] ?? 'active'),
        ];
    }
}
