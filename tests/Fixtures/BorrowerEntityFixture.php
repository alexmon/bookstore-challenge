<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerId;
use Faker\Factory;

class BorrowerEntityFixture
{
    public static function create(): BorrowerEntity {
        $faker = Factory::create();

        $borrower = new BorrowerEntity(
            BorrowerId::generate(),
            $faker->name(),
        );

        return $borrower;
    }
}
