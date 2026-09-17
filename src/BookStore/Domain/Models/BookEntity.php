<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Exceptions\BorrowException;
use BookStoreAPI\BookStore\Domain\Exceptions\ReturnException;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;

/**
 * Book root aggregate
 */
class BookEntity
{
    private ?\DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        private BookId $id,
        private string $title,
        private Isbn $isbn,
        private ?AuthorEntity $author = null,
        private bool $isActive = true,
        private ?BorrowerEntity $borrower = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable();
    }

    public function getId(): BookId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getIsbn(): Isbn
    {
        return $this->isbn;
    }
    public function getAuthor(): ?AuthorEntity
    {
        return $this->author;
    }

    public function setAuthor(AuthorEntity $author): void
    {
        $this->author = $author;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getBorrower(): ?BorrowerEntity
    {
        return $this->borrower;
    }

    public function isAvailable(): bool
    {
        return $this->borrower === null;
    }

    /**
     * Root aggregate business logic for borrowing a book.
     */
    public function borrow(BorrowerEntity $borrower): void
    {
        if (!$this->isActive) {
            throw BorrowException::createRequestOnInactive();
        }

        if ($this->borrower !== null) {
            throw BorrowException::createRequestOnUnavailable();
        }

        $this->borrower = $borrower;
    }

    /**
     * Root aggregate business logic for returning a book.
     */
    public function return(): void
    {
        if ($this->borrower === null) {
            throw ReturnException::create();
        }

        $this->borrower = null;
    }
}
