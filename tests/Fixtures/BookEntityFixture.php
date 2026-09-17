<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use Faker\Factory;
use Tests\Fixtures\AuthorEntityFixture;

class BookEntityFixture
{
    public static function create(): BookEntity {
        $faker = Factory::create();

        $book = new BookEntity(
            BookId::generate(),
            $faker->words(3, true),
            new Isbn($faker->isbn13()),
            AuthorEntityFixture::create(),
        );

        return $book;
    }
}
