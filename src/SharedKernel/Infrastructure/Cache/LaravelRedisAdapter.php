<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Cache;

use Illuminate\Support\Facades\Redis;
use Psr\SimpleCache\CacheInterface;

class LaravelRedisAdapter implements CacheInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Redis::get($key) ?? $default;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        if ($ttl instanceof \DateInterval) {
            $ttl = (new \DateTime())->add($ttl)->getTimestamp() - time();
        }

        if ($ttl !== null) {
            return (bool) Redis::set($key, $value, 'EX', $ttl);
        }

        return (bool) Redis::set($key, $value);
    }

    public function delete(string $key): bool
    {
        return Redis::del($key) > 0;
    }

    public function clear(): bool
    {
        return Redis::flushDB()->getPayload() === 'OK';
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = Redis::mget($keys);
        return array_map(fn($value) => $value ?? $default, $results);
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            $success = $success && $this->set($key, $value, $ttl);
        }
        return $success;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return Redis::del($keys) > 0;
    }

    public function has(string $key): bool
    {
        return Redis::exists($key) > 0;
    }
}
