<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Domain\Cache;

// ISP principle client should depend on this interface for caching search results
interface BookSearchResultsCacheService
{
    public function get(string $key): array;

    public function set(string $key, array $results): void;

    public function has(string $key): bool;
}
