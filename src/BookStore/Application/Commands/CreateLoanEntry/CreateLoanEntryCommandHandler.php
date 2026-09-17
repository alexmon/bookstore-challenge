<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry;

use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use BookStoreAPI\BookStore\Domain\Models\LoanEntity;
use BookStoreAPI\BookStore\Domain\Models\LoanRepository;

class CreateLoanEntryCommandHandler
{
    public function __construct(
        private readonly ValidationService $validationService,
        private readonly LoanRepository $loanRepository,
    ) {}

    public function handle(CreateLoanEntryCommand $command): void
    {
        $this->validationService->validate($command);

        $loan = new LoanEntity(
            $command->book->getId(),
            $command->borrower->getId(),
            $command->action,
        );

        $this->loanRepository->save($loan);
    }
}
