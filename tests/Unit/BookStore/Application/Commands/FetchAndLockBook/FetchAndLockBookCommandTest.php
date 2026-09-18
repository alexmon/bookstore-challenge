<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\FetchAndLockBook;

use BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook\FetchAndLockBookCommand;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class FetchAndLockBookCommandTest extends TestCase
{
    private ValidationService $validationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->validationService = $this->app->make(ValidationService::class);
    }

    #[TestDox('Validation service catches invalid FetchAndLockBookCommand input')]
    #[DataProvider('invalidInputProvider')]
    public function testValidationServiceCatchesInvalidInput(?string $uuid): void
    {
        $command = new FetchAndLockBookCommand(
            $uuid,
        );
        $this->expectException(\InvalidArgumentException::class);

        $this->validationService->validate($command);
    }

    public static function invalidInputProvider(): array
    {
        return [
                'empty_uuid' => [
                    'uuid' => '',
                ],
                'uuid_too_long' => [
                    'uuid' => str_repeat('a', 37),
                ],
                'uuid too short' => [
                    'uuid' => str_repeat('a', 35),
                ],
        ];
    }
}
