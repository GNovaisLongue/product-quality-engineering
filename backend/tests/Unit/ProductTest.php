<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Api\ProductFactory;
use App\Domain\Book;
use App\Domain\Dimensions;
use App\Domain\DVD;
use App\Domain\Furniture;
use App\Domain\ValidationException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testDvdValid(): void
    {
        $dvd = new DVD('DVD001', 'Example DVD', 20, 200);
        $dvd->validate();
        $this->assertSame('DVD', $dvd->getType());
        $this->assertSame('200', $dvd->getAttribute());
        $this->assertSame(['size' => 200.0], $dvd->getAttributePayload());
    }

    public function testDvdRejectsZeroAndNegative(): void
    {
        $this->expectException(ValidationException::class);
        (new DVD('DVD002', 'Bad', 10, 0))->validate();
    }

    public function testBookValid(): void
    {
        $book = new Book('BOOK001', 'Example Book', '15.50', '2.5');
        $book->validate();
        $this->assertSame('Book', $book->getType());
        $this->assertSame('2.5', $book->getAttribute());
    }

    public function testBookRejectsNonNumericWeight(): void
    {
        $this->expectException(ValidationException::class);
        (new Book('BOOK002', 'Bad', 10, 'abc'))->validate();
    }

    public function testFurnitureValidAndRoundTrip(): void
    {
        $f = new Furniture('FUR001', 'Chair', 99.99, new Dimensions(200, 100, 50));
        $f->validate();
        $this->assertSame('200x100x50', $f->getAttribute());
        // fromString polymorphic parse
        $parsed = Dimensions::fromString('200x100x50');
        $this->assertSame('200x100x50', (string) $parsed);
    }

    public function testFurnitureRejectsMalformed(): void
    {
        $this->expectException(ValidationException::class);
        Dimensions::fromString('200xabc');
    }

    public function testFurnitureRejectsZeroDimension(): void
    {
        $this->expectException(ValidationException::class);
        (new Furniture('FUR002', 'Bad', 10, new Dimensions(0, 10, 10)))->validate();
    }

    public function testBaseValidationRejectsBadSkuAndPrice(): void
    {
        $this->expectException(ValidationException::class);
        (new DVD('bad sku!', '', -5, 10))->validate();
    }

    public function testFactoryCreatesEachSubtype(): void
    {
        $dvd = ProductFactory::createFromRequest([
            'sku' => 'D1', 'name' => 'd', 'price' => 10,
            'product_type' => 'DVD', 'product_attribute' => '700',
        ]);
        $book = ProductFactory::createFromRequest([
            'sku' => 'B1', 'name' => 'b', 'price' => 10,
            'product_type' => 'Book', 'weight' => 1.2,
        ]);
        $fur = ProductFactory::createFromRequest([
            'sku' => 'F1', 'name' => 'f', 'price' => 10,
            'product_type' => 'Furniture', 'height' => 10, 'width' => 20, 'length' => 30,
        ]);
        $this->assertInstanceOf(DVD::class, $dvd);
        $this->assertInstanceOf(Book::class, $book);
        $this->assertInstanceOf(Furniture::class, $fur);
        $this->assertSame('10x20x30', $fur->getAttribute());
    }

    public function testFactoryRejectsUnknownType(): void
    {
        $this->expectException(ValidationException::class);
        ProductFactory::createFromRequest([
            'sku' => 'X1', 'name' => 'x', 'price' => 10, 'product_type' => 'Software',
        ]);
    }

    public function testToArrayContract(): void
    {
        $dvd = new DVD('DVD9', 'N', 20, 200);
        $arr = $dvd->toArray();
        $this->assertSame(['sku', 'name', 'price', 'product_type', 'product_attribute', 'attribute'], array_keys($arr));
        $this->assertSame('DVD', $arr['product_type']);
    }
}
