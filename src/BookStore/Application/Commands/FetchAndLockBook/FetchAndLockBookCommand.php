<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook;

use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;
use Symfony\Component\Validator\Constraints as Assert;

class FetchAndLockBookCommand extends Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\NotNull]
        public readonly string $uuid,
    ) {}
}
