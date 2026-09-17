<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

class BookAlreadyExists extends \InvalidArgumentException
{
    public static function create(): self
    {
        return new self('', ErrorCode::BOOK_ALREADY_EXISTS->value);
    }
}
