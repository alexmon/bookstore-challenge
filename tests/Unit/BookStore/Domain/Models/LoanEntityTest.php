<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use BookStoreAPI\BookStore\Domain\Models\LoanEntity;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\BookEntityFixture;
use Tests\Fixtures\BorrowerEntityFixture;

class LoanEntityTest extends TestCase
{
    public function testLoanEntityCreation(): void
    {
        $borrower = BorrowerEntityFixture::create();
        $book = BookEntityFixture::create();
        $loanAction = LoanAction::BORROW;

        $loanEntity = new LoanEntity(
            $book->getId(),
            $borrower->getId(),
            $loanAction
        );

        $this->assertSame($borrower->getId(), $loanEntity->getBorrowerId());
        $this->assertSame($book->getId(), $loanEntity->getBookId());
        $this->assertSame($loanAction, $loanEntity->getAction());
        $this->assertInstanceOf(\DateTimeImmutable::class, $loanEntity->getCapturedAt());
    }
}
