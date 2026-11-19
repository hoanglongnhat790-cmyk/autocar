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
│   │   ├── AdminController.php   # Trang quản trị & chủ xe thêm/xóa xe
│   │   ├── AuthController.php    # Đăng nhập / đăng xuất
│   │   ├── CustomerController.php# CRUD khách hàng
│   │   └── AccountController.php # CRUD tài khoản, khóa/mở khóa
│   ├── Core/
│   │   ├── Router.php            # Bộ định tuyến URL sạch (Clean URLs)
│   │   ├── Controller.php        # Base Controller render View & Redirect
│   │   ├── Auth.php              # Kiểm tra đăng nhập & phân quyền
│   │   ├── Validator.php         # Kiểm tra dữ liệu đầu vào
│   │   └── helpers.php           # base_url, format_vnd, e() (chống XSS)
│   ├── Models/
│   │   ├── CarModel.php          # Truy vấn lọc xe, thêm/xóa xe
│   │   ├── BookingModel.php      # Lưu đơn đặt xe, kiểm tra mã khuyến mãi
│   │   ├── CustomerModel.php     # Truy vấn khách hàng
│   │   └── AccountModel.php      # Tài khoản, hash/verify mật khẩu
│   └── Views/
│       ├── layouts/              # Header & Footer dùng chung
│       ├── auth/                 # Trang đăng nhập
│       ├── customers/            # Danh sách & form khách hàng
│       ├── accounts/             # Danh sách & form tài khoản
│       ├── cars/                 # Danh sách & chi tiết xe
│       ├── bookings/             # Lịch sử chuyến đi
│       └── admin/                # Trang quản trị
├── database/
│   └── vietcar.sql               # File tạo bảng & dữ liệu mẫu
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
2. Mở **phpMyAdmin**, import trực tiếp tệp `database/vietcar.sql` (tự tạo CSDL `vietcar_db`).
3. Kiểm tra thông số kết nối MySQL trong `app/Config/Database.php` (mặc định `user: root`, `pass: ''`).
4. Truy cập `http://localhost/autocar/public/` (hoặc `http://localhost/autocar/`).

## 3. Quản lý khách hàng & tài khoản
- Đăng nhập tại `/login`, sau đó dùng menu **Khách hàng** và **Tài khoản**.
- `customers`: thông tin khách hàng (họ tên, email, phone, CCCD, địa chỉ, trạng thái).
- `accounts`: tên đăng nhập, mật khẩu đã hash, vai trò (`admin`/`staff`/`customer`), khách hàng liên kết, trạng thái.
- Chỉ `admin` được truy cập trang tài khoản; `admin` và `staff` được quản lý khách hàng.
- Không thể khóa/xóa chính tài khoản đang đăng nhập hoặc admin cuối còn hoạt động.

Tài khoản mẫu sau khi import SQL:

| Tên đăng nhập | Mật khẩu | Vai trò |
|---|---|---|
| `admin` | `Admin@123` | Quản trị viên |
| `staff` | `Staff@123` | Nhân viên |
| `an.nguyen` | `Customer@123` | Khách hàng |

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
