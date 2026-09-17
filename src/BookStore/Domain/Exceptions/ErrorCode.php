<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

enum ErrorCode: int
{
    // Invalid Author operations
    case AUTHOR_NOT_FOUND = 1001;

    case AUTHOR_INVALID_ID_PROVIDED = 1002;

    // Invalid Book Operations
    case BOOK_NOT_FOUND = 2001;

    case BOOK_INVALID_ID_PROVIDED = 2002;

    case BOOK_BORROW_REQUEST_ON_INACTIVE = 2003;

    case BOOK_BORROW_REQUEST_ON_UNAVAILABLE = 2004;

    case BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK = 2005;

    case BOOK_ALREADY_EXISTS = 2006;

    // Invalid Borrower Operations
    case BORROWER_NOT_FOUND = 3001;

    case BORROWER_INVALID_ID_PROVIDED = 3002;

    case BORROWER_ALREADY_EXISTS = 3003;

}
