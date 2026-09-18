<?php

declare(strict_types=1);

use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use PHPUnit\Framework\TestCase;

class BorrowerIdTest extends TestCase
{
    public function testGenerateReturnsValidBorrowerId(): void
    {
        $borrowerId = BorrowerId::generate();
        $this->assertInstanceOf(BorrowerId::class, $borrowerId);
        $this->assertNotEmpty($borrowerId->getValue());
    }

    public function testConstructorThrowsExceptionForInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BorrowerId('invalid-uuid');
    }
}
