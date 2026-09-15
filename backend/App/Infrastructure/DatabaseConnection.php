<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $shared = null;

    public static function get(): PDO
    {
        if (self::$shared instanceof PDO) {
            return self::$shared;
        }

        $dsn = (string) (getenv('PHP_DSN') ?: 'ecommerce_products');
        $host = (string) (getenv('PHP_HOST') ?: 'mysql');
        $user = (string) (getenv('PHP_USER') ?: 'root');
        $pass = (string) (getenv('PHP_PASSWD') ?: 'root');

        try {
            $pdo = new PDO("mysql:dbname={$dsn};host={$host};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('[db] connection failed: ' . $e->getMessage());
            throw $e;
        }

        self::$shared = $pdo;
        return $pdo;
    }

    public static function reset(): void
    {
        self::$shared = null;
    }
}
