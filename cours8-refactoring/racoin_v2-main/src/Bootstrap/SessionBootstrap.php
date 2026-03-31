<?php

declare(strict_types=1);

namespace App\Bootstrap;

final class SessionBootstrap
{
    public static function boot(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['formStarted'] ??= true;

        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = md5(uniqid((string) rand(), true));
            $_SESSION['token_time'] = time();
        }
    }
}
