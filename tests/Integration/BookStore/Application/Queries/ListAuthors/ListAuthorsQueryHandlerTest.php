<?php

declare(strict_types=1);

namespace Tests\Integration\BookStore\Queries\ListAuthors;

use App\Models\Author;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListAuthorsQueryHandlerTest extends TestCase
{
    use RefreshDatabase;

    private ListAuthorsQueryHandler $queryHandler;

    public function testListAuthorsQueryHandler(): void
    {
        $this->queryHandler = $this->app->make(ListAuthorsQueryHandler::class);

        Author::factory()->count(5)->create();

        $query = new ListAuthorsQuery();
        $authors = $this->queryHandler->handle($query);

        $this->assertCount(5, $authors);
    }
}
