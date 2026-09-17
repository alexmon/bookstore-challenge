<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\SearchBooks;

use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use Psr\SimpleCache\CacheInterface;

class SearchBooksQueryHandler
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ValidationService $validationService,
        private readonly CacheInterface $cache,
    ) {
    }

    public function handle(SearchBooksQuery $query): array
    {
        $this->validationService->validate($query);

        // Redis long keys performance issue (consider hashing the key)
        $cacheKey = \md5(\sprintf(
            'search_books_%s_%s_%s_%s_%s',
            $query->search ?? '',
            $query->page,
            $query->perPage,
            $query->authorUuid ?? '',
            $query->available ?? ''
        ));

        if ($this->cache->has($cacheKey)) {
            return \json_decode($this->cache->get($cacheKey), true);
        }

        $booksResults = $this->bookRepository->search(
            search: $query->search,
            page: $query->page,
            perPage: $query->perPage,
            authorUuid: $query->authorUuid,
            available: $query->available,
        );

        // TODO make cache duration configurable
        $this->cache->set($cacheKey, \json_encode($booksResults), 300); // Cache for 5 mins

        return $booksResults;
    }
}
