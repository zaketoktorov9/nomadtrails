USE kgviptravel;

-- Users table for Google Auth
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE NOT NULL,
  image VARCHAR(255),
  google_id VARCHAR(255) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Update bookings to include user_id
ALTER TABLE bookings ADD COLUMN user_id INT UNSIGNED AFTER id;
ALTER TABLE bookings ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Update bookings to handle hotels and transport too
ALTER TABLE bookings MODIFY COLUMN tour_id INT UNSIGNED NULL;
ALTER TABLE bookings ADD COLUMN hotel_id INT UNSIGNED AFTER tour_id;
ALTER TABLE bookings ADD COLUMN transport_id INT UNSIGNED AFTER hotel_id;
ALTER TABLE bookings ADD COLUMN item_type ENUM('tour', 'hotel', 'transport') NOT NULL DEFAULT 'tour' AFTER user_id;

-- Add foreign keys for hotels and transport if they exist
-- Assuming transport_options table is used for transport_id
ALTER TABLE bookings ADD CONSTRAINT fk_hotel FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE SET NULL;
ALTER TABLE bookings ADD CONSTRAINT fk_transport FOREIGN KEY (transport_id) REFERENCES transport_options(id) ON DELETE SET NULL;
