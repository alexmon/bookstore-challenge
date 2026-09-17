<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\BorrowException;
use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use Error;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BorrowerEntityFixture;

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

    // @testdox borrow method throws exception when book is inactive
    public function testBorrowThrowsExceptionWhenBookIsInactive(): void
    {
        $bookId = new BookId('123e4567-e89b-12d3-a456-426614174000');
        $isbn = new Isbn('978-3-16-148410-0');
        $title = 'Sample Book';

        $bookEntity = new BookEntity(
            $bookId,
            $title,
            $isbn,
            null,
            false, // inactive book
        );

        $this->expectException(BorrowException::class);
        $this->expectExceptionCode(ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value);

        $bookEntity->borrow(BorrowerEntityFixture::create());
    }

    // @testdox borrow method throws BorrowException when book is already borrowed
    public function testBorrowThrowsExceptionWhenBookIsAlreadyBorrowed(): void
    {
        $bookId = new BookId('123e4567-e89b-12d3-a456-426614174000');
        $isbn = new Isbn('978-3-16-148410-0');
        $title = 'Sample Book';

        $borrower = BorrowerEntityFixture::create();
        $bookEntity = new BookEntity(
            $bookId,
            $title,
            $isbn,
            null,
            true, // active book
            $borrower, // already borrowed by this borrower
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')),
        );

        $newBorrower = BorrowerEntityFixture::create();

        $this->expectException(BorrowException::class);
        $this->expectExceptionCode(ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value);

        $bookEntity->borrow($newBorrower);
    }
}


