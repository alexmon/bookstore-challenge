<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\FetchBook;

use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\Query;
use Symfony\Component\Validator\Constraints as Assert;

class FetchBookQuery implements Query
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 36, max: 36)]
        #[Assert\NotNull]
        public readonly string $uuid,
    ) {}
}
