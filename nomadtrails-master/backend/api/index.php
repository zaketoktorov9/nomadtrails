<?php
/**
 * backend/api/index.php
 * Front controller – routes all /api/* requests.
 *
 * Place the entire `backend/` folder inside your OpenServer/XAMPP htdocs
 * under a subdomain e.g. api.kgviptravel/ or kgviptravel/backend/api/
 */
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Response.php';

use KGVip\Core\Response;

// ── CORS ──────────────────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ── Router ────────────────────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip a potential prefix (e.g. /backend/api)
$path = preg_replace('#^.*/api#', '', $uri) ?: '/';
$segments = array_filter(explode('/', $path));
$resource = array_shift($segments) ?? '';
$id       = (int) (array_shift($segments) ?? 0);

match (true) {
    $resource === 'locations'  => require __DIR__ . '/handlers/locations.php',
    $resource === 'tours'      => require __DIR__ . '/handlers/tours.php',
    $resource === 'hotels'     => require __DIR__ . '/handlers/hotels.php',
    $resource === 'transport'  => require __DIR__ . '/handlers/transport.php',
    $resource === 'bookings'   => require __DIR__ . '/handlers/bookings.php',
    $resource === 'contact'    => require __DIR__ . '/handlers/contact.php',
    $resource === 'auth'       => require __DIR__ . '/handlers/auth.php',
    default                    => Response::error('Endpoint not found', 404),
};
