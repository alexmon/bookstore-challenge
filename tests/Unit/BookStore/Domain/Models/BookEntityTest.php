<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\Isbn;
use PHPUnit\Framework\TestCase;

class BookEntityTest extends TestCase
{
    public function testBookEntityCreation(): void
    {
        $bookId = new BookId('123e4567-e89b-12d3-a456-426614174000');
        $isbn = new Isbn('978-3-16-148410-0');
        $title = 'Sample Book';

        $bookEntity = new BookEntity(
            $bookId,
            $title,
            $isbn,
        );

        $this->assertSame($bookId, $bookEntity->getId());
        $this->assertSame($title, $bookEntity->getTitle());
        $this->assertSame($isbn, $bookEntity->getIsbn());
        $this->assertNull($bookEntity->getAuthor());
        $this->assertTrue($bookEntity->isActive());
        $this->assertInstanceOf(\DateTimeImmutable::class, $bookEntity->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $bookEntity->getUpdatedAt());
    }
}


