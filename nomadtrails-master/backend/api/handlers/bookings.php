<?php
// backend/api/handlers/bookings.php
use KGVip\Core\Database;
use KGVip\Core\Response;

$db = Database::getInstance()->getConnection();

switch ($method) {
    case 'POST': // Public: submit booking
        $d = json_decode(file_get_contents('php://input'), true);
        if (empty($d['full_name']) || empty($d['email'])) Response::error('Name and email required');
        $stmt = $db->prepare("INSERT INTO bookings
            (tour_id,full_name,email,phone,preferred_date,guests,special_requests)
            VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['tour_id'] ?? null,
            htmlspecialchars($d['full_name']),
            filter_var($d['email'], FILTER_SANITIZE_EMAIL),
            htmlspecialchars($d['phone'] ?? ''),
            $d['preferred_date'] ?? null,
            (int)($d['guests'] ?? 1),
            htmlspecialchars($d['special_requests'] ?? ''),
        ]);
        Response::success(['id' => $db->lastInsertId()], 'Booking request received');

    case 'GET': // Admin only
        requireAdmin();
        $status = htmlspecialchars($_GET['status'] ?? '');
        $sql    = "SELECT b.*, t.name_en AS tour_name FROM bookings b LEFT JOIN tours t ON b.tour_id=t.id";
        $params = [];
        if ($status) { $sql .= ' WHERE b.status=?'; $params[] = $status; }
        $sql .= ' ORDER BY b.created_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        Response::success($stmt->fetchAll());

    case 'PUT': // Admin: update status
        requireAdmin();
        if (!$id) Response::error('ID required');
        $d = json_decode(file_get_contents('php://input'), true);
        $allowed = ['new','contacted','confirmed','cancelled'];
        if (!in_array($d['status'] ?? '', $allowed)) Response::error('Invalid status');
        $db->prepare("UPDATE bookings SET status=? WHERE id=?")->execute([$d['status'], $id]);
        Response::success(null, 'Status updated');

    default:
        Response::error('Method not allowed', 405);
}

function requireAdmin(): void
{
    session_start();
    if (empty($_SESSION['admin_id'])) \KGVip\Core\Response::error('Unauthorized', 401);
}
