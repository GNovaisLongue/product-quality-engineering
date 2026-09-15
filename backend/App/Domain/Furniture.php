<?php

declare(strict_types=1);

namespace App\Domain;

class Furniture extends Product
{
    private Dimensions $dimensions;

    public function __construct(string $sku, string $name, float|int|string $price, Dimensions $dimensions)
    {
        parent::__construct($sku, $name, $price);
        $this->dimensions = $dimensions;
    }

    public function getType(): string
    {
        return 'Furniture';
    }

    public function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    public function getAttribute(): string
    {
        return (string) $this->dimensions;
    }

    public function getAttributePayload(): array
    {
        return $this->dimensions->toArray();
    }

    public function validate(): void
    {
        $this->assertNoBaseErrors();
        $this->dimensions->validate();
    }
}
