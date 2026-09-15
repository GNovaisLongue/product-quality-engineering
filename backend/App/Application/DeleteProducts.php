<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\ProductRepository;
use App\Domain\ValidationException;

class DeleteProducts
{
    public function __construct(private ProductRepository $repo)
    {
    }

    /**
     * @param string[]|mixed $skus
     */
    public function execute(mixed $skus): int
    {
        if (!is_array($skus) || $skus === []) {
            throw new ValidationException(['sku' => 'Provide a non-empty array of SKUs to delete.']);
        }
        $clean = [];
        foreach ($skus as $s) {
            if (!is_string($s) || trim($s) === '') {
                throw new ValidationException(['sku' => 'Each SKU must be a non-empty string.']);
            }
            $clean[] = trim($s);
        }
        return $this->repo->deleteBySkus(array_values(array_unique($clean)));
    }
}
