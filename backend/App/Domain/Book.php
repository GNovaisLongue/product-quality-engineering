<?php

declare(strict_types=1);

namespace App\Domain;

class Book extends Product
{
    private float $weight;

    public function __construct(string $sku, string $name, float|int|string $price, float|int|string $weight)
    {
        parent::__construct($sku, $name, $price);
        $this->weight = (float) $weight;
    }

    public function getType(): string
    {
        return 'Book';
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getAttribute(): string
    {
        return self::formatNumber($this->weight);
    }

    public function getAttributePayload(): array
    {
        return ['weight' => $this->weight];
    }

    public function validate(): void
    {
        $this->assertNoBaseErrors();
        if (!is_numeric((string) $this->weight) || $this->weight <= 0) {
            throw new ValidationException(['weight' => 'Weight must be a number greater than 0 (KG).']);
        }
    }
}
