<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Domain\Exceptions;

class ValidationException extends \InvalidArgumentException implements \Stringable
{
    private array $errors = [];

    public static function badRequest(array $errors): self
    {
        $e = new self('Invalid attribute', 400);
        $e->errors = $errors;
        return $e;
    }

    public function addError(string $message, string $attribute): void
    {
        $this->errors[] = ['reason' => $message, 'property' => $attribute];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __toString(): string
    {
        return \sprintf('%s with errors: %s. %s', $this->message, json_encode($this->errors), parent::__toString());
    }
}
