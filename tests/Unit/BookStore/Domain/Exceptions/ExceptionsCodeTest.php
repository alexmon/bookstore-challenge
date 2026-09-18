<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Exceptions;

use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BookAlreadyExists;
use BookStoreAPI\BookStore\Domain\Exceptions\BookNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BorrowerNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BorrowException;
use BookStoreAPI\BookStore\Domain\Exceptions\CreateBorrowerAlreadyExistsException;
use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBookIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBorrowerIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\ReturnException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ExceptionsCodeTest extends TestCase
{
    #[TestDox('AuthorNotFoundException should have the correct error code')]
    public function testAuthorNotFoundException(): void
    {
        $exception = AuthorNotFoundException::create();
        $this->assertSame(ErrorCode::AUTHOR_NOT_FOUND->value, $exception->getCode());
    }

    #[TestDox('BookAlreadyExistsException should have the correct error code')]
    public function testBookAlreadyExistsException(): void
    {
        $exception = BookAlreadyExists::create();
        $this->assertSame(ErrorCode::BOOK_ALREADY_EXISTS->value, $exception->getCode());
    }

    #[TestDox('BookNotFoundException should have the correct error code')]
    public function testBookNotFoundException(): void
    {
        $exception = BookNotFoundException::create();
        $this->assertSame(ErrorCode::BOOK_NOT_FOUND->value, $exception->getCode());
    }

    #[TestDox('BorrowerNotFoundException should have the correct error code')]
    public function testBorrowerNotFoundException(): void
    {
        $exception = BorrowerNotFoundException::create();
        $this->assertSame(ErrorCode::BORROWER_NOT_FOUND->value, $exception->getCode());
    }

    #[TestDox('BorrowException::createRequestOnInactive should have the correct error code')]
    public function testBorrowExceptionRequestOnInactive(): void
    {
        $exception = BorrowException::createRequestOnInactive();
        $this->assertSame(ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value, $exception->getCode());
    }

     #[TestDox('BorrowException::createRequestOnUnavailable should have the correct error code')]
     public function testBorrowExceptionRequestOnUnavailable(): void
    {
        $exception = BorrowException::createRequestOnUnavailable();
        $this->assertSame(ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value, $exception->getCode());
    }

    #[TestDox('CreateBorrowerAlreadyExistsException should have the correct error code')]
    public function testCreateBorroerAlreadyExistsException(): void
    {
        $exception = CreateBorrowerAlreadyExistsException::create();
        $this->assertSame(ErrorCode::BORROWER_ALREADY_EXISTS->value, $exception->getCode());
    }

    #[TestDox('InvalidAuthorIdException should have the correct error code')]
    public function testInvalidAuthorIdException(): void
    {
        $exception = InvalidAuthorIdException::create();
        $this->assertSame(ErrorCode::AUTHOR_INVALID_ID_PROVIDED->value, $exception->getCode());
    }

    #[TestDox('InvalidBookIdException should have the correct error code')]
    public function testInvalidBookIdException(): void
    {
        $exception = InvalidBookIdException::create();
        $this->assertSame(ErrorCode::BOOK_INVALID_ID_PROVIDED->value, $exception->getCode());
    }

    #[TestDox('InvalidBorrowerIdException should have the correct error code')]
    public function testInvalidBorrowerIdException(): void
    {
        $exception = InvalidBorrowerIdException::create();
        $this->assertSame(ErrorCode::BORROWER_INVALID_ID_PROVIDED->value, $exception->getCode());
    }

    #[TestDox('ReturnException should have the correct error code')]
    public function testReturnException(): void
    {
        $exception = ReturnException::create();
        $this->assertSame(ErrorCode::BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK->value, $exception->getCode());
    }
}
