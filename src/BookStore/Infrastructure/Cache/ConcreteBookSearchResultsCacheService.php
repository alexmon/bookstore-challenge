<?php

declare(strict_types=1);

namespace BookStoreAPI\BookStore\Infrastructure\Cache;

use BookStoreAPI\BookStore\Domain\Cache\BookSearchResultsCacheService;
use Psr\SimpleCache\CacheInterface;

class ConcreteBookSearchResultsCacheService implements BookSearchResultsCacheService
{
    const SEARCH_BOOKS_CACHE_TTL = 300;
    const CACHE_KEY_PREFIX = 'search_books:';

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public function get(string $key): array
    {
        $value = $this->cache->get($this->getKey($key));
        return $value !== null ? \json_decode($value, true) : [];
    }

    public function set(string $key, array $results): void
    {
        $this->cache->set($key, \json_encode($results), $this->getTTl());
    }

    public function has(string $key): bool
    {
        return $this->cache->has($this->getKey($key));
    }

    private function getKey(string $key): string
    {
        return self::CACHE_KEY_PREFIX . $key;
    }

    private function getTTl(): int
    {
        return  (int) env('SEARCH_BOOKS_CACHE_TTL', self::SEARCH_BOOKS_CACHE_TTL);
    }
}
