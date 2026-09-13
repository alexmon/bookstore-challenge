<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Domain\Bus\CommandBus;

interface CommandBus
{
    public function handle(Command $command): void;
}
