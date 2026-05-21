USE kgviptravel;

-- Add role to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER google_id;

-- Make a specific email an admin (replace with your email if you want to test)
-- UPDATE users SET role = 'admin' WHERE email = 'admin@yourdomain.com';
