<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\ReturnBook;

use BookStoreAPI\BookStore\Domain\Models\BookRepository;

class ReturnBookCommandHandler
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {}

    public function handle(ReturnBookCommand $command): void
    {
        $book = $command->book;

        $book->return();

        $this->bookRepository->save($book);
    }
}
