<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateBook;

use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBookCommand extends Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\NotNull]
        public readonly ?string $title,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 20)]
        #[Assert\NotNull]
        public readonly ?string $isbn,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\NotNull]
        public readonly ?string $authorUuid
    ) {}
}
