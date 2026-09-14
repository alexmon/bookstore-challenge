<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Domain\Bus\CommandBus;

abstract class Command
{
    private mixed $result;

    public function getResult()
    {
        return $this->result;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }
}
