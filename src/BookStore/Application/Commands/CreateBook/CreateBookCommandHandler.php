<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateBook;

use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\InvalidISBNException;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

/**
 * @throws ValidationException|AuthorNotFoundException|InvalidAuthorIdException|InvalidISBNException
 */
readonly class CreateBookCommandHandler
{
    public function __construct(
        private BookRepository $bookRepository,
        private AuthorRepository $authorRepository,
        private ValidationService $validationService,
    ) {}

    public function handle(CreateBookCommand $command): void
    {
        $this->validationService->validate($command);

        $author = $this->authorRepository->findById(
            new AuthorId($command->authorUuid)
        );

        if (null === $author) {
            throw new AuthorNotFoundException;
        }

        $book = new BookEntity(
            BookId::generate(),
            $command->title,
            new Isbn($command->isbn),
            $author->getId(),
        );

        $this->bookRepository->save($book);

        $command->setResult([
            'book' => $this->bookRepository->findById($book->getId()),
            'author' => $author,
        ]);
    }
}
