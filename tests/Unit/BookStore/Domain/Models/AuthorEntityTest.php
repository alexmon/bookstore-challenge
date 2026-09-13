<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use PHPUnit\Framework\TestCase;

class AuthorEntityTest extends TestCase
{
    public function testAuthorEntityCreation(): void
    {
        $authorId = new AuthorId('123e4567-e89b-12d3-a456-426614174000');
        $name = 'John Doe';

        $authorEntity = new AuthorEntity(
            $authorId,
            $name
        );

        $this->assertSame($authorId, $authorEntity->getId());
        $this->assertSame($name, $authorEntity->getName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $authorEntity->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $authorEntity->getUpdatedAt());
    }
}
