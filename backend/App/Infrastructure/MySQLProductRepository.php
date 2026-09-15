<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Api\ProductFactory;
use App\Domain\Product;
use App\Domain\ProductRepository;
use PDO;

class MySQLProductRepository implements ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return Product[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT `sku`,`name`,`price`,`product_type`,`product_attribute` FROM `products` ORDER BY `sku` ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $row) {
            try {
                $out[] = ProductFactory::createFromRow($row);
            } catch (\Throwable) {
                // Skip corrupt legacy rows rather than failing the whole list.
                continue;
            }
        }
        return $out;
    }

    public function findBySku(string $sku): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT `sku`,`name`,`price`,`product_type`,`product_attribute` FROM `products` WHERE `sku` = ? LIMIT 1');
        $stmt->execute([$sku]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return ProductFactory::createFromRow($row);
    }

    public function exists(string $sku): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM `products` WHERE `sku` = ? LIMIT 1');
        $stmt->execute([$sku]);
        return $stmt->fetchColumn() !== false;
    }

    public function save(Product $product): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `products` (`sku`,`name`,`price`,`product_type`,`product_attribute`) VALUES (?,?,?,?,?)'
        );
        $stmt->execute([
            $product->getSku(),
            $product->getName(),
            Product::formatNumber($product->getPrice()),
            $product->getType(),
            $product->getAttribute(),
        ]);
    }

    public function deleteBySkus(array $skus): int
    {
        if ($skus === []) {
            return 0;
        }
        $binds = implode(',', array_fill(0, count($skus), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM `products` WHERE `sku` IN ({$binds})");
        $stmt->execute(array_values($skus));
        return $stmt->rowCount();
    }
}
