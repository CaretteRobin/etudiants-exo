<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Bootstrap\AppFactory;

$app = (new AppFactory(__DIR__))->create();
$app->run();
