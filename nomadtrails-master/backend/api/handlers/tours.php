<?php
// backend/api/handlers/tours.php
use KGVip\Core\Database;
use KGVip\Core\Response;

$db   = Database::getInstance()->getConnection();
$lang = in_array($_GET['lang'] ?? '', ['en','ru','ky']) ? $_GET['lang'] : 'en';

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $db->prepare("SELECT id,slug,duration_days,price_usd,difficulty,
                group_min,group_max,rating,reviews_count,image_url,active,
                name_{$lang} AS name, desc_{$lang} AS description,
                includes_{$lang} AS includes
                FROM tours WHERE id=? AND active=1");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if (!$row) Response::error('Tour not found',404);
            $row['includes'] = json_decode($row['includes'] ?? '[]');
            Response::success($row);
        }
        $stmt = $db->prepare("SELECT id,slug,duration_days,price_usd,difficulty,
            group_min,group_max,rating,reviews_count,image_url,
            name_{$lang} AS name, desc_{$lang} AS description,
            includes_{$lang} AS includes
            FROM tours WHERE active=1 ORDER BY id");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) $r['includes'] = json_decode($r['includes'] ?? '[]');
        Response::success($rows);

    case 'POST':
        requireAdmin();
        $d = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO tours
            (slug,duration_days,price_usd,difficulty,group_min,group_max,image_url,
             name_en,name_ru,name_ky,desc_en,desc_ru,desc_ky,includes_en,includes_ru,includes_ky)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $d['slug'], $d['duration_days'], $d['price_usd'], $d['difficulty'] ?? 'Moderate',
            $d['group_min'] ?? 2, $d['group_max'] ?? 10, $d['image_url'] ?? null,
            $d['name_en'], $d['name_ru'] ?? null, $d['name_ky'] ?? null,
            $d['desc_en'] ?? null, $d['desc_ru'] ?? null, $d['desc_ky'] ?? null,
            json_encode($d['includes_en'] ?? []), json_encode($d['includes_ru'] ?? []), json_encode($d['includes_ky'] ?? []),
        ]);
        Response::success(['id' => $db->lastInsertId()], 'Tour created');

    case 'PUT':
        requireAdmin();
        if (!$id) Response::error('ID required');
        $d = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE tours SET
            slug=?,duration_days=?,price_usd=?,difficulty=?,group_min=?,group_max=?,image_url=?,
            name_en=?,name_ru=?,name_ky=?,desc_en=?,desc_ru=?,desc_ky=?,
            includes_en=?,includes_ru=?,includes_ky=?,active=? WHERE id=?");
        $stmt->execute([
            $d['slug'], $d['duration_days'], $d['price_usd'], $d['difficulty'] ?? 'Moderate',
            $d['group_min'] ?? 2, $d['group_max'] ?? 10, $d['image_url'] ?? null,
            $d['name_en'], $d['name_ru'] ?? null, $d['name_ky'] ?? null,
            $d['desc_en'] ?? null, $d['desc_ru'] ?? null, $d['desc_ky'] ?? null,
            json_encode($d['includes_en'] ?? []), json_encode($d['includes_ru'] ?? []), json_encode($d['includes_ky'] ?? []),
            (int)($d['active'] ?? 1), $id,
        ]);
        Response::success(null, 'Tour updated');

    case 'DELETE':
        requireAdmin();
        if (!$id) Response::error('ID required');
        $db->prepare("UPDATE tours SET active=0 WHERE id=?")->execute([$id]);
        Response::success(null, 'Tour deactivated');

    default:
        Response::error('Method not allowed', 405);
}

function requireAdmin(): void
{
    session_start();
    if (empty($_SESSION['admin_id'])) \KGVip\Core\Response::error('Unauthorized', 401);
}
