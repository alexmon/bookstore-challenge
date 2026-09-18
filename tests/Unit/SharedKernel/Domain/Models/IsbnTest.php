<?php

declare(strict_types=1);

namespace Tests\Unit\SharedKernel\Domain\Models;

use BookStoreAPI\SharedKernel\Domain\Exceptions\InvalidISBNException;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    public function testValidIsbn(): void
    {
        $isbn = Isbn::from('978-3-16-148410-0');
        $this->assertSame('978-3-16-148410-0', $isbn->getValue());
    }

    public function testInvalidIsbn(): void
    {
        $this->expectException(InvalidISBNException::class);
        Isbn::from('invalid-isbn');
    }
}
