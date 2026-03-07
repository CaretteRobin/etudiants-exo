<?php

namespace db;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class connection {

    public static function createConn() {
        $capsule = new DB;
        $configPath = "./config/config.ini";

        if (file_exists($configPath)) {
            $connectionConfig = parse_ini_file($configPath);
        } else {
            $connectionConfig = array(
                'driver' => getenv('DB_DRIVER') ?: 'mysql',
                'host' => getenv('DB_HOST') ?: 'db',
                'database' => getenv('DB_DATABASE') ?: 'racoin',
                'username' => getenv('DB_USERNAME') ?: 'racoin',
                'password' => getenv('DB_PASSWORD') ?: 'racoin',
                'port' => getenv('DB_PORT') ?: '3306',
                'charset' => getenv('DB_CHARSET') ?: 'utf8',
                'collation' => getenv('DB_COLLATION') ?: 'utf8_general_ci',
                'prefix' => getenv('DB_PREFIX') ?: '',
            );
        }

        $capsule->addConnection($connectionConfig);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
