<?php
// backend/api/handlers/auth.php
use KGVip\Core\Database;
use KGVip\Core\Response;

session_start();

$db = Database::getInstance()->getConnection();
$d  = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        $action = $d['action'] ?? 'login';
        if ($action === 'login') {
            if (empty($d['username']) || empty($d['password']))
                Response::error('Username and password required');
            $stmt = $db->prepare("SELECT id, password_hash FROM admins WHERE username=?");
            $stmt->execute([htmlspecialchars($d['username'])]);
            $admin = $stmt->fetch();
            if (!$admin || !password_verify($d['password'], $admin['password_hash']))
                Response::error('Invalid credentials', 401);
            $_SESSION['admin_id'] = $admin['id'];
            Response::success(['admin_id' => $admin['id']], 'Login successful');
        }
        if ($action === 'logout') {
            session_destroy();
            Response::success(null, 'Logged out');
        }
        Response::error('Unknown action');

    case 'GET': // Check session
        $loggedIn = !empty($_SESSION['admin_id']);
        Response::success(['authenticated' => $loggedIn]);

    default:
        Response::error('Method not allowed', 405);
}
