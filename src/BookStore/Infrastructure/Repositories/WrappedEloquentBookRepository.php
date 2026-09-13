<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Repositories;

use App\Models\Author;
use App\Models\Book;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\BookStore\Domain\Models\Isbn;

class WrappedEloquentBookRepository implements BookRepository
{
    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $books = Book::all();
        $bookEntities = [];
        foreach ($books as $book) {
            $bookEntities[] = new BookEntity(
                new BookId($book->uuid),
                $book->title,
                new Isbn($book->isbn),
                $book->author_id,
                $book->is_active,
                \DateTimeImmutable::createFromInterface($book->created_at),
                \DateTimeImmutable::createFromInterface($book->updated_at),
            );
        }
        return $bookEntities;
    }

    public function findById(BookId $id): ?BookEntity
    {
        $book = Book::where('uuid', $id->getValue())->first();
        if ($book === null) {
            return null;
        }
        return new BookEntity(
            new BookId($book->uuid),
            $book->title,
            new Isbn($book->isbn),
            $book->author_id,
            $book->is_active,
            \DateTimeImmutable::createFromInterface($book->created_at),
            \DateTimeImmutable::createFromInterface($book->updated_at),
        );
    }

    public function save(BookEntity $book): void
    {
        $eloquentBook = Book::where('uuid', $book->getId()->getValue())->first() ?? new Book();
        $eloquentAuthor = null;
        if ($book->getAuthor() !== null) {
           $eloquentAuthor = Author::where('uuid', $book->getAuthor()->getValue())->first();
           if ($eloquentAuthor === null) {
               throw new \DomainException('Author not found');
           }
           $eloquentBook->author = $eloquentAuthor->id;
        }
        $eloquentBook->uuid = $book->getId()->getValue();
        $eloquentBook->title = $book->getTitle();
        $eloquentBook->isbn = $book->getIsbn()->getValue();
        $eloquentBook->is_active = $book->isActive();
        $eloquentBook->created_at = $book->getCreatedAt();
        $eloquentBook->updated_at = $book->getUpdatedAt();
        $eloquentBook->save();
    }
}
