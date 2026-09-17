<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

class CreateBorrowerAlreadyExistsException extends \DomainException
{
    public static function create(): self
    {
        return new self('', ErrorCode::BORROWER_ALREADY_EXISTS->value);
    }
}
