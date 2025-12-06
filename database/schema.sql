-- ================================================
-- MELAMCHI WATER ALERT SYSTEM - DATABASE SCHEMA
-- ================================================
-- Run this script in phpMyAdmin or MySQL command line
-- to create the database and all tables

-- Create database
CREATE DATABASE IF NOT EXISTS melamchi_alert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE melamchi_alert;

-- ================================================
-- TABLE: locations
-- Stores all available locations in Nepal
-- ================================================
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    zone VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location_name (location_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: users
-- Stores registered users
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
-- TABLE: admins
-- Stores admin users
-- ================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE: water_events
-- Records when water arrives in each location
-- ================================================
CREATE TABLE IF NOT EXISTS water_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    arrival_date DATE NOT NULL,
    arrival_time TIME NOT NULL,
    admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_location_date (location_id, arrival_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- INSERT SAMPLE LOCATIONS (Kathmandu Valley)
-- ================================================
INSERT INTO locations (location_name, district, zone) VALUES
-- Kathmandu District
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

-- Lalitpur District
('Patan', 'Lalitpur', 'Bagmati'),
('Lagankhel', 'Lalitpur', 'Bagmati'),
('Kupondole', 'Lalitpur', 'Bagmati'),
('Sanepa', 'Lalitpur', 'Bagmati'),
('Jawalakhel', 'Lalitpur', 'Bagmati'),
('Ekantakuna', 'Lalitpur', 'Bagmati'),
('Satdobato', 'Lalitpur', 'Bagmati'),
('Imadol', 'Lalitpur', 'Bagmati'),
('Gwarko', 'Lalitpur', 'Bagmati'),

-- Bhaktapur District
('Bhaktapur', 'Bhaktapur', 'Bagmati'),
('Thimi', 'Bhaktapur', 'Bagmati'),
('Suryabinayak', 'Bhaktapur', 'Bagmati'),
('Madhyapur', 'Bhaktapur', 'Bagmati'),

-- Additional Major Cities
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
-- CREATE DEFAULT ADMIN USER
-- Username: admin
-- Password: admin123
-- ================================================
-- Password hash for 'admin123'
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: In production, change this password immediately!
-- To generate a new password hash, use PHP:
-- echo password_hash('your_password', PASSWORD_DEFAULT);

-- ================================================
-- INSERT SAMPLE DATA (Optional - for testing)
-- ================================================

-- Sample users (Password for all: test123)
INSERT INTO users (name, email, password, location_id) VALUES
('Ramesh Sharma', 'ramesh@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),
('Sita Thapa', 'sita@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4),
('Hari Prasad', 'hari@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('Maya Gurung', 'maya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5);

-- Sample water events
INSERT INTO water_events (location_id, arrival_date, arrival_time, admin_id) VALUES
(4, CURDATE(), '14:30:00', 1),
(4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '16:00:00', 1),
(4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '09:15:00', 1),
(3, CURDATE(), '09:30:00', 1),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '15:45:00', 1),
(5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '11:20:00', 1);

-- ================================================
-- VERIFICATION QUERIES
-- ================================================
-- Run these to verify the database was created correctly

-- Check tables
-- SHOW TABLES;

-- Check locations count
-- SELECT COUNT(*) as total_locations FROM locations;

-- Check admin user
-- SELECT id, username FROM admins;

-- Check sample users
-- SELECT u.name, u.email, l.location_name 
-- FROM users u 
-- JOIN locations l ON u.location_id = l.id;

-- Check sample water events
-- SELECT we.id, l.location_name, we.arrival_date, we.arrival_time 
-- FROM water_events we 
-- JOIN locations l ON we.location_id = l.id 
-- ORDER BY we.arrival_date DESC;

-- ================================================
-- DATABASE SETUP COMPLETE!
-- ================================================

