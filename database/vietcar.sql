CREATE DATABASE IF NOT EXISTS vietcar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vietcar_db;

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    category VARCHAR(50) NOT NULL,
    seats INT NOT NULL DEFAULT 5,
    transmission VARCHAR(50) NOT NULL DEFAULT 'Số tự động',
    fuel VARCHAR(30) NOT NULL DEFAULT 'Xăng',
    consumption VARCHAR(50) DEFAULT '6.5L/100km',
    price_per_day INT NOT NULL,
    city VARCHAR(50) NOT NULL,
    district VARCHAR(100) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 5.0,
    trips INT DEFAULT 0,
    image VARCHAR(255) NOT NULL,
    features VARCHAR(255) DEFAULT '',
    host_name VARCHAR(100) DEFAULT 'VietCar Partner',
    description TEXT,
    status ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) NOT NULL UNIQUE,
    car_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    driver_license VARCHAR(50) NOT NULL,
    pickup_date DATETIME NOT NULL,
    return_date DATETIME NOT NULL,
    rental_days INT NOT NULL,
    delivery_type ENUM('pickup', 'delivery') DEFAULT 'pickup',
    promo_code VARCHAR(30) DEFAULT '',
    discount_amount INT DEFAULT 0,
    total_amount INT NOT NULL,
    status ENUM('confirmed', 'completed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    discount_type ENUM('fixed', 'percent') NOT NULL,
    discount_value INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO promotions (code, discount_type, discount_value, is_active) VALUES
('CHAO2026', 'fixed', 150000, 1),
('GIAM10', 'percent', 10, 1);

INSERT INTO cars (name, brand, category, seats, transmission, fuel, consumption, price_per_day, city, district, rating, trips, image, features, host_name, description) VALUES
('VinFast VF 8 Plus 2025', 'VinFast', 'Xe điện', 5, 'Số tự động', 'Điện', '420 km/lần sạc', 1150000, 'Hà Nội', 'Cầu Giấy, Hà Nội', 4.9, 68, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=900&q=80', 'Camera 360,Cửa sổ trời,Cảm biến lùi,Bản đồ GPS', 'Minh Tuấn Auto', 'SUV điện thông minh trang bị trợ lý ảo tiếng Việt, miễn phí sạc tại trạm V-Green.'),
('Mazda 3 Luxury 2024', 'Mazda', '4 chỗ', 4, 'Số tự động', 'Xăng', '6.2L/100km', 750000, 'Hà Nội', 'Thanh Xuân, Hà Nội', 4.8, 112, 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=900&q=80', 'Cảm biến lùi,Bản đồ GPS,Cửa sổ trời', 'Hoàng Nam Car', 'Thiết kế KODO sang trọng, vận hành êm ái, tiết kiệm nhiên liệu.'),
('Hyundai SantaFe Calligraphy 2025', 'Hyundai', '7 chỗ', 7, 'Số tự động', 'Dầu', '6.8L/100km', 1350000, 'TP.HCM', 'Quận 7, TP.HCM', 5.0, 54, 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=900&q=80', 'Camera 360,Cửa sổ trời,Cảm biến lùi,Bản đồ GPS', 'Saigon Premium Fleet', 'SUV 7 chỗ thế hệ mới rộng rãi cho gia đình du lịch dài ngày.'),
('Toyota Corolla Cross 1.8V 2024', 'Toyota', '5 chỗ', 5, 'Số tự động', 'Xăng', '6.5L/100km', 890000, 'Đà Nẵng', 'Hải Châu, Đà Nẵng', 4.9, 95, 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=900&q=80', 'Camera 360,Cảm biến lùi,Bản đồ GPS', 'Đà Nẵng Travel Car', 'Gầm cao linh hoạt, giao xe miễn phí tại sân bay Quốc tế Đà Nẵng.'),
('Ford Ranger Wildtrak 2024', 'Ford', 'Bán tải', 5, 'Số tự động', 'Dầu', '7.5L/100km', 1100000, 'Phú Quốc', 'Dương Đông, Phú Quốc', 4.8, 41, 'https://images.unsplash.com/photo-1559416523-140ddc3d238c?auto=format&fit=crop&w=900&q=80', 'Camera 360,Cảm biến lùi,Bản đồ GPS', 'Đảo Ngọc Auto', 'Bán tải mạnh mẽ chinh phục mọi cung đường đảo ngọc.'),
('Kia Carnival Signature 2024', 'Kia', '7 chỗ', 7, 'Số tự động', 'Dầu', '7.0L/100km', 1650000, 'Hà Nội', 'Long Biên, Hà Nội', 4.9, 73, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=900&q=80', 'Camera 360,Cửa sổ trời,Cảm biến lùi,Bản đồ GPS', 'VIP Limousine Hà Nội', 'MPV 7 ghế thương gia chỉnh điện, cửa lùa tự động.');
