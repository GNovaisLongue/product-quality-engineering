<?php

declare(strict_types=1);

namespace App\Domain;

class DVD extends Product
{
    private float $size;

    public function __construct(string $sku, string $name, float|int|string $price, float|int|string $size)
    {
        parent::__construct($sku, $name, $price);
        $this->size = (float) $size;
    }

    public function getType(): string
    {
        return 'DVD';
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getAttribute(): string
    {
        return self::formatNumber($this->size);
    }

    public function getAttributePayload(): array
    {
        return ['size' => $this->size];
    }

    public function validate(): void
    {
        $this->assertNoBaseErrors();
        if (!is_numeric((string) $this->size) || $this->size <= 0) {
            throw new ValidationException(['size' => 'Size must be a number greater than 0 (MB).']);
        }
    }
}
