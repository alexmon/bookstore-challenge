<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Services;

use BookStoreAPI\BookStore\Domain\Services\BookEntityBuilder;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use PHPUnit\Framework\TestCase;

class BookEntityBuilderTest extends TestCase
{
    public function testCanBuildBookEntity(): void
    {
        $bookId = BookId::from('123e4567-e89b-12d3-a456-426614174000');
        $title = 'Test Book';
        $isbn = Isbn::from('978-3-16-148410-0');
        $isActive = true;

        $builder = new BookEntityBuilder($bookId, $title, $isbn, $isActive);
        $bookEntity = $builder->build();

        $this->assertEquals($bookId, $bookEntity->getId());
        $this->assertEquals($title, $bookEntity->getTitle());
        $this->assertEquals($isbn, $bookEntity->getIsbn());
        $this->assertEquals($isActive, $bookEntity->isActive());
    }
}
