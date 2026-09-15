<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Api\ApiResponse;
use App\Api\ProductController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$controller = new ProductController();

/** @return mixed decoded JSON (assoc) or null when empty/invalid */
function readJsonBody(): mixed
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return null;
    }
    $decoded = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false; // sentinel: malformed
    }
    return $decoded;
}

// Healthcheck for Docker / CI smoke.
if ($path === '/health' && $method === 'GET') {
    ApiResponse::success(['ok' => true, 'service' => 'backend'], 200);
    exit;
}

// ---- New REST API ----
if ($path === '/api/products' && $method === 'GET') {
    $controller->list(false);
    exit;
}

if ($path === '/api/products' && $method === 'POST') {
    $body = readJsonBody();
    if ($body === false) {
        ApiResponse::error('BAD_REQUEST', 'Malformed JSON body.', 400);
        exit;
    }
    $controller->create($body);
    exit;
}

if ($path === '/api/products' && $method === 'DELETE') {
    $body = readJsonBody();
    if ($body === false) {
        ApiResponse::error('BAD_REQUEST', 'Malformed JSON body.', 400);
        exit;
    }
    $controller->deleteMany($body);
    exit;
}

if (str_starts_with($path, '/api/products/') && ($method === 'GET' || $method === 'DELETE')) {
    $sku = urldecode(substr($path, strlen('/api/products/')));
    if ($sku === '') {
        ApiResponse::error('BAD_REQUEST', 'SKU is required.', 400);
        exit;
    }
    if ($method === 'GET') {
        $controller->get($sku);
        exit;
    }
    $controller->deleteOne($sku);
    exit;
}

// ---- Legacy routes (BC for React UI: GET / bare array, POST / object|array) ----
if ($path === '/' && $method === 'GET') {
    $controller->list(true);
    exit;
}

if ($path === '/' && $method === 'POST') {
    $body = readJsonBody();
    if ($body === false) {
        ApiResponse::error('BAD_REQUEST', 'Failed to parse content.', 400);
        exit;
    }
    if (is_array($body) && array_is_list($body)) {
        // Mass delete (legacy 000webhost workaround: POST with SKU array).
        if ($body === []) {
            ApiResponse::error('BAD_REQUEST', 'DELETE - Empty deletion string.', 400);
            exit;
        }
        $controller->deleteMany($body);
        exit;
    }
    if (is_array($body)) {
        $controller->create($body);
        exit;
    }
    ApiResponse::error('BAD_REQUEST', 'Failed to parse content.', 400);
    exit;
}

ApiResponse::error('NOT_FOUND', "No route for {$method} {$path}.", 404);
