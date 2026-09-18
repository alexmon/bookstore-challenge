<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook;

use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

class FetchAndLockBookCommandHandler
{
     public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ValidationService $validationService,
    ) {}

    public function handle(FetchAndLockBookCommand $command): void
    {
        $this->validationService->validate($command);

        $book = $this->bookRepository->findById(
            id: BookId::from($command->uuid),
            lock: true,
        );

        $command->setResult($book);
    }
}
