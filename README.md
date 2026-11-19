# VietCar - Hệ Thống Cho Thuê Xe Tự Lái (PHP 8 + MVC + MySQL)

## 1. Cấu trúc thư mục chuẩn MVC
```text
autocar/
├── app/
│   ├── Config/
│   │   └── Database.php          # Kết nối PDO Singleton tới MySQL
│   ├── Controllers/
│   │   ├── CarController.php     # Trang chủ tìm kiếm & chi tiết xe
│   │   ├── BookingController.php # Đặt xe, tính tiền, mã giảm giá, hủy chuyến
│   │   ├── AuthController.php    # Đăng ký, đăng nhập, đăng xuất
│   │   ├── CustomerController.php# CRUD khách hàng (admin)
│   │   ├── AccountController.php # CRUD tài khoản (admin)
│   │   ├── SupportController.php # Hỗ trợ khách hàng bằng Gemini
│   │   └── AdminController.php   # Quản trị xe và xem lịch sử hỗ trợ
│   ├── Core/
│   │   ├── Router.php            # Bộ định tuyến URL sạch (Clean URLs)
│   │   ├── Controller.php        # Base Controller render View, redirect, CSRF
│   │   ├── Validator.php         # Kiểm tra dữ liệu khách hàng/tài khoản
│   │   └── helpers.php           # base_url, format_vnd, e(), csrf_field(), current_user()
│   ├── Models/
│   │   ├── CarModel.php          # Truy vấn lọc xe, thêm/xóa xe
│   │   ├── BookingModel.php      # Lưu đơn đặt xe theo tài khoản
│   │   ├── CustomerModel.php     # Truy vấn khách hàng
│   │   ├── AccountModel.php      # Quản lý bảng users (hash/verify mật khẩu)
│   │   ├── AuthModel.php         # Tài khoản và phân quyền
│   │   └── SupportModel.php      # Lịch sử chat MySQL
│   ├── Services/
│   │   └── GeminiService.php     # Tích hợp Gemini API
│   └── Views/
│       ├── layouts/              # Header & Footer dùng chung
│       ├── auth/                 # Đăng ký và đăng nhập
│       ├── customers/            # Danh sách & form khách hàng
│       ├── accounts/             # Danh sách & form tài khoản
│       ├── cars/                 # Giao diện danh sách & chi tiết xe
│       ├── bookings/             # Lịch sử chuyến đi
│       ├── support/              # Chat hỗ trợ
│       └── admin/                # Trang quản trị và lịch sử hỗ trợ
├── database/
│   ├── vietcar.sql               # File tạo bảng & dữ liệu mẫu
│   └── migrations/               # 001 tài khoản/hỗ trợ, 002 khách hàng/quản lý tài khoản
├── scripts/
│   └── create_admin.php          # Tạo tài khoản quản trị đầu tiên
├── tests/
│   ├── bootstrap.php             # CSDL SQLite in-memory cho test
│   ├── ValidatorTest.php
│   ├── CustomerModelTest.php
│   └── AccountModelTest.php
├── composer.json                 # Autoload PSR-4 & PHPUnit
├── phpunit.xml
└── public/
    ├── index.php                 # Front Controller & Autoloader PSR-4
    ├── .htaccess                 # Rewrite URL cho Apache
    ├── css/style.css
    └── js/main.js
```

## 2. Hướng dẫn cài đặt (XAMPP / Laragon)
1. Giải nén thư mục `autocar` vào `htdocs` (XAMPP) hoặc `www` (Laragon).
2. Mở **phpMyAdmin**, tạo CSDL hoặc import trực tiếp tệp `database/vietcar.sql`.
3. Mặc định MySQL dùng `127.0.0.1:3306`, database `vietcar_db`, user `root`, mật khẩu trống. Nếu cấu hình khác, đặt các biến môi trường `VIETCAR_DB_HOST`, `VIETCAR_DB_PORT`, `VIETCAR_DB_NAME`, `VIETCAR_DB_USER`, `VIETCAR_DB_PASS`.
4. Trong phpMyAdmin, chọn database `vietcar_db` rồi import **theo thứ tự**:
   - `database/migrations/001_accounts_support.sql` — bảng `users`, `support_messages`, liên kết `bookings.user_id`.
   - `database/migrations/002_customer_account_management.sql` — bảng `customers`, các cột `status`/`customer_id`/`last_login_at` của `users` và dữ liệu khách hàng mẫu.
5. Tạo admin đầu tiên từ Laragon Terminal/PowerShell sau khi đặt `VIETCAR_ADMIN_EMAIL` và `VIETCAR_ADMIN_PASSWORD` (ít nhất 12 ký tự):
   ```powershell
   $env:VIETCAR_ADMIN_EMAIL = "admin@example.com"
   $env:VIETCAR_ADMIN_PASSWORD = "thay-bang-mat-khau-manh"
   & "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" scripts\create_admin.php
   ```
   Đây là giá trị ví dụ; hãy dùng email và mật khẩu riêng. Script không tạo tài khoản nếu email đã tồn tại.
6. Đặt `VIETCAR_GEMINI_API_KEY` trong biến môi trường Windows để bật chatbot, sau đó khởi động lại Laragon để Apache nhận biến. Có thể đặt model bằng `VIETCAR_GEMINI_MODEL` (mặc định `gemini-2.5-flash`). Không đưa API key vào mã nguồn hoặc chia sẻ công khai.
7. Truy cập `http://localhost/autocar/public/` (hoặc `http://localhost/autocar/`). Khách hàng đăng ký qua nút **Đăng ký**; admin đăng nhập bằng tài khoản đã tạo và vào **Quản trị**.

## 3. Quản lý khách hàng & tài khoản
- Sau khi đăng nhập bằng tài khoản `admin`, dùng menu **Khách hàng** và **Tài khoản**.
- `customers`: họ tên, email, điện thoại, CCCD, địa chỉ, trạng thái (`active`/`inactive`); tìm kiếm và lọc theo trạng thái.
- Tài khoản dùng chung bảng `users` với đăng nhập/đăng ký: thêm/sửa/xóa người dùng, đổi vai trò (`user`/`admin`), đặt mật khẩu mới, khóa/mở khóa, liên kết với khách hàng.
- Tài khoản bị khóa không đăng nhập được. Không thể khóa/xóa chính tài khoản đang đăng nhập hoặc admin cuối còn hoạt động.
- Mọi form POST đều bắt buộc CSRF token (`csrf_field()`).

## 4. Kiểm thử (PHPUnit)
Yêu cầu: PHP >= 8.0, Composer, extension PDO SQLite (`pdo_sqlite`).

```bash
composer install
composer test
# hoặc: vendor/bin/phpunit
```

Test dùng CSDL SQLite in-memory, không cần MySQL. Để chạy riêng một file:

```bash
vendor/bin/phpunit tests/AccountModelTest.php
```
