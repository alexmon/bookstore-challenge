<?php

declare(strict_types=1);

namespace BookStoreApi\SharedKernel\Infrastructure\Service;

class ExceptionHelper
{
    public static function getFileLineAsString(\Throwable $e): string
    {
        return $e->getFile() . ':' . $e->getLine();
    }
}
