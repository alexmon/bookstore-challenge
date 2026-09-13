<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Bus\QueryBus;

use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\Query;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use League\Tactician\CommandBus as LeagueTacticianQueryBus;

readonly class TacticianQueryBus implements QueryBus
{
    public function __construct(
        private LeagueTacticianQueryBus $bus
    ) {
    }

    public function handle(Query $query): mixed
    {
        return $this->bus->handle($query);
    }
}
