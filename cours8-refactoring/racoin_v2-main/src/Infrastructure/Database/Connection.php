<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

final class Connection
{
    public static function createFromConfig(string $configPath): void
    {
        $configuration = parse_ini_file($configPath);
        if ($configuration === false) {
            throw new RuntimeException(sprintf('Unable to read database configuration from "%s".', $configPath));
        }

        $capsule = new Capsule();
        $capsule->addConnection($configuration);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
