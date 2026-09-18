<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateBook;

use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class CreateBookCommandTest extends TestCase
{
    private ValidationService $validationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->validationService = $this->app->make(ValidationService::class);
    }

    #[TestDox('Validation service catches invalid CreateBookCommand input')]
    #[DataProvider('invalidInputProvider')]
    public function testValidationServiceCatchesInvalidInput(
        ?string $title,
        ?string $author,
        ?string $isbn,
    ): void
    {
        $command = new CreateBookCommand(
            $title,
            $author,
            $isbn,
        );
        $this->expectException(\InvalidArgumentException::class);

        $this->validationService->validate($command);
    }

    public static function invalidInputProvider(): array
    {
        return [
                'missing_title' => [
                    'title' => null,
                    'author' => 'Some Author',
                    'isbn' => '1234567890',
                ],
                'empty_title' => [
                    'title' => '',
                    'author' => 'Some Author',
                    'isbn' => '1234567890',
                ],
                'invalid_isbn' => [
                    'title' => 'Some Title',
                    'author' => 'Some Author',
                    'isbn' => 'invalid_isbn',
                ],
                'missing_author' => [
                    'title' => 'Some Title',
                    'author' => null,
                    'isbn' => '1234567890',
                ],
                'empty_author' => [
                    'title' => 'Some Title',
                    'author' => '',
                    'isbn' => '1234567890',
                ],
                'author id is wrong' => [
                    'title' => 'Some Title',
                    'author' => 'invalid_author_id',
                    'isbn' => '1234567890',
                ],
        ];
    }
}
