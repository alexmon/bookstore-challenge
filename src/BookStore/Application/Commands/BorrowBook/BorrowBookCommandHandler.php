<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\BorrowBook;

use BookStoreAPI\BookStore\Application\Commands\BorrowBook\BorrowBookCommand;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;

class BorrowBookCommandHandler
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {}

    public function handle(BorrowBookCommand $command): void
    {
        $book = $command->book;
        $borrower = $command->borrower;

        $book->borrow($borrower);

        $this->bookRepository->save($book);
    }
}
