<?php
use App\Models\CustomerModel;
use PHPUnit\Framework\TestCase;

final class CustomerModelTest extends TestCase {
    private PDO $db;
    private CustomerModel $model;

    protected function setUp(): void {
        $this->db = TestDatabase::create();
        $this->model = new CustomerModel($this->db);
    }

    public function testCreateAndFindById(): void {
        $id = $this->model->create([
            'full_name' => 'Nguyễn Văn An',
            'email' => ' AN.NGUYEN@Example.COM ',
            'phone' => '0901234567',
            'address' => 'Hà Nội',
            'identity_number' => '001202012345',
            'status' => 'active',
        ]);

        $this->assertGreaterThan(0, $id);
        $customer = $this->model->findById($id);
        $this->assertNotNull($customer);
        $this->assertSame('Nguyễn Văn An', $customer['full_name']);
        $this->assertSame('an.nguyen@example.com', $customer['email']);
        $this->assertSame('active', $customer['status']);
    }

    public function testFindByEmailExcludesGivenId(): void {
        $id = $this->model->create([
            'full_name' => 'Khách A', 'email' => 'a@example.com', 'phone' => '0900000000',
        ]);
        $this->assertNotNull($this->model->findByEmail('a@example.com'));
        $this->assertNull($this->model->findByEmail('a@example.com', $id));
        $this->assertNull($this->model->findByEmail('missing@example.com'));
    }

    public function testGetAllFiltersByKeywordAndStatus(): void {
        foreach ([
            ['Nguyễn Văn An', 'an@example.com', 'active'],
            ['Trần Thị Bình', 'binh@example.com', 'active'],
            ['Lê Văn Cường', 'cuong@example.com', 'inactive'],
        ] as [$name, $email, $status]) {
            $this->model->create([
                'full_name' => $name, 'email' => $email, 'phone' => '0901234567', 'status' => $status,
            ]);
        }

        $this->assertCount(3, $this->model->getAll());
        $this->assertCount(2, $this->model->getAll('', 'active'));
        $this->assertCount(1, $this->model->getAll('Bình'));
        $this->assertCount(0, $this->model->getAll('không tồn tại'));
    }

    public function testUpdateAndSetStatus(): void {
        $id = $this->model->create([
            'full_name' => 'Tên cũ', 'email' => 'old@example.com', 'phone' => '0900000000',
        ]);

        $this->assertTrue($this->model->update($id, [
            'full_name' => 'Tên mới', 'email' => 'NEW@example.com', 'phone' => '0912345678',
            'address' => 'Đà Nẵng', 'status' => 'inactive',
        ]));
        $updated = $this->model->findById($id);
        $this->assertSame('Tên mới', $updated['full_name']);
        $this->assertSame('new@example.com', $updated['email']);
        $this->assertSame('Đà Nẵng', $updated['address']);

        $this->assertTrue($this->model->setStatus($id, 'active'));
        $this->assertFalse($this->model->setStatus($id, 'unknown'));
        $this->assertSame('active', $this->model->findById($id)['status']);
    }

    public function testDeleteRemovesCustomer(): void {
        $id = $this->model->create([
            'full_name' => 'Xóa thử', 'email' => 'delete@example.com', 'phone' => '0900000000',
        ]);
        $this->assertTrue($this->model->delete($id));
        $this->assertNull($this->model->findById($id));
    }
}
