<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\FetchBook;

use BookStoreAPI\BookStore\Application\Queries\FetchBook\FetchBookQuery;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBookIdException;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

class FetchBookQueryHandler
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ValidationService $validationService,
    ) {}

    /**
     * @throws ValidationException|InvalidBookIdException
     */
    public function handle(FetchBookQuery $query): ?BookEntity
    {
        $this->validationService->validate($query);

        return $this->bookRepository->findById(BookId::from($query->uuid));
    }
}
