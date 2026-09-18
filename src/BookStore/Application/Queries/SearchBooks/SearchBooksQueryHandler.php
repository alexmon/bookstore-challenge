<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Application\Queries\SearchBooks;

use BookStoreAPI\BookStore\Domain\Cache\BookSearchResultsCacheService;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;

class SearchBooksQueryHandler
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ValidationService $validationService,
        private readonly BookSearchResultsCacheService $bookSearchResultsCacheService,
    ) {
    }

    public function handle(SearchBooksQuery $query): array
    {
        $this->validationService->validate($query);

        $cacheKey = $this->generateCacheKey($query);

        if ($this->bookSearchResultsCacheService->has($cacheKey)) {
            return $this->bookSearchResultsCacheService->get($cacheKey);
        }

        $booksResults = $this->bookRepository->search(
            search: $query->search,
            page: $query->page,
            perPage: $query->perPage,
            authorUuid: $query->authorUuid,
            available: $query->available,
        );

        $this->bookSearchResultsCacheService->set($cacheKey, $booksResults);

        return $booksResults;
    }

    private function generateCacheKey(SearchBooksQuery $query): string
    {
        // Redis long keys performance issue (consider hashing the key)
        return \md5(\sprintf(
            'search_books_%s_%s_%s_%s_%s',
            $query->search ?? '',
            $query->page,
            $query->perPage,
            $query->authorUuid ?? '',
            $query->available ?? ''
        ));
    }
}
