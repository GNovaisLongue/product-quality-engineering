<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Product;
use App\Domain\ProductRepository;

class ListProducts
{
    public function __construct(private ProductRepository $repo)
    {
    }

    /** @return Product[] */
    public function execute(): array
    {
        return $this->repo->findAll();
    }
}
