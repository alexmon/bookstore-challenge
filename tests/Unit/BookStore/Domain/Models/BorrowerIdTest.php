<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BorrowerIdTest extends TestCase
{
    public function testGenerateReturnsValidBorrowerId(): void
    {
        $borrowerId = BorrowerId::generate();
        $this->assertInstanceOf(BorrowerId::class, $borrowerId);
        $this->assertNotEmpty($borrowerId->getValue());
    }

    #[TestDox("Constructor throws exception for invalid UUID")]
    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BorrowerId::from('invalid-uuid');
    }

    #[TestDox('assert static from method returns valid BorrowerId')]
    public function testStaticFromReturnsValidBorrowerId(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $borrowerId = BorrowerId::from($uuid);
        $this->assertInstanceOf(BorrowerId::class, $borrowerId);
        $this->assertEquals($uuid, $borrowerId->getValue());
    }
}
