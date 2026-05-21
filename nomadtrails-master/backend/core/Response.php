<?php
/**
 * backend/core/Response.php
 * JSON response helper.
 */
declare(strict_types=1);

namespace KGVip\Core;

class Response
{
    public static function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function error(string $message, int $code = 400): never
    {
        self::json(['success' => false, 'error' => $message], $code);
    }

    public static function success(mixed $data = null, string $message = 'OK'): never
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data]);
    }
}
