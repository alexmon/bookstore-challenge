<?php

declare(strict_types=1);

namespace Tests\Integration\BookStore\Application\Commands\CreateBorrower;

use App\Models\Borrower;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommandHandler;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class FindOrCreateBorrowerCommandHandlerTest extends TestCase
{
    use RefreshDatabase;

    private FindOrCreateBorrowerCommandHandler $commandHandler;

    protected function setUp(): void
    {
        parent::setUp();

       $this->commandHandler = $this->app->make(FindOrCreateBorrowerCommandHandler::class);
    }

    #[TestDox("Create a borrower happy path")]
    public function testCreateBorrowerHappyPath(): void
    {
        $borrowerName = 'John Doe';
        $command = new FindOrCreateBorrowerCommand($borrowerName);

        $this->commandHandler->handle($command);

        $this->assertNotNull($command->getResult());
        /**@var BorrowerEntity $borrower */
        $borrower = $command->getResult();
        $this->assertSame($borrowerName, $borrower->getName());
        $this->assertDatabaseHas('borrowers', ['uuid' => $borrower->getId()->getValue()]);
    }

    #[TestDox("Find an existing borrower happy path")]
    public function testFindExistingBorrowerHappyPath(): void
    {
        $faker = Factory::create();
        $borrowerName = 'John Doe';
        $uuid = $faker->uuid;
        $command = new FindOrCreateBorrowerCommand($borrowerName);
        Borrower::create(['uuid' => $uuid, 'name' => $borrowerName]);

        $this->commandHandler->handle($command);

        $this->assertNotNull($command->getResult());
        /**@var BorrowerEntity $borrower */
        $borrower = $command->getResult();
        $this->assertSame($borrowerName, $borrower->getName());
        $this->assertSame($uuid, $borrower->getId()->getValue());
    }
}
