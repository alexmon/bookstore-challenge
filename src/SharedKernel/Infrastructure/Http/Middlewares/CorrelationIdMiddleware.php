<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Ramsey\Uuid\Uuid;

class CorrelationIdMiddleware
{
    protected string $header = 'X-Correlation-ID';

    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->headers->get($this->header);

        if (empty($correlationId)) {
            $correlationId = (string) Uuid::uuid4();
        }

        $request->attributes->set('correlationId', $correlationId);

        $response = $next($request);

        $response->headers->set($this->header, $correlationId);

        return $response;
    }
}
