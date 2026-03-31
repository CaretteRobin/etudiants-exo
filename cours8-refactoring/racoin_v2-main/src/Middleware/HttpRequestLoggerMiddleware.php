<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class HttpRequestLoggerMiddleware
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request, Response $response, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request, $response);
        $durationMs = (microtime(true) - $start) * 1000;

        $this->logger->info(
            sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath()),
            [
                'status' => $response->getStatusCode(),
                'duration_ms' => round($durationMs, 2),
                'ip' => $request->getServerParam('REMOTE_ADDR', 'unknown'),
            ]
        );

        return $response;
    }
}
