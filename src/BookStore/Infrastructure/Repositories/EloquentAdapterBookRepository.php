<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Repositories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrower;
use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BookAlreadyExists;
use BookStoreAPI\BookStore\Domain\Exceptions\BorrowerNotFoundException;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use BookStoreAPI\BookStore\Domain\Services\BookEntityBuilder;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;

class EloquentAdapterBookRepository implements BookRepository
{
    /**
     * TODO fix contract return type
     * @return array|array{"current_page": int, "has_more_pages": bool, items: array, "last_page": int, "per_page": int, total: int, "total_pages": int|array{"current_page": int, "has_more_pages": bool, items: BookEntity[], "last_page": int, "per_page": int, total: int, "total_pages": int}}
     */
    public function search(
        int $page,
        int $perPage,
        ?string $search,
        ?string $authorUuid = null,
        ?bool $available = null,
    ): array {
        $query = Book::with(['author', 'borrower']);

        if ($search !== null) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if ($authorUuid !== null) {
            $author = Author::where('uuid', $authorUuid)->first();
            if ($author) {
                $query->where('author_id', $author->id);
            } else {
                return [];
            }
        }

        if ($available !== null) {
            $available
                ? $query->whereNull('borrower_id')
                : $query->whereNotNull('borrower_id');
        }

        $books = $query->paginate($perPage, ['*'], 'page', $page);

        $bookEntities = [];
        foreach ($books->items() as $book) {
            $authorEntity = $book->author
                ? new AuthorEntity(
                    AuthorId::from($book->author->uuid),
                    $book->author->name
                )
                : null;

            $bookEntityBuilder = new BookEntityBuilder(
                BookId::from($book->uuid),
                $book->title,
                Isbn::from($book->isbn),
                (bool) $book->is_active
            );
            $bookEntityBuilder
                ->setAuthor($authorEntity)
                ->setBorrower($book->borrower ? new BorrowerEntity(BorrowerId::from($book->borrower->uuid), $book->borrower->name) : null)
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($book->created_at))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($book->updated_at));

            $bookEntities[] = $bookEntityBuilder->build();
        }

        return [
            'current_page' => $books->currentPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
            'last_page' => $books->lastPage(),
            'has_more_pages' => $books->hasMorePages(),
            'items' => $bookEntities,
            'total_pages' => $books->lastPage(),
        ];
    }

    public function findById(BookId $id, bool $lock = false): ?BookEntity
    {
        $query = Book::with(['author', 'borrower'])->where('uuid', $id->getValue());
        if (true === $lock) {
            $query->lockForUpdate();
        }
        $book = $query->first();
        if ($book === null) {
            return null;
        }

        $authorEntity = $book->author
            ? new AuthorEntity(
                AuthorId::from($book->author->uuid),
                $book->author->name
            )
            : null;

        $bookEntityBuilder = new BookEntityBuilder(
            BookId::from($book->uuid),
            $book->title,
            Isbn::from($book->isbn),
            (bool) $book->is_active
        );
        $bookEntityBuilder
            ->setAuthor($authorEntity)
            ->setBorrower($book->borrower ? new BorrowerEntity(BorrowerId::from($book->borrower->uuid), $book->borrower->name) : null)
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($book->created_at));
        if ($book->updated_at !== null) {
            $bookEntityBuilder->setUpdatedAt(\DateTimeImmutable::createFromInterface($book->updated_at));
        }

        return $bookEntityBuilder->build();
    }

    public function save(BookEntity $book): void
    {
        $eloquentBook = Book::where('uuid', $book->getId()->getValue())->first() ?? new Book();

        if ($book->getAuthor() !== null) {
           $eloquentAuthor = Author::where('uuid', $book->getAuthor()->getId()->getValue())->first();
           if ($eloquentAuthor === null) {
               throw AuthorNotFoundException::create();
           }
           $eloquentBook->author_id = $eloquentAuthor->id;
        }

        if ($book->getBorrower() !== null) {
            $eloquentBorrower = Borrower::where('uuid', $book->getBorrower()->getId()->getValue())->first();
            if ($eloquentBorrower === null) {
                throw BorrowerNotFoundException::create();
            }
            $eloquentBook->borrower_id = $eloquentBorrower->id;
        } else {
            $eloquentBook->borrower_id = null;
        }

        $eloquentBook->uuid = $book->getId()->getValue();
        $eloquentBook->title = $book->getTitle();
        $eloquentBook->isbn = $book->getIsbn()->getValue();
        $eloquentBook->is_active = $book->isActive();
        $eloquentBook->created_at = $book->getCreatedAt();
        $eloquentBook->updated_at = $book->getUpdatedAt();

        try {
            $eloquentBook->save();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') { // Integrity constraint violation
                throw BookAlreadyExists::create();
            } else {
                throw $e;
            }
        }
    }
}
