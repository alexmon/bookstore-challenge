<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Domain\Database;

interface DatabaseTransaction
{
    public function beginTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;
}
