<?php
use App\Models\AccountModel;
use App\Models\CustomerModel;
use PHPUnit\Framework\TestCase;

final class AccountModelTest extends TestCase {
    private PDO $db;
    private AccountModel $model;
    private CustomerModel $customers;

    protected function setUp(): void {
        $this->db = TestDatabase::create();
        $this->model = new AccountModel($this->db);
        $this->customers = new CustomerModel($this->db);
    }

    private function makeCustomer(): int {
        return $this->customers->create([
            'full_name' => 'Khách hàng test',
            'email' => 'khach@test.com',
            'phone' => '0901234567',
        ]);
    }

    public function testCreateHashesPasswordAndLoginWorks(): void {
        $customerId = $this->makeCustomer();
        $id = $this->model->create([
            'username' => ' User.Test ',
            'password' => 'MatKhau@123',
            'role' => 'customer',
            'customer_id' => $customerId,
            'status' => 'active',
        ]);

        $account = $this->model->findById($id);
        $this->assertGreaterThan(0, $id);
        $this->assertSame('user.test', $account['username']);
        $this->assertNotSame('MatKhau@123', $account['password_hash']);
        $this->assertTrue(password_verify('MatKhau@123', $account['password_hash']));
        $this->assertSame('Khách hàng test', $account['customer_name']);

        $loggedIn = $this->model->verifyCredentials('user.test', 'MatKhau@123');
        $this->assertNotNull($loggedIn);
        $this->assertArrayNotHasKey('password_hash', $loggedIn);
        $this->assertNotNull($loggedIn['last_login_at']);
    }

    public function testDuplicateUsernameIsRejected(): void {
        $this->model->create(['username' => 'admin', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->assertTrue($this->model->usernameExists('admin'));
        $this->assertFalse($this->model->usernameExists('admin', 1));

        $this->expectException(InvalidArgumentException::class);
        $this->model->create(['username' => 'ADMIN', 'password' => 'Admin@123', 'role' => 'admin']);
    }

    public function testGetAllFiltersByRoleStatusAndKeyword(): void {
        $customerId = $this->makeCustomer();
        $this->model->create(['username' => 'admin01', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['username' => 'staff01', 'password' => 'Staff@123', 'role' => 'staff', 'status' => 'locked']);
        $this->model->create([
            'username' => 'khach01', 'password' => 'Customer@123', 'role' => 'customer', 'customer_id' => $customerId,
        ]);

        $this->assertCount(3, $this->model->getAll());
        $this->assertCount(1, $this->model->getAll('', 'admin'));
        $this->assertCount(2, $this->model->getAll('', 'all', 'active'));
        $results = $this->model->getAll('Khách hàng test');
        $this->assertCount(1, $results);
        $this->assertSame('khach01', $results[0]['username']);
    }

    public function testUpdateCanChangePasswordAndKeepItWhenBlank(): void {
        $id = $this->model->create([
            'username' => 'old.name', 'password' => 'OldPass@123', 'role' => 'staff',
        ]);
        $hashBefore = $this->model->findById($id)['password_hash'];

        $this->assertTrue($this->model->update($id, [
            'username' => 'new.name', 'password' => '', 'role' => 'admin', 'status' => 'active',
        ]));
        $account = $this->model->findById($id);
        $this->assertSame('new.name', $account['username']);
        $this->assertSame('admin', $account['role']);
        $this->assertSame($hashBefore, $account['password_hash']);

        $this->model->update($id, [
            'username' => 'new.name', 'password' => 'NewPass@456', 'role' => 'admin', 'status' => 'active',
        ]);
        $this->assertFalse(password_verify('OldPass@123', $this->model->findById($id)['password_hash']));
        $this->assertTrue(password_verify('NewPass@456', $this->model->findById($id)['password_hash']));
    }

    public function testVerifyCredentialsRejectsWrongPasswordAndLockedAccount(): void {
        $this->model->create(['username' => 'login01', 'password' => 'Pass@12345', 'role' => 'customer']);
        $this->assertNull($this->model->verifyCredentials('login01', 'wrong-password'));
        $this->assertNull($this->model->verifyCredentials('missing', 'Pass@12345'));

        $this->model->setStatus(1, 'locked');
        $this->assertNull($this->model->verifyCredentials('login01', 'Pass@12345'));
    }

    public function testStatusGuardAndDelete(): void {
        $this->model->create(['username' => 'admin01', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['username' => 'admin02', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['username' => 'staff01', 'password' => 'Staff@123', 'role' => 'staff']);
        $this->model->setStatus(2, 'locked');

        $this->assertSame(1, $this->model->countActiveAdmins());
        $this->assertSame(2, $this->model->countActiveAdmins(1));
        $this->assertFalse($this->model->setStatus(1, 'unknown'));

        $this->assertTrue($this->model->setStatus(3, 'locked'));
        $this->assertSame('locked', $this->model->findById(3)['status']);
        $this->assertTrue($this->model->delete(3));
        $this->assertNull($this->model->findById(3));
    }
}
