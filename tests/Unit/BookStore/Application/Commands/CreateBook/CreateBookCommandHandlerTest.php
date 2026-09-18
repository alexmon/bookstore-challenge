<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Commands\CreateBook;

use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommandHandler;
use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use Faker\Factory;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Tests\Fixtures\AuthorEntityFixture;
use Tests\Fixtures\BookEntityFixture;
use Tests\TestCase;

class CreateBookCommandHandlerTest extends TestCase
{
    private CreateBookCommandHandler $commandHandler;
    private BookRepository&MockObject $bookRepository;
    private AuthorRepository&MockObject $authorRepository;
    private ValidationService&MockObject $validationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->authorRepository = $this->createMock(AuthorRepository::class);
        $this->validationService = $this->createMock(ValidationService::class);

        $this->commandHandler = new CreateBookCommandHandler(
            $this->bookRepository,
            $this->authorRepository,
            $this->validationService
        );
    }

    #[TestDox('ValidationException is not shallowed')]
    public function testValidationExceptionIsNotShallowed(): void
    {
        $command = $this->createMock(CreateBookCommand::class);

        $this->validationService->expects($this->once())->method('validate')->willThrowException(new ValidationException());
        $this->expectException(ValidationException::class);

        $this->commandHandler->handle($command);
    }

    #[TestDox('if $author is  null throw AuthorNotFoundException')]
    public function testAuthorNotFoundExceptionIsThrown(): void
    {
        $this->authorRepository->method('findById')->willReturn(null);

        $this->expectException(AuthorNotFoundException::class);

        $command = new CreateBookCommand(
            Factory::create()->title(),
            Factory::create()->isbn13(),
            Uuid::uuid4()->toString(),
        );

        $this->commandHandler->handle($command);
    }

    #[TestDox('mocked methods save, findbyId are called and the $command setResult method is invoked with correct parameters')]
    public function testSaveAndFindByIdMethodsAreCalledAndSetResultIsInvoked(): void
    {
        $author = AuthorEntityFixture::create();
        $book = BookEntityFixture::create();
        $faker = Factory::create();
        $command = new CreateBookCommand(
            $faker->words(3, true),
            $faker->isbn13(),
            Uuid::uuid4()->toString(),
        );

        $this->authorRepository->method('findById')->willReturn($author);
        $this->bookRepository->expects($this->once())->method('save');
        $this->bookRepository->method('findById')->willReturn($book);

        $this->commandHandler->handle($command);

        $result = $command->getResult();
        $this->assertSame($book, $result['book']);
        $this->assertSame($author, $result['author']);
    }
}
