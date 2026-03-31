<?php

declare(strict_types=1);

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public static function createHttpLogger(string $logFilePath): LoggerInterface
    {
        $logDirectory = dirname($logFilePath);
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        $handler = new StreamHandler($logFilePath, Level::Info);
        $handler->setFormatter(new LineFormatter("[%datetime%] %level_name% %message% %context%\n", 'Y-m-d H:i:s', true, true));

        return new Logger('http', [$handler]);
    }
}
