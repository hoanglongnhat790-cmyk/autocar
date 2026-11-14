# VietCar - Hệ Thống Cho Thuê Xe Tự Lái (PHP 8 + MVC + MySQL)

## 1. Cấu trúc thư mục chuẩn MVC
```text
vietcar-php-mvc/
├── app/
│   ├── Config/
│   │   └── Database.php         # Kết nối PDO Singleton tới MySQL
│   ├── Controllers/
│   │   ├── CarController.php    # Trang chủ tìm kiếm & chi tiết xe
│   │   ├── BookingController.php# Xử lý đặt xe, tính tiền, mã giảm giá, hủy chuyến
│   │   └── AdminController.php  # Trang quản trị & chủ xe thêm/xóa xe
│   ├── Core/
│   │   ├── Router.php           # Bộ định tuyến URL sạch (Clean URLs)
│   │   ├── Controller.php       # Base Controller render View & Redirect
│   │   └── helpers.php          # Các hàm tiện ích base_url, format_vnd, XSS escape
│   ├── Models/
│   │   ├── CarModel.php         # Truy vấn lọc xe, thêm/xóa xe
│   │   └── BookingModel.php     # Lưu đơn đặt xe, kiểm tra mã khuyến mãi
│   └── Views/
│       ├── layouts/             # Header & Footer dùng chung
│       ├── cars/                # Giao diện danh sách & chi tiết xe
│       ├── bookings/            # Lịch sử chuyến đi
│       └── admin/               # Trang quản trị
├── database/
│   └── vietcar.sql              # File tạo bảng & dữ liệu mẫu
└── public/
    ├── index.php                # Front Controller & Autoloader PSR-4
    ├── .htaccess                # Rewrite URL cho Apache
    ├── css/style.css
    └── js/main.js
```

## 2. Hướng dẫn cài đặt (XAMPP / Laragon)
1. Giải nén thư mục `vietcar-php-mvc` vào `htdocs` (XAMPP) hoặc `www` (Laragon).
2. Mở **phpMyAdmin**, tạo CSDL hoặc import trực tiếp tệp `database/vietcar.sql`.
3. Kiểm tra thông số kết nối MySQL trong `app/Config/Database.php` (mặc định `user: root`, `pass: ''`).
4. Truy cập trình duyệt: `http://localhost/vietcar-php-mvc/public/` (hoặc `http://localhost/vietcar-php-mvc/`).
