<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Http\Response;

use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;

class ErrorMessages
{
    public const AUTHOR_NOT_FOUND = 'Author not found.';
    public const AUTHOR_INVALID_ID_PROVIDED = 'Invalid author ID provided.';
    public const BOOK_NOT_FOUND = 'Book not found.';
    public const BOOK_INVALID_ID_PROVIDED = 'Invalid book ID provided.';
    public const BOOK_BORROW_REQUEST_ON_INACTIVE = 'Borrow request on inactive book.';
    public const BOOK_BORROW_REQUEST_ON_UNAVAILABLE = 'Borrow request on unavailable book.';
    public const BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK = 'Return request on available book.';
    public const BOOK_ALREADY_EXISTS = 'Book already exists.';
    public const BORROWER_NOT_FOUND = 'Borrower not found.';
    public const BORROWER_INVALID_ID_PROVIDED = 'Invalid borrower ID provided.';
    public const BORROWER_ALREADY_EXISTS = 'Borrower already exists.';

    public static function getMessageByErrorCode(int $errorCode): string
    {
        return match ($errorCode) {
            ErrorCode::AUTHOR_NOT_FOUND->value => self::AUTHOR_NOT_FOUND,
            ErrorCode::AUTHOR_INVALID_ID_PROVIDED->value => self::AUTHOR_INVALID_ID_PROVIDED,
            ErrorCode::BOOK_NOT_FOUND->value => self::BOOK_NOT_FOUND,
            ErrorCode::BOOK_INVALID_ID_PROVIDED->value => self::BOOK_INVALID_ID_PROVIDED,
            ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value => self::BOOK_BORROW_REQUEST_ON_INACTIVE,
            ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value => self::BOOK_BORROW_REQUEST_ON_UNAVAILABLE,
            ErrorCode::BOOK_ALREADY_EXISTS->value => self::BOOK_ALREADY_EXISTS,
            ErrorCode::BORROWER_NOT_FOUND->value => self::BORROWER_NOT_FOUND,
            ErrorCode::BORROWER_INVALID_ID_PROVIDED->value => self::BORROWER_INVALID_ID_PROVIDED,
            ErrorCode::BORROWER_ALREADY_EXISTS->value => self::BORROWER_ALREADY_EXISTS,
            ErrorCode::BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK->value => self::BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK,
            default => 'Server error.',
        };
    }
}



