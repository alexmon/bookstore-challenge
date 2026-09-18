<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class LoanActionTest extends TestCase
{
    #[TestDox('test isBorrow')]
    public function testIsBorrow(): void
    {
        $loanAction = LoanAction::BORROW;
        $this->assertTrue($loanAction->isBorrow());
    }

    #[TestDox('test isReturn')]
    public function testIsReturn(): void
    {
        $loanAction = LoanAction::RETURN;
        $this->assertTrue($loanAction->isReturn());
    }
}
