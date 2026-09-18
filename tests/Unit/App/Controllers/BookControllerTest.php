<?php

declare(strict_types=1);

namespace Tests\Unit\App\Http\Controllers;

use App\Http\Controllers\BookController;
use BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook\FetchAndLockBookCommand;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Domain\Database\DatabaseTransaction;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    private CommandBus&MockObject $commandBus;
    private QueryBus&MockObject $queryBus;
    private DatabaseTransaction&MockObject $databaseTransaction;
    private BookController $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->createMock(CommandBus::class);
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->databaseTransaction = $this->createMock(DatabaseTransaction::class);
        $this->controller = new BookController(
            $this->commandBus,
            $this->queryBus,
            $this->databaseTransaction
        );
    }

    #[TestDox('on borrow if exception is thrown assert db transaction is rolledback')]
    public function testDatabaseTransactionIsRolledback(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $name = 'test';

        // cannot mock because method is builtin on Request :/
        $request = Request::create('/books/' . $uuid . '/borrow', 'POST', [
            'borrower' => $name,
        ]);

        $fetchAndLockBookCommand = new FetchAndLockBookCommand($uuid);
        $this->commandBus
           ->method('handle')
           ->with($fetchAndLockBookCommand)
           ->willThrowException(new ValidationException());
        $this->databaseTransaction
           ->expects($this->once())
           ->method('rollBackTransaction');
        $this->expectException(ValidationException::class);

        $this->controller->borrow($uuid, $request);
    }

    #[TestDox('on borrow if fetched book is null expect NotFoundHttpException')]
    public function testBorrowThrowsNotFoundHttpException(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/books/' . $uuid . '/borrow', 'POST', [
            'borrower' => 'test',
        ]);
        $fetchAndLockBookCommand = new FetchAndLockBookCommand($uuid);
        $fetchAndLockBookCommand->setResult(null);

        $this->commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (FetchAndLockBookCommand $command) use ($uuid) {
                $command->setResult(null);
                return true;
            }));
        $this->expectException(NotFoundHttpException::class);

        $this->controller->borrow($uuid, $request);
    }


    #[TestDox('on return if exception is thrown assert db transaction is rolledback')]
    public function testDatabaseTransactionIsRolledbackOnReturn(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        // cannot mock because method is builtin on Request :/
        $request = Request::create('/books/' . $uuid . '/return', 'POST', []);

        $fetchAndLockBookCommand = new FetchAndLockBookCommand($uuid);
        $this->commandBus
           ->method('handle')
           ->with($fetchAndLockBookCommand)
           ->willThrowException(new ValidationException());
        $this->databaseTransaction
           ->expects($this->once())
           ->method('rollBackTransaction');
        $this->expectException(ValidationException::class);

        $this->controller->returnBook($uuid, $request);
    }
}
