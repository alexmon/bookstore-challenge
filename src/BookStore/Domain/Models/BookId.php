<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBookIdException;
use Ramsey\Uuid\Uuid;

class BookId implements \Stringable
{
    protected function __construct(private string $id)
    {
        if (!Uuid::isValid($id)) {
            throw InvalidBookIdException::create();
        }
    }

    public static function from(string $id): self
    {
        return new self($id);
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
