<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AuthorIdTest extends TestCase
{
    public function testGenerateReturnsValidAuthorId(): void
    {
        $authorId = AuthorId::generate();
        $this->assertInstanceOf(AuthorId::class, $authorId);
        $this->assertNotEmpty($authorId->getValue());
    }

    #[TestDox("Constructor throws exception for invalid UUID")]
    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        AuthorId::from('invalid-uuid');
    }

    #[TestDox('assert static from')]
    public function testStaticFromReturnsValidAuthorId(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $authorId = AuthorId::from($uuid);
        $this->assertInstanceOf(AuthorId::class, $authorId);
        $this->assertSame($uuid, $authorId->getValue());
    }
}
