USE vietcar_db;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) DEFAULT '',
    identity_number VARCHAR(30) DEFAULT '',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE users
    ADD COLUMN status ENUM('active', 'locked') NOT NULL DEFAULT 'active' AFTER role,
    ADD COLUMN customer_id INT NULL AFTER status,
    ADD COLUMN last_login_at DATETIME NULL AFTER customer_id,
    ADD INDEX idx_users_customer_id (customer_id),
    ADD CONSTRAINT fk_users_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;

INSERT INTO customers (full_name, email, phone, address, identity_number, status) VALUES
('Nguyễn Văn An', 'an.nguyen@example.com', '0901234567', 'Cầu Giấy, Hà Nội', '001202012345', 'active'),
('Trần Thị Bình', 'binh.tran@example.com', '0912345678', 'Quận 7, TP.HCM', '001202054321', 'active'),
('Lê Hoàng Cường', 'cuong.le@example.com', '0923456789', 'Hải Châu, Đà Nẵng', '001202077889', 'inactive');
