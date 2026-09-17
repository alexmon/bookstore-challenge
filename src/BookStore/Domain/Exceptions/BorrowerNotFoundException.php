<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;

class BorrowerNotFoundException extends \DomainException
{
    public static function create(): self
    {
        return new self('', ErrorCode::BORROWER_NOT_FOUND->value);
    }
}
