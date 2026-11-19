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
        $data = [
            'name' => 'Người dùng', 'email' => 'user@example.com',
            'password' => '', 'role' => 'user', 'status' => 'active',
        ];
        $this->assertArrayHasKey('password', Validator::account($data, true));
    }

    public function testAccountValidatorAllowsEmptyPasswordWhenUpdating(): void {
        $data = [
            'name' => 'Người dùng', 'email' => 'user@example.com',
            'password' => '', 'role' => 'user', 'status' => 'active',
        ];
        $this->assertSame([], Validator::account($data, false));
    }

    public function testAccountValidatorRejectsInvalidEmailRoleAndStatus(): void {
        $errors = Validator::account([
            'name' => '',
            'email' => 'bad-email',
            'password' => '12345678',
            'role' => 'superadmin',
            'status' => 'banned',
            'customer_id' => -1,
        ], true);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('role', $errors);
        $this->assertArrayHasKey('status', $errors);
        $this->assertArrayHasKey('customer_id', $errors);
    }
}
