<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Http\ViewModels;

use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\SharedKernel\Domain\ViewModels\ViewModel;

class FetchBookViewModel implements ViewModel
{
    public function __construct(
        private readonly BookEntity $book,
    ) {}

    /**
     * @return array{uuid: string, title: string, isbn: string, is_active: bool, author: array{name: string, uuid: string}|null, borrower: array{name: string, uuid: string}|null}
     */
    public function render(): array
    {
        return [
            'uuid' => $this->book->getId()->getValue(),
            'title' => $this->book->getTitle(),
            'isbn' => $this->book->getIsbn()->getValue(),
            'is_active' => $this->book->isActive(),
            'author' => $this->book->getAuthor() ? [
                'uuid' => $this->book->getAuthor()->getId()->getValue(),
                'name' => $this->book->getAuthor()->getName(),
            ] : null,
            'is_available' => $this->book->isAvailable(),
            'borrower' => $this->book->getBorrower() ? [
                'uuid' => $this->book->getBorrower()->getId()->getValue(),
                'name' => $this->book->getBorrower()->getName(),
            ] : null,
        ];
    }
}
