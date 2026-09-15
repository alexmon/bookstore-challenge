<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Http\ViewModels;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\SharedKernel\Domain\ViewModels\ViewModel;

class CreateBookViewModel implements ViewModel
{
    public function __construct(
        private readonly BookEntity $book,
        private readonly AuthorEntity $author,
    ) {}

    /**
     * @return array{author: array{name: string, uuid: string, "is_active": bool, isbn: string, title: string, uuid: string}}
     */
    public function render(): array
    {
        return [
            'uuid' => $this->book->getId()->getValue(),
            'title' => $this->book->getTitle(),
            'isbn' => $this->book->getIsbn(),
            'is_active' => $this->book->isActive(),
            'author' => [
                'uuid' => $this->author->getId()->getValue(),
                'name' => $this->author->getName(),
            ],
        ];
    }
}

