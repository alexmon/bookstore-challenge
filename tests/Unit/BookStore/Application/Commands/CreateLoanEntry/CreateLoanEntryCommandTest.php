<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateLoanEntry;

use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommand;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Fixtures\BookEntityFixture;
use Tests\Fixtures\BorrowerEntityFixture;
use Tests\TestCase;

class CreateLoanEntryCommandTest extends TestCase
{
    private ValidationService $validationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->validationService = $this->app->make(ValidationService::class);
    }

    #[TestDox('Validation service catches invalid CreateLoanEntryCommand input')]
    #[DataProvider('invalidInputProvider')]
    public function testValidationServiceCatchesInvalidInput(?BookEntity $book, ?BorrowerEntity $borrower, LoanAction $action): void
    {
        $command = new CreateLoanEntryCommand(
            $book,
            $borrower,
            $action,
        );
        $this->expectException(\InvalidArgumentException::class);

        $this->validationService->validate($command);
    }

    public static function invalidInputProvider(): array
    {
        return [
            'null book' => [null, BorrowerEntityFixture::create(), LoanAction::BORROW],
            'null borrower' => [BookEntityFixture::create(), null, LoanAction::RETURN],
        ];
    }
}
