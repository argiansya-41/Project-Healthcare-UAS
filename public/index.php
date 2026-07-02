<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

try {
    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>Primary Boot Error Chain</h1>";
    
    $current = $e;
    $index = 1;
    while ($current) {
        echo "<hr><h3>Exception #{$index}: " . htmlspecialchars(get_class($current)) . "</h3>";
        echo "<p><b>Message:</b> " . htmlspecialchars($current->getMessage()) . "</p>";
        echo "<p><b>File:</b> " . htmlspecialchars($current->getFile()) . " on line " . $current->getLine() . "</p>";
        echo "<pre>" . htmlspecialchars($current->getTraceAsString()) . "</pre>";
        $current = $current->getPrevious();
        $index++;
    }
}
