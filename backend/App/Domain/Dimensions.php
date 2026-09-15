<?php

declare(strict_types=1);

namespace App\Domain;

/** Value object: Furniture dimensions in centimetres. */
class Dimensions
{
    private float $height;
    private float $width;
    private float $length;

    public function __construct(float|int|string $height, float|int|string $width, float|int|string $length)
    {
        $this->height = (float) $height;
        $this->width = (float) $width;
        $this->length = (float) $length;
    }

    public static function fromString(string $raw): self
    {
        $parts = array_map('trim', explode('x', trim($raw)));
        if (count($parts) !== 3 || in_array('', $parts, true)) {
            throw new ValidationException(['dimensions' => 'Dimensions must be in HxWxL format, e.g. "200x100x50".']);
        }
        foreach ($parts as $p) {
            if (!is_numeric($p)) {
                throw new ValidationException(['dimensions' => 'Dimensions must be numeric HxWxL, e.g. "200x100x50".']);
            }
        }
        return new self($parts[0], $parts[1], $parts[2]);
    }

    public function validate(): void
    {
        $errors = [];
        if ($this->height <= 0) {
            $errors['height'] = 'Height must be greater than 0.';
        }
        if ($this->width <= 0) {
            $errors['width'] = 'Width must be greater than 0.';
        }
        if ($this->length <= 0) {
            $errors['length'] = 'Length must be greater than 0.';
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function toArray(): array
    {
        return ['height' => $this->height, 'width' => $this->width, 'length' => $this->length];
    }

    public function __toString(): string
    {
        return Product::formatNumber($this->height) . 'x'
            . Product::formatNumber($this->width) . 'x'
            . Product::formatNumber($this->length);
    }
}
