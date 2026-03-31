<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Infrastructure\Database\Connection;
use App\Logging\LoggerFactory;
use App\Middleware\HttpRequestLoggerMiddleware;
use App\Middleware\TrailingSlashMiddleware;
use App\Routes;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class AppFactory
{
    public function __construct(private readonly string $projectRoot)
    {
    }

    public function create(): App
    {
        Connection::createFromConfig($this->projectRoot . '/config/config.ini');
        SessionBootstrap::boot();

        $app = new App([
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ]);

        $twig = new Environment(new FilesystemLoader($this->projectRoot . '/template'));
        $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        $httpLogger = LoggerFactory::createHttpLogger($this->projectRoot . '/var/log/http.log');

        $app->add(new HttpRequestLoggerMiddleware($httpLogger));
        $app->add(new TrailingSlashMiddleware());

        Routes::register($app, $twig, $basePath);

        return $app;
    }
}
