<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use Ramsey\Uuid\Uuid;

/**
 * AuthorId value object
 */
class AuthorId implements \Stringable
{
    public function __construct(private string $id)
    {
        if (!Uuid::isValid($id)) {
            throw InvalidAuthorIdException::create();
        }
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
