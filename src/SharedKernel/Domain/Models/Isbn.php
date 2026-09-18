<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Domain\Models;

use BookStoreAPI\SharedKernel\Domain\Exceptions\InvalidISBNException;

/**
 * Isbn value object
 */
class Isbn implements \Stringable
{

    protected function __construct(private string $value)
    {
        if (!preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $value)) {
            throw new InvalidISBNException();
        }
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
