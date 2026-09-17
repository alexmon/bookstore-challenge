<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;

interface BookRepository
{
    public function search(
        int $page,
        int $perPage,
        ?string $search,
        ?string $authorUuid = null,
        ?bool $available = null,
    ): array;

    public function findById(BookId $id, bool $lock = false): ?BookEntity;

    public function save(BookEntity $book): void;
}
