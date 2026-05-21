<?php
// backend/config.php – Database configuration
// Copy this file outside webroot or use environment variables in production.

define('DB_HOST', 'localhost');
define('DB_NAME', 'kgviptravel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// CORS origins allowed to call this API (set to your Next.js domain)
define('ALLOWED_ORIGIN', 'http://localhost:3000');

// Secret key for admin JWT / session (change this!)
define('APP_SECRET', 'kg-vip-super-secret-2024-change-me');
