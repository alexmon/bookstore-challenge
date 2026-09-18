<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BookId;
use BookStoreAPI\BookStore\Domain\Services\BookEntityBuilder;
use BookStoreAPI\SharedKernel\Domain\Models\Isbn;
use Faker\Factory;
use Tests\Fixtures\AuthorEntityFixture;

class BookEntityFixture
{
    public static function create(): BookEntity {
        $faker = Factory::create();

        $bookEntityBuilder = new BookEntityBuilder(
            BookId::generate(),
            $faker->words(3, true),
            Isbn::from($faker->isbn13()),
            true
        );
        $bookEntityBuilder
            ->setAuthor(AuthorEntityFixture::create())
            ->setCreatedAtNow()
            ->setUpdatedAtNow();

        return $bookEntityBuilder->build();
    }
}
