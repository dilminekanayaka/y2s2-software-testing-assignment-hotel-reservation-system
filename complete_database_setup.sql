-- Complete Hotel Reservation System Database Setup
-- Flower Garden Hotel Reservation System with Coupon Functionality
-- Run this script to create the complete database structure

-- Create database
CREATE DATABASE IF NOT EXISTS ellaflowergarden CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ellaflowergarden;

-- Users table for customer registration
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    country VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0.0,
    amenities TEXT, -- JSON string of amenities
    images TEXT, -- JSON string of image URLs
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Room types table
CREATE TABLE IF NOT EXISTS room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    type_name VARCHAR(100) NOT NULL, -- e.g., "Standard Room", "Deluxe Suite"
    description TEXT,
    max_occupancy INT NOT NULL,
    bed_type VARCHAR(100), -- e.g., "King Bed", "Twin Beds"
    room_size VARCHAR(50), -- e.g., "25 sq m"
    amenities TEXT, -- JSON string of room amenities
    images TEXT, -- JSON string of room images
    base_price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

-- Coupons table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0.00,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Reservations table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    room_type_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    num_guests INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    coupon_id INT DEFAULT NULL,
    coupon_code VARCHAR(50) DEFAULT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    final_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    special_requests TEXT,
    booking_reference VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    card_number VARCHAR(50) NOT NULL, -- Masked card number
    card_name VARCHAR(255) NOT NULL,
    card_expiry VARCHAR(10) NOT NULL,
    card_cvv VARCHAR(10) NOT NULL, -- Masked CVV
    billing_address TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

-- Admin users table (for hotel management)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role ENUM('admin', 'manager') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Flower Garden hotel branches
INSERT INTO hotels (name, description, address, city, state, country, postal_code, phone, email, rating, amenities, images) VALUES
('Flower Garden - Colombo', 'A modern city hotel in the heart of Colombo offering business and leisure facilities. Conveniently located with easy access to shopping, dining, and cultural attractions.', '123 Business District, Colombo', 'Colombo', 'Western Province', 'Sri Lanka', '10000', '+94 11 234 5678', 'colombo@flowergarden.com', 4.4, '["WiFi", "City Views", "Business Center", "Gym", "Restaurant", "Parking", "Room Service", "Concierge"]', '["https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800", "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800"]'),
('Flower Garden - Ella', 'A luxurious boutique hotel nestled in the heart of Ella, surrounded by lush tea plantations and offering breathtaking mountain views. Experience the perfect blend of modern comfort and traditional Sri Lankan hospitality.', '456 Flower Garden Road, Ella', 'Ella', 'Uva Province', 'Sri Lanka', '90090', '+94 57 234 5678', 'ella@flowergarden.com', 4.8, '["WiFi", "Mountain Views", "Restaurant", "Spa", "Garden", "Parking", "Room Service", "Concierge"]', '["https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800", "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800"]'),
('Flower Garden - Matara', 'A serene beachfront hotel in Matara offering stunning ocean views and tropical gardens. Perfect for relaxation and beach activities with modern amenities and warm hospitality.', '789 Beach Road, Matara', 'Matara', 'Southern Province', 'Sri Lanka', '81000', '+94 41 234 5678', 'matara@flowergarden.com', 4.6, '["WiFi", "Beach Access", "Restaurant", "Pool", "Garden", "Parking", "Room Service", "Concierge"]', '["https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800", "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800"]'),
('Flower Garden - Nuwara Eliya', 'A charming hill station hotel in Nuwara Eliya surrounded by tea plantations and cool mountain air. Experience the beauty of Sri Lanka\'s tea country with luxury accommodations.', '321 Tea Estate Road, Nuwara Eliya', 'Nuwara Eliya', 'Central Province', 'Sri Lanka', '22200', '+94 52 234 5678', 'nuwaraeliya@flowergarden.com', 4.7, '["WiFi", "Mountain Views", "Restaurant", "Spa", "Garden", "Parking", "Room Service", "Concierge", "Tea Plantation Tours"]', '["https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800", "https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800"]');

-- Insert room types for each Flower Garden branch
-- Flower Garden - Colombo rooms
INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price) VALUES
(1, 'Standard Room', 'Comfortable room with city view, perfect for business travelers.', 2, 'Queen Bed', '25 sq m', '["Air Conditioning", "WiFi", "TV", "Mini Bar", "Safe", "City View"]', '["https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800"]', 150.00),
(1, 'Deluxe Suite', 'Spacious suite with separate living area and premium amenities.', 4, 'King Bed', '45 sq m', '["Air Conditioning", "WiFi", "TV", "Mini Bar", "Safe", "City View", "Living Area", "Balcony"]', '["https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800"]', 280.00),
(1, 'Presidential Suite', 'Luxury suite with panoramic city views and butler service.', 6, 'King Bed', '80 sq m', '["Air Conditioning", "WiFi", "TV", "Mini Bar", "Safe", "City View", "Living Area", "Balcony", "Butler Service", "Jacuzzi"]', '["https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=800"]', 500.00);

