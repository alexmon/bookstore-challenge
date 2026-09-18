<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateBorrower;

use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class FindOrCreateBorrowerCommandTest extends TestCase
{
    private ValidationService $validationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->validationService = $this->app->make(ValidationService::class);
    }

    #[TestDox('Validation service catches invalid FindOrCreateBorrowerCommand input')]
    #[DataProvider('invalidInputProvider')]
    public function testValidationServiceCatchesInvalidInput(?string $name): void
    {
        $command = new FindOrCreateBorrowerCommand(
            $name,
        );
        $this->expectException(\InvalidArgumentException::class);

        $this->validationService->validate($command);
    }

    public static function invalidInputProvider(): array
    {
        return [
                'missing_name' => [
                    'name' => null,
                ],
                'empty_name' => [
                    'name' => '',
                ],
                'name_too_long' => [
                    'name' => str_repeat('a', 256),
                ],
        ];
    }
}
