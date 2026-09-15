<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateAuthor;

use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommand;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

readonly class CreateAuthorCommandHandler
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private ValidationService $validationService,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function handle(CreateAuthorCommand $command)
    {
        $this->validationService->validate($command);

        $author = new AuthorEntity(
            AuthorId::generate(),
            name: $command->name,
        );

        $this->authorRepository->save($author);

        $command->setResult($author);
    }
}

