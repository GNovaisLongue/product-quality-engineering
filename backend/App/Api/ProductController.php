<?php

declare(strict_types=1);

namespace App\Api;

use App\Application\CreateProduct;
use App\Application\DeleteProducts;
use App\Application\ListProducts;
use App\Domain\ConflictException;
use App\Domain\NotFoundException;
use App\Domain\Product;
use App\Domain\ValidationException;
use App\Infrastructure\DatabaseConnection;
use App\Infrastructure\MySQLProductRepository;
use PDOException;

class ProductController
{
    private MySQLProductRepository $repo;

    public function __construct(?MySQLProductRepository $repo = null)
    {
        $this->repo = $repo ?? new MySQLProductRepository(DatabaseConnection::get());
    }

    /** GET /api/products — legacy shape on ?legacy=1 or legacy route. */
    public function list(bool $legacy = false): void
    {
        try {
            $products = (new ListProducts($this->repo))->execute();
            $data = array_map(fn (Product $p) => $legacy ? $p->toLegacyArray() : $p->toArray(), $products);
            if ($legacy) {
                // BC: old frontend expects a bare array.
                ApiResponse::json($data, 200);
                return;
            }
            ApiResponse::success($data, 200, ['count' => count($data)]);
        } catch (PDOException $e) {
            error_log('[api] list failed: ' . $e->getMessage());
            ApiResponse::error('DB_ERROR', 'Failed to list products.', 500);
        }
    }

    public function get(string $sku): void
    {
        try {
            $product = $this->repo->findBySku($sku);
            if ($product === null) {
                ApiResponse::error('NOT_FOUND', "Product '{$sku}' not found.", 404);
                return;
            }
            ApiResponse::success($product->toArray(), 200);
        } catch (PDOException $e) {
            error_log('[api] get failed: ' . $e->getMessage());
            ApiResponse::error('DB_ERROR', 'Failed to fetch product.', 500);
        }
    }

    /** @param array<string,mixed>|mixed $body */
    public function create(mixed $body): void
    {
        try {
            if (!is_array($body) || $body === []) {
                ApiResponse::error('BAD_REQUEST', 'Request body must be a product object.', 400);
                return;
            }
            $product = (new CreateProduct($this->repo))->execute($body);
            ApiResponse::success($product->toArray(), 201);
        } catch (ValidationException $e) {
            ApiResponse::error('VALIDATION_ERROR', $e->getMessage(), 422, $e->getErrors());
        } catch (ConflictException $e) {
            ApiResponse::error('CONFLICT', $e->getMessage(), 409);
        } catch (PDOException $e) {
            error_log('[api] create failed: ' . $e->getMessage());
            ApiResponse::error('DB_ERROR', 'Failed to create product.', 500);
        }
    }

    /** @param string[]|mixed $skus */
    public function deleteMany(mixed $skus): void
    {
        try {
            $deleted = (new DeleteProducts($this->repo))->execute($skus);
            ApiResponse::success(['deleted' => $deleted], 200);
        } catch (ValidationException $e) {
            ApiResponse::error('VALIDATION_ERROR', $e->getMessage(), 422, $e->getErrors());
        } catch (PDOException $e) {
            error_log('[api] delete failed: ' . $e->getMessage());
            ApiResponse::error('DB_ERROR', 'Failed to delete products.', 500);
        }
    }

    public function deleteOne(string $sku): void
    {
        $this->deleteMany([$sku]);
    }
}
