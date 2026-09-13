<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

/**
 * Isbn value object
 */
class Isbn implements \Stringable
{

    public function __construct(private string $value)
    {
        if (!preg_match('/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/', $value)) {
            throw new \InvalidArgumentException('Invalid ISBN format');
        }
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
