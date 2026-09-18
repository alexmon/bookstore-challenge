<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\BookId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BookIdTest extends TestCase
{
    public function testGenerateReturnsValidBookId(): void
    {
        $bookId = BookId::generate();
        $this->assertInstanceOf(BookId::class, $bookId);
        $this->assertNotEmpty($bookId->getValue());
    }

    #[TestDox('constructor throws exception for invalid UUID')]
    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BookId::from('invalid-uuid');
    }

    #[TestDox('assert static from method returns valid BookId')]
    public function testStaticFromMethodReturnsValidBookId(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $bookId = BookId::from($uuid);
        $this->assertInstanceOf(BookId::class, $bookId);
        $this->assertSame($uuid, $bookId->getValue());
    }
}
