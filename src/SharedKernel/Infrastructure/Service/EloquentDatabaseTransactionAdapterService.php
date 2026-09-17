<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Service;

use BookStoreAPI\SharedKernel\Domain\Database\DatabaseTransaction;
use Illuminate\Support\Facades\DB;

class EloquentDatabaseTransactionAdapterService implements DatabaseTransaction
{
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commitTransaction(): void
    {
        DB::commit();
    }

    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }
}


