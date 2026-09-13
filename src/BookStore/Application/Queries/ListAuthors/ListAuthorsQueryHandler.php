<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\ListAuthors;

use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;

class ListAuthorsQueryHandler
{
    public function __construct(
        private readonly AuthorRepository $repository
    ) {
    }

    public function handle(ListAuthorsQuery $query): array
    {
        return $this->repository->findAll();
    }
}
