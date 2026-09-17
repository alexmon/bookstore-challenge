<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\SearchBooks;

use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\Query;
use Symfony\Component\Validator\Constraints as Assert;

readonly class SearchBooksQuery implements Query
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $search = null,
        public ?string $authorUuid = null,
        public ?bool $available = null,
        #[Assert\Range(min: 1)]
        public int $page = 1,
        #[Assert\Range(min: 1, max: 100)]
        public int $perPage = 10,
    ) {
    }
}

