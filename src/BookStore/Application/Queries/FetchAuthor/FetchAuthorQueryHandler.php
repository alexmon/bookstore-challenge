<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\FetchAuthor;

use BookStoreAPI\BookStore\Application\Queries\FetchAuthor\FetchAuthorQuery;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

class FetchAuthorQueryHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly ValidationService $validationService,
    ) {}

    /**
     * @throws ValidationException|InvalidAuthorIdException
     */
    public function handle(FetchAuthorQuery $query): ?AuthorEntity
    {
        $this->validationService->validate($query);

        return $this->authorRepository->findById(AuthorId::from($query->uuid));
    }
}
