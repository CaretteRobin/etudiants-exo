<?php

declare(strict_types=1);

namespace App\Middleware;

use Closure;
use Slim\Http\Request;
use Slim\Http\Response;

final class TrailingSlashMiddleware
{
    public function __invoke(Request $request, Response $response, Closure $next): Response
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path !== '/' && str_ends_with($path, '/')) {
            $uri = $uri->withPath(substr($path, 0, -1));

            if ($request->getMethod() === 'GET') {
                return $response->withRedirect((string) $uri, 301);
            }

            return $next($request->withUri($uri), $response);
        }

        return $next($request, $response);
    }
}
