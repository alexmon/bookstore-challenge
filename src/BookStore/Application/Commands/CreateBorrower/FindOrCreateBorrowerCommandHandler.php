<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Commands\CreateBorrower;

use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

readonly class FindOrCreateBorrowerCommandHandler
{
    public function __construct(
        private BorrowerRepository $borrowerRepository,
        private ValidationService $validationService,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function handle(FindOrCreateBorrowerCommand $command)
    {
        $this->validationService->validate($command);

        $existingBorrower = $this->borrowerRepository->findByName($command->name);

        if (null !== $existingBorrower) {
            $command->setResult($existingBorrower);

            return;
        }

        $borrower = new BorrowerEntity(
            BorrowerId::generate(),
            name: $command->name,
        );

        $this->borrowerRepository->save($borrower);

        $command->setResult($borrower);
    }
}

