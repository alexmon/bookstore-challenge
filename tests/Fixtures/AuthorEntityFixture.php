<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use Faker\Factory;

class AuthorEntityFixture
{
    public static function create(): AuthorEntity {
        $faker = Factory::create();

        $author = new AuthorEntity(
            AuthorId::generate(),
            $faker->name(),
        );

        return $author;
    }
}
