<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

enum LoanAction: int
{
    case BORROW = 1;
    case RETURN = 2;

    /** @return int[] */
    public static function mapCasesToValues(): array
    {
        return \array_map(fn(LoanAction $action) => $action->value, self::cases());
    }

    public function isBorrow(): bool
    {
        return $this === self::BORROW;
    }

    public function isReturn(): bool
    {
        return $this === self::RETURN;
    }
}
