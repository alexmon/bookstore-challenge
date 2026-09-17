<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\BorrowBook;

use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;

class BorrowBookCommand extends Command
{
    public function __construct(
        public readonly BookEntity $book,
        public readonly BorrowerEntity $borrower,
    ) {}
}
