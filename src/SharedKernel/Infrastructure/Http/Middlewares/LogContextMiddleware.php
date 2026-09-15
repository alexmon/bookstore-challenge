<?php

declare(strict_types=1);

namespace BookStoreAPI\SharedKernel\Infrastructure\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->attributes->get('correlationId');

        if (! empty($correlationId)) {
            Log::withContext([
                'correlationId' => $correlationId,
            ]);
        }

        return $next($request);
    }
}
