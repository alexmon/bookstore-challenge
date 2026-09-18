<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateBorrower;

use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommandHandler;
use BookStoreAPI\BookStore\Domain\Models\BorrowerRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Fixtures\BorrowerEntityFixture;
use Tests\TestCase;

class FindOrCreateBorrowerCommandHandlerTest extends TestCase
{
    private FindOrCreateBorrowerCommandHandler $commandHandler;
    private BorrowerRepository&MockObject $borrowerRepository;
    private ValidationService&MockObject $validationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->borrowerRepository = $this->createMock(BorrowerRepository::class);
        $this->validationService = $this->createMock(ValidationService::class);

        $this->commandHandler = new FindOrCreateBorrowerCommandHandler(
            $this->borrowerRepository,
            $this->validationService
        );
    }

     #[TestDox('ValidationException is not shallowed')]
    public function testValidationExceptionIsNotShallowed(): void
    {
        $command = $this->createMock(FindOrCreateBorrowerCommand::class);

        $this->validationService->expects($this->once())->method('validate')->willThrowException(new ValidationException());
        $this->expectException(ValidationException::class);

        $this->commandHandler->handle($command);
    }

    #[TestDox('Borrower exists and is returned and save is not invoked')]
    public function testBorrowerExistsAndIsReturned(): void
    {
        $borrower = BorrowerEntityFixture::create();
        $command = new FindOrCreateBorrowerCommand(
            $borrower->getName(),
        );

        $this->borrowerRepository->expects($this->once())->method('findByName')->willReturn($borrower);
        $this->borrowerRepository->expects($this->never())->method('save');

        $this->commandHandler->handle($command);

        $this->assertSame($borrower, $command->getResult());
    }
}
