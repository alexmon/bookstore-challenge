<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Http\ViewModels;

use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\SharedKernel\Domain\ViewModels\ViewModel;

class ListAuthorsViewModel implements ViewModel
{
    public function __construct(
        /** @var array<int, AuthorEntity> */
        private readonly array $authors
    ) {
    }

    /**
     * @return array<int, {
     *     'uuid': string,
     *     'name': string,
     *     'created_at': string,
     *     'updated_at': string,
     * }>
     */
    public function render(): array
    {
        return \array_map(
            static fn(AuthorEntity $author): array => [
                'uuid' => $author->getId()->getValue(),
                'name' => $author->getName(),
                'created_at' => $author->getCreatedAt()->format(\DateTime::ATOM),
                'updated_at' => $author->getUpdatedAt()->format(\DateTime::ATOM),
            ],
            $this->authors
        );
    }
}
