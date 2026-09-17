<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

class BorrowException extends \LogicException
{
    public static function createRequestOnInactive(): self
    {
        return new self('', ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value);
    }

    public static function createRequestOnUnavailable(): self
    {
        return new self('', ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value);
    }
}

