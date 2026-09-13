<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\AuthorId;

/**
 * Author root aggregate
 */
class AuthorEntity
{
    public function __construct(
        private AuthorId $id,
        private string $name,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt = $this->createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $this->updatedAt ?? new \DateTimeImmutable();
    }

    public function getId(): AuthorId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
