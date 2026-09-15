<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
/**
 * Book root aggregate
 */
class BookEntity
{
    public function __construct(
        private BookId $id,
        private string $title,
        private Isbn $isbn,
        private ?AuthorId $author = null,
        private bool $isActive = true,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
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
    public function getAuthor(): ?AuthorId
    {
        return $this->author;
    }

    public function setAuthor(AuthorId $author): void
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
}
