<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Repositories;

use App\Models\Book;
use App\Models\Borrower;
use App\Models\Loan;
use BookStoreAPI\BookStore\Domain\Exceptions\BookNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BorrowerNotFoundException;
use BookStoreAPI\BookStore\Domain\Models\LoanEntity;
use BookStoreAPI\BookStore\Domain\Models\LoanRepository;

class EloquentAdapterLoanRepository implements LoanRepository
{
    public function save(LoanEntity $loan): void
    {
        $eloquentBorrower = null;
        $eloquentBook = Book::with(['author', 'borrower'])->where('uuid', $loan->getBookId()->getValue())->first();

        if (null === $eloquentBook) {
            throw BookNotFoundException::create();
        }

        if (null === $eloquentBook->borrower && $loan->getAction()->isBorrow()) {
            throw BorrowerNotFoundException::create();
        }

        $eloquentBorrower = Borrower::where('uuid', $loan->getBorrowerId()->getValue())->first();

        $eloquentLoan = new Loan();
        $eloquentLoan->book_id = $eloquentBook->id;
        $eloquentLoan->borrower_id = $eloquentBorrower->id;
        $eloquentLoan->action = $loan->getAction()->value;
        $eloquentLoan->date_captured = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $eloquentLoan->save();
    }
}
