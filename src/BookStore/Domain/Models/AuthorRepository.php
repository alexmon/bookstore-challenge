<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;

interface AuthorRepository
{
    /**
     * @return AuthorEntity[]
     */
    public function findAll(): array;

    public function findById(AuthorId $id): ?AuthorEntity;

    public function save(AuthorEntity $author): void;
}
