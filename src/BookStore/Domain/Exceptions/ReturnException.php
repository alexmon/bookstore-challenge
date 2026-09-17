<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Exceptions;

class ReturnException extends \LogicException
{
   public static function create(): self
   {
       return new self('', ErrorCode::BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK->value);
   }
}

