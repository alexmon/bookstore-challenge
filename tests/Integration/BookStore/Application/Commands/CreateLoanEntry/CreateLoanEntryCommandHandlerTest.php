<?php

declare(strict_types=1);

namespace Tests\Integration\BookStore\Application\Commands\CreateLoanEntry;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrower;
use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommandHandler;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use BookStoreAPI\BookStore\Domain\Models\Loan;
use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use BookStoreAPI\BookStore\Domain\Services\BookEntityBuilder;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class CreateLoanEntryCommandHandlerTest extends TestCase
{
    use RefreshDatabase;

    private CreateLoanEntryCommandHandler $commandHandler;

    protected function setUp(): void
    {
        parent::setUp();

       $this->commandHandler = $this->app->make(CreateLoanEntryCommandHandler::class);
    }

    #[TestDox("Create a loan entry happy path")]
    public function testCreateLoanEntryHappyPath(): void
    {
        $faker = Factory::create();
        $author = Author::create(['uuid' => $faker->uuid, 'name' => $faker->name]);
        $authorEntity = new AuthorEntity(AuthorId::from($author->uuid), $author->name);
        $borrower = Borrower::create(['uuid' => $faker->uuid, 'name' => $faker->name]);
        $borrowerEntity = new BorrowerEntity(BorrowerId::from($borrower->uuid), $borrower->name);
        $book = Book::create([
            'uuid' => $faker->uuid,
            'title' => $faker->sentence,
            'isbn' => $faker->isbn13,
            'is_active' => true,
            'author_id' => $author->id,
            'borrower_id' => $borrower->id,
        ]);

        // TODO parametric fixture
        $bookEntityBuilder = new BookEntityBuilder(
            BookId::from($book->uuid),
            $book->title,
            Isbn::from($book->isbn),
            true,
        );
        $bookEntityBuilder
            ->setAuthor($authorEntity)
            ->setBorrower($borrowerEntity);
        $bookEntity = $bookEntityBuilder->build();

        $command = new CreateLoanEntryCommand(
            $bookEntity,
            $borrowerEntity,
            LoanAction::BORROW,
        );

        $this->commandHandler->handle($command);

        $this->assertDatabaseHas('loans', [
            'book_id' => $book->id,
            'borrower_id' => $borrower->id,
            'action' => LoanAction::BORROW->value,
        ]);
    }
}
