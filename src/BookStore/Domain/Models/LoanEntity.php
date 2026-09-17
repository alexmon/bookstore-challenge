<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Models;

readonly class LoanEntity
{
    private ?\DateTimeImmutable $capturedAt;

    public function __construct(
        private BookId $bookId,
        private BorrowerId $borrowerId,
        private LoanAction $action,
        ?\DateTimeImmutable $capturedAt = null,
    ) {
        $this->capturedAt = $capturedAt ?? new \DateTimeImmutable();
    }

    public function getBookId(): BookId
    {
        return $this->bookId;
    }

    public function getBorrowerId(): BorrowerId
    {
        return $this->borrowerId;
    }

    public function getAction(): LoanAction
    {
        return $this->action;
    }

    public function getCapturedAt(): \DateTimeImmutable
    {
        return $this->capturedAt;
    }
}
