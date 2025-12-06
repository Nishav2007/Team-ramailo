-- ================================================
-- MELAMCHI WATER ALERT SYSTEM - COMPLETE DATABASE
-- Version 2.0 - With Live Water Flow Status
-- ================================================

CREATE DATABASE IF NOT EXISTS melamchi_alert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE melamchi_alert;

-- ================================================
-- TABLE: locations (with water_status for live updates)
-- ================================================
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    zone VARCHAR(100),
    water_status ENUM('flowing', 'not_flowing') DEFAULT 'not_flowing',
    status_updated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location_name (location_name),
    INDEX idx_water_status (water_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: users
-- ================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    location_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_location (location_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: admins (not used - login hardcoded)
-- ================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: water_events (history tracking)
-- ================================================
CREATE TABLE IF NOT EXISTS water_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    arrival_date DATE NOT NULL,
    arrival_time TIME NOT NULL,
    admin_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    INDEX idx_location_date (location_id, arrival_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- INSERT 42 LOCATIONS
-- ================================================
INSERT INTO locations (location_name, district, zone) VALUES
-- Kathmandu District (19 locations)
('Swayambhu', 'Kathmandu', 'Bagmati'),
('Baneshwor', 'Kathmandu', 'Bagmati'),
('Koteshwor', 'Kathmandu', 'Bagmati'),
('Chabahil', 'Kathmandu', 'Bagmati'),
('Thamel', 'Kathmandu', 'Bagmati'),
('Balaju', 'Kathmandu', 'Bagmati'),
('Kalimati', 'Kathmandu', 'Bagmati'),
('Maharajgunj', 'Kathmandu', 'Bagmati'),
('Boudha', 'Kathmandu', 'Bagmati'),
('Naxal', 'Kathmandu', 'Bagmati'),
('Tripureshwor', 'Kathmandu', 'Bagmati'),
('Gongabu', 'Kathmandu', 'Bagmati'),
('Kalanki', 'Kathmandu', 'Bagmati'),
('Sitapaila', 'Kathmandu', 'Bagmati'),
('Bouddha', 'Kathmandu', 'Bagmati'),
('Jorpati', 'Kathmandu', 'Bagmati'),
('Pepsicola', 'Kathmandu', 'Bagmati'),
('Budhanilkantha', 'Kathmandu', 'Bagmati'),
('Thankot', 'Kathmandu', 'Bagmati'),

-- Lalitpur District (9 locations)
('Patan', 'Lalitpur', 'Bagmati'),
('Lagankhel', 'Lalitpur', 'Bagmati'),
('Kupondole', 'Lalitpur', 'Bagmati'),
('Sanepa', 'Lalitpur', 'Bagmati'),
('Jawalakhel', 'Lalitpur', 'Bagmati'),
('Ekantakuna', 'Lalitpur', 'Bagmati'),
('Satdobato', 'Lalitpur', 'Bagmati'),
('Imadol', 'Lalitpur', 'Bagmati'),
('Gwarko', 'Lalitpur', 'Bagmati'),

-- Bhaktapur District (4 locations)
('Bhaktapur', 'Bhaktapur', 'Bagmati'),
('Thimi', 'Bhaktapur', 'Bagmati'),
('Suryabinayak', 'Bhaktapur', 'Bagmati'),
('Madhyapur', 'Bhaktapur', 'Bagmati'),

-- Other Major Cities (10 locations)
('Pokhara', 'Kaski', 'Gandaki'),
('Biratnagar', 'Morang', 'Koshi'),
('Birgunj', 'Parsa', 'Madhesh'),
('Bharatpur', 'Chitwan', 'Bagmati'),
('Janakpur', 'Dhanusha', 'Madhesh'),
('Hetauda', 'Makwanpur', 'Bagmati'),
('Dharan', 'Sunsari', 'Koshi'),
('Butwal', 'Rupandehi', 'Lumbini'),
('Nepalgunj', 'Banke', 'Lumbini'),
('Itahari', 'Sunsari', 'Koshi');

-- ================================================
-- DEFAULT ADMIN (for table only - login hardcoded)
-- ================================================
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ================================================
-- SAMPLE USERS FOR TESTING (Password: test123)
-- ================================================
INSERT INTO users (name, email, password, location_id) VALUES
('Ramesh Sharma', 'ramesh@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),
('Sita Thapa', 'sita@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),
('Hari Prasad', 'hari@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('Maya Gurung', 'maya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5);

-- ================================================
-- SAMPLE WATER EVENTS
-- ================================================
INSERT INTO water_events (location_id, arrival_date, arrival_time, admin_id) VALUES
(4, CURDATE(), '14:30:00', 1),
(4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '16:00:00', 1),
(3, CURDATE(), '09:30:00', 1),
(5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '11:20:00', 1);

-- ================================================
-- DATABASE SETUP COMPLETE!
-- ================================================
SELECT 'Database created successfully! 42 locations, 4 sample users loaded.' as message;

