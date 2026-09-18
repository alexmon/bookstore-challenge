<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use PHPUnit\Framework\TestCase;

class BorrowerEntityTest extends TestCase
{
    public function testBorrowerEntityCreation(): void
    {
        $borrowerId = BorrowerId::from('123e4567-e89b-12d3-a456-426614174000');
        $name = 'John Doe';

        $borrowerEntity = new BorrowerEntity(
            $borrowerId,
            $name
        );

        $this->assertSame($borrowerId, $borrowerEntity->getId());
        $this->assertSame($name, $borrowerEntity->getName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $borrowerEntity->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $borrowerEntity->getUpdatedAt());
    }
}
