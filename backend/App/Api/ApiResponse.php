<?php

declare(strict_types=1);

namespace App\Api;

class ApiResponse
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function success(mixed $data, int $status = 200, array $meta = []): void
    {
        $body = ['success' => true, 'data' => $data];
        if ($meta !== []) {
            $body['meta'] = $meta;
        }
        self::json($body, $status);
    }

    public static function error(string $code, string $message, int $status, array $details = []): void
    {
        $body = ['success' => false, 'error' => ['code' => $code, 'message' => $message]];
        if ($details !== []) {
            $body['error']['details'] = $details;
        }
        self::json($body, $status);
    }
}
