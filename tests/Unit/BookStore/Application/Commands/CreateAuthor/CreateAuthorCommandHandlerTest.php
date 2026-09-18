<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateAuthor;

use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommandHandler;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class CreateAuthorCommandHandlerTest extends TestCase
{
    private CreateAuthorCommandHandler $commandHandler;
    private AuthorRepository&MockObject $authorRepository;
    private ValidationService&MockObject $validationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorRepository = $this->createMock(AuthorRepository::class);
        $this->validationService = $this->createMock(ValidationService::class);

        $this->commandHandler = new CreateAuthorCommandHandler(
            $this->authorRepository,
            $this->validationService
        );
    }

    #[TestDox('ValidationException is not shallowed')]
    public function testValidationExceptionIsNotShallowed(): void
    {
        $command = $this->createMock(CreateAuthorCommand::class);

        $this->validationService->expects($this->once())->method('validate')->willThrowException(new ValidationException());
        $this->expectException(ValidationException::class);

        $this->commandHandler->handle($command);
    }
}
