<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (str_contains(__DIR__, '/var/task')) {
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo "<h1>ORIGINAL BOOT EXCEPTION DETECTED!</h1>";
            echo "<p><b>Class:</b> " . get_class($e) . "</p>";
            echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
            
            $certPath = sys_get_temp_dir() . '/isrgrootx1.pem';
            echo "<h2>SSL CA Debug Info</h2>";
            echo "<p><b>Temp Dir:</b> " . htmlspecialchars(sys_get_temp_dir()) . "</p>";
            echo "<p><b>Cert Path:</b> " . htmlspecialchars($certPath) . "</p>";
            echo "<p><b>Cert File Exists:</b> " . (file_exists($certPath) ? 'YES' : 'NO') . "</p>";
            if (file_exists($certPath)) {
                echo "<p><b>Cert File Size:</b> " . filesize($certPath) . " bytes</p>";
            }
            
            echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            exit;
        });
    })->create();
