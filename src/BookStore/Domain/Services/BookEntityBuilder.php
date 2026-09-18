<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Services;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;

class BookEntityBuilder
{
    private BookEntity $book;

    public function __construct(
        BookId $id,
        string $title,
        Isbn $isbn,
        bool $isActive
    )
    {
        $this->book = new BookEntity(
            $id,
            $title,
            $isbn,
            $isActive
        );
    }

    public function setAuthor(?AuthorEntity $author): self
    {
        $this->book->setAuthor($author);
        return $this;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->book->setCreatedAt($createdAt);
        return $this;
    }

    public function setCreatedAtNow(): self
    {
        $this->book->setCreatedAt(new \DateTimeImmutable());
        return $this;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->book->setUpdatedAt($updatedAt);
        return $this;
    }

    public function setUpdatedAtNow(): self
    {
        $this->book->setUpdatedAt(new \DateTimeImmutable());
        return $this;
    }

    public function setBorrower(?BorrowerEntity $borrower): self
    {
        $this->book->setBorrower($borrower);
        return $this;
    }

    public function build(): BookEntity
    {
        return $this->book;
    }
}
