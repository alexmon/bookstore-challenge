<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Bus\CommandBus;

use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\Command;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use League\Tactician\CommandBus as LeagueTacticianCommandBus;

readonly class TacticianCommandBus implements CommandBus
{
    public function __construct(
        private LeagueTacticianCommandBus $bus
    ) {
    }

    public function handle(Command $command): void
    {
        $this->bus->handle($command);
    }
}
