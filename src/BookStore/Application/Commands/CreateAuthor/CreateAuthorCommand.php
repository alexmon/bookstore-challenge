<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateAuthor;

use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;
use Symfony\Component\Validator\Constraints as Assert;

class CreateAuthorCommand extends Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        #[Assert\NotNull]
        public readonly ?string $name,
    ) {
    }
}
