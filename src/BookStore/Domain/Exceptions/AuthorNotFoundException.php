<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

class AuthorNotFoundException extends \DomainException
{
    public static function create(): self
    {
        return new self('', ErrorCode::AUTHOR_NOT_FOUND->value);
    }
}
