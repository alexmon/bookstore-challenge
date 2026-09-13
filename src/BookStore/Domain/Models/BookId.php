<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use Ramsey\Uuid\Uuid;

class BookId implements \Stringable
{
    public function __construct(private string $id)
    {
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException("Invalid UUID string: $id");
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
