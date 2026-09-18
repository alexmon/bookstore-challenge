<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\BorrowException;
use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use Faker\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\AuthorEntityFixture;
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

    #[TestDox('borrow method throws exception when book is inactive')]
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

    #[TestDox('borrow method throws BorrowException when book is already borrowed')]
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

    #[TestDox('test is available aggregate')]
    #[DataProvider('availabilityDataProvider')]
    public function testIsAvailable(bool $isActive, ?BorrowerEntity $borrower, bool $expected): void
    {
        $faker = Factory::create();
        $bookId = new BookId($faker->uuid);
        $isbn = new Isbn($faker->isbn13());
        $title = $faker->name;
        $author = AuthorEntityFixture::create();

        $bookEntity = new BookEntity(
            $bookId,
            $title,
            $isbn,
            $author,
            $isActive,
            $borrower
        );

        $this->assertSame($expected, $bookEntity->isAvailable());
    }

    public static function availabilityDataProvider(): array
    {
        return [
            'available when active and not borrowed' => [true, null, true],
            'not available when inactive' => [false, null, false],
            'not available when already borrowed' => [true, BorrowerEntityFixture::create(), false],
        ];
    }

    #[TestDox('test json serializable')]
    public function testJsonSerializable(): void
    {
        $faker = Factory::create();
        $bookId = new BookId($faker->uuid);
        $isbn = new Isbn($faker->isbn13());
        $title = $faker->name;
        $author = AuthorEntityFixture::create();
        $borrower = BorrowerEntityFixture::create();
        $bookEntity = new BookEntity(
            $bookId,
            $title,
            $isbn,
            $author,
            true,
            $borrower
        );

        $json = json_encode($bookEntity);

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('id', $decoded);
        $this->assertArrayHasKey('title', $decoded);
        $this->assertArrayHasKey('isbn', $decoded);
        $this->assertArrayHasKey('author', $decoded);
        $this->assertArrayHasKey('is_active', $decoded);
        $this->assertArrayHasKey('borrower', $decoded);
        $this->assertArrayHasKey('created_at', $decoded);
        $this->assertArrayHasKey('updated_at', $decoded);
    }
}


