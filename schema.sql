-- Create database
CREATE DATABASE IF NOT EXISTS lawyer_db;
USE lawyer_db;

-- Users table (for customers, lawyers, and admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'lawyer', 'admin') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Lawyer profiles table
CREATE TABLE lawyer_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    experience_years INT NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    education TEXT,
    bio TEXT,
    consultation_fee DECIMAL(10,2),
    location VARCHAR(100) NOT NULL,
    office_address TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    availability_status ENUM('available', 'busy', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Lawyer services table
CREATE TABLE lawyer_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lawyer_id INT NOT NULL,
    service_type ENUM('criminal', 'divorce', 'civil', 'corporate', 'family', 'property', 'immigration', 'tax', 'labor', 'intellectual_property') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lawyer_id) REFERENCES lawyer_profiles(id) ON DELETE CASCADE
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    lawyer_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    meeting_type ENUM('office', 'online', 'phone') DEFAULT 'office',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES lawyer_profiles(id) ON DELETE CASCADE
);

-- Time slots table (for lawyer availability)
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lawyer_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lawyer_id) REFERENCES lawyer_profiles(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    lawyer_id INT NOT NULL,
    appointment_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES lawyer_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
);

-- Contact inquiries table
CREATE TABLE contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    lawyer_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lawyer_id) REFERENCES lawyer_profiles(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, user_type, full_name, phone) 
VALUES ('admin', 'admin@lawyerapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', '+1234567890');

-- Insert sample lawyer data
INSERT INTO users (username, email, password, user_type, full_name, phone, address) VALUES
('john_lawyer', 'john@lawfirm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer', 'John Smith', '+1234567891', '123 Law Street, New York, NY'),
('sarah_lawyer', 'sarah@legalservices.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer', 'Sarah Johnson', '+1234567892', '456 Justice Ave, Los Angeles, CA'),
('mike_lawyer', 'mike@criminallaw.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lawyer', 'Michael Brown', '+1234567893', '789 Court Road, Chicago, IL');

-- Insert lawyer profiles
INSERT INTO lawyer_profiles (user_id, specialization, experience_years, license_number, education, bio, consultation_fee, location, office_address, rating, total_reviews, is_verified) VALUES
(2, 'Corporate Law', 10, 'LIC001', 'Harvard Law School - JD', 'Experienced corporate lawyer with 10+ years in business law and contracts.', 300.00, 'New York', '123 Law Street, New York, NY 10001', 4.5, 25, TRUE),
(3, 'Family Law', 8, 'LIC002', 'Yale Law School - JD', 'Specialized in family law, divorce, and custody cases.', 250.00, 'Los Angeles', '456 Justice Ave, Los Angeles, CA 90001', 4.8, 42, TRUE),
(4, 'Criminal Law', 12, 'LIC003', 'Columbia Law School - JD', 'Criminal defense attorney with extensive courtroom experience.', 350.00, 'Chicago', '789 Court Road, Chicago, IL 60601', 4.3, 38, TRUE);

-- Insert lawyer services
INSERT INTO lawyer_services (lawyer_id, service_type, description) VALUES
(1, 'corporate', 'Business formation, contracts, mergers and acquisitions'),
(1, 'civil', 'Civil litigation and dispute resolution'),
(2, 'family', 'Divorce, custody, adoption, and family matters'),
(2, 'divorce', 'Divorce proceedings and settlement negotiations'),
(3, 'criminal', 'Criminal defense for all types of charges'),
(3, 'civil', 'Civil rights and personal injury cases');

-- Insert sample time slots
INSERT INTO time_slots (lawyer_id, day_of_week, start_time, end_time) VALUES
(1, 'monday', '09:00:00', '17:00:00'),
(1, 'tuesday', '09:00:00', '17:00:00'),
(1, 'wednesday', '09:00:00', '17:00:00'),
(1, 'thursday', '09:00:00', '17:00:00'),
(1, 'friday', '09:00:00', '16:00:00'),
(2, 'monday', '10:00:00', '18:00:00'),
(2, 'tuesday', '10:00:00', '18:00:00'),
(2, 'wednesday', '10:00:00', '18:00:00'),
(2, 'thursday', '10:00:00', '18:00:00'),
(2, 'friday', '10:00:00', '17:00:00'),
(3, 'monday', '08:00:00', '16:00:00'),
(3, 'tuesday', '08:00:00', '16:00:00'),
(3, 'wednesday', '08:00:00', '16:00:00'),
(3, 'thursday', '08:00:00', '16:00:00'),
(3, 'friday', '08:00:00', '15:00:00');
