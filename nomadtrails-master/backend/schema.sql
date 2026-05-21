-- KG VIP Travel Database Schema
-- MySQL 8.0+ / MariaDB 10.6+
-- Run: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS kgviptravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kgviptravel;

-- Admin users
CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(180),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Locations / Destinations
CREATE TABLE locations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,
  category ENUM('mountains','lakes','history','canyons') NOT NULL,
  image_url VARCHAR(500),
  name_en VARCHAR(200) NOT NULL,
  name_ru VARCHAR(200),
  name_ky VARCHAR(200),
  region_en VARCHAR(200),
  region_ru VARCHAR(200),
  region_ky VARCHAR(200),
  desc_en TEXT,
  desc_ru TEXT,
  desc_ky TEXT,
  featured TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tours
CREATE TABLE tours (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  duration_days TINYINT UNSIGNED NOT NULL,
  price_usd DECIMAL(10,2) NOT NULL,
  difficulty ENUM('Easy','Moderate','Hard') DEFAULT 'Moderate',
  group_min TINYINT DEFAULT 2,
  group_max TINYINT DEFAULT 10,
  rating DECIMAL(3,2) DEFAULT 5.00,
  reviews_count INT DEFAULT 0,
  image_url VARCHAR(500),
  name_en VARCHAR(250) NOT NULL,
  name_ru VARCHAR(250),
  name_ky VARCHAR(250),
  desc_en TEXT,
  desc_ru TEXT,
  desc_ky TEXT,
  includes_en JSON,
  includes_ru JSON,
  includes_ky JSON,
  active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hotels & Accommodation
CREATE TABLE hotels (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  type ENUM('yurt','lodge','hotel','guesthouse') NOT NULL,
  price_per_night DECIMAL(10,2) NOT NULL,
  rating DECIMAL(3,2) DEFAULT 5.00,
  reviews_count INT DEFAULT 0,
  image_url VARCHAR(500),
  name_en VARCHAR(250) NOT NULL,
  name_ru VARCHAR(250),
  name_ky VARCHAR(250),
  location_en VARCHAR(200),
  location_ru VARCHAR(200),
  location_ky VARCHAR(200),
  amenities JSON,
  active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transport options
CREATE TABLE transport_options (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('jeep','flight','visa','safety') NOT NULL,
  icon VARCHAR(50),
  title_en VARCHAR(250) NOT NULL,
  title_ru VARCHAR(250),
  title_ky VARCHAR(250),
  desc_en TEXT,
  desc_ru TEXT,
  desc_ky TEXT,
  sort_order INT DEFAULT 0
);

-- Booking requests
CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tour_id INT UNSIGNED,
  full_name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  phone VARCHAR(50),
  preferred_date DATE,
  guests TINYINT DEFAULT 1,
  special_requests TEXT,
  status ENUM('new','contacted','confirmed','cancelled') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL
);

-- Contact messages
CREATE TABLE contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  subject VARCHAR(300),
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Seed: default admin (password: admin123 – change immediately!)
-- -------------------------------------------------------
INSERT INTO admins (username, password_hash, email)
VALUES ('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@kgviptravel.com');

-- Sample locations
INSERT INTO locations (slug, category, image_url, name_en, name_ru, name_ky, region_en, region_ru, region_ky, desc_en, desc_ru, desc_ky, featured, sort_order) VALUES
('kel-suu', 'lakes', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600', 'Kel-Suu Lake', 'Озеро Кел-Суу', 'Кел-Суу Көлү', 'Naryn Oblast', 'Нарынская область', 'Нарын облусу', 'A hidden turquoise gem near the Chinese border.', 'Скрытая бирюзовая жемчужина у китайской границы.', 'Кытай чек арасынын жанындагы жашырын феруза асыл таш.', 1, 1),
('skazka-canyon', 'canyons', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600', 'Skazka Canyon', 'Каньон Сказка', 'Сказка Каньону', 'Issyk-Kul Oblast', 'Иссык-Кульская область', 'Ысык-Көл облусу', 'Fairy-tale red sandstone formations.', 'Сказочные формации из красного песчаника.', 'Эртектеги кызыл кумташ формациялары.', 1, 2),
('tash-rabat', 'history', 'https://images.unsplash.com/photo-1491555103944-7c647fd857e6?w=600', 'Tash-Rabat', 'Таш-Рабат', 'Таш-Рабат', 'Naryn Oblast', 'Нарынская область', 'Нарын облусу', 'A 15th-century stone caravanserai on the Silk Road.', 'Каменный каравансарай XV века на Шёлковом пути.', 'Жибек жолундагы XV кылымдагы таш керме-сарай.', 1, 3);

-- Sample tours
INSERT INTO tours (slug, duration_days, price_usd, difficulty, group_min, group_max, rating, reviews_count, image_url, name_en, name_ru, name_ky, includes_en) VALUES
('kel-suu-explorer', 7, 890.00, 'Moderate', 2, 8, 4.9, 47, 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=700', 'Kel-Suu & Tash-Rabat Explorer', 'Тур Кел-Суу и Таш-Рабат', 'Кел-Суу жана Таш-Рабат саякаты', '["Guide","Transport","Yurt Stays","Meals","Permits"]'),
('enilchek-expedition', 12, 2400.00, 'Hard', 2, 6, 5.0, 18, 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=700', 'Enilchek Glacier Expedition', 'Экспедиция на ледник Энилчек', 'Энилчек мөңгүсүнө экспедиция', '["Helicopter","Guide","Camp Equipment","Meals","Insurance"]');
