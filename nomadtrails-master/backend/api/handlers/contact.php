<?php
// backend/api/handlers/contact.php
use KGVip\Core\Database;
use KGVip\Core\Response;

$db = Database::getInstance()->getConnection();

switch ($method) {
    case 'POST':
        $d = json_decode(file_get_contents('php://input'), true);
        if (empty($d['name']) || empty($d['email']) || empty($d['message']))
            Response::error('Name, email and message are required');
        $stmt = $db->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->execute([
            htmlspecialchars($d['name']),
            filter_var($d['email'], FILTER_SANITIZE_EMAIL),
            htmlspecialchars($d['subject'] ?? ''),
            htmlspecialchars($d['message']),
        ]);
        Response::success(null, 'Message received');

    case 'GET':
        requireAdmin();
        $stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        Response::success($stmt->fetchAll());

    case 'PUT': // Mark as read
        requireAdmin();
        if (!$id) Response::error('ID required');
        $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$id]);
        Response::success(null, 'Marked as read');

    case 'DELETE':
        requireAdmin();
        if (!$id) Response::error('ID required');
        $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$id]);
        Response::success(null, 'Deleted');

    default:
        Response::error('Method not allowed', 405);
}

function requireAdmin(): void
{
    session_start();
    if (empty($_SESSION['admin_id'])) \KGVip\Core\Response::error('Unauthorized', 401);
}
