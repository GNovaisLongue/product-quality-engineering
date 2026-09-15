<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Base product. The SKU/Name/Price contract lives here;
 * type-specific behaviour lives in subclasses (polymorphism).
 */
abstract class Product
{
    protected string $sku;
    protected string $name;
    protected float $price;

    public function __construct(string $sku, string $name, float|int|string $price)
    {
        $this->sku = trim($sku);
        $this->name = trim($name);
        $this->price = (float) $price;
    }

    abstract public function getType(): string;

    /** Persistence form: "200" for DVD/Book, "HxWxL" for Furniture. */
    abstract public function getAttribute(): string;

    /** API form: e.g. ["size" => 200] / ["weight" => 2.5] / ["height"=>..,"width"=>..,"length"=>..]. */
    abstract public function getAttributePayload(): array;

    abstract public function validate(): void;

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Shared validation for all products. Returns field => message map.
     *
     * @return array<string,string>
     */
    protected function validateBase(): array
    {
        $errors = [];

        if ($this->sku === '') {
            $errors['sku'] = 'SKU is required.';
        } elseif (strlen($this->sku) > 64) {
            $errors['sku'] = 'SKU must not exceed 64 characters.';
        } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $this->sku)) {
            $errors['sku'] = 'SKU may contain only letters, numbers, dash and underscore.';
        }

        if ($this->name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'Name must not exceed 255 characters.';
        }

        if (!is_numeric((string) $this->price) || $this->price <= 0) {
            $errors['price'] = 'Price must be a number greater than 0.';
        } elseif ($this->price > 99999999.99) {
            $errors['price'] = 'Price is too large.';
        }

        return $errors;
    }

    protected function assertNoBaseErrors(): void
    {
        $errors = $this->validateBase();
        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    public static function formatNumber(float|int|string $value): string
    {
        $f = (float) $value;
        // Keep integers clean ("200" not "200.00"), keep decimals otherwise.
        if (abs($f - round($f)) < 0.000001) {
            return (string) (int) round($f);
        }
        // Trim trailing zeros, max 2 decimals for money-like but generic for size/weight too.
        $s = number_format($f, 2, '.', '');
        return rtrim(rtrim($s, '0'), '.');
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'price' => self::formatNumber($this->getPrice()),
            'product_type' => $this->getType(),
            'product_attribute' => $this->getAttribute(),
            'attribute' => $this->getAttributePayload(),
        ];
    }

    /** Legacy row shape expected by the React UI (flat strings). */
    public function toLegacyArray(): array
    {
        return [
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'price' => self::formatNumber($this->getPrice()),
            'product_type' => $this->getType(),
            'product_attribute' => $this->getAttribute(),
        ];
    }
}
