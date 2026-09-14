<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Service;

use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function validate(object $object, array $context = []): void
    {
        $errors = $this->validator->validate($object, null, $context);
        if ($errors->count() !== 0) {
            $violations = [];
            foreach ($errors as $error) {
                $violations[] = ['reason' => $error->getMessage(), 'property' => $error->getPropertyPath()];
            }

            throw ValidationException::badRequest($violations);
        }
    }
}
