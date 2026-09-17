<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Repositories;

use App\Models\Author;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\AuthorId;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;

class EloquentAdapterAuthorRepository implements AuthorRepository
{
    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        $authors = Author::all();
        $authorEntities = [];
        foreach ($authors as $author) {
            $authorEntities[] = new AuthorEntity(
                new AuthorId($author->uuid),
                $author->name,
                \DateTimeImmutable::createFromInterface($author->created_at),
                \DateTimeImmutable::createFromInterface($author->updated_at),
            );
        }
        return $authorEntities;
    }

    public function findById(AuthorId $id): ?AuthorEntity
    {
        $author = Author::where('uuid', $id->getValue())->first();
        if ($author === null) {
            return null;
        }
        return new AuthorEntity(
            new AuthorId($author->uuid),
            $author->name,
            \DateTimeImmutable::createFromInterface($author->created_at),
            \DateTimeImmutable::createFromInterface($author->updated_at),
        );
    }

    public function save(AuthorEntity $author): void
    {
        $eloquentAuthor = Author::where('uuid', $author->getId()->getValue())->first() ?? new Author();
        $eloquentAuthor->uuid = $author->getId()->getValue();
        $eloquentAuthor->name = $author->getName();
        $eloquentAuthor->created_at = $author->getCreatedAt();
        $eloquentAuthor->updated_at = $author->getUpdatedAt();
        $eloquentAuthor->save();
    }
}
