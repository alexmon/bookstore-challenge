<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\BookId;
use PHPUnit\Framework\TestCase;

class BookIdTest extends TestCase
{
    public function testGenerateReturnsValidBookId(): void
    {
        $bookId = BookId::generate();
        $this->assertInstanceOf(BookId::class, $bookId);
        $this->assertNotEmpty($bookId->getValue());
    }

    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BookId('invalid-uuid');
    }
}
