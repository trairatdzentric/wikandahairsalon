-- Wikanda Hair Salon - Data Import SQL
-- รันไฟล์นี้ใน phpMyAdmin หลังจาก import schema แล้ว
-- Generated: 2026-06-06

SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE reviews;
TRUNCATE TABLE payments;
TRUNCATE TABLE bookings;
TRUNCATE TABLE staff;
TRUNCATE TABLE services;
TRUNCATE TABLE users;
TRUNCATE TABLE settings;

-- Insert Users
INSERT INTO users (id, uuid, username, email, password_hash, full_name, phone, role, line_user_id, avatar, created_at, updated_at) VALUES
(1, 'a1b2c3d4-0001-4e5f-8a9b-000000000001', 'admin', 'admin@wikanda.local', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'ผู้ดูแลระบบ', '0800000001', 'admin', NULL, NULL, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(2, 'a1b2c3d4-0001-4e5f-8a9b-000000000002', 'owner', 'owner@wikanda.local', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'คุณวิกานดา (เจ้าของร้าน)', '0800000002', 'owner', NULL, NULL, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(3, 'a1b2c3d4-0001-4e5f-8a9b-000000000003', 'staff01', 'staff01@wikanda.local', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'ช่างแนน', '0800000003', 'staff', NULL, NULL, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(4, 'a1b2c3d4-0001-4e5f-8a9b-000000000004', 'staff02', 'staff02@wikanda.local', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'ช่างโบว์', '0800000004', 'staff', NULL, NULL, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(5, 'a1b2c3d4-0001-4e5f-8a9b-000000000005', 'member01', 'member01@example.com', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'สมหญิง รักผม', '0810000001', 'member', NULL, NULL, '2026-02-15 10:30:00', '2026-02-15 10:30:00'),
(6, 'a1b2c3d4-0001-4e5f-8a9b-000000000006', 'member02', 'member02@example.com', '$2y$10$0Hfy76xeClHgFjJfDeHbp.npAAIRYoz3GWRGQRryiRowCJZIfzPja', 'อรพิน สวยงาม', '0810000002', 'member', NULL, NULL, '2026-03-01 14:00:00', '2026-03-01 14:00:00');

-- Insert Services
INSERT INTO services (id, uuid, name, name_en, description, price, duration_minutes, category, image, is_active, created_at, updated_at) VALUES
(1, 'b2c3d4e5-0002-4f6a-8b9c-000000000001', 'ตัดผมชาย', 'Men\'s Haircut', 'บริการตัดผมชาย รวมสระเป่า / Men haircut including wash and blow', 200.00, 30, 'haircut', 'service-men-haircut.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(2, 'b2c3d4e5-0002-4f6a-8b9c-000000000002', 'ตัดผมหญิง', 'Women\'s Haircut', 'บริการตัดผมหญิง รวมสระเป่า / Women haircut including wash and blow', 350.00, 45, 'haircut', 'service-women-haircut.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(3, 'b2c3d4e5-0002-4f6a-8b9c-000000000003', 'สระไดร์', 'Wash & Blow Dry', 'สระผมและเป่าจัดทรง / Hair wash and blow dry styling', 150.00, 30, 'wash', 'service-wash.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(4, 'b2c3d4e5-0002-4f6a-8b9c-000000000004', 'ทำสีผม', 'Hair Coloring', 'ย้อมสีผมทั้งหัว / Full hair coloring service', 1500.00, 120, 'color', 'service-color.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(5, 'b2c3d4e5-0002-4f6a-8b9c-000000000005', 'ดัดผม', 'Hair Perm', 'ดัดผมทำลอน / Hair perm and curl', 1800.00, 150, 'perm', 'service-perm.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(6, 'b2c3d4e5-0002-4f6a-8b9c-000000000006', 'ยืดผม', 'Hair Straightening', 'ยืดผมตรงสวย / Hair straightening service', 2500.00, 180, 'straighten', 'service-straighten.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(7, 'b2c3d4e5-0002-4f6a-8b9c-000000000007', 'ทรีตเมนต์', 'Hair Treatment', 'บำรุงผมด้วยทรีตเมนต์พรีเมียม / Premium hair treatment', 600.00, 45, 'treatment', 'service-treatment.jpg', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00');

-- Insert Staff
INSERT INTO staff (id, uuid, user_id, display_name, specialty, bio, is_active, created_at, updated_at) VALUES
(1, 'c3d4e5f6-0003-4a7b-8c9d-000000000001', 3, 'ช่างแนน', 'ตัดผม, ทำสี / Haircut, Color', 'ประสบการณ์ 8 ปี เชี่ยวชาญตัดผมสไตล์เกาหลี', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00'),
(2, 'c3d4e5f6-0003-4a7b-8c9d-000000000002', 4, 'ช่างโบว์', 'ดัด, ยืด, ทรีตเมนต์ / Perm, Straighten, Treatment', 'ประสบการณ์ 5 ปี เชี่ยวชาญงานเคมีและทรีตเมนต์', 1, '2026-01-01 09:00:00', '2026-01-01 09:00:00');

-- Insert Bookings
INSERT INTO bookings (id, uuid, booking_code, member_id, service_id, staff_id, booking_date, start_time, end_time, total_price, status, note, created_at, updated_at) VALUES
(1, 'd4e5f6a7-0004-4b8c-8d9e-000000000001', 'WK20260501-001', 5, 2, 1, '2026-05-20', '10:00:00', '10:45:00', 350.00, 'confirmed', 'ลูกค้าต้องการตัดสั้น / Customer wants short cut', '2026-05-15 14:23:11', '2026-05-15 14:25:00'),
(2, 'd4e5f6a7-0004-4b8c-8d9e-000000000002', 'WK20260518-002', 6, 4, 1, '2026-05-22', '13:00:00', '15:00:00', 1500.00, 'pending', 'อยากทำสีน้ำตาลคาราเมล', '2026-05-17 19:10:22', '2026-05-17 19:10:22'),
(3, 'd4e5f6a7-0004-4b8c-8d9e-000000000003', 'WK20260510-003', 5, 7, 2, '2026-05-10', '11:00:00', '11:45:00', 600.00, 'completed', '', '2026-05-08 09:30:00', '2026-05-10 11:50:00'),
(4, 'd4e5f6a7-0004-4b8c-8d9e-000000000004', 'WK20260512-004', 6, 5, 2, '2026-05-12', '14:00:00', '16:30:00', 1800.00, 'completed', 'ดัดลอนใหญ่', '2026-05-09 16:00:00', '2026-05-12 16:35:00'),
(5, 'd4e5f6a7-0004-4b8c-8d9e-000000000005', 'WK20260519-005', 5, 3, 1, '2026-05-19', '16:00:00', '16:30:00', 150.00, 'cancelled', 'ลูกค้าติดธุระ', '2026-05-16 08:00:00', '2026-05-18 09:15:00');

-- Insert Payments
INSERT INTO payments (id, uuid, booking_id, amount, method, status, slip_path, slip_uploaded_at, verified_at, verified_by, note, created_at, updated_at) VALUES
(1, 'e5f6a7b8-0005-4c9d-8e9f-000000000001', 1, 100.00, 'promptpay', 'verified', 'slip_001.jpg', '2026-05-15 14:24:00', '2026-05-15 14:30:00', 1, 'มัดจำ 100 บาท / Deposit 100 THB', '2026-05-15 14:24:00', '2026-05-15 14:30:00'),
(2, 'e5f6a7b8-0005-4c9d-8e9f-000000000002', 3, 600.00, 'cash', 'verified', NULL, '2026-05-10 11:50:00', '2026-05-10 11:50:00', 3, 'ชำระเต็มจำนวนหน้าร้าน / Paid full at salon', '2026-05-10 11:50:00', '2026-05-10 11:50:00'),
(3, 'e5f6a7b8-0005-4c9d-8e9f-000000000003', 4, 1800.00, 'promptpay', 'verified', 'slip_004.jpg', '2026-05-12 16:38:00', '2026-05-12 16:40:00', 1, '', '2026-05-12 16:38:00', '2026-05-12 16:40:00');

-- Insert Reviews
INSERT INTO reviews (id, uuid, member_id, service_id, booking_id, rating, comment, is_visible, created_at, updated_at) VALUES
(1, 'f6a7b8c9-0006-4d0e-8f0a-000000000001', 5, 7, 3, 5, 'ช่างมือเบา ทำดีมากค่ะ ผมนุ่มลื่นเลย', 1, '2026-05-11 09:00:00', '2026-05-11 09:00:00'),
(2, 'f6a7b8c9-0006-4d0e-8f0a-000000000002', 6, 5, 4, 5, 'ดัดออกมาสวยมาก ลอนเป็นธรรมชาติ จะกลับมาอีกแน่นอน!', 1, '2026-05-13 11:30:00', '2026-05-13 11:30:00');

-- Insert Settings
INSERT INTO settings (id, uuid, `key`, `value`, `type`, created_at, updated_at) VALUES
(1, 'd6c73560-56b0-4b9a-bf61-800000000001', 'line.enabled', '0', 'boolean', '2026-06-06 00:00:00', '2026-06-06 00:00:00'),
(2, 'd6c73560-56b0-4b9a-bf61-800000000002', 'line.channel_access_token', '', 'secret', '2026-06-06 00:00:00', '2026-06-06 00:00:00'),
(3, 'd6c73560-56b0-4b9a-bf61-800000000003', 'slip2go.enabled', '0', 'boolean', '2026-06-06 00:00:00', '2026-06-06 00:00:00'),
(4, 'd6c73560-56b0-4b9a-bf61-800000000004', 'slip2go.api_key', '', 'secret', '2026-06-06 00:00:00', '2026-06-06 00:00:00');

SET FOREIGN_KEY_CHECKS = 1;
