<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use PHPUnit\Framework\TestCase;

class AuthorIdTest extends TestCase
{
    public function testGenerateReturnsValidAuthorId(): void
    {
        $authorId = AuthorId::generate();
        $this->assertInstanceOf(AuthorId::class, $authorId);
        $this->assertNotEmpty($authorId->getValue());
    }

    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new AuthorId('invalid-uuid');
    }
}
