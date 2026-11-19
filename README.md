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
│   │   ├── AuthController.php   # Đăng ký, đăng nhập, đăng xuất
│   │   ├── SupportController.php# Hỗ trợ khách hàng bằng Gemini
│   │   └── AdminController.php  # Quản trị xe và xem lịch sử hỗ trợ
│   ├── Core/
│   │   ├── Router.php           # Bộ định tuyến URL sạch (Clean URLs)
│   │   ├── Controller.php       # Base Controller render View & Redirect
│   │   └── helpers.php          # Các hàm tiện ích base_url, format_vnd, XSS escape
│   ├── Models/
│   │   ├── CarModel.php         # Truy vấn lọc xe, thêm/xóa xe
│   │   ├── BookingModel.php     # Lưu đơn đặt xe theo tài khoản
│   │   ├── AuthModel.php        # Tài khoản và phân quyền
│   │   └── SupportModel.php     # Lịch sử chat MySQL
│   ├── Services/
│   │   └── GeminiService.php   # Tích hợp Gemini API
│   └── Views/
│       ├── layouts/             # Header & Footer dùng chung
│       ├── auth/                # Đăng ký và đăng nhập
│       ├── cars/                # Giao diện danh sách & chi tiết xe
│       ├── bookings/            # Lịch sử chuyến đi
│       ├── support/             # Chat hỗ trợ
│       └── admin/               # Trang quản trị và lịch sử hỗ trợ
├── database/
│   ├── vietcar.sql              # File tạo bảng & dữ liệu mẫu
│   └── migrations/              # Bổ sung tài khoản và hỗ trợ
├── scripts/
│   └── create_admin.php         # Tạo tài khoản quản trị đầu tiên
└── public/
    ├── index.php                # Front Controller & Autoloader PSR-4
    ├── .htaccess                # Rewrite URL cho Apache
    ├── css/style.css
    └── js/main.js
```

## 2. Hướng dẫn cài đặt (XAMPP / Laragon)
1. Giải nén thư mục `vietcar-php-mvc` vào `htdocs` (XAMPP) hoặc `www` (Laragon).
2. Mở **phpMyAdmin**, tạo CSDL hoặc import trực tiếp tệp `database/vietcar.sql`.
3. Mặc định MySQL dùng `127.0.0.1:3306`, database `vietcar_db`, user `root`, mật khẩu trống. Nếu cấu hình khác, đặt các biến môi trường `VIETCAR_DB_HOST`, `VIETCAR_DB_PORT`, `VIETCAR_DB_NAME`, `VIETCAR_DB_USER`, `VIETCAR_DB_PASS`.
4. Trong phpMyAdmin, chọn database `vietcar_db` rồi import một lần `database/migrations/001_accounts_support.sql` để thêm tài khoản, lịch sử hỗ trợ và liên kết đơn đặt xe với khách hàng.
5. Tạo admin đầu tiên từ Laragon Terminal/PowerShell sau khi đặt `VIETCAR_ADMIN_EMAIL` và `VIETCAR_ADMIN_PASSWORD` (ít nhất 12 ký tự):
   ```powershell
   $env:VIETCAR_ADMIN_EMAIL = "admin@example.com"
   $env:VIETCAR_ADMIN_PASSWORD = "thay-bang-mat-khau-manh"
   & "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" scripts\create_admin.php
   ```
   Đây là giá trị ví dụ; hãy dùng email và mật khẩu riêng. Script không tạo tài khoản nếu email đã tồn tại.
6. Đặt `VIETCAR_GEMINI_API_KEY` trong biến môi trường Windows để bật chatbot, sau đó khởi động lại Laragon để Apache nhận biến. Có thể đặt model bằng `VIETCAR_GEMINI_MODEL` (mặc định `gemini-2.5-flash`). Không đưa API key vào mã nguồn hoặc chia sẻ công khai.
7. Truy cập `http://localhost/vietcar-php-mvc/public/` (hoặc `http://localhost/vietcar-php-mvc/`). Khách hàng đăng ký qua nút **Đăng ký**; admin đăng nhập bằng tài khoản đã tạo và vào **Quản trị**.

Tài khoản khách chỉ có quyền khách hàng; không có trường chọn role trong form đăng ký. Chatbot và lịch sử hỗ trợ yêu cầu người dùng đăng nhập. Admin xem lịch sử tại **Quản trị → Lịch sử hỗ trợ**.
