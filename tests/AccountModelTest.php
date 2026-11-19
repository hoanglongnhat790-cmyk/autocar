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
            'name' => 'Người dùng test',
            'email' => ' User.Test@Example.com ',
            'password' => 'MatKhau@123',
            'role' => 'user',
            'customer_id' => $customerId,
            'status' => 'active',
        ]);

        $account = $this->model->findById($id);
        $this->assertGreaterThan(0, $id);
        $this->assertSame('Người dùng test', $account['name']);
        $this->assertSame('user.test@example.com', $account['email']);
        $this->assertNotSame('MatKhau@123', $account['password_hash']);
        $this->assertTrue(password_verify('MatKhau@123', $account['password_hash']));
        $this->assertSame('Khách hàng test', $account['customer_name']);

        $loggedIn = $this->model->verifyCredentials('user.test@example.com', 'MatKhau@123');
        $this->assertNotNull($loggedIn);
        $this->assertArrayNotHasKey('password_hash', $loggedIn);
        $this->assertNotNull($loggedIn['last_login_at']);
    }

    public function testDuplicateEmailIsRejected(): void {
        $this->model->create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->assertTrue($this->model->emailExists('admin@example.com'));
        $this->assertFalse($this->model->emailExists('admin@example.com', 1));

        $this->expectException(InvalidArgumentException::class);
        $this->model->create(['name' => 'Admin 2', 'email' => 'ADMIN@example.com', 'password' => 'Admin@123', 'role' => 'admin']);
    }

    public function testGetAllFiltersByRoleStatusAndKeyword(): void {
        $customerId = $this->makeCustomer();
        $this->model->create(['name' => 'Admin One', 'email' => 'admin1@example.com', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['name' => 'User Locked', 'email' => 'locked@example.com', 'password' => 'User@12345', 'role' => 'user', 'status' => 'locked']);
        $this->model->create([
            'name' => 'Khách hàng test', 'email' => 'khach01@example.com', 'password' => 'Customer@123', 'role' => 'user', 'customer_id' => $customerId,
        ]);

        $this->assertCount(3, $this->model->getAll());
        $this->assertCount(1, $this->model->getAll('', 'admin'));
        $this->assertCount(2, $this->model->getAll('', 'all', 'active'));
        $results = $this->model->getAll('Khách hàng test');
        $this->assertCount(1, $results);
        $this->assertSame('khach01@example.com', $results[0]['email']);
    }

    public function testUpdateCanChangePasswordAndKeepItWhenBlank(): void {
        $id = $this->model->create([
            'name' => 'Old Name', 'email' => 'old@example.com', 'password' => 'OldPass@123', 'role' => 'user',
        ]);
        $hashBefore = $this->model->findById($id)['password_hash'];

        $this->assertTrue($this->model->update($id, [
            'name' => 'New Name', 'email' => 'new@example.com', 'password' => '', 'role' => 'admin', 'status' => 'active',
        ]));
        $account = $this->model->findById($id);
        $this->assertSame('New Name', $account['name']);
        $this->assertSame('new@example.com', $account['email']);
        $this->assertSame('admin', $account['role']);
        $this->assertSame($hashBefore, $account['password_hash']);

        $this->model->update($id, [
            'name' => 'New Name', 'email' => 'new@example.com', 'password' => 'NewPass@456', 'role' => 'admin', 'status' => 'active',
        ]);
        $this->assertFalse(password_verify('OldPass@123', $this->model->findById($id)['password_hash']));
        $this->assertTrue(password_verify('NewPass@456', $this->model->findById($id)['password_hash']));
    }

    public function testVerifyCredentialsRejectsWrongPasswordAndLockedAccount(): void {
        $this->model->create([
            'name' => 'Login User', 'email' => 'login@example.com', 'password' => 'Pass@12345', 'role' => 'user',
        ]);
        $this->assertNull($this->model->verifyCredentials('login@example.com', 'wrong-password'));
        $this->assertNull($this->model->verifyCredentials('missing@example.com', 'Pass@12345'));

        $this->model->setStatus(1, 'locked');
        $this->assertNull($this->model->verifyCredentials('login@example.com', 'Pass@12345'));
    }

    public function testStatusGuardAndDelete(): void {
        $this->model->create(['name' => 'Admin 1', 'email' => 'admin1@example.com', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['name' => 'Admin 2', 'email' => 'admin2@example.com', 'password' => 'Admin@123', 'role' => 'admin']);
        $this->model->create(['name' => 'User 1', 'email' => 'user1@example.com', 'password' => 'User@12345', 'role' => 'user']);
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
