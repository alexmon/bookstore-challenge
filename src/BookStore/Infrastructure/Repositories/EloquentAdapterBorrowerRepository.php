<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Repositories;

use App\Models\Borrower;
use BookStoreAPI\BookStore\Domain\Exceptions\CreateBorrowerAlreadyExistsException;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerRepository;

class EloquentAdapterBorrowerRepository implements BorrowerRepository
{
    public function findById(BorrowerId $id): ?BorrowerEntity
    {
        $borrower = Borrower::where('uuid', $id->getValue())->first();

        if (null === $borrower) {
            return null;
        }

        return new BorrowerEntity(
            new BorrowerId($borrower->uuid),
            $borrower->name,
        );
    }

    public function findByName(string $name): ?BorrowerEntity
    {
        $borrower = Borrower::where('name', $name)->first();

        if (null === $borrower) {
            return null;
        }

        return new BorrowerEntity(
            new BorrowerId($borrower->uuid),
            $borrower->name,
        );
    }

    public function save(BorrowerEntity $borrower): void
    {
        $eloquentBorrower = Borrower::where('uuid', $borrower->getId()->getValue())->first() ?? new Borrower();
        $eloquentBorrower->uuid = $borrower->getId()->getValue();
        $eloquentBorrower->name = $borrower->getName();
        $eloquentBorrower->created_at = $borrower->getCreatedAt();
        $eloquentBorrower->updated_at = $borrower->getUpdatedAt();
        try {
            $eloquentBorrower->save();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') { // Integrity constraint violation
                throw CreateBorrowerAlreadyExistsException::create();
            } else {
                throw $e;
            }
        }
    }
}
