<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\Isbn;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    public function testValidIsbn(): void
    {
        $isbn = new Isbn('978-3-16-148410-0');
        $this->assertSame('978-3-16-148410-0', $isbn->getValue());
    }

    public function testInvalidIsbn(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Isbn('invalid-isbn');
    }
}
