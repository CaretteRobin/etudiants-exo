<?php

declare(strict_types=1);

namespace Tests\Middleware;

use App\Middleware\HttpRequestLoggerMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

final class HttpRequestLoggerMiddlewareTest extends TestCase
{
    public function testItLogsEachRequestWithStatusCode(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('info')
            ->with(
                'GET /demo',
                self::callback(static function (array $context): bool {
                    return $context['status'] === 204
                        && isset($context['duration_ms'])
                        && $context['ip'] === '127.0.0.1';
                })
            );

        $middleware = new HttpRequestLoggerMiddleware($logger);
        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn(new Uri('', '', null, '/demo'));
        $request->method('getServerParam')->with('REMOTE_ADDR', 'unknown')->willReturn('127.0.0.1');

        $response = $this->createMock(Response::class);
        $result = $this->createMock(Response::class);
        $result->method('getStatusCode')->willReturn(204);

        $result = $middleware($request, $response, static fn (Request $request, Response $response): Response => $result);

        self::assertSame(204, $result->getStatusCode());
    }
}
