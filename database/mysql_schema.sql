-- Wikanda Hair Salon - MySQL schema for InfinityFree
-- Database: YOUR_DB_NAME
-- Import this file in InfinityFree phpMyAdmin.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    username VARCHAR(80) NOT NULL,
    email VARCHAR(160) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(160) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role ENUM('admin','owner','staff','member') NOT NULL DEFAULT 'member',
    line_user_id VARCHAR(120) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_uuid_unique (uuid),
    UNIQUE KEY users_username_unique (username),
    UNIQUE KEY users_email_unique (email),
    KEY users_role_index (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    name VARCHAR(160) NOT NULL,
    name_en VARCHAR(160) DEFAULT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_minutes INT UNSIGNED NOT NULL DEFAULT 30,
    category VARCHAR(80) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY services_uuid_unique (uuid),
    KEY services_active_index (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    display_name VARCHAR(160) NOT NULL,
    specialty VARCHAR(160) DEFAULT NULL,
    bio TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY staff_uuid_unique (uuid),
    KEY staff_user_index (user_id),
    CONSTRAINT staff_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    booking_code VARCHAR(40) NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    staff_id INT UNSIGNED NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','confirmed','in_service','completed','cancelled') NOT NULL DEFAULT 'pending',
    note TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY bookings_uuid_unique (uuid),
    UNIQUE KEY bookings_code_unique (booking_code),
    KEY bookings_member_index (member_id),
    KEY bookings_staff_date_index (staff_id, booking_date),
    KEY bookings_status_index (status),
    CONSTRAINT bookings_member_fk FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT bookings_service_fk FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    CONSTRAINT bookings_staff_fk FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    booking_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    method VARCHAR(40) NOT NULL DEFAULT 'transfer',
    status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    slip_path VARCHAR(255) DEFAULT NULL,
    slip_uploaded_at DATETIME DEFAULT NULL,
    verified_at DATETIME DEFAULT NULL,
    verified_by INT UNSIGNED DEFAULT NULL,
    note TEXT,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY payments_uuid_unique (uuid),
    KEY payments_booking_index (booking_id),
    KEY payments_status_index (status),
    CONSTRAINT payments_booking_fk FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT payments_verified_by_fk FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    staff_id INT UNSIGNED DEFAULT NULL,
    booking_id INT UNSIGNED DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY reviews_uuid_unique (uuid),
    KEY reviews_member_index (member_id),
    KEY reviews_service_index (service_id),
    KEY reviews_staff_index (staff_id),
    CONSTRAINT reviews_member_fk FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT reviews_service_fk FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    CONSTRAINT reviews_staff_fk FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT reviews_booking_fk FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    `key` VARCHAR(120) NOT NULL,
    `value` TEXT,
    `type` VARCHAR(40) NOT NULL DEFAULT 'string',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY settings_uuid_unique (uuid),
    UNIQUE KEY settings_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (uuid, `key`, `value`, `type`, created_at, updated_at) VALUES
('d6c73560-56b0-4b9a-bf61-800000000001', 'line.enabled', '0', 'boolean', NOW(), NOW()),
('d6c73560-56b0-4b9a-bf61-800000000002', 'line.channel_access_token', '', 'secret', NOW(), NOW()),
('d6c73560-56b0-4b9a-bf61-800000000003', 'slip2go.enabled', '0', 'boolean', NOW(), NOW()),
('d6c73560-56b0-4b9a-bf61-800000000004', 'slip2go.api_key', '', 'secret', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);

