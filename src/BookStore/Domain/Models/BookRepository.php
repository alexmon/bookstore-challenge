<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;

interface BookRepository
{
    /**
     * @return BookEntity[]
     */
    public function findAll(): array;

    public function findById(BookId $id): ?BookEntity;

    public function save(BookEntity $book): void;
}
