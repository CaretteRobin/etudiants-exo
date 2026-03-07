#!/usr/bin/env sh
set -eu

composer install --no-interaction --prefer-dist --no-plugins

exec php -S 0.0.0.0:80 -t . router.php
