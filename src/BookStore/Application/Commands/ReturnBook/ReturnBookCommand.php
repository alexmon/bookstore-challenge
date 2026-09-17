<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\ReturnBook;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;

class ReturnBookCommand extends Command
{
    public function __construct(
        public readonly BookEntity $book,
        public readonly BorrowerEntity $borrower,
    ) {}
}
