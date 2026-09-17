<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\CreateBorrowerAlreadyExistsException;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;

interface BorrowerRepository
{
    public function findById(BorrowerId $id): ?BorrowerEntity;

    public function findByName(string $name): ?BorrowerEntity;

    /**
     * @throws CreateBorrowerAlreadyExistsException
     */
    public function save(BorrowerEntity $borrower): void;
}
