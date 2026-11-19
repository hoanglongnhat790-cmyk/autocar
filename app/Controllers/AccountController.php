<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\AccountModel;
use App\Models\CustomerModel;

class AccountController extends Controller {
    private AccountModel $accountModel;
    private CustomerModel $customerModel;

    public function __construct() {
        $this->requireAdmin();
        $this->accountModel = new AccountModel();
        $this->customerModel = new CustomerModel();
    }

    public function index(): void {
        $keyword = trim((string)($_GET['keyword'] ?? ''));
        $role = (string)($_GET['role'] ?? 'all');
        $status = (string)($_GET['status'] ?? 'all');

        $this->view('accounts/index', [
            'title'    => 'Quản lý tài khoản - VietCar',
            'accounts' => $this->accountModel->getAll($keyword, $role, $status),
            'keyword'  => $keyword,
            'role'     => $role,
            'status'   => $status
        ]);
    }

    public function createForm(): void {
        $this->view('accounts/form', [
            'title'     => 'Thêm tài khoản - VietCar',
            'account'   => null,
            'customers' => $this->customerModel->getAll(),
            'errors'    => [],
            'old'       => []
        ]);
    }

    public function store(): void {
        $this->verifyCsrfToken();
        $data = $this->postData();
        $errors = Validator::account($data, true);
        if (!$errors) {
            if ($this->accountModel->emailExists($data['email'])) {
                $errors['email'] = 'Email này đã được đăng ký.';
            }
            if (!empty($data['customer_id']) && !$this->customerModel->findById((int)$data['customer_id'])) {
                $errors['customer_id'] = 'Khách hàng liên kết không tồn tại.';
            }
        }

        if ($errors) {
            $this->view('accounts/form', [
                'title'     => 'Thêm tài khoản - VietCar',
                'account'   => null,
                'customers' => $this->customerModel->getAll(),
                'errors'    => $errors,
                'old'       => $data
            ]);
            return;
        }

        $this->accountModel->create($data);
        $_SESSION['flash_success'] = 'Đã tạo tài khoản mới.';
        $this->redirect('/accounts');
    }

    public function editForm(): void {
        $account = $this->accountModel->findById((int)($_GET['id'] ?? 0));
        if (!$account) {
            $this->redirect('/accounts');
        }
        $this->view('accounts/form', [
            'title'     => 'Sửa tài khoản - VietCar',
            'account'   => $account,
            'customers' => $this->customerModel->getAll(),
            'errors'    => [],
            'old'       => $account
        ]);
    }

    public function update(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        $account = $this->accountModel->findById($id);
        if (!$account) {
            $this->redirect('/accounts');
        }

        $data = $this->postData();
        $errors = Validator::account($data, false);
        if (!$errors) {
            if ($this->accountModel->emailExists($data['email'], $id)) {
                $errors['email'] = 'Email này đã được đăng ký.';
            }
            if (!empty($data['customer_id']) && !$this->customerModel->findById((int)$data['customer_id'])) {
                $errors['customer_id'] = 'Khách hàng liên kết không tồn tại.';
            }
        }

        if ($errors) {
            $this->view('accounts/form', [
                'title'     => 'Sửa tài khoản - VietCar',
                'account'   => $account,
                'customers' => $this->customerModel->getAll(),
                'errors'    => $errors,
                'old'       => $data
            ]);
            return;
        }

        $this->accountModel->update($id, $data);
        $_SESSION['flash_success'] = 'Đã cập nhật tài khoản.';
        $this->redirect('/accounts');
    }

    public function toggleStatus(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        $account = $this->accountModel->findById($id);

        if ($account && $status === 'locked') {
            if ($id === (int)(current_user()['id'] ?? 0)) {
                $_SESSION['flash_error'] = 'Không thể khóa tài khoản đang đăng nhập.';
                $this->redirect('/accounts');
            }
            if ($account['role'] === 'admin' && $this->accountModel->countActiveAdmins($id) === 0) {
                $_SESSION['flash_error'] = 'Phải giữ lại ít nhất một quản trị viên đang hoạt động.';
                $this->redirect('/accounts');
            }
        }

        if ($id > 0 && $this->accountModel->setStatus($id, $status)) {
            $_SESSION['flash_success'] = 'Đã cập nhật trạng thái tài khoản.';
        }
        $this->redirect('/accounts');
    }

    public function delete(): void {
        $this->verifyCsrfToken();
        $id = (int)($_POST['id'] ?? 0);
        $account = $this->accountModel->findById($id);

        if (!$account) {
            $this->redirect('/accounts');
        }
        if ($id === (int)(current_user()['id'] ?? 0)) {
            $_SESSION['flash_error'] = 'Không thể xóa tài khoản đang đăng nhập.';
            $this->redirect('/accounts');
        }
        if ($account['role'] === 'admin' && $this->accountModel->countActiveAdmins($id) === 0) {
            $_SESSION['flash_error'] = 'Phải giữ lại ít nhất một quản trị viên đang hoạt động.';
            $this->redirect('/accounts');
        }

        $this->accountModel->delete($id);
        $_SESSION['flash_success'] = 'Đã xóa tài khoản.';
        $this->redirect('/accounts');
    }

    private function postData(): array {
        return [
            'name'        => trim((string)($_POST['name'] ?? '')),
            'email'       => strtolower(trim((string)($_POST['email'] ?? ''))),
            'password'    => (string)($_POST['password'] ?? ''),
            'role'        => (string)($_POST['role'] ?? 'user'),
            'customer_id' => (string)($_POST['customer_id'] ?? ''),
            'status'      => (string)($_POST['status'] ?? 'active'),
        ];
    }
}
