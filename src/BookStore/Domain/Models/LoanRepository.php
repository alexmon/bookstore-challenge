<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

use BookStoreAPI\BookStore\Domain\Models\LoanEntity;

interface LoanRepository
{
    public function save(LoanEntity $loan): void;
}
