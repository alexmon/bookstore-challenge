<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Http\ViewModels;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;

class CreateAuthorViewModel
{
    public function __construct(
        private readonly AuthorEntity $author,
    ) {
    }

    /**
     * @return array{name: string, uuid: string}
     */
    public function render(): array
    {
        return [
            'uuid' => $this->author->getId()->getValue(),
            'name' => $this->author->getName(),
        ];
    }
}
