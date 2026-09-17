<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;

class InvalidAuthorIdException extends \InvalidArgumentException
{
    public static function create(): self
    {
        return new self('', ErrorCode::AUTHOR_INVALID_ID_PROVIDED->value);
    }
}

