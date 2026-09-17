<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;
use Symfony\Component\Validator\Constraints as Assert;

class CreateLoanEntryCommand extends Command
{
    public function __construct(
        #[Assert\NotNull]
        public readonly ?BookEntity $book,
        #[Assert\NotNull]
        public readonly ?BorrowerEntity $borrower,
        public readonly LoanAction $action,
    ) {
    }
}
