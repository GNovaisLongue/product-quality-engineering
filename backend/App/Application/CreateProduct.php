<?php

declare(strict_types=1);

namespace App\Application;

use App\Api\ProductFactory;
use App\Domain\ConflictException;
use App\Domain\Product;
use App\Domain\ProductRepository;
use App\Domain\ValidationException;

class CreateProduct
{
    public function __construct(private ProductRepository $repo)
    {
    }

    /**
     * @param array<string,mixed> $input
     */
    public function execute(array $input): Product
    {
        $product = ProductFactory::createFromRequest($input);
        $product->validate();

        if ($this->repo->exists($product->getSku())) {
            throw new ConflictException('SKU already exists!');
        }

        $this->repo->save($product);
        return $product;
    }
}
