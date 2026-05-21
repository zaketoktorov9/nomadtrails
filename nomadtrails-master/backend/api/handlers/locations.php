<?php
// backend/api/handlers/locations.php
use KGVip\Core\Database;
use KGVip\Core\Response;

$db  = Database::getInstance()->getConnection();
$lang = htmlspecialchars($_GET['lang'] ?? 'en');
if (!in_array($lang, ['en','ru','ky'])) $lang = 'en';

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $db->prepare("SELECT id, slug, category, image_url, featured,
                name_{$lang} AS name, region_{$lang} AS region, desc_{$lang} AS description
                FROM locations WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if (!$row) Response::error('Not found', 404);
            Response::success($row);
        }
        $cat = $_GET['category'] ?? '';
        $sql  = "SELECT id, slug, category, image_url, featured, name_{$lang} AS name,
                    region_{$lang} AS region, desc_{$lang} AS description
                 FROM locations";
        $params = [];
        if ($cat) { $sql .= ' WHERE category = ?'; $params[] = $cat; }
        $sql .= ' ORDER BY sort_order, id';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        Response::success($stmt->fetchAll());

    case 'POST':
        requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO locations
            (slug,category,image_url,name_en,name_ru,name_ky,region_en,region_ru,region_ky,desc_en,desc_ru,desc_ky,featured,sort_order)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['slug'], $data['category'], $data['image_url'] ?? null,
            $data['name_en'], $data['name_ru'] ?? null, $data['name_ky'] ?? null,
            $data['region_en'] ?? null, $data['region_ru'] ?? null, $data['region_ky'] ?? null,
            $data['desc_en'] ?? null, $data['desc_ru'] ?? null, $data['desc_ky'] ?? null,
            (int)($data['featured'] ?? 0), (int)($data['sort_order'] ?? 0),
        ]);
        Response::success(['id' => $db->lastInsertId()], 'Location created');

    case 'PUT':
        requireAdmin();
        if (!$id) Response::error('ID required');
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE locations SET
            slug=?,category=?,image_url=?,name_en=?,name_ru=?,name_ky=?,
            region_en=?,region_ru=?,region_ky=?,desc_en=?,desc_ru=?,desc_ky=?,
            featured=?,sort_order=? WHERE id=?");
        $stmt->execute([
            $data['slug'], $data['category'], $data['image_url'] ?? null,
            $data['name_en'], $data['name_ru'] ?? null, $data['name_ky'] ?? null,
            $data['region_en'] ?? null, $data['region_ru'] ?? null, $data['region_ky'] ?? null,
            $data['desc_en'] ?? null, $data['desc_ru'] ?? null, $data['desc_ky'] ?? null,
            (int)($data['featured'] ?? 0), (int)($data['sort_order'] ?? 0), $id,
        ]);
        Response::success(null, 'Updated');

    case 'DELETE':
        requireAdmin();
        if (!$id) Response::error('ID required');
        $db->prepare("DELETE FROM locations WHERE id=?")->execute([$id]);
        Response::success(null, 'Deleted');

    default:
        Response::error('Method not allowed', 405);
}

function requireAdmin(): void
{
    // Simple session-based auth check
    session_start();
    if (empty($_SESSION['admin_id'])) {
        \KGVip\Core\Response::error('Unauthorized', 401);
    }
}
