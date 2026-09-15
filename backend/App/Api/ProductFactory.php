<?php

declare(strict_types=1);

namespace App\Api;

use App\Domain\Book;
use App\Domain\Dimensions;
use App\Domain\DVD;
use App\Domain\Furniture;
use App\Domain\Product;
use App\Domain\ValidationException;

/**
 * Single place that converts external data (API JSON / DB row)
 * into the right Product subtype. Controllers never branch on type.
 */
class ProductFactory
{
    /**
     * @param array<string,mixed> $data
     */
    public static function createFromRequest(array $data): Product
    {
        $sku = (string) ($data['sku'] ?? '');
        $name = (string) ($data['name'] ?? '');
        $price = $data['price'] ?? '';
        $type = (string) ($data['product_type'] ?? $data['type'] ?? '');

        $normalized = strtolower(trim($type));

        switch ($normalized) {
            case 'dvd':
                $size = $data['size'] ?? $data['attribute'] ?? $data['product_attribute'] ?? null;
                if ($size === null || $size === '') {
                    throw new ValidationException(['size' => 'Size is required for DVD.']);
                }
                if (is_array($size)) {
                    throw new ValidationException(['size' => 'Size must be a single numeric value.']);
                }
                return new DVD($sku, $name, $price, $size);

            case 'book':
                $weight = $data['weight'] ?? $data['attribute'] ?? $data['product_attribute'] ?? null;
                if ($weight === null || $weight === '') {
                    throw new ValidationException(['weight' => 'Weight is required for Book.']);
                }
                if (is_array($weight)) {
                    throw new ValidationException(['weight' => 'Weight must be a single numeric value.']);
                }
                return new Book($sku, $name, $price, $weight);

            case 'furniture':
                // Accept H/W/L fields, nested attribute, or legacy "HxWxL" string.
                $h = $data['height'] ?? null;
                $w = $data['width'] ?? null;
                $l = $data['length'] ?? null;
                if (is_array($data['attribute'] ?? null)) {
                    $h = $h ?? $data['attribute']['height'] ?? null;
                    $w = $w ?? $data['attribute']['width'] ?? null;
                    $l = $l ?? $data['attribute']['length'] ?? null;
                }
                if ($h !== null && $w !== null && $l !== null && $h !== '' && $w !== '' && $l !== '') {
                    return new Furniture($sku, $name, $price, new Dimensions($h, $w, $l));
                }
                $raw = $data['dimensions'] ?? $data['attribute'] ?? $data['product_attribute'] ?? null;
                if ($raw === null || $raw === '') {
                    throw new ValidationException(['dimensions' => 'Dimensions (height, width, length or "HxWxL") are required for Furniture.']);
                }
                if (is_array($raw)) {
                    throw new ValidationException(['dimensions' => 'Dimensions must be height/width/length or "HxWxL".']);
                }
                return new Furniture($sku, $name, $price, Dimensions::fromString((string) $raw));

            default:
                throw new ValidationException(['product_type' => 'product_type must be one of: DVD, Book, Furniture.']);
        }
    }

    /**
     * @param array<string,mixed> $row DB row: sku,name,price,product_type,product_attribute
     */
    public static function createFromRow(array $row): Product
    {
        return self::createFromRequest([
            'sku' => (string) ($row['sku'] ?? ''),
            'name' => (string) ($row['name'] ?? ''),
            'price' => $row['price'] ?? '',
            'product_type' => (string) ($row['product_type'] ?? ''),
            'product_attribute' => (string) ($row['product_attribute'] ?? ''),
        ]);
    }
}
