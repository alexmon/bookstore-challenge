<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;

class BookNotFoundException extends \DomainException
{
    public static function create(): self
    {
        return new self('', ErrorCode::BOOK_NOT_FOUND->value);
    }
}