-- Flower Garden - Ella rooms
INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price) VALUES
(2, 'Garden View Room', 'Comfortable room with beautiful garden views and modern amenities.', 2, 'Queen Bed', '25 sq m', '["Air Conditioning", "WiFi", "TV", "Garden View", "Private Bathroom", "Mini Bar"]', '["https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800"]', 120.00),
(2, 'Heritage Suite', 'Spacious suite featuring traditional architecture and modern comfort.', 4, 'King Bed', '40 sq m', '["Air Conditioning", "WiFi", "TV", "Traditional Decor", "Garden View", "Living Area", "Balcony"]', '["https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800"]', 200.00);

-- Flower Garden - Matara rooms
INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price) VALUES
(3, 'Beach View Room', 'Comfortable room with stunning ocean views and beach access.', 2, 'Queen Bed', '28 sq m', '["Air Conditioning", "WiFi", "TV", "Beach View", "Private Bathroom", "Mini Bar"]', '["https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800"]', 130.00),
(3, 'Ocean Suite', 'Spacious suite with panoramic ocean views and premium amenities.', 4, 'King Bed', '45 sq m', '["Air Conditioning", "WiFi", "TV", "Ocean View", "Living Area", "Balcony", "Mini Bar"]', '["https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800"]', 220.00);

-- Flower Garden - Nuwara Eliya rooms
INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price) VALUES
(4, 'Tea Garden Room', 'Cozy room with views of tea plantations and cool mountain air.', 2, 'Queen Bed', '26 sq m', '["Air Conditioning", "WiFi", "TV", "Tea Garden View", "Private Bathroom", "Mini Bar"]', '["https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800"]', 140.00),
(4, 'Mountain Suite', 'Luxury suite with panoramic mountain views and tea plantation access.', 4, 'King Bed', '50 sq m', '["Air Conditioning", "WiFi", "TV", "Mountain View", "Living Area", "Balcony", "Tea Plantation Tours"]', '["https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800"]', 250.00);

-- Insert sample coupons
INSERT INTO coupons (code, name, description, discount_type, discount_value, min_amount, max_discount, usage_limit, valid_from, valid_until) VALUES
('WELCOME10', 'Welcome Discount', '10% off your first booking', 'percentage', 10.00, 100.00, 50.00, 100, '2024-01-01', '2024-12-31'),
('SAVE20', 'Summer Special', '20% off bookings over $200', 'percentage', 20.00, 200.00, 100.00, 50, '2024-01-01', '2024-12-31'),
('FLAT50', 'Fixed Discount', '$50 off bookings over $300', 'fixed', 50.00, 300.00, NULL, 25, '2024-01-01', '2024-12-31'),
('EARLY15', 'Early Bird', '15% off bookings made 30+ days in advance', 'percentage', 15.00, 150.00, 75.00, 75, '2024-01-01', '2024-12-31'),
('LOYAL25', 'Loyalty Reward', '25% off for returning customers', 'percentage', 25.00, 250.00, 125.00, 30, '2024-01-01', '2024-12-31');

-- Insert default admin user
INSERT INTO admin_users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@flowergarden.com', 'admin'),
('manager', 'manager123', 'manager@flowergarden.com', 'manager');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_hotels_city ON hotels(city);
CREATE INDEX idx_hotels_status ON hotels(status);
CREATE INDEX idx_room_types_hotel_id ON room_types(hotel_id);
CREATE INDEX idx_room_types_status ON room_types(status);
CREATE INDEX idx_reservations_user_id ON reservations(user_id);
CREATE INDEX idx_reservations_hotel_id ON reservations(hotel_id);
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_reservations_payment_status ON reservations(payment_status);
CREATE INDEX idx_reservations_check_in ON reservations(check_in_date);
CREATE INDEX idx_reservations_booking_reference ON reservations(booking_reference);
CREATE INDEX idx_reservations_coupon_id ON reservations(coupon_id);
CREATE INDEX idx_payments_reservation_id ON payments(reservation_id);
CREATE INDEX idx_payments_status ON payments(payment_status);
CREATE INDEX idx_payments_payment_date ON payments(payment_date);
CREATE INDEX idx_coupons_code ON coupons(code);
CREATE INDEX idx_coupons_status ON coupons(status);
CREATE INDEX idx_coupons_valid_dates ON coupons(valid_from, valid_until);

-- Display completion message
SELECT 'Complete Hotel Reservation System Database Setup Completed Successfully!' as message;
SELECT 'Database: ellaflowergarden' as database_name;
SELECT 'Tables Created: users, hotels, room_types, coupons, reservations, payments, admin_users' as tables;
SELECT 'Sample Data: 4 hotels, 10 room types, 5 coupons, 2 admin users' as sample_data;
SELECT 'Ready to use the Flower Garden Hotel Reservation System!' as status;
