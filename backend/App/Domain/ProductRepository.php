<?php

declare(strict_types=1);

namespace App\Domain;

interface ProductRepository
{
    /** @return Product[] */
    public function findAll(): array;

    public function findBySku(string $sku): ?Product;

    public function exists(string $sku): bool;

    public function save(Product $product): void;

    /** @param string[] $skus @return int rows deleted */
    public function deleteBySkus(array $skus): int;
}
