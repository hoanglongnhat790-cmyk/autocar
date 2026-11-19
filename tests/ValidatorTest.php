<?php
use App\Core\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase {
    public function testCustomerValidatorAcceptsValidData(): void {
        $errors = Validator::customer([
            'full_name' => 'Nguyễn Văn A',
            'email' => 'a@example.com',
            'phone' => '0901 234 567',
            'status' => 'active',
        ]);
        $this->assertSame([], $errors);
    }

    public function testCustomerValidatorRejectsMissingAndInvalidFields(): void {
        $errors = Validator::customer([
            'full_name' => '',
            'email' => 'not-an-email',
            'phone' => '123',
            'status' => 'unknown',
        ]);
        $this->assertArrayHasKey('full_name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('phone', $errors);
        $this->assertArrayHasKey('status', $errors);
    }

    public function testAccountValidatorRequiresPasswordWhenCreating(): void {
        $data = ['username' => 'user_01', 'password' => '', 'role' => 'staff', 'status' => 'active'];
        $this->assertArrayHasKey('password', Validator::account($data, true));
    }

    public function testAccountValidatorAllowsEmptyPasswordWhenUpdating(): void {
        $data = ['username' => 'user_01', 'password' => '', 'role' => 'staff', 'status' => 'active'];
        $this->assertSame([], Validator::account($data, false));
    }

    public function testAccountValidatorRejectsInvalidUsernameRoleAndStatus(): void {
        $errors = Validator::account([
            'username' => 'a b',
            'password' => '12345678',
            'role' => 'superadmin',
            'status' => 'banned',
            'customer_id' => -1,
        ], true);
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('role', $errors);
        $this->assertArrayHasKey('status', $errors);
        $this->assertArrayHasKey('customer_id', $errors);
    }
}
